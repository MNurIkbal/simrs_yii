<?php

namespace app\modules\bedah\components\traits;

use Yii;
use app\components\DocoHelpers;
use Doco\bedah\models\LaporanOperasiForm;
use yii\helpers\ArrayHelper;

trait LaporanOperasiTrait
{
   public function actionLaporanOperasi()
   {
      $request = Yii::$app->request;
      $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
      $id = $request->get('id', null);
      $title = 'Laporan Operasi';
      $model = new LaporanOperasiForm;
      $dokterBedah = $this->getDokterBedah($id);
      $attributes = $this->setAttributes($id, $model);
      $model->attributes = ArrayHelper::getValue($attributes, 'model');
      $params = [
         'id' => $id,
         'title' => $title,
         'model' => $model,
         'ruanganId' => $ruangan_id,
         'dokter_operator' => $dokterBedah,
      ];
      return $this->renderAjax('detail-partial/_laporan_operasi', $params);
   }

   private function getDataLaporan($id)
   {
      return $this->guzzleExec($this->_restBedah, [
         'url' => 'laporan-operasi/get-data-api',
         'payload' => [
            'query' => [
               'id' => $id
            ]
         ],
      ]);
   }

   public function actionGetDataMultiple()
   {
      $request = Yii::$app->request;
      $limit = $request->get('limit', 10);
      $data = $this->guzzleExec($this->_restBedah, [
         'url' => 'laporan-operasi/get-data-multiple',
         'payload' => [
            'query' => $request->get()
         ],
         'returnResponse' => true,
      ]);
      $result = [];
      $result['results'] = [];
      $data = ArrayHelper::getValue($data, 'data');
      if (!empty($data)) {
         foreach ($data as $value) {
            $result['results'][] = [
               'id' => ArrayHelper::getValue($value, 'id'),
               'text' => ArrayHelper::getValue($value, 'text')
            ];
         }
      }
      $results = ArrayHelper::getValue($result, 'results');
      return DocoHelpers::response([
         'result' => $results,
         'total_count' => count($results),
         'incomplete_results' => false,
         'pagination' => ['more' => count($results) === $limit ? true : false]
      ]);
   }

   public function actionSimpanLaporan()
   {
      $request = Yii::$app->request;
      $id = $request->get('id', null);
      $model = new LaporanOperasiForm;
      $formName = substr(strrchr(get_class($model), "\\"), 1);
      if($request->post()) {
         $model->load($request->post());
         $postData = $request->post('LaporanOperasiForm');
         $model->attributes = $postData;
         $model->pasienmasukpenunjang_id = $id;
         if($model->validate()) {
            return $this->guzzleExec($this->_restBedah, [
               'url' => 'laporan-operasi/simpan-laporan',
               'method' => 'POST',
               'payload' => [
                  'form_params' => $model->attributes,
                  'query' => [
                     'id' => $id
                  ]
               ],
               'returnResponse' => true,
            ]);
         }
         else {
            $response = $model->errors;
            return DocoHelpers::response($response,422,$formName);
         }
      }
   }

   public function actionCetak()
   {
      $request = Yii::$app->request;
      $id = $request->get('laporan_id', null);
      $req_id = $request->get('id', null);
      $pasienmasukpenunjang_id = $request->get('pasienmasukpenunjang_id', null);
      $pasienmasukpenunjang_id  = is_null($pasienmasukpenunjang_id) ? $req_id : $pasienmasukpenunjang_id;
      $path = Yii::getAlias("@download") . "/laporan-operasi.pdf";
      $urlReport = 'laporan-bedah';
      try {
         if(Yii::$app->report->isAvailable($urlReport)){
            return Yii::$app->report->exec($urlReport, [
                  'queryParameter' => [
                     'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id,
                     'id' => $id
                  ],
            ]);
         }
         $response = $this->_restBedah->get('laporan-operasi/cetak-laporan', [
            'query' => [
               'id' => $id,
               'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id,
            ],
            'save_to' => $path
         ]);
         $body = json_decode($response->getBody(), true);
         return DocoHelpers::previewPdf($path);
     } catch (RequestException $e) {
         throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
     } catch (\Exception $e) {
         throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
     }
   }

   private function setAttributes($id, $model)
   {
      $data = $this->getDataLaporan($id);
      $inpost = ArrayHelper::getValue($data, 'data', []);
      $prosedur = ArrayHelper::getValue($data, 'prosedur', []);

      $laporan = ArrayHelper::getValue($inpost, 'laporanOperasi', []);
      $additionalData = ArrayHelper::getValue($laporan, 'additional_data', []);
      $additionalData = !empty($additionalData) ? json_decode($additionalData, true) : [];
      $laporanId = ArrayHelper::getValue($laporan, 'laporanoperasi_id');
      
      $kategori = ArrayHelper::getValue($data, 'kategori', []);
      $pembiusan = ArrayHelper::getValue($data, 'pembiusan', []);
      $tim_operasi = ArrayHelper::getValue($data, 'tim_operasi', []);
      $dokter_operator = ArrayHelper::getValue($tim_operasi, 'dokter_operator');
      $tanggalOperasiInPost = ArrayHelper::getValue($inpost, 'tgl_kirimpasien');
      $tanggalOperasiInPost = !empty($tanggalOperasiInPost) ? date('Y-m-d', strtotime($tanggalOperasiInPost)) : '';
      $mulaiOperasiInpost = ArrayHelper::getValue($inpost, 'mulai_operasi');
      $mulaiOperasiInpost = !empty($mulaiOperasiInpost) ?  ( $tanggalOperasiInPost .' '. date('H:i', strtotime($mulaiOperasiInpost))) : '';
      $selesaiOperasiInpost = ArrayHelper::getValue($inpost, 'selesai_operasi');
      $selesaiOperasiInpost = !empty($selesaiOperasiInpost) ? ( $tanggalOperasiInPost .' '. date('H:i', strtotime($selesaiOperasiInpost))) : '';
      $mulaiAnastesiInpost = ArrayHelper::getValue($inpost, 'mulai_anastesi');
      $mulaiAnastesiInpost = !empty($mulaiAnastesiInpost) ? date('Y-m-d H:i', strtotime($mulaiAnastesiInpost)) : '';
      $selesaiAnastesiInpost = ArrayHelper::getValue($inpost, 'selesai_anastesi');
      $selesaiAnastesiInpost = !empty($selesaiAnastesiInpost) ? date('Y-m-d H:i', strtotime($selesaiAnastesiInpost)) : '';

      $mulaiOperasi = ArrayHelper::getValue($laporan, 'mulai_operasi');
      $mulaiOperasi = !empty($mulaiOperasi) ? date('Y-m-d H:i', strtotime($mulaiOperasi)) : $mulaiOperasiInpost;
      $selesaiOperasi = ArrayHelper::getValue($laporan, 'selesai_operasi');
      $selesaiOperasi = !empty($selesaiOperasi) ? date('Y-m-d H:i', strtotime($selesaiOperasi)) : $selesaiOperasiInpost;
      $mulaiAnastesi = ArrayHelper::getValue($laporan, 'mulai_pembiusan');
      $mulaiAnastesi = !empty($mulaiAnastesi) ? date('Y-m-d H:i', strtotime($mulaiAnastesi)) : $mulaiAnastesiInpost;
      $selesaiAnastesi = ArrayHelper::getValue($laporan, 'selesai_pembiusan');
      $selesaiAnastesi = !empty($selesaiAnastesi) ? date('Y-m-d H:i', strtotime($selesaiAnastesi)) : $selesaiAnastesiInpost;
      
      // $dokter_anastesi = ArrayHelper::getValue($tim_operasi, 'dokter_anastesi');
      // $asistenIntra = ArrayHelper::getValue($tim_operasi, 'asisten');
      // $asisten_instrumen_intra = ArrayHelper::getValue($tim_operasi, 'asisten_instrumen');
     
      $model->is_jaringan_dikirim = 0;
      $model->jam_masuk_rec = $mulaiOperasi;
      $model->jam_keluar_rec = $selesaiOperasi;
      $model->mulai_pembiusan = $mulaiAnastesi;
      $model->selesai_pembiusan = $selesaiAnastesi;
      $interval = $this->setInterval($model->jam_keluar_rec, $model->jam_masuk_rec);
      $model->lama_pembedahan = $interval;
      // $model->kategori_operasi = ArrayHelper::getValue($laporan, 'kategori_operasi');
      if(!empty($additionalData)) {
         $dokter_bedah = ArrayHelper::getValue($additionalData, 'dokter_bedah');
      }
      else {
         $dokter_bedah = ArrayHelper::getValue($laporan, 'dokter_bedah');
         $splitBedah = !empty($dokter_bedah) ? explode('<br/>', $dokter_bedah) : '';
         $dokter_bedah = !empty($splitBedah) ? $splitBedah : $dokter_operator;
      }

      return [
         'model' => $model->attributes,
         'kategori' => $kategori,
         'prosedur' => $prosedur,
         'pembiusan' => $pembiusan,
         'tim_operasi' => $tim_operasi,
         'laporanId' => $laporanId,
         'dokter_bedah' => $dokter_bedah,
      ];
   }

   private function setInterval($endDate, $startDate)
   {
      $interval = '';
      if (!empty($endDate) && !empty($startDate)) {
         $end = new \DateTime($endDate);
         $start = new \DateTime($startDate);
         $interval = $end->diff($start);
         $interval = $interval->format('%h') . " Jam " . $interval->format('%i') . " Menit";
      }
      return $interval;
   }

   public function actionGetDataTim()
   {
      $request = Yii::$app->request;
      $id = $request->get('id', null);
      $dokter_id = $request->get('dokter_id', null);
      $laporan_id = $request->get('laporan_id', null);
      
      return $this->guzzleExec($this->_restBedah, [
         'url' => 'laporan-operasi/get-data-tim',
         'payload' => [
            'query' => [
               'id' => $id,
               'dokter_id' => $dokter_id,
               'laporan_id' => $laporan_id,
            ]
         ],
         'returnResponse' => true,
      ]);
   }

   public function actionGetFilters()
   {
      return $this->guzzleExec($this->_restBedah, [
         'url' => 'laporan-operasi/filters',
         'payload' => [
            'query' => Yii::$app->request->get()
         ],
         'returnResponse' => true
      ]);
   }

   private function getDokterBedah($id)
   {
      return $this->guzzleExec($this->_restBedah, [
         'url' => 'laporan-operasi/get-dokter-bedah',
         'payload' => [
            'query' => [
               'id' => $id,
            ]
         ],
      ]);
   }

   public function actionDeleteDokter()
   {
      $request = Yii::$app->request;
      $id = $request->get('id', null);
      $dokter_id = $request->get('data_dokter_id', null);
      $penunjang_id = $request->get('penunjang_id', null);

      return $this->guzzleExec($this->_restBedah, [
         'url' => 'verifikasi-tagihan/delete-laporan-dokter',
         'payload' => [
            'query' => [
               'id' => $id,
               'dokter_id' => $dokter_id,
               'penunjang_id' => $penunjang_id,
            ]
         ],
         'returnResponse' => true,
      ]);
   }
}
