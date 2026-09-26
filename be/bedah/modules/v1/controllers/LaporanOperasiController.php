<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use app\modules\v1\models\LaporanOperasi;
use app\modules\v1\models\LaporanOperasiView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\TimOperasi;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Operasi;
use app\modules\v1\models\InpostOperasi;
use app\modules\v1\models\InfoPasienOperasiView;
use app\modules\v1\models\InfoInpostOperasiDetailView;
use app\modules\v1\models\InfoPasienPenunjangView;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\RencanaOperasi;
use app\modules\v1\models\TindakanLuarOperasi;
use Doco\models\Bedah\PasienMasukPenunjang;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\models\Bedah\VerifikasiBedahR;
use Doco\models\ProfilRsView;
use SirsCore\models\DaftarTindakan;

class LaporanOperasiController extends DocoActiveController
{
   public $modelClass = 'app\modules\v1\models\LaporanOperasi';
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

   public function actionGetDataApi()
   {
      $request = Yii::$app->request;
      $id = $request->get('id', null);
      $dokterId = $request->get('dokter_id', null);
      $dataTimOperasi = [];
      try {
         $data = InpostOperasi::find()
            ->select([
               'inpostoperasi_t.pasienmasukpenunjang_id', 'inpostoperasi_t.inpostoperasi_id',
               'inpostoperasi_t.mulai_operasi', 'inpostoperasi_t.selesai_operasi',
               'inpostoperasi_t.mulai_anastesi', 'inpostoperasi_t.selesai_anastesi', 'inpostoperasi_t.additional_data', 'unit_lain.tgl_kirimpasien'
            ])
            ->leftJoin('pasienkirimkeunitlain_t AS unit_lain', 'inpostoperasi_t.pasienmasukpenunjang_id= unit_lain.pasienmasukpenunjang_id')
            // ->joinWith(['laporanOperasi', 'detailOperasi' => function ($detail) {
            //    $detail->select([
            //       'infoinpostoperasidetail_v.pasienmasukpenunjang_id',
            //       'infoinpostoperasidetail_v.daftartindakan_id', 'infoinpostoperasidetail_v.daftartindakan_nama',
            //    ]);
            // }, 'timOperasi' => function ($tim) {
            //    $tim->joinWith(['pegawai' => function ($pegawai) {
            //       $pegawai->select(['pegawai_m.pegawai_id', 'pegawai_m.nama_pegawai']);
            //    }]);
            //    $tim->select([
            //       'timoperasi_t.pasienmasukpenunjang_id', 'timoperasi_t.inpostoperasi_id',
            //       'timoperasi_t.pegawai_id', 'timoperasi_t.posisi_tim'
            //    ]);
            // }])
            ->where(['inpostoperasi_t.pasienmasukpenunjang_id' => $id])
            ->asArray()->one();

         $arrProsedure = [];
         if (isset($data['detailOperasi']) && !empty($data['detailOperasi'])) {
            foreach ($data['detailOperasi'] as $vDetail) {
               $arrProsedure[$vDetail['daftartindakan_nama']] = ArrayHelper::getValue($vDetail, 'daftartindakan_nama');
            }
         }

         if (isset($data['timOperasi']) && !empty($data['timOperasi'])) {
            $dokterOp = $dokterAnestesi = $asistenBedah = $asistenAnestesi = [];
            foreach ($data['timOperasi'] as $vTim) {
               $pegawai = ArrayHelper::getValue($vTim, 'pegawai');
               $posisiTim = ArrayHelper::getValue($vTim, 'posisi_tim');
               if ($posisiTim == DocoConstants::TIM_DOKTER_BEDAH) {
                  $dokterOp[] = $pegawai;
               } elseif ($posisiTim == DocoConstants::TIM_DOKTER_ANASTESI) {
                  $dokterAnestesi[] = $pegawai;
               } elseif ($posisiTim == DocoConstants::TIM_ASS_ANASTESI1 || $posisiTim == DocoConstants::TIM_ASS_ANASTESI2) {
                  $asistenAnestesi[] = $pegawai;
               } elseif ($posisiTim == DocoConstants::TIM_ASS_BEDAH1 || $posisiTim == DocoConstants::TIM_ASS_BEDAH2) {
                  $asistenBedah[] = $pegawai;
               }
            }

            $dokterOp = ArrayHelper::map($dokterOp, 'nama_pegawai', 'nama_pegawai');
            $dokterAnestesi = ArrayHelper::map($dokterAnestesi, 'nama_pegawai', 'nama_pegawai');
            $asistenAnestesi = ArrayHelper::map($asistenAnestesi, 'nama_pegawai', 'nama_pegawai');
            $asistenBedah = ArrayHelper::map($asistenBedah, 'nama_pegawai', 'nama_pegawai');
            $dataTimOperasi = [
               'dokter_operator' => $dokterOp,
               'dokter_anastesi' => $dokterAnestesi,
               'asisten' => $asistenAnestesi,
               'asisten_instrumen' => $asistenBedah,
            ];
         }

         $kategoriOperasi = Lookup::find()
            ->select(['lookup_id AS id', 'lookup_value AS text'])
            ->where(['lookup_type' => 'kategori_operasi_lap'])
            ->orderBy(['lookup_value' => SORT_ASC])
            ->asArray()->all();

         if (!empty($kategoriOperasi)) {
            $kategoriOperasi = ArrayHelper::map($kategoriOperasi, 'text', 'text');
         }

         $caraPembiusan = Lookup::find()
            ->select(['lookup_id AS id', 'lookup_value AS text'])
            ->where(['lookup_type' => 'pembiusan_operasi_lap'])
            ->orderBy(['lookup_value' => SORT_ASC])
            ->asArray()->all();

         if (!empty($caraPembiusan)) {
            $caraPembiusan = ArrayHelper::map($caraPembiusan, 'text', 'text');
         }

         $additionalData = ArrayHelper::getValue($data, 'additional_data', []);
         $posisi = !(empty($additionalData)) ? json_decode($additionalData, true) : null;
         $posisi = ArrayHelper::getValue($posisi, 'pos', 0);

         return [
            'data' => $data,
            'prosedur' => $arrProsedure,
            'tim_operasi' => $dataTimOperasi,
            'kategori' => $kategoriOperasi,
            'pembiusan' => $caraPembiusan,
            'posisi' => $posisi,

         ];
      } catch (\yii\db\Exception $e) {
         return $e->getMessage();
         $result = [];
      } catch (\Exception $e) {
         return $e->getMessage();
         $result = [];
      }
   }

   public function actionGetDataMultiple()
   {
      $request = Yii::$app->request;
      $payload = $request->get('payload', []);
      $term = isset($payload['q']) ? $payload['q'] : null;
      $type = isset($payload['type']) ? $payload['type'] : null;
      $resultData = [];
      $model = ($type == 'prosedur') ? Operasi::find() : Pegawai::find();
      $select = ($type == 'prosedur') ? ['operasi_id as id', 'operasi_nama as text'] : ['pegawai_id as id', 'nama_pegawai as text'];
      $desc = ($type == 'prosedur') ? 'operasi_nama' : 'nama_pegawai';
      $result = $model->select($select);

      if ($type != 'prosedur') {
         if ($type == 'dokter_op') {
            $result->where(['kelompokpegawai_id' => 1]);
         } elseif ($type == 'asisten') {
            $result->where(['IN', 'kelompokpegawai_id', [1, 2]]);
         } else {
            $result->where([
               'kelompokpegawai_id' => 1,
               'spesialis_id' => $this->constans->actionGetAdditional('spesialis_bedah', true)
            ]);
         }
      }

      if (!empty($term)) {
         $result->andWhere(['like', 'LOWER(' . $desc . ')', $term]);
      }

      $result->orderBy([$desc => SORT_ASC]);
      $resultData = $result->asArray()->all();
      return $resultData;
   }

   public function actionSimpanLaporan()
   {
      $request = Yii::$app->request;
      $id = $request->get('id', null);
      $post = $request->post();
      $connection = Yii::$app->db;
      $transaction = $connection->beginTransaction();
      $result = [];
      try {
         $pasienPenunjang = PasienMasukPenunjang::findOne($id);
         if (empty($pasienPenunjang)) {
            $result['status'] = 500;
            $result['title'] = 'Proses Gagal!';
            $result['text'] = 'Penyimpanan Laporan Operasi Gagal';
         }

         $dokterBedah = ArrayHelper::getValue($post, 'dokter_bedah');
         $jamMasukRec = ArrayHelper::getValue($post, 'jam_masuk_rec');
         $jamKeluarRec = ArrayHelper::getValue($post, 'jam_keluar_rec');
         $mulaiPembiusan = ArrayHelper::getValue($post, 'mulai_pembiusan');
         $selesaiPembiusan = ArrayHelper::getValue($post, 'selesai_pembiusan');
         $laporanoperasi_id = ArrayHelper::getValue($post, 'laporanoperasi_id');

         $cekDataExist = [];
         if(!empty($laporanoperasi_id)){
            $cekDataExist = LaporanOperasi::find()->where(['laporanoperasi_id' => $laporanoperasi_id])->one();
         }else{
            if(isset($post['laporanoperasi_id'])){
               unset($post['laporanoperasi_id']);
            }
         }
         $model = !empty($cekDataExist) ? $cekDataExist : new LaporanOperasi;
         $model->attributes = $post;
         $model->pasienmasukpenunjang_id = $id;
         $model->mulai_operasi = !empty($jamMasukRec) ? date('Y-m-d H:i', strtotime($jamMasukRec)) : null;
         $model->selesai_operasi = !empty($jamKeluarRec) ? date('Y-m-d H:i', strtotime($jamKeluarRec)) : null;
         $model->mulai_pembiusan = !empty($mulaiPembiusan) ? date('Y-m-d H:i', strtotime($mulaiPembiusan)) : null;
         $model->selesai_pembiusan = !empty($selesaiPembiusan) ? date('Y-m-d H:i', strtotime($selesaiPembiusan)) : null;

         $end = new \DateTime($model->selesai_operasi);
         $start = new \DateTime($model->mulai_operasi);
         $interval = $end->diff($start);
         if (!empty($interval)) {
            $interval = $interval->format('%h') . " Jam " . $interval->format('%i') . " Menit";
         }
         $model->lama_pembedahan = $interval;

         $dataMapping = $this->mapingTim($post);
         $dokterBedahNama = ArrayHelper::getValue($dataMapping, 'dokterBedahNama');
         $listDokterAnastesi = ArrayHelper::getValue($dataMapping, 'listDokterAnastesi');
         $listAsisten = ArrayHelper::getValue($dataMapping, 'listAsisten');
         $listAsistenIns = ArrayHelper::getValue($dataMapping, 'listAsistenIns');
         $listProsedurBedah = ArrayHelper::getValue($dataMapping, 'listProsedurBedah');
         $additionalData = ArrayHelper::getValue($dataMapping, 'additionalData');

         $model->dokter_bedah = !empty($dokterBedahNama) ? $dokterBedahNama : '';
         $model->dokte_anastesi = !empty($listDokterAnastesi) ? $listDokterAnastesi : '';
         $model->asisten = !empty($listAsisten) ? $listAsisten : '';
         $model->asisten_instrumen = !empty($listAsistenIns) ? $listAsistenIns : '';
         $model->nama_prosedur = !empty($listProsedurBedah) ? $listProsedurBedah : '';
         $model->diagnosis_paskabedah = ArrayHelper::getValue($post, 'diagnosis_paska_bedah');
         $model->uraian = ArrayHelper::getValue($post, 'uraian_pembedahan');
         $model->is_kirimkepatologi = ArrayHelper::getValue($post, 'is_jaringan_dikirim', false);
         $model->additional_data = json_encode($additionalData);
         $model->dokter_id = $dokterBedah;

         if ($model->validate() && $model->save()) {
            $transaction->commit();
            $result = [
               'status' => 200,
               'title' => 'Input Berhasil',
               'text' => 'Laporan Operasi Berhasil Disimpan'
            ];
         } else {
            $transaction->rollBack();
            $result['status'] = 500;
            $result['title'] = 'Gagal insert';
            $result['text'] = $model->getErrors();
         }
         return $result;
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

   private function getDescription($data)
   {
      $html = '';
      if (!empty($data)) {
         $maxKey = max(array_keys($data));
         foreach ($data as $kData => $datas) {
            $separator = ($kData != $maxKey) ? "<br/>" : "";
            $html .= $datas . $separator;
         }
      }
      return $html;
   }

   /**
    * @controller actionCetakLaporan
    * @attribute #table# => Menampilkan Laporan Operasi
    * @attribute #nama_pasien# => nama pasien
    * @attribute #alamat_pasien# => alamat pasien
    * @attribute #umur# => umur
    * @attribute #jeniskelamin# => jenis kelamin
    * @attribute #no_rekam_medik# => no RM
    * @attribute #no_pendaftaran# => no pendaftaran
    * @attribute #tglmasukpenunjang# => tgl masuk penunjang
    * @attribute #agama# => agama
    * @attribute #no_identitas# => Menampilkan No Identitas
    * @attribute #tempat_lahir# => Menampilkan Tempat Lahir
    * @attribute #tgl_selesai_operasi# => Menampilkan Tanggal Selesai Operasi
    * @attribute #nama_dokter# => Menampilkan Nama Dokter Bedah
    **/
   public function actionCetakLaporan()
   {
      $request = Yii::$app->request;
      $id = $request->get('id', null);
      $pasienmasukpenunjang_id = $request->get('pasienmasukpenunjang_id', null);
      try {
         if(!empty($id) || !empty($pasienmasukpenunjang_id)) {
            $data = LaporanOperasi::find()
               ->select(['kategori.lookup_name AS kategori_operasi_nama', 'cara_pembiusan.lookup_name AS cara_pembiusan_nama', 'infopasienoperasidetail_v.jenis_operasi AS jenis_operasi', 'laporanoperasi_r.*'])
               ->leftJoin('lookup_m AS kategori', 'laporanoperasi_r.kategori_operasi::TEXT = kategori.lookup_id::TEXT')
               ->leftJoin('lookup_m AS cara_pembiusan', 'laporanoperasi_r.cara_pembiusan::TEXT = cara_pembiusan.lookup_id::TEXT')
               ->leftJoin('infopasienoperasidetail_v', 'laporanoperasi_r.pasienmasukpenunjang_id = infopasienoperasidetail_v.pasienmasukpenunjang_id');

               if(!empty($pasienmasukpenunjang_id)) {
                  $data = $data->andWhere(['laporanoperasi_r.pasienmasukpenunjang_id' => $pasienmasukpenunjang_id])->asArray()->all();
               }
               else {
                  $data = $data->where(['laporanoperasi_r.laporanoperasi_id' => $id])->asArray()->one();
               }

            $penunjangId = !empty($pasienmasukpenunjang_id) ? $pasienmasukpenunjang_id : ArrayHelper::getValue($data, 'pasienmasukpenunjang_id');
            if(!empty($penunjangId)) {
               $dataPasien = InfoPasienPenunjangView::find()
                  ->select([
                     'nama_pasien', 'alamat_pasien', 'umur',
                     'jeniskelamin', 'no_rekam_medik', 'no_pendaftaran',
                     'tglmasukpenunjang', 'pasien_id'
                  ])
               ->where(['pasienmasukpenunjang_id' => $penunjangId])->one();

               $print = new DocoPrint('laporan-operasi');
               if(!empty($pasienmasukpenunjang_id)) {
                  if(!empty($data)){
                     foreach ($data as $key => $value) {
                        $print->attributes = $this->setAttributes($dataPasien, $value);
                        $break = (($key + 1) == count($data)) ? false : true;
                        $print->generateHtml($break);
                     }
                     $print->Output(true);
                  }else{
                     $print->attributes = $this->setAttributes($dataPasien, []);
                     $print->Output();
                  }
               }
               else {
                  $print->attributes = $this->setAttributes($dataPasien, $data);
                  $print->Output();
               }
            }
         }
      } catch (\yii\db\Exception $e) {
         \Yii::$app->response->statusCode = 500;
         return [
            'message' => $e->getMessage()
         ];
      } catch (\Exception $e) {
         \Yii::$app->response->statusCode = 500;
         return [
            'message' => $e->getMessage()
         ];
      }
   }

   protected function getProfileRs()
   {
      $profilRs = Yii::$app->cache->getOrSet('profile-rs', function ($cache) {
         return ProfilRsView::find()->asArray()->one();
      });

      $kota = $profilRs['kota'];
      $namaRs = !empty($profilRs['nama_rumahsakit']) ? $profilRs['nama_rumahsakit'] : '-';
      if (!empty($profilRs['kota'])) {
         if ($match = preg_match("/KOTA ADM. /i", $kota)) {
            $pattern = "KOTA ADM. ";
         } elseif ($match = preg_match("/KAB. ADM. /i", $kota)) {
            $pattern = "KAB. ADM. ";
         } elseif ($match = preg_match("/KAB. /i", $kota)) {
            $pattern = "KAB. ";
         } elseif ($match = preg_match("/KOTA /i", $kota)) {
            $pattern = "KOTA ";
         }

         $kota = str_replace($pattern, "", $kota);
      }

      return [
         'namaRs' => $namaRs,
         'kota' => $kota,
         'alamat' => !empty($profilRs['alamatlokasi_rumahsakit']) ? $profilRs['alamatlokasi_rumahsakit'] : '-',
         'no_telp' => !empty($profilRs['no_telp_profilrs']) ? $profilRs['no_telp_profilrs'] : '-',
      ];
   }

   public function actionGetDataTim()
   {
      $request = Yii::$app->request;
      $dataLaporan = [];
      $id = $request->get('id', null);
      $dokterId = $request->get('dokter_id', null);
      $laporanId = $request->get('laporan_id', null);
      $posisiDokterOperator = DocoConstants::TIM_OPERASI_DOKTER_BEDAH;

      $data = TimOperasi::find()
         ->select(['daftartindakan_id'])
         ->where(['pasienmasukpenunjang_id' => $id, 'pegawai_id' => $dokterId, 'posisi_tim' => $posisiDokterOperator])
         ->all();

      $listTindakanId = [];
      if(!empty($data)) {
         foreach ($data as $key => $value) {
            $listTindakanId[] = ArrayHelper::getValue($value, 'daftartindakan_id');
         }
      }

      $result = $tindakanDiluarBedah = $listResults = [];
      if(!empty($listTindakanId)) {
         $result = TimOperasi::find()
            ->leftJoin('pegawai_m', 'timoperasi_t.pegawai_id = pegawai_m.pegawai_id')
            ->select(['timoperasi_t.pegawai_id', 'pegawai_m.nama_pegawai AS dokter', 'timoperasi_t.posisi_tim'])
            ->where(['timoperasi_t.pasienmasukpenunjang_id' => $id, 'timoperasi_t.daftartindakan_id' => $listTindakanId])
            ->andWhere(['<>', 'timoperasi_t.posisi_tim', $posisiDokterOperator])
            ->asArray()->all();

         if(!empty($result)) {
            foreach ($result as $key => $value) {
               $posisiTim = ArrayHelper::getValue($value, 'posisi_tim');
               $listResults[$posisiTim][] = $value;
            }
         }
         $verifikasiBedah = VerifikasiBedahR::find()
         ->select(['dokter_id', 'harga'])
         ->where(['pasienmasukpenunjang_id' => $id, 'kode_posisi' => $posisiDokterOperator,'dokter_id' => $dokterId])
         ->orderBy(['harga' => SORT_DESC])
         ->one();

         $dokterOperatorId = ArrayHelper::getValue($verifikasiBedah, 'dokter_id');
         if(!empty($dokterOperatorId)) {
            if($dokterOperatorId == $dokterId) {
               $tindakanDiluarBedah = TindakanLuarOperasi::find()
               ->select(['daftartindakan_m.daftartindakan_id','daftartindakan_m.daftartindakan_nama'])
               ->leftJoin('daftartindakan_m', 'tindakanluaroperasi_t.daftartindakan_id = daftartindakan_m.daftartindakan_id')
               ->where(['tindakanluaroperasi_t.pasienmasukpenunjang_id' => $id,'tindakanluaroperasi_t.parent_id' => $listTindakanId])
               ->asArray()->all();
            }
         }
      }

      if(!empty($laporanId)) {
         $dataLaporan = LaporanOperasi::find()
            ->select(['kategori.lookup_name AS kategori_nama', 'cara_pembiusan.lookup_name AS cara_pembiusan_nama', 'laporanoperasi_r.*'])
            ->leftJoin('lookup_m AS kategori', 'laporanoperasi_r.kategori_operasi::INTEGER = kategori.lookup_id')
            ->leftJoin('lookup_m AS cara_pembiusan', 'laporanoperasi_r.cara_pembiusan::INTEGER = cara_pembiusan.lookup_id')
            ->where(['laporanoperasi_r.laporanoperasi_id' => $laporanId])->asArray()->one();
      }

      if(!empty($dataLaporan)){
         // Apabila Data Laporan tidak kosong, kosongkan tindakan di luar bedah
         $tindakanDiluarBedah = [];
      }
      return [
         'data_tim' => $listResults,
         'tindakan_diluar_bedah' => $tindakanDiluarBedah,
         'laporan_per_dokter' => $dataLaporan,
      ];
   }

   public function actionFilters()
   {
      $type = Yii::$app->request->get('type', null);
      $payload = Yii::$app->request->get('payload', []);
      $page = isset($payload['page']) ? $payload['page'] : 1;
      $limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
      $term = isset($payload['term']) ? $payload['term'] : null;
      $result = [];
      if($type == 'kategori') {
         $result = Lookup::find()
            ->select(['lookup_id AS id', 'lookup_name AS text'])
            ->where(['lookup_type' => 'kategori_operasi_lap', 'is_active' => true]);

         if(!empty($term)) {
            $result->andWhere(['like', 'LOWER(lookup_name)', strtolower($term)]);
         }

         $result->orderBy(['lookup_name' => SORT_ASC]);
      }
      elseif($type == 'cara_pembiusan') {
         $result = Lookup::find()
            ->select(['lookup_id AS id', 'lookup_name AS text'])
            ->where(['lookup_type' => 'pembiusan_operasi_lap', 'is_active' => true]);

         if(!empty($term)) {
            $result->andWhere(['like', 'LOWER(lookup_name)', strtolower($term)]);
         }

         $result->orderBy(['lookup_name' => SORT_ASC]);
      }
      elseif($type == 'dokter_bedah') {
         $result = Pegawai::find()
            ->select(['pegawai_id AS id', 'nama_pegawai AS text'])
            ->where(['is_active' => true, 'kelompokpegawai_id' => 1]);

         if(!empty($term)) {
            $result->andWhere(['like', 'LOWER(nama_pegawai)', strtolower($term)]);
         }

         $result->orderBy(['nama_pegawai' => SORT_ASC]);
      }

      if(!empty($result)) {
         $result = $result->limit($limit + 1)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
      }

      return $result;
   }

   public function actionGetDokterBedah()
   {
      $request = Yii::$app->request;
      $id = $request->get('id', null);
      $data = $listDokter = [];
      if(!empty($id)) {
         $data = LaporanOperasi::find()
            ->select(['laporanoperasi_id', 'dokter_bedah', 'dokter_id'])
            ->where(['pasienmasukpenunjang_id' => $id])->all();

         if(!empty($data)) {
            foreach ($data as $key => $value) {
               $laporanId = ArrayHelper::getValue($value, 'laporanoperasi_id');
               $dokterId = ArrayHelper::getValue($value, 'dokter_id');
               $dokterNama = ArrayHelper::getValue($value, 'dokter_bedah');
               $listDokter[$laporanId] = [
                  'dokter_id' => $dokterId,
                  'dokter_nama' => $dokterNama,
               ];
            }
         }
      }
      return $listDokter;
   }

   private function mapingTim($post)
   {
      $dokterBedahNama = '';
      $dataPegawai = $additionalDokterBedah = [];

      $dokterBedah = ArrayHelper::getValue($post, 'dokter_bedah');
      $asisten = ArrayHelper::getValue($post, 'asisten', []);
      $asistenInstrumen = ArrayHelper::getValue($post, 'asisten_instrumen', []);
      $dokterAnestesi = ArrayHelper::getValue($post, 'dokter_anestesi', []);
      $prosedurBedah = ArrayHelper::getValue($post, 'prosedur_bedah', []);

      if(!empty($dokterBedah)) {
         $dataPegawai = Pegawai::findOne($dokterBedah);
         $dokterBedahNama = ArrayHelper::getValue($dataPegawai, 'nama_pegawai');
         $additionalDokterBedah = [$dokterBedah => $dokterBedahNama];
      }

      $dataAsisten = $this->setDataValue($asisten);
      $listAsisten = ArrayHelper::getValue($dataAsisten, 'listId', []);
      $additionalAsisten = ArrayHelper::getValue($dataAsisten, 'listGrouped', []);

      $dataAsistenIns = $this->setDataValue($asistenInstrumen);
      $listAsistenIns = ArrayHelper::getValue($dataAsistenIns, 'listId', []);
      $additionalAsistenIns = ArrayHelper::getValue($dataAsistenIns, 'listGrouped', []);

      $dataDokterAnastesi = $this->setDataValue($dokterAnestesi);
      $listDokterAnastesi = ArrayHelper::getValue($dataDokterAnastesi, 'listId', []);
      $additionalDokterAnastesi = ArrayHelper::getValue($dataDokterAnastesi, 'listGrouped', []);
      $params = [
         'id' => 'operasi_id',
         'nama' => 'operasi_nama',
         'model' => new Operasi,
      ];
      $dataProsedurBedah = $this->setDataValue($prosedurBedah, $params, true);
      $listProsedurBedah = ArrayHelper::getValue($dataProsedurBedah, 'listId', []);
      $additionalProsedurBedah = ArrayHelper::getValue($dataProsedurBedah, 'listGrouped', []);
      $additionalData = [
         'dokter_bedah' => $additionalDokterBedah,
         'asisten' => $additionalAsisten,
         'asisten_instrumen' => $additionalAsistenIns,
         'dokter_anastesi' => $additionalDokterAnastesi,
         'prosedur_bedah' => $additionalProsedurBedah,
      ];

      return [
         'dokterBedahNama' => $dokterBedahNama,
         'listAsisten' => $listAsisten,
         'listAsistenIns' => $listAsistenIns,
         'listDokterAnastesi' => $listDokterAnastesi,
         'listProsedurBedah' => $listProsedurBedah,
         'additionalData' => $additionalData,
      ];
   }

   private function setDataValue($dataArray, $params = [], $allowFreetext = false)
   {
      if(empty($params)) {
         $model = new Pegawai;
         $params = [
            'id' => 'pegawai_id',
            'nama' => 'nama_pegawai',
            'model' => $model
         ];
      }

      $model = ArrayHelper::getValue($params, 'model');
      $selectId = ArrayHelper::getValue($params, 'id');
      $selectNama = ArrayHelper::getValue($params, 'nama');
      $listId = $listGrouped = $additional = [];
      if(!empty($dataArray)) {
         foreach ($dataArray as $key => $value) {
            if(!empty($value)) {
               $listId[$value] = (int) $value;
            }
         }
         $data = $model->find()->select([$selectId, $selectNama])->where([$selectId => $listId])->all();
         if(!empty($data)) {
            foreach ($data as $key => $value) {
               $keyId = ArrayHelper::getValue($value, $selectId);
               $keyNama = ArrayHelper::getValue($value, $selectNama);
               $listGrouped[$keyId] = $keyNama;
            }
         }

         if ($allowFreetext) {
            foreach ($dataArray as $key => $value) {
                if (!is_numeric($value)) {
                    $listGrouped[$value] = $value;
                }
            }
         }

         $listId = "";
         if(!empty($listGrouped)) {
            foreach ($listGrouped as $key => $value) {
               if(!empty($value)) {
                  $listId .= $value.'<br>';
               }
            }
         }
         $additional = $listGrouped;
      }
      return [
         'listId' => $listId,
         'listGrouped' => $listGrouped,
         'additional' => $additional,
      ];
   }

   private function setAttributes($dataPasien, $data)
   {
      $profilRs = $this->getProfileRs();
      if(!empty($dataPasien)) {
         $pasienId = ArrayHelper::getValue($dataPasien, 'pasien_id');
         $pasien = Pasien::findOne($pasienId);
         $agamaId = ArrayHelper::getValue($pasien, 'agama');
         $lookup = [];
         if(!empty($agamaId)){
            $lookup = Lookup::findOne($agamaId);
         }
         $agama = ArrayHelper::getValue($lookup, 'lookup_value', '-');
         $tglMasukPenunjang = ArrayHelper::getValue($dataPasien, 'tglmasukpenunjang');
         $tglMasukPenunjang = !empty($tglMasukPenunjang) ? date('d M Y', strtotime($tglMasukPenunjang)) : '-';
         $no_identitas_pasien = isset($pasien['no_identitas_pasien']) && !empty($pasien['no_identitas_pasien']) ? $pasien['no_identitas_pasien'] : '-';
         $additionalPasien = isset($pasien['additional_pasien']) && !empty($pasien['additional_pasien']) ? $pasien['additional_pasien'] : '-';
         if(!empty($additionalPasien) && $additionalPasien != '-'){
            $additionalPasien = json_decode($additionalPasien, true);
            foreach ($additionalPasien as $value) {
                $no_identitas_pasien = isset($value['no_identitas_pasien']) && !empty($value['no_identitas_pasien']) ? $value['no_identitas_pasien'] : '-';
            }
         }
         $tempat_lahir = isset($pasien['tempat_lahir']) && !empty($pasien['tempat_lahir']) ? $pasien['tempat_lahir'] : '-';
      }

      return [
         '#nama_pasien#' => ArrayHelper::getValue($dataPasien, 'nama_pasien', '-'),
         '#alamat_pasien#' => ArrayHelper::getValue($dataPasien, 'alamat_pasien', '-'),
         '#umur#' => ArrayHelper::getValue($dataPasien, 'umur', '-'),
         '#jeniskelamin#' => ArrayHelper::getValue($dataPasien, 'jeniskelamin', '-'),
         '#no_rekam_medik#' => ArrayHelper::getValue($dataPasien, 'no_rekam_medik', '-'),
         '#no_pendaftaran#' => ArrayHelper::getValue($dataPasien, 'no_pendaftaran', '-'),
         '#agama#' => $agama,
         '#tglmasukpenunjang#' => $tglMasukPenunjang,
         '#rs_name#' => ArrayHelper::getValue($profilRs, 'namaRs', '-'),
         '#kota#' => ArrayHelper::getValue($profilRs, 'kota', '-'),
         '#alamat#' => ArrayHelper::getValue($profilRs, 'alamat', '-'),
         '#no_telp#' => ArrayHelper::getValue($profilRs, 'no_telp', '-'),
         '#no_identitas#' => $no_identitas_pasien,
         '#tempat_lahir#' => $tempat_lahir,
         '#tgl_selesai_operasi#' => !empty($data['selesai_operasi']) ? date('d M Y', strtotime($data['selesai_operasi'])) : '-',
         '#nama_dokter#' => !empty($data['dokter_bedah']) ? $data['dokter_bedah'] : '-',
         '#table#' => $this->renderPartial( $this->ViewPathCetakan() , ['data' => $data]),
      ];
   }

   private function ViewPathCetakan()
   {
      return Yii::$app->docoPlugin->execute('cetak_laporan_operasi');
   }
}
