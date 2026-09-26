<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\db\Expression;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\InpostOperasi;
use app\modules\v1\models\InpostOperasiDetail;
use GuzzleHttp\Exception\RequestException;
use Doco\Services\KasirService;
use Doco\models\Bedah\PemeriksaanPelengkap;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\StokObatAlkes;
use Doco\models\Bedah\PasienMasukPenunjang;
use app\modules\v1\models\BmhpOperasi;
use app\modules\v1\models\InfoPasienOperasiView;
use SirsCore\businessLogic\TagihanBedah;
use app\modules\v1\models\VerifikasiBedahR;
use app\modules\v1\models\TimOperasi;
use Doco\exceptions\ValidationException;
use SirsCore\features\FeatureTindakanBmhp;
use yii\helpers\ArrayHelper;

class VerifikasiTagihanController extends DocoActiveController
{
   public $modelClass = 'app\modules\v1\models\InpostOperasiDetail';
   public function verbs()
   {
      $verbs = parent::verbs();
      $verbs["index"] = ["GET"];
      return $verbs;
   }

   public function actions()
   {
      $actions = parent::actions();
      unset($actions['index']);
      unset($actions['delete']);
      unset($actions['view']);
      unset($actions['create']);
      unset($actions['update']);
      return $actions;
   }

   public function init()
   {
      parent::init();
      $this->request = Yii::$app->request;
   }

   /**
    * Function Get Bills
    * 
    * @param String inpostoperasi_id
    * @return Array []
    * @author : Rizqi Fitrianto (rizqi@docotel.com)
    * @modified : Budi
    * A product of PT. Docotel Teknologi
    * Powered by Sirs
    */
   public function actionGetBills()
   {
      return Yii::$app->docoPlugin->execute('get_bills');
   }

   /**
    * Function Save Bills
    * 
    * @param String pasienpenunjangId
    * @return Array result
    * @author : Rizqi Fitrianto (rizqi@docotel.com)
    * @modified : Budi
    * A product of PT. Docotel Teknologi
    * Powered by Sirs
    */
   public function actionSaveVerifikasi()
   {
      return Yii::$app->docoPlugin->execute('save_verifikasi');
   }

   public function actionGetVerifikasiLog(){
      $posisiDokterOperator = DocoConstants::TIM_OPERASI_DOKTER_BEDAH;
      $pasienpenunjangId = $this->request->get('pasienpenunjangId');
      $result = [];
      $verifikasiBedah = VerifikasiBedahR::find()
         ->where(['pasienmasukpenunjang_id' => $pasienpenunjangId])
         ->orderBy(['id' => SORT_ASC])
         ->asArray()->all();
      
      if(!empty($verifikasiBedah)) {
         foreach ($verifikasiBedah as $row ){
               $row['isDisabled'] = "disabled";
               if($row['total_harga'] == $row['total_harga_real'] ){
                  $row['isChecked'] = "checked";
               }else{
                  $row['isChecked'] = "";
               }
               if($row['kode_posisi'] == $posisiDokterOperator) {
                  $row['is_dokteroperator'] = true;
               }else{
                  $row['is_dokteroperator'] = false;
               }
               $row['harga_persentase'] = $row['total_harga_real'] - $row['total_harga'];
               array_push($result, $row);
         }
      }
      
      return $result;
   }

   public function actionBatalVerifikasi()
   {
      $request = Yii::$app->request;
      $no_masukpenunjang = $request->post('no_masukpenunjang', null);
      $dataPenunjang = PasienMasukPenunjang::find()->where(['no_masukpenunjang' => $no_masukpenunjang])->one();
      if(empty($dataPenunjang)) {
         return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION, [
               'text' => 'Data Penunjang tidak ditemukan!'
         ]);
      }
      $ruangan_id = isset($dataPenunjang['ruangan_id']) ? $dataPenunjang['ruangan_id'] : null;
      $pendaftaranId = isset($dataPenunjang['pendaftaran_id']) ? $dataPenunjang['pendaftaran_id'] : null;
      $cekPembayaran = Yii::$app->db->createCommand("SELECT no_pendaftaran, status_bayar FROM pendaftaran_t WHERE pendaftaran_id = $pendaftaranId AND is_deleted = FALSE")->queryOne();
      $noPendaftaran = isset($cekPembayaran['no_pendaftaran']) ? $cekPembayaran['no_pendaftaran'] : null;
      $status_bayar = isset($cekPembayaran['status_bayar']) ? $cekPembayaran['status_bayar'] : null;
      if($status_bayar == DocoConstants::LUNAS) {
         return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION, [
               'text' => 'Pasien sudah melakukan pembayaran, tidak bisa membatalkan verifikasi!'
         ]);
      }
      
      $pasienmasukpenunjang_id = ($dataPenunjang) ? $dataPenunjang['pasienmasukpenunjang_id'] : null;
      $dataRuangan = Ruangan::findOne($ruangan_id);
      $instalasi_id = ($dataRuangan) ? $dataRuangan['instalasi_id'] : null;
      $inpostOperasi = InpostOperasi::find()->where(['pasienmasukpenunjang_id' => $pasienmasukpenunjang_id])->one();
      if(empty($inpostOperasi)) {
         return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION, [
               'text' => 'Data Operasi tidak ditemukan!'
         ]);
      }
      
      $inpostoperasi_id = ($inpostOperasi) ? $inpostOperasi['inpostoperasi_id'] : null;
      $params = [
         'no_pendaftaran' => $noPendaftaran,
         'no_masukpenunjang' => $no_masukpenunjang,
         'ruangan_id' => $ruangan_id,
         'instalasi_id' => $instalasi_id,
         'detail_obat' => [],
      ];
      
      $connection = Yii::$app->db;
      $transaction = $connection->beginTransaction();
      $arrData = $obatId = [];
      try {
         /** 
         *  ? delete bmhp & balikin stok
         */ 
         
         $getObatAlkes = ObatAlkesPasien::find()->select(['obatalkespasien_id', 'obatalkes_id', 'ruangan_id'])
            ->where(['pasienmasukpenunjang_id' => $pasienmasukpenunjang_id, 'ruangan_id' => $ruangan_id ])
            ->asArray()->all();
         
         if( !empty($getObatAlkes )) {
            $arrData = $obatAlkesPasienId = [];
            foreach($getObatAlkes as $key => $value) {
               $obatAlkesPasienId[] = isset($value['obatalkespasien_id']) ? $value['obatalkespasien_id'] : null;
            }
            $integrateHapusBmhp = FeatureTindakanBmhp::hapusTindakanBmhp($obatAlkesPasienId);
            // $inCondition = "(" . implode(",", $obatAlkesPasienId) . ")";
            // $now = date('Y-m-d H:i:s');
            // $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
            // $userId = !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null;
            // $deleteObat = "UPDATE obatalkespasien_t SET is_deleted = TRUE, deleted_date = '".$now."', deleted_by = {$userId} WHERE obatalkespasien_id IN {$inCondition}";
            // $deleteObat = Yii::$app->db->createCommand($deleteObat)->execute();
            // $stokObatAlkes = "SELECT * FROM stokobatalkes_t WHERE obatalkespasien_id IN {$inCondition}";
            // $stokObatAlkes = Yii::$app->db->createCommand($stokObatAlkes)->queryAll();
            
            // if(!empty($stokObatAlkes)) {
            //    foreach($stokObatAlkes as $key => $value) {
            //       $arrData[] = [
            //          'ruangan_id' => $value['ruangan_id'],
            //          'obatalkespasien_id' => $value['obatalkespasien_id'],
            //          'obatalkes_id' => $value['obatalkes_id'],
            //          'tglkadaluarsa' => $value['tglkadaluarsa'],
            //          'nobatch' => $value['nobatch'],
            //          'tglstok_in' => date('Y-m-d H:i:s'),
            //          'qtystok_in' => $value['qtystok_out'],
            //          'qtystok_out' => 0,
            //          'harganetto' => $value['harganetto'],
            //          'persendiscount' => $value['persendiscount'],
            //          'jmldiscount' => $value['jmldiscount'],
            //          'persenppn' => $value['persenppn'],
            //          'jmlppn' => $value['jmlppn'],
            //          'persenmargin' => $value['persenmargin'],
            //          'jmlmargin' => $value['jmlmargin'],
            //          'stokoa_aktif' => $value['stokoa_aktif'],
            //          'stokobatalkesasal_id' => $value['stokobatalkesasal_id'],
            //          'satuankecil_id' => $value['satuankecil_id'],
            //          'persenpph' => $value['persenpph'],
            //       ];
            //    }
            //    StokObatAlkes::batchInsert($arrData, false);
            // }
         }
         
         /** 
         *  ? update status verifikasi & status periksa
         */ 
         InpostOperasiDetail::updateAll([
            'is_verifikasi' => false
         ], "inpostoperasi_id = ".$inpostoperasi_id);
      
         PasienMasukPenunjang::updateAll([
            'status_periksa' => DocoConstants::ST_P_PEN_SDG_OPRS
         ], 'pasienmasukpenunjang_id = '.$pasienmasukpenunjang_id);

         // $logVerifikasi = VerifikasiBedahR::find()
         //    ->where(['pasienmasukpenunjang_id' => $pasienmasukpenunjang_id])
         //    ->all();
         
         // if(!empty($logVerifikasi)) {
         //    (new VerifikasiBedahR)->delete([
         //       'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id
         //    ]);
         // }

         // $intra = InpostOperasi::find()->where(['pasienmasukpenunjang_id' => $pasienmasukpenunjang_id])->one();
         // if(!empty($intra)) {
         //    $additionalData = json_encode([
         //       'pos' => 3
         //    ]);
         //    $intra->additional_data = $additionalData;
         //    if (!$intra->validate() || !$intra->save()) {
         //       \Yii::error(["errors" => $intra->getErrors()]);
         //       return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION, [
         //          'text' => 'Terjadi kesalahan saat simpan.'
         //       ]);
         //    }
         // }

         /** 
         *  ? batal billing
         */
         $billKasir = (new KasirService)->batalTindakan($params, []);
         if(isset($billKasir['meta']['result'])) {
            $result = $billKasir['meta']['result'];
            if($result == 'failed') {
               return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION, [
                  'text' => ArrayHelper::getValue($billKasir, 'message')
               ]);
            }
         }
         
         $transaction->commit();
         return [
            'title' => 'Proses berhasil', 
            'text' => '<b> Verifikasi Berhasil di Batalkan </b>'
         ];

      } catch (\Exception $e) {
         $transaction->rollBack();
         \Yii::$app->response->statusCode = 500;
         \Yii::error([
            "File" => $e->getFile(),
            "Message" => $e->getMessage(),
            "Line" => $e->getLine(),
         ]);
         return [
            'message' => $e->getMessage()
         ];
      }
   }

   public function actionGetDataBills($pasienpenunjangId)
   {
      $statusVerfikasi = PasienMasukPenunjang::find()
         ->selectStatusPeriksa()
         ->findById($pasienpenunjangId)
         ->one();

      $data = VerifikasiBedahR::find()
         ->where(['pasienmasukpenunjang_id' => $pasienpenunjangId])
         ->orderBy(['id' => SORT_ASC, 'kode_posisi' => SORT_ASC])
         ->all();

      return [
         'status_operasi' => $statusVerfikasi,
         'data' => $data,
      ];
   }

   public function actionEditVerifikasi()
   {
      $request = Yii::$app->request;
      $id = $request->get('id', null);
      $type = $request->get('type', null);
      $tindakanId = $request->get('tindakan_id', null);
      $connection = Yii::$app->db;
      $transaction = $connection->beginTransaction();
      $posisiOperator = DocoConstants::TIM_OPERASI_DOKTER_BEDAH;
      try {
         if(!empty($id)) {
            $model = VerifikasiBedahR::findOne($id);
            $daftartindakan_id = ($model->daftartindakan_id) ? $model->daftartindakan_id : null;
            $isDokterOp = ($model->kode_posisi == $posisiOperator) ? true : false;
            $total = 0;
            $qty = $request->get('qty', 1);
            $persentase = $request->get('persentase', 0);
            $is_cyto = $request->get('is_cyto', null);
            $is_cyto = is_null($is_cyto) ? $model->is_cyto : $is_cyto;
            $is_cyto = filter_var($is_cyto, FILTER_VALIDATE_BOOLEAN);
            $is_penyulit = $request->get('is_penyulit', null);
            $is_penyulit = is_null($is_penyulit) ? $model->is_penyulit : $is_penyulit;
            $is_penyulit = filter_var($is_penyulit, FILTER_VALIDATE_BOOLEAN);
            $qty = !empty($qty) ? $qty : $model->qty;
            $persentase = !empty($persentase) ? $persentase : $model->persentase;
            $hargaCyto = is_null($is_cyto) ? $model->harga_cyto : (($is_cyto) ? ($model->persencyto_tindakan/100) * ($model->harga) : 0);
            $hargaPenyulit = is_null($is_penyulit) ? $model->harga_penyulit : (($is_penyulit) ? ($model->persen_penyulit/100) * ($model->harga) : 0);
            $total = ($model->harga + $hargaPenyulit + $hargaCyto) * (($persentase /100) * $qty);
            $penunjangId = $request->get('penunjang_id', null);

            if(!empty($request->get('qty')) || !empty($request->get('persentase'))) {
               if(!$isDokterOp) {
                  $timoperasi_id = ($model) ? $model->timoperasi_id : null;
                  if(!empty($timoperasi_id)) {
                     $timOperasi = TimOperasi::findOne($timoperasi_id);
                     $daftartindakan_id = ($timOperasi) ? $timOperasi->daftartindakan_id : null;
                  }
                  $verifikasiBedah = $this->getTarifOperator($penunjangId, $daftartindakan_id);
                  $hargaSatuan = ArrayHelper::getValue($verifikasiBedah, 'harga', 0);
                  $hargaCyto = ArrayHelper::getValue($verifikasiBedah, 'harga_cyto', 0);
                  $hargaPenyulit = ArrayHelper::getValue($verifikasiBedah, 'harga_penyulit', 0);
                  $total = $hargaSatuan + $hargaCyto + $hargaPenyulit;
                  $harga = ($persentase/100) * $total;
                  $total = $harga * $qty;
                  $model->total_harga_real = $total;
                  $model->harga = $harga;
               }
            }
            
            $model->qty = $qty;
            $model->persentase = $persentase;
            $model->is_cyto = $is_cyto;
            $model->is_penyulit = $is_penyulit;
            $model->harga_cyto = $isDokterOp ? $hargaCyto : 0;
            $model->harga_penyulit = $isDokterOp ? $hargaPenyulit : 0;
            $model->total_harga = $total;
            $model->total_harga_real = $total;
            if($model->save()) {
               if($isDokterOp) {
                  $timoperasi_id = ($model) ? $model->timoperasi_id : null;
                  $penunjang_id = $request->get('penunjang_id');
                  if(!empty($timoperasi_id)) {
                     $timOperasi = TimOperasi::findOne($timoperasi_id);
                     $daftartindakan_id = ($timOperasi) ? $timOperasi->daftartindakan_id : null;
                     $additionalData = json_encode([
                        'qty' => $model->qty,
                        'persentase' => $model->persentase,
                        'harga' => $total,
                        'is_cyto' => $model->is_cyto,
                        'is_penyulit' => $model->is_penyulit,
                        'persencyto_tindakan' => $model->persencyto_tindakan,
                        'persen_penyulit' => $model->persen_penyulit,
                        'harga_cyto' => $model->harga_cyto,
                        'harga_penyulit' => $model->harga_penyulit,
                     ]);
                     $timOperasi->additional_data = $additionalData;
                     $timOperasi->save();
                  }
                  
                  $timOperasi = TimOperasi::find()
                     ->where(['pasienmasukpenunjang_id' => $penunjang_id, 'daftartindakan_id' => $daftartindakan_id])
                     ->andWhere(['<>', 'posisi_tim', $posisiOperator])
                     ->all();
                  $timoperasiId = [];
                  $inCondition = '';
                  if(!empty($timOperasi)) {
                     foreach ($timOperasi as $key => $value) {
                        $timoperasiId[] = $value['timoperasi_id'];
                     }
                     $inCondition = "(" . implode(",", $timoperasiId) . ")";
                     $log = "SELECT * FROM verifikasibedah_r WHERE pasienmasukpenunjang_id = $penunjang_id AND timoperasi_id IN {$inCondition}";
                     $queryLog = Yii::$app->db->createCommand($log)->queryAll();
                     
                     if(!empty($queryLog)) {
                        foreach ($queryLog as $key => $value) {
                           $primaryKey = $value['id'];
                           $persentase = !empty($value['persentase']) ? $value['persentase'] : 0;
                           $child = VerifikasiBedahR::findOne($primaryKey);
                           $tarifOperator = $model->harga + $model->harga_cyto + $model->harga_penyulit;
                           $totalHargaChild = ($persentase/100) * $tarifOperator;
                           $child->total_harga = $totalHargaChild * $child->qty;
                           $child->harga = $totalHargaChild;
                           $child->total_harga_real = $child->total_harga;
                           $child->is_cyto = $is_cyto;
                           $child->is_penyulit = $is_penyulit;
                           $child->save();
                           
                           $updateTimOperasi = TimOperasi::findOne($child->timoperasi_id);
                           $additionalData = json_encode([
                              'qty' => $child->qty,
                              'persentase' => $persentase,
                              'harga' => $totalHargaChild,
                           ]);
                           $updateTimOperasi->additional_data = $additionalData;
                           $updateTimOperasi->save();
                        }
                     }
                  }
               }
               else {
                  $postOperasi = InpostOperasi::find()
                  ->where(['pasienmasukpenunjang_id' => $request->get('penunjang_id')])
                  ->one();
                  
                  if($postOperasi) {
                     $detail = InpostOperasiDetail::find()->where([
                        'inpostoperasi_id' => $postOperasi['inpostoperasi_id'],
                        'daftartindakan_id' => $tindakanId,
                     ])->one();

                     if($detail) {
                        $detail->is_cyto = $model->is_cyto;
                        $detail->is_penyulit = $model->is_penyulit;
                        $detail->save();
                     }
                     if(!empty($request->get('qty')) || !empty($request->get('persentase'))) {
                        $timoperasi_id = ($model) ? $model->timoperasi_id : null;
                        if(!empty($timoperasi_id)) {
                           $timOperasi = TimOperasi::findOne($timoperasi_id);
                           $daftartindakan_id = ($timOperasi) ? $timOperasi->daftartindakan_id : null;
                        }
                        
                        $verifikasiBedah = $this->getTarifOperator($penunjangId, $daftartindakan_id);
                        $tarifOperator = ArrayHelper::getValue($verifikasiBedah, 'harga', 0);
                        $tarifCitoOperator = ArrayHelper::getValue($verifikasiBedah, 'harga_cyto', 0);
                        $tarifPenyulitOperator = ArrayHelper::getValue($verifikasiBedah, 'harga_penyulit', 0);
                        $totalHargaOperator = $tarifOperator + $tarifCitoOperator + $tarifPenyulitOperator;
                        $persentase = $request->get('persentase', 0);
                        if(!$persentase) {
                           $persentase = $model->persentase;
                        }
                        $additionalData = json_encode([
                           'qty' => $model->qty,
                           'persentase' => $persentase,
                           'harga' => ($persentase/100) * $totalHargaOperator
                        ]);
                        
                        $update = TimOperasi::findOne($timoperasi_id);
                        if($update) {
                           $update->additional_data = $additionalData;
                           $update->save();
                        }
                     }
                  }
               }
               $transaction->commit();
               return [
                  'title' => 'Proses berhasil', 
                  'text' => '<b> Data Berhasil di Update </b>'
               ];
            }
         }
      } catch (\Exception $e) {
         $transaction->rollBack();
         \Yii::$app->response->statusCode = 500;
         \Yii::error([
            "File" => $e->getFile(),
            "Message" => $e->getMessage(),
            "Line" => $e->getLine(),
         ]);
         return [
            'message' => $e->getMessage()
         ];
      }
   }

   public function actionSaveLogVerifikasi()
   {
      $pasienpenunjangId = $this->request->post('pasienpenunjangId', null);
      $dataPenunjang = PasienMasukPenunjang::findOne($pasienpenunjangId);
      $ruangan_id = isset($dataPenunjang['ruangan_id']) ? $dataPenunjang['ruangan_id'] : null;
      if(is_null($pasienpenunjangId)){
         throw new ValidationException(422, $this->_error, [
            'text' => 'Parameter pasienpenunjangId tidak boleh kosong!'
         ]);
      }
      $statusPeriksa = PasienMasukPenunjang::find()
         ->selectStatusPeriksa()
         ->findById($pasienpenunjangId)
         ->one();

      if($statusPeriksa['status_periksa'] != DocoConstants::ST_P_PEN_SDH_OPRS) {
         $this->insertLogBilling($pasienpenunjangId, $ruangan_id);
      }
      $pendaftaranId = $dataPenunjang->pendaftaran_id;
      $isStopAkomodasi = Yii::$app->db->createCommand("SELECT is_stopakomodasi FROM pendaftaran_t WHERE pendaftaran_id = $pendaftaranId AND is_deleted = FALSE")->queryOne();
      return [
         'status_periksa' => ($statusPeriksa) ? $statusPeriksa['status_periksa'] : null,
         'is_stop_akomodasi' => ($isStopAkomodasi) ? $isStopAkomodasi['is_stopakomodasi'] : false,
      ];
   }

   private function insertLogBilling($pasienpenunjangId, $ruangan_id)
   {
      $dataInsert = [];
      $dataBills = (new TagihanBedah)->getBills($pasienpenunjangId, $ruangan_id);
      $posisiDokterOperator = DocoConstants::TIM_OPERASI_DOKTER_BEDAH;
      $detail = ArrayHelper::getValue($dataBills, 'detail', []);
      
      if(!empty($detail)) {
         foreach ($detail as $key => $value) {
            $kode_posisi = isset($value['posisi_tim']) ? $value['posisi_tim'] : null;
            $harga = isset($value['harga']) ? $value['harga'] : 0;
            $total_harga = isset($value['total_harga']) ? $value['total_harga'] : 0;
            $persentase = isset($value['persentase']) ? $value['persentase'] : 0;
            $daftartindakan_id = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
            $daftartindakan_nama = isset($value['daftartindakan_nama']) ? $value['daftartindakan_nama'] : null;
            $is_akomodasi = isset($value['is_akomodasi']) ? $value['is_akomodasi'] : false;
            $pegawai_id = isset($value['dokter_id']) ? $value['dokter_id'] : null;
            $kode_posisi = isset($value['posisi_tim']) ? $value['posisi_tim'] : null;
            if($value['useprice']) {
               $pegawai_id = isset($value['pegawai_id']) ? $value['pegawai_id'] : null;
            }
            if($kode_posisi != $posisiDokterOperator && $value['useprice'] && !$is_akomodasi) {
               $daftarTindakan = $this->getMappingTindakanOperasi($kode_posisi);
               $daftartindakan_id = ($daftarTindakan) ? $daftarTindakan['daftartindakan_id'] : null;
               $daftartindakan_nama = ($daftarTindakan) ? $daftarTindakan['daftartindakan_nama'] : null;
            }
            if ($kode_posisi == DocoConstants::TIM_OPERASI_DOKTER_BEDAH) {
               $tindakan_id =  ArrayHelper::getValue($value, 'daftartindakan_id');
               $tindakan_nama = ArrayHelper::getValue($value, 'daftartindakan_nama');
            }
            
            if (ArrayHelper::getValue($value, 'kegiatanoperasi_id', false)) {
                $cekMapping = $this->getMappingTindakanOperasi($kode_posisi, $tindakan_id, true);
                if (!$cekMapping) {
                  $daftartindakan_id = $tindakan_id;
                  $daftartindakan_nama = $tindakan_nama;
                }
            }
            if(!$value['useprice'] && isset($value['obatalkes_id'])) {
               if($harga == 0){
                  continue;
               }
               $daftartindakan_id = isset($value['obatalkes_id']) ? $value['obatalkes_id'] : null;
               $daftartindakan_nama = isset($value['obatalkes_nama']) ? $value['obatalkes_nama'] : null;
            }
            $dataInsert[] = [
               'inpostoperasi_id' => isset($value['inpostoperasi_id']) ? $value['inpostoperasi_id'] : null,
               'pasienmasukpenunjang_id' => isset($value['pasienmasukpenunjang_id']) ? (int) $value['pasienmasukpenunjang_id'] : null,
               'timoperasi_id' => isset($value['timoperasi_id']) ? $value['timoperasi_id'] : null,
               'posisi_tim' => isset($value['posisi']) ? $value['posisi'] : null,
               'posisi_operasi' => isset($value['posisi']) ? $value['posisi'] : null,
               'kode_posisi' => isset($value['posisi_tim']) ? $value['posisi_tim'] : null,
               'dokter_id' => $pegawai_id,
               'nama_pegawai' => isset($value['nama_pegawai']) ? $value['nama_pegawai'] : null,
               'daftartindakan_id' => $daftartindakan_id,
               'daftartindakan_nama' => $daftartindakan_nama,
               'persentase' => $persentase,
               'harga' => $harga,
               'operasi_id' => isset($value['operasi_id']) ? $value['operasi_id'] : null,
               'operasi_nama' => isset($value['operasi_nama']) ? $value['operasi_nama'] : null,
               'kegiatanoperasi_id' => isset($value['kegiatanoperasi_id']) ? $value['kegiatanoperasi_id'] : null,
               'kegiatanoperasi_nama' => isset($value['kegiatanoperasi_nama']) ? $value['kegiatanoperasi_nama'] : null,
               'golonganoperasi_id' => isset($value['golonganoperasi_id']) ? $value['golonganoperasi_id'] : null,
               'golonganoperasi_nama' => isset($value['golonganoperasi_nama']) ? $value['golonganoperasi_nama'] : null,
               'pegawai_input' => isset($value['pegawai_input']) ? $value['pegawai_input'] : null,
               'useprice' => isset($value['useprice']) ? $value['useprice'] : false,
               'perawat_id' => isset($value['perawat_id']) ? $value['perawat_id'] : null,
               'tipepaket_id' => isset($value['tipepaket_id']) ? $value['tipepaket_id'] : null,
               'qty' => isset($value['qty']) ? $value['qty'] : 1,
               'is_cyto' => isset($value['is_cyto']) ? $value['is_cyto'] : false,
               'is_penyulit' => isset($value['is_penyulit']) ? $value['is_penyulit'] : false,
               'persentase_harga' => isset($value['persentase_harga']) ? $value['persentase_harga'] : 0,
               'harga_persentase' => isset($value['harga_persentase']) ? $value['harga_persentase'] : 0,
               'is_dokteroperator' => isset($value['is_dokteroperator']) ? $value['is_dokteroperator'] : false,
               'persencyto_tindakan' => isset($value['persencyto_tindakan']) ? $value['persencyto_tindakan'] : 0,
               'persen_penyulit' => isset($value['persen_penyulit']) ? $value['persen_penyulit'] : 0,
               'harga_cyto' => isset($value['harga_cyto']) ? $value['harga_cyto'] : 0,
               'harga_penyulit' => isset($value['harga_penyulit']) ? $value['harga_penyulit'] : 0,
               'total_harga' => $total_harga,
               'total_harga_real' => $total_harga,
               'additional_data' => json_encode(['is_akomodasi' => $is_akomodasi]),
            ];
         }
      }
      VerifikasiBedahR::deleteAll('pasienmasukpenunjang_id=:pasienmasukpenunjang_id', [
         ':pasienmasukpenunjang_id' => $pasienpenunjangId
      ]);
      VerifikasiBedahR::batchInsert($dataInsert);
   }

   private function getMappingTindakanOperasi($kode_posisi, $daftartindakan_id = null, $cek = false)
   {
      if ($cek) {
         if ($kode_posisi !== null) {
            $conditions = "WHERE tindakanoperasi_mp.timoperasi_id = $kode_posisi AND tindakanoperasi_mp.daftartindakan_id = $daftartindakan_id";
         } else {
               $conditions = "WHERE tindakanoperasi_mp.daftartindakan_id = $daftartindakan_id";
         }
      } else {
         if ($kode_posisi !== null) {
            $conditions = "WHERE tindakanoperasi_mp.timoperasi_id = $kode_posisi";
         } else {
               $conditions = "";
         }
      }

      $data = "SELECT tindakanoperasi_mp.daftartindakan_id, daftartindakan_m.daftartindakan_nama
      FROM tindakanoperasi_mp 
      JOIN daftartindakan_m ON tindakanoperasi_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
      $conditions";

      return Yii::$app->db->createCommand($data)->queryOne();
   }


   private function getTarifOperator($penunjangId, $daftartindakanId)
   {
      $posisiOperator = DocoConstants::TIM_OPERASI_DOKTER_BEDAH;
      return VerifikasiBedahR::find()->where([
         'pasienmasukpenunjang_id' => $penunjangId,
         'daftartindakan_id' => $daftartindakanId, 
         'kode_posisi' => $posisiOperator
      ])->one();
   }

   public function actionDeleteLaporanDokter(){
      $request = Yii::$app->request;
      $dataLaporan = [];
      $id = $request->get('id', null);
      $dokterId = $request->get('dokter_id', null);
      $penunjangId = $request->get('penunjang_id', null);
      $date_now = date('Y-m-d H:i:s');
      $loginpemakai_id = isset(Yii::$app->jwt->user->loginpemakai_id) ? Yii::$app->jwt->user->loginpemakai_id : null ;
      try {
         $connection = Yii::$app->db;
         $transaction = $connection->beginTransaction();
         $deletedArr = "is_deleted = true, is_active = false, deleted_date = '{$date_now}', deleted_by = {$loginpemakai_id}";
         $result = $connection->createCommand("update laporanoperasi_r set {$deletedArr} where laporanoperasi_id = {$id}")->execute();
         $transaction->commit();
         return [
             'status' => 200,
             'title' => 'Proses Berhasil',
             'text' => 'Penghapusan Laporan Operasi Berhasil'
         ];
      } catch (\Exception $e) {
         $transaction->rollBack();
         \Yii::$app->response->statusCode = 500;
         \Yii::error([
            "File" => $e->getFile(),
            "Message" => $e->getMessage(),
            "Line" => $e->getLine(),
         ]);
         return [
            'message' => $e->getMessage()
         ];
      }
   }
}

