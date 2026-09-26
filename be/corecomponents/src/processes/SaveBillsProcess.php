<?php 
/**
 * @author : Budi
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\processes;
use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\Services\KasirService;
use Doco\Traits\IntegrateKasirTrait;
use SirsCore\features\IntegrasiAkunting;

use Doco\models\TarifTotalFn;
use Doco\models\Lookup;
use Doco\models\Bedah\InpostOperasi;
use Doco\models\Bedah\InpostOperasiDetail;
use Doco\models\Bedah\TimOperasi;
use Doco\models\Bedah\PasienMasukPenunjang;
use Doco\models\Bedah\InfoPasienOperasiView;
use Doco\models\Bedah\AlatDitubuh;
use Doco\models\Bedah\PenggunaanCairan;
use Doco\models\Bedah\KonsultasiTindakan;
use Doco\models\Bedah\PemeriksaanPelengkap;
use Doco\models\Bedah\BmhpOperasi;
use Doco\models\Bedah\InstrumenOperasi;
use Doco\models\Bedah\PasangInfus;
use Doco\models\Bedah\MappingPosisiOperasi;
use Doco\models\Bedah\TindakanLuarOperasi;
use SirsCore\businessLogic\TagihanBedah;
use Doco\models\Bedah\VerifikasiBedahR;

class SaveBillsProcess extends \Doco\components\DocoBaseProcessExtension
{
   use IntegrateKasirTrait;

	/**
   * [$ruangan_id ruangan id]
   * @var integer
   */
   protected $ruangan_id;

   /**
   * [$inpostoperasi_id inpostoperasi id]
   * @var integer
   */
   protected $inpostoperasi_id;

   /**
   * [$pasienmasukpenunjang_id pasienmasukpenunjang id]
   * @var integer
   */
   protected $pasienmasukpenunjang_id;

   /**
   * [$isPostOperasi is post operasi]
   * @var integer
   */
   protected $isPostOperasi;

   /**
   * [$instalasi_id instalasi id]
   * @var integer
   */
   protected $instalasi_id;

   /**
   * [$additional additional]
   * @var integer
   */
   protected $additional;

   /**
   * [$postData post data]
   * @var integer
   */
   protected $postData;

   /**
   * [$allowStatus allowStatus]
   * @var integer
   */
   protected $allowStatus;

   /**
   * [$lastProccess lastProccess]
   * @var integer
   */
   protected $lastProccess;

   /**
   * [$status_periksa status_periksa]
   * @var integer
   */
   protected $status_periksa;

   /**
   * [$info info]
   * @var integer
   */
   protected $info;

   /**
   * [$is_pasanginfus is_pasanginfus]
   * @var integer
   */
   protected $is_pasanginfus;

   /**
   * [$isPenyulit isPenyulit]
   * @var integer
   */
   protected $isPenyulit = false;
  
   /**
   * [$isCyto isCyto]
   * @var integer
   */
   protected $isCyto = false;

   /**
   * [$pasienPenunjang pasien penunjang]
   * @var array
   */

   protected $pasienPenunjang;
   protected $daftarTindakan;

   protected function validation()
   {
      $instalasi_id = Yii::$app->jwt->instalasi_id;
      $additional = $this->_requestData->post('additional', []);
      $data = $this->_requestData->post('data');
      $ruangan_id = $this->_requestData->post('ruangan_id');
      $inpostoperasi_id = ArrayHelper::getValue($data, 'inpostoperasi_id');
      $pasienmasukpenunjang_id = ArrayHelper::getValue($data, 'pasienmasukpenunjang_id');
      $isPostOperasi = isset($data['is_post']) ? true : false;
      $lastProccess = (isset($data['last']) && $data['last']) ? true : false;
      $allowStatus = [DocoConstants::ST_P_PEN_BLM_OPRS, (int) DocoConstants::VAR_P_SO];
      $status_periksa = $lastProccess ? DocoConstants::VAR_P_SdhO :  DocoConstants::VAR_P_SO;
      $pasienPenunjang = $this->cekPasienPenunjang($pasienmasukpenunjang_id);
      $cekPegawaiOp = $this->cekPegawaiOp($inpostoperasi_id);
      if(empty($cekPegawaiOp)) {
         if(isset($additional['pegawaioperasi']) && empty($additional['pegawaioperasi'])) {
            throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
               'text' => "Pegawai Operasi tidak boleh kosong"
            ]);
         }
      }

      $cekDetailInpostOperasi = $this->cekDetailInpostOperasi($inpostoperasi_id);
      if(empty($cekDetailInpostOperasi)) {
         if(isset($additional['itemoperasi']) && empty($additional['itemoperasi'])) {
            throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
               'text' => "Jenis Operasi tidak boleh kosong"
            ]);
         }
      }

      if (empty($pasienPenunjang)) {
         throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
            'text' => "Data tidak ditemukan"
         ]);
      } else if (!in_array($pasienPenunjang['status_periksa'], $allowStatus)) {
         throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
            'text' => "Pastikan data masih berstatus belum atau sedang dioperasi."
         ]);
      }
      
      $this->allowStatus = $allowStatus;
      $this->additional = $additional;
      $this->postData = $data;
      $this->ruangan_id = $ruangan_id;
      $this->inpostoperasi_id = $inpostoperasi_id;
      $this->pasienmasukpenunjang_id = $pasienmasukpenunjang_id;
      $this->instalasi_id = $instalasi_id;
      $this->isPostOperasi = $isPostOperasi;
      $this->lastProccess = $lastProccess;
      $this->status_periksa = $status_periksa; 
      $this->pasienPenunjang = $pasienPenunjang;
   }

   protected function save()
   {
      $positions = ['pos' => 2]; //post operasi posisi
      if( $this->isPostOperasi ){
         $positions = [
            'pos'=> 3
         ]; //after post operasi posisi
      }

      $model = $this->cekInpostOperasi($this->pasienmasukpenunjang_id, $this->inpostoperasi_id);
      $model = empty($model) ? new InpostOperasi : $model;
      
      $model->attributes = $this->postData;
      $model->additional_data = json_encode($positions);
      $pemberitahu_perawat = $perawat_datang = date('Y-m-d H:i:s');
      if(isset($data['pemberitahu_perawat']) && isset($data['perawat_datang'])) {
         $pemberitahu_perawat = date('Y-m-d H:i:s', strtotime($data['pemberitahu_perawat']));
         $perawat_datang = date('Y-m-d H:i:s', strtotime($data['perawat_datang']));
         $model->pemberitahu_perawat = $pemberitahu_perawat;
         $model->perawat_datang = $perawat_datang;
      }
      if($model->validate() && $model->save()) {
         $is_pasanginfus = ($model) ? $model->is_pasanginfus : false;
         $this->is_pasanginfus = $is_pasanginfus;
         $this->saveDetailPemeriksaan();
         $this->updatePenunjang($this->pasienmasukpenunjang_id);
      }
      else {
         throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
            'text' => "Terjadi Kesalahan"
         ]);
      }
   }

   protected function cekPegawaiOp($inpostoperasi_id)
   {
      return TimOperasi::find()->where(['inpostoperasi_id' => $inpostoperasi_id])->all(); 
   }

   protected function cekDetailInpostOperasi($inpostoperasi_id)
   {
      return InpostOperasiDetail::find()->where(['inpostoperasi_id' => $inpostoperasi_id])->all();
   }

   protected function cekInpostOperasi($pasienmasukpenunjang_id, $inpostoperasi_id)
   {
      return InpostOperasi::find()->where(['pasienmasukpenunjang_id' => $pasienmasukpenunjang_id, 'inpostoperasi_id' => $inpostoperasi_id])->one();
   }

   protected function dataInpostOperasi($pasienmasukpenunjang_id)
   {
      return InfoPasienOperasiView::find()
         ->selectAttr()->findByPenunjangId($pasienmasukpenunjang_id)->asArray()->one();
   }

	protected function cekPasienPenunjang($pasienmasukpenunjang_id)
   {
      return PasienMasukPenunjang::find()->select([
         'status_periksa', 
         'pasienmasukpenunjang_id'
      ])->andWhere([
            'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id
      ])->asArray()->one(); 
   }

   protected function updatePenunjang($pasienmasukpenunjang_id)
   {
      $model = $this->cekPasienPenunjang($pasienmasukpenunjang_id); 
      if($model) {
      $query = PasienMasukPenunjang::find()
         ->where([
            'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id
         ])->one();

         $query->status_periksa = $this->status_periksa;
         if($query->validate()) {
            $query->save();
         }
         else {
            throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
            'text' => "Terjadi Kesalahan"
         ]);
         }
      }
   }

   protected function saveDetailPemeriksaan()
   {
      $info = $this->dataInpostOperasi($this->pasienmasukpenunjang_id);
      $add = $this->additional;
      $penunjang_id = $this->pasienmasukpenunjang_id;

      // additional
      $pemasanganinfus = isset($add['pemasanganinfus']) ? $add['pemasanganinfus'] : [];
      $alatditubuh = isset($add['alatditubuh']) ? $add['alatditubuh'] : [];
      $instrumen = isset($add['instrumen']) ? $add['instrumen'] : [];
      $penggunaancairan = isset($add['penggunaancairan']) ? $add['penggunaancairan'] : [];
      $konsultindakan = isset($add['konsultindakan']) ? $add['konsultindakan'] : [];
      $itemoperasi = isset($add['itemoperasi']) ? $add['itemoperasi'] : [];
      $pegawaioperasi = isset($add['pegawaioperasi']) ? $add['pegawaioperasi'] : [];
      $tindakanluarbedah = isset($add['tindakanluarbedah']) ? $add['tindakanluarbedah'] : [];
      $penggunaanbmhp = isset($add['penggunaanbmhp']) ? $add['penggunaanbmhp'] : [];
      $pemeriksaanpelengkap = isset($add['pemeriksaanpelengkap']) ? $add['pemeriksaanpelengkap'] : [];
      $newpegawaioperasi = [];
      $dokterOp = [];
      if(!empty($pegawaioperasi)) {
         foreach ($pegawaioperasi as $key => $value) {
            if($value['posisi_tim'] == DocoConstants::TIM_OPERASI_DOKTER_BEDAH) {
               $value['prosentase'] = 100;
               $dokterOp[] = $value['pegawai_id'];
            }
            $newpegawaioperasi[] = $value;
         }
      }
      if ( in_array($this->status_periksa, $this->allowStatus) ) {
         $info['ruangan_id'] = $this->ruangan_id;
         unset($info['status']);

         if(!$this->isPostOperasi) {
            $this->savePemeriksaan('alatditubuh', $alatditubuh);
            $this->savePemeriksaan('penggunaancairan', $penggunaancairan);
            $this->savePemeriksaan('konsultindakan', $konsultindakan);
            $this->savePemeriksaan('pemeriksaanpelengkap', $pemeriksaanpelengkap);
            $this->savePemeriksaan('penggunaanbmhp', $penggunaanbmhp);
            $this->savePemeriksaan('instrumen', $instrumen);

            if(empty($newpegawaioperasi)) {
               throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
                  'text' => "Pegawai Operasi tidak boleh kosong"
               ]);
            }
            
            // usort($dokterOp, function ($a, $b) {
            //   return ($a > $b) ? 1 : -1;
            // });

            // $dokterTindakan = [];
            // foreach ($itemoperasi as $key => $value) {
            //   $dokterTindakan[] = $value['pegawai_id'];
            // }
            // usort($dokterTindakan, function ($a, $b) {
            //     return ($a > $b) ? 1 : -1;
            // });
            
            // $isValid = ($dokterOp == $dokterTindakan) ? true : false;

            // if(!$isValid) {
            //   throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
            //     'text' => "Jenis Operasi dan Dokter Operator Tidak Sesuai"
            //   ]);
            // }

            InpostOperasiDetail::deleteAll('inpostoperasi_id=:inpostoperasi_id', [
               ':inpostoperasi_id' => $this->inpostoperasi_id]);

            if(empty($itemoperasi)) {
               throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
                  'text' => "Jenis Operasi tidak boleh kosong"
               ]);
            }

            $this->saveItemOperasi($info, $itemoperasi);
            $this->savePegawaiOperasi($newpegawaioperasi);
            $this->saveTindakanLuarBedah($info, $tindakanluarbedah);
         }
         else {
            $this->savePemeriksaan('pemasanganinfus', $pemasanganinfus);
         }

         IntegrasiAkunting::integrateTindakanBmhpPenunjang($penunjang_id, $this->instalasi_id);
      }
   }

   protected function savePemeriksaan($jenis, $data)
   {
      try {
         $penunjang_id = $this->pasienmasukpenunjang_id;
         $add = $this->additional;
         $id = '';
         $deletedItem = [];
         $listDeletedId = [];
         $listId = [];
         switch ($jenis) {
            case 'alatditubuh':
               $model = new AlatDitubuh;
               $id = 'alatditubuh_id';
               $deletedItem = !empty($add['deleted-alatditubuh']) ? $add['deleted-alatditubuh'] : [];
               break;
            
            case 'penggunaancairan':
               $model = new PenggunaanCairan;
               $id = 'penggunaancairan_id';
               $deletedItem = !empty($add['deleted-penggunaancairan']) ? $add['deleted-penggunaancairan'] : [];
               break;

            case 'konsultindakan':
               $model = new KonsultasiTindakan;
               $id = 'konsultasitindakan_id';
               $deletedItem = !empty($add['deleted-konsultindakan']) ? $add['deleted-konsultindakan'] : [];
               break;

            case 'pemeriksaanpelengkap':
               $model = new PemeriksaanPelengkap;
               $id = 'pemeriksaanlengkap_id';
               $deletedItem = !empty($add['deleted-alatditubuh']) ? $add['deleted-alatditubuh'] : [];
               break;

            case 'penggunaanbmhp':
               $model = new BmhpOperasi;
               $id = 'obatalkes_id';
               $deletedItem = !empty($add['deleted-penggunaanbmhp']) ? $add['deleted-penggunaanbmhp'] : [];
               break;

            case 'instrumen':
               $model = new InstrumenOperasi;
               $id = 'instrumenoperasi_id';
               $deletedItem = !empty($add['deleted-instrumen']) ? $add['deleted-instrumen'] : [];
               break;

            case 'pemasanganinfus':
               $model = new PasangInfus;
               $id = 'pasanginfus_id';
               $deletedItem = !empty($add['deleted-pemasanganinfus']) ? $add['deleted-pemasanganinfus'] : [];
               break;

            default:
               # code...
               break;
         }

         foreach($deletedItem as $val){
            if(!empty($val[$id])){
               $listDeletedId[] = $val[$id]; //handle data yang didelete di intra akan didelete di table
            }
         }

         foreach($data as $val){
            if(!empty($val[$id])){
               $listId[] = $val[$id]; //untuk data yang akan diupdate akan didelet juga sebelum nanti dimasukkan yang baru
            }
         }

         if($jenis == 'penggunaanbmhp'){ /// saat ini hanya akomodir yang penggunaan bmhp.
            $model::deleteAll(['AND', 
               'pasienmasukpenunjang_id=:pasienmasukpenunjang_id', 
               ['OR', 
                  ['IN', $id,$listDeletedId],
                  ['IN', $id,$listId]
               ]
            ], 
            [
               ':pasienmasukpenunjang_id' => $penunjang_id,
            ]);
         }else{
            $model::deleteAll('pasienmasukpenunjang_id=:pasienmasukpenunjang_id', [
               ':pasienmasukpenunjang_id' => $penunjang_id,
            ]);
         }

         if(!empty($data) || ($this->is_pasanginfus)) {
            $save = $model::batchInsert($data);
            return $save;
         }
      } catch (\yii\db\Exception $e) {
         throw new \Exception($e->getMessage());
      } catch (\Exception $e) {
         throw new \Exception($e->getMessage());
      }
   }

   protected function saveItemOperasi($info, $data)
   {
      try {
         $result = [];
         $surgeryId = [];
         $kelaspelayanan_id = !empty($info['kelaspelayanan_id']) ? $info['kelaspelayanan_id'] : null;
         $pendaftaran_id = !empty($info['pendaftaran_id']) ? $info['pendaftaran_id'] : null;
         if(!empty($pendaftaran_id)){
            $kelaspelayanan_id = $this->getKelasDitagihkan($pendaftaran_id, $kelaspelayanan_id);
         }
         if(!empty($data)) {
            foreach ($data as $value) {
               $penyulit = isset($value['penyulit']) ? $value['penyulit'] : false;
               $cyto = isset($value['cyto']) ? $value['cyto'] : (isset($value['is_cyto']) ? $value['is_cyto']  : false);
               $is_penyulit = ($penyulit == 1) ? true : false;
               $is_cyto = ($cyto) ? true : false;
               $surgeryId[] = $value['daftartindakan_id'];
               $result['operasi-' . $value['daftartindakan_id'].'-'.$value['pegawai_id']] = [
                  'daftartindakan_id' => $value['daftartindakan_id'],
                  'golonganoperasi_id' => $value['golonganoperasi_id'],
                  'inpostoperasi_id' => $value['inpostoperasi_id'],
                  'operasi_id' => $value['operasi_id'],
                  'pasienmasukpenunjang_id' => $value['pasienmasukpenunjang_id'],
                  'dokter_id' => $value['pegawai_id'],
                  'is_cyto' => $is_cyto,
                  'is_penyulit' => $is_penyulit,
               ];
               if ($is_penyulit) {
                  $this->isPenyulit = true;
               }
               if ($is_cyto) {
                  $this->isCyto = true;
               }
            }
            
            $surgeryRecord = (new TarifTotalFn([
               'extParam' => [
                  $info['ruangan_id'],
                  $info['penjamin_id'],
                  $kelaspelayanan_id,
                  'penunjang'
               ]
            ]))
            ->find()
            ->select(['daftartindakan_id', 'kelaspelayanan_id', 'penjamin_id', 'ruangan_id', 'harga_tariftindakan', 'persencyto_tindakan', 'persendiskon_tindakan', 'persen_penyulit'])
            ->where(['daftartindakan_id' => $surgeryId])
            ->asArray()
            ->all();

            $dataSurgery = $newData = [];
            if(!empty($surgeryRecord)) {
               foreach ($surgeryRecord as $key => $value) {
               $dataSurgery[$value['daftartindakan_id']] = $value;
               }
            }
            
            foreach ($result as $key => $value) {
               $harga = isset($dataSurgery[$value['daftartindakan_id']]) ? $dataSurgery[$value['daftartindakan_id']]['harga_tariftindakan'] : 0;
               $persencyto_tindakan = isset($dataSurgery[$value['daftartindakan_id']]) ? $dataSurgery[$value['daftartindakan_id']]['persencyto_tindakan'] : 0;
               $persencyto_tindakan = ($value['is_cyto']) ? $persencyto_tindakan : 0;
               $persen_penyulit = isset($dataSurgery[$value['daftartindakan_id']]) ? $dataSurgery[$value['daftartindakan_id']]['persen_penyulit'] : 0;
               $persen_penyulit = ($value['is_penyulit']) ? $persen_penyulit : 0;
               $persendiskon_tindakan = isset($dataSurgery[$value['daftartindakan_id']]) ? $dataSurgery[$value['daftartindakan_id']]['persendiskon_tindakan'] : 0;
               $penyulitValue = $value['is_penyulit'] ? ($harga * $persen_penyulit / 100) : 0;
               $harga += $penyulitValue;
               $cytoValue = ($value['is_cyto']) ? ($harga * $persencyto_tindakan / 100) : 0;
               $totalHarga = ($harga + $cytoValue);
               $discountValue = $harga * $persendiskon_tindakan / 100;
               $result['operasi-'. $value['daftartindakan_id'].'-'. $value['dokter_id']]['harga_tariftindakan'] = $harga;
               $result['operasi-'. $value['daftartindakan_id'].'-'. $value['dokter_id']]['persencyto_tindakan'] = $persencyto_tindakan;
               $result['operasi-'. $value['daftartindakan_id'].'-'. $value['dokter_id']]['persen_penyulit'] = $persen_penyulit;
               $result['operasi-'. $value['daftartindakan_id'].'-'. $value['dokter_id']]['persendiskon_tindakan'] = $persendiskon_tindakan;
               $result['operasi-'. $value['daftartindakan_id'].'-'. $value['dokter_id']]['total_cyto'] = $cytoValue;
               $result['operasi-'. $value['daftartindakan_id'].'-'. $value['dokter_id']]['total_diskon'] = $discountValue;
               $result['operasi-'. $value['daftartindakan_id'].'-'. $value['dokter_id']]['total_penyulit'] = $penyulitValue;
               $result['operasi-'. $value['daftartindakan_id'].'-'. $value['dokter_id']]['harga'] = $totalHarga;
            }
            
            if (count($result)) {
               $result = array_values($result);
               $this->daftarTindakan = $result;
               usort($this->daftarTindakan, function ($a, $b) {
                  return $a['harga_tariftindakan'] < $b['harga_tariftindakan'] ? 1 : -1;
               });
               $save = InpostOperasiDetail::batchInsert($result);
               return $save;
            }
            return true;
         }
      } catch (\yii\db\Exception $e) {
         throw new \Exception($e->getMessage());
      } catch (\Exception $e) {
         throw new \Exception($e->getMessage());
      }
   }

   protected function savePegawaiOperasi($data)
   {
      try {
         TimOperasi::deleteAll('pasienmasukpenunjang_id=:pasienmasukpenunjang_id', [':pasienmasukpenunjang_id' => $this->pasienmasukpenunjang_id]);
         // first group all team by role
         $teamByRole = [];
         $listRole = [];
         if(!empty($data)) {
            foreach ($data as $team) {
               if (!in_array($team['posisi_tim'], $listRole)) {
                  $posisiTim = ArrayHelper::getValue($team, 'posisi_tim');
                  $listRole[] = $posisiTim;
               }
               $teamByRole['role-' . $posisiTim][] = $team;
            }
            
            // get percentage of role
            $percentages = MappingPosisiOperasi::find()
               ->select([
                  'timoperasi_id as position_id',
                  'prosentase as percentage',
                  'daftartindakan_id'
               ])
               ->andWhere(['in', 'timoperasi_id', $listRole])
               ->andWhere(['is_active' => true, 'is_deleted' => false])
               ->asArray()
               ->all();

            $newPercentages = [];
            if(!empty($percentages)) {
               foreach ($percentages as $key => $value) {
                  /**
                   * Comment Persentase berdasarkan mappingan
                   */
                  // if($value['position_id'] == DocoConstants::TIM_OPERASI_DOKTER_BEDAH) {
                  //    $value['percentage'] = 100;
                  // }
                  $newPercentages[] = $value;
               }
            }

            $percentageArray = [];
            $percentageTindakanArray = [];
            foreach ($newPercentages as $percentage) {
               $percentageArray['rate-' . $percentage['position_id']] = $percentage;
               $percentageTindakanArray[$percentage['daftartindakan_id'].'-'.$percentage['position_id']] = $percentage;
            }
            
            $daftarTindakan = $this->daftarTindakan;
            $primaryRate = $daftarTindakan[0];
            // checkin cyto or no
            if ($this->isCyto) {
               $primaryRate['harga'] = $primaryRate['harga_tariftindakan'] + ($primaryRate['harga_tariftindakan'] * $primaryRate['persencyto_tindakan'] / 100);
            }
            if ($this->isPenyulit) {
               $primaryRate['harga'] = $primaryRate['harga'] + ($primaryRate['harga_tariftindakan'] * $primaryRate['persen_penyulit'] / 100);
            }

            $dokterBedahId = DocoConstants::TIM_OPERASI_DOKTER_BEDAH;
            // dokter bedah
            $teamByRole['role-' . $dokterBedahId][0]['harga'] = $primaryRate['harga'];
            $teamByRole['role-' . $dokterBedahId][0]['persentase'] = 100;
            if (count($daftarTindakan) > 1) {
               $secondaryRate = $daftarTindakan[1];
               if ($this->isCyto) {
                  $secondaryRate['harga'] = $secondaryRate['harga_tariftindakan'] + ($secondaryRate['harga_tariftindakan'] * $secondaryRate['persencyto_tindakan'] / 100);
               }
               if ($this->isPenyulit) {
                  $secondaryRate['harga'] = $secondaryRate['harga'] + ($secondaryRate['harga_tariftindakan'] * $secondaryRate['persen_penyulit'] / 100);
               }
               if (count($teamByRole['role-' . $dokterBedahId]) > 1) {
                  $teamByRole['role-' . $dokterBedahId][1]['harga'] = $secondaryRate['harga'];
                  $teamByRole['role-' . $dokterBedahId][1]['persentase'] = 100;
               } else {
                  $teamByRole['role-' . $dokterBedahId][0]['harga'] = $teamByRole['role-' . $dokterBedahId][0]['harga'] + ($secondaryRate['harga_tariftindakan'] * 50 / 100);
                  $teamByRole['role-' . $dokterBedahId][0]['persentase'] = 150;
               }
            }
            $insertArray = [];
            $arrayRecord = [];
            foreach ($teamByRole as $keyRole => $arrayRole) {
               foreach ($arrayRole as $role) {
                  $role['kegiatanoperasi_id'] = !empty($role['kegiatanoperasi_id']) ? $role['kegiatanoperasi_id'] : null;
                  $role['golonganoperasi_id'] = !empty($role['golonganoperasi_id']) ? $role['golonganoperasi_id'] : null;
                  $persentase = isset($role['prosentase']) ? $role['prosentase'] : 0;
                  $defaultPercent = $role['posisi_tim'] == DocoConstants::TIM_OPERASI_DOKTER_BEDAH ? $persentase : 0;
                  $persentase = isset($percentageTindakanArray[$role['daftartindakan_id'].'-'.$role['posisi_tim']]) ? $percentageTindakanArray[$role['daftartindakan_id'].'-'.$role['posisi_tim']]['percentage'] : $defaultPercent;
                  
                  $arrayRecord[] = array_merge($role, [
                     'harga' => $primaryRate['harga'] * $persentase / 100,
                     'persentase' => $persentase
                  ]);
               }
               $insertArray = array_merge($insertArray, $arrayRecord);
               $arrayRecord = [];
            }

            $save = TimOperasi::batchInsert($insertArray);
            return $save;
         }
      } catch (\yii\db\Exception $e) {
         throw new \Exception($e->getMessage());
      } catch (\Exception $e) {
         throw new \Exception($e->getMessage());
      }
   }

   /**
   * This function will save tindakan luar bedah
   * 
   * @param Array $header
   * @param Array $tindakan
   * @return Array
   * @author : Tsani Nashrullah (tsani@docotel.com)
   * A product of PT. Docotel Teknologi
   * Powered by Sirs
   */
   protected function saveTindakanLuarBedah($info, $tindakan)
   {
      $result = [];
      $tindakanIds = [];
      $tindakanByKey = [];
      TindakanLuarOperasi::deleteAll('pasienmasukpenunjang_id=:pasienmasukpenunjang_id', [':pasienmasukpenunjang_id' => $this->pasienmasukpenunjang_id]);
      if(!empty($tindakan)) {
         foreach ($tindakan as $eachTindakan) {
            if (isset($eachTindakan['tindakanluarbedah_id']) && !empty($eachTindakan['tindakanluarbedah_id']) && isset($eachTindakan['qtytindakan']) && !empty($eachTindakan['qtytindakan']) && !in_array($eachTindakan['tindakanluarbedah_id'], $tindakanIds)) {
               $tindakanIds[] = $eachTindakan['tindakanluarbedah_id'];
               $tindakanByKey['tindakan-' . $eachTindakan['tindakanluarbedah_id']] = $eachTindakan;
            }
         }
         
         $kelaspelayanan_id = !empty($info['kelaspelayanan_id']) ? $info['kelaspelayanan_id'] : null;
         $pendaftaran_id = !empty($info['pendaftaran_id']) ? $info['pendaftaran_id'] : null;
         if(!empty($pendaftaran_id)){
            $kelaspelayanan_id = $this->getKelasDitagihkan($pendaftaran_id, $kelaspelayanan_id);
         }
         if (!empty($tindakanIds)) {
            $recordTindakan = (new TarifTotalFn([
               'extParam' => [
                  $info['ruangan_id'],
                  $info['penjamin_id'],
                  $kelaspelayanan_id,
                  'pelayanan'
               ]
            ]))
            ->find()
            ->select([
               'kelaspelayanan_id',
               'penjamin_id',
               'dokter_id',
               'daftartindakan_id',
               'harga_tariftindakan as harga'
            ])
            ->andWhere([
               'in',
               'daftartindakan_id',
               $tindakanIds
            ])
            ->andWhere(['IS', 'dokter_id', NULL])
            ->asArray()
            ->all();
            
            if (!empty($recordTindakan)) {
               foreach ($recordTindakan as $key => $record) {
                  $result[$record['daftartindakan_id']] = array_merge($record, [
                     'qty' => $tindakanByKey['tindakan-' . $record['daftartindakan_id']]['qtytindakan'],
                     'pasienmasukpenunjang_id' => $this->pasienmasukpenunjang_id
                  ]);
               }
               TindakanLuarOperasi::batchInsert($result);
            }
         }
      }
      
      return $result;
   }

   private function getKelasDitagihkan($pendaftaran_id, $kelaspelayanan_id)
   {
      $admisi = "SELECT pendaftaran_id, kelaspelayanan_id, kelas_ditagihkan_id, is_pasientitipan
         FROM infopasienri_v
         WHERE pendaftaran_id = {$pendaftaran_id}";
      $admisi = Yii::$app->db->createCommand($admisi)->queryOne();
      $kelasPelayananId = $kelaspelayanan_id;
      if($admisi) {
         if(!empty($admisi)) {
            $isTitipan = !empty($admisi['is_pasientitipan']) ? $admisi['is_pasientitipan'] : false;
            if($isTitipan) {
               if(!empty($admisi['kelas_ditagihkan_id'])) {
                  $kelasPelayananId = $admisi['kelas_ditagihkan_id'];
               }
            }
         }
      }
      return $kelasPelayananId;
   }

   protected function processFlow()
   {
      $this->validation();
      $this->startDBTransaction();
      $this->save();
      $this->commitDBTransaction();
      return [
         'message' => 'Simpan Intra Operasi Berhasil!', 
         'integrateKasir' => $this->sentToKasir($this->pasienmasukpenunjang_id, $this->lastProccess),
         'integrateObat' => $this->sentToApotek($this->pasienmasukpenunjang_id, $this->lastProccess)
      ];
   }
}
