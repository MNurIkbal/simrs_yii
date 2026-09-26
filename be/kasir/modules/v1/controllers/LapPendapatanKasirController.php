<?php
/**
 * @author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use app\modules\v1\models\LaporanPendapatanTindakanView;
use app\modules\v1\models\KelompokTindakan;
use app\modules\v1\models\DaftarTindakan;
use Doco\models\KelasPelayanan;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;

class LapPendapatanKasirController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPendapatanTindakanView';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

   public function actionIndex()
   {
      $data = $this->actionGetData();
      return  isset($data['data']) ? $data['data'] : [];
   }

   public function actionGetData()
   {
      $start = $end = $startPulang = $endPulang = $periodePembayaran = $periodePulang = $unit = $kelas = $listKelompok = $listTindakan = $listUnit = '-';
      $data = [];
      if(isset($_GET['advanced-filter'])) {
         $advancedFilter = $_GET['advanced-filter'];
         $model = new LaporanPendapatanTindakanView;
         $query = $model->find()->select([
            'kelompoktindakan_nama', 'daftartindakan_kode', 'daftartindakan_nama', 'SUM(qty) AS qty', 'SUM(total_harga) AS total_harga'
         ]);
         $query->groupBy(['kelompoktindakan_nama', 'daftartindakan_kode', 'daftartindakan_nama']);
         $query->orderBy(['kelompoktindakan_nama'=> SORT_ASC]);
         if(isset($advancedFilter['tgl_pembayaran']) && !empty($advancedFilter['tgl_pembayaran'])) {
            $explode = explode(" - ", $advancedFilter['tgl_pembayaran']);
            if(count($explode) == 2) {
               $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
               $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
               $periodePembayaran = date('j M Y', strtotime($start)) . ' - ' . date('j M Y', strtotime($end));
               $query->andWhere(['between', 'tgl_pembayaran', $start, $end]);
            }
         }
         if(isset($advancedFilter['tgl_pulang']) && !empty($advancedFilter['tgl_pulang'])) {
            $explode = explode(" - ", $advancedFilter['tgl_pulang']);
            if(count($explode) == 2) {
               $startPulang = date('Y-m-d 00:00:00', strtotime($explode[0]));
               $endPulang = date('Y-m-d 23:59:59', strtotime($explode[1]));
               $periodePulang = date('j M Y', strtotime($startPulang)) . ' - ' . date('j M Y', strtotime($endPulang));
               $query->andWhere(['between', 'tglpasienpulang', $startPulang, $endPulang]);
            }
         }
         if(isset($advancedFilter['unit']) && !empty($advancedFilter['unit'])) {
            $unit = $advancedFilter['unit'];
            $listUnit = ($unit == 'RAJAL') ? 'RAWAT JALAN' : 'RAWAT INAP';
            $query->andWhere(['pelayanan' => $unit]);
         }
         if(isset($advancedFilter['kelas']) && !empty($advancedFilter['kelas'])) {
            $kelas = $advancedFilter['kelas'];
            $query->andWhere(['kelaspelayanan_id' => $kelas]);
         }
         if(isset($advancedFilter['kelompok']) && !empty($advancedFilter['kelompok'])) {
            $kelompoktindakan_nama = $advancedFilter['kelompok'];
            $listKelompok = $kelompoktindakan_nama;
            $kelompoktindakan_nama = explode(",",$kelompoktindakan_nama);
            $query->andWhere(['kelompoktindakan_nama' => $kelompoktindakan_nama]);
         }
         if(isset($advancedFilter['tindakan']) && !empty($advancedFilter['tindakan'])) {
            $daftartindakan_nama = $advancedFilter['tindakan'];
            $listTindakan = $daftartindakan_nama;
            $daftartindakan_nama = explode(",",$daftartindakan_nama);
            $query->andWhere(['daftartindakan_nama' => $daftartindakan_nama]);
         }
         $data = $query->asArray()->all();
      }

      $header = [
         'Tanggal Pembayaran' => $periodePembayaran,
         'Tanggal Pulang' => $periodePulang,
         'Unit Pelayanan' => $listUnit,
         'Kelas Tagihan' => $kelas,
         'Kelompok Tindakan' => $listKelompok,
         'Nama Tindakan' => $listTindakan
      ];

      return [
         'data' => $data,
         'header' => $header,
      ];
   }

   private function getheader(){
      $column = [];

      $column = [
      [
          'title' => 'Kode Tindakan',
          'data' => 'daftartindakan_kode',
          'searchable' => false,
          'visible' => true,
      ],
      [
         'title' => 'Nama Tindakan',
         'data' => 'daftartindakan_nama',
         'searchable' => false,
         'visible' => true,
     ],
      [
          'title' => 'Jumlah',
          'data' => 'qty',
          'searchable' => false,
          'visible' => true,
      ],
      [
          'title' => 'Sub Total',
          'data' => 'total_harga',
          'searchable' => false,
          'visible' => true,
      ],
      ];

      return $column;
  }

  public function actionExportExcelBgProses() 
  {
      $request = Yii::$app->request;
      $get = $request->get();
      $advancedFilter = $get['advanced-filter'];
      $xOwner = $request->getHeaders()->get('X-Owner');
      $auth = $request->getHeaders()->get('Authorization');
      $randString = isset($get['randString']) ? $get['randString'] : null;
      $headerExcel = [];
      if (isset($get['page'])) unset($get['page']);
      if (isset($get['per-page'])) unset($get['per-page']);
      /** set header excel */
      
      $startPembayaran = date('d-M-Y');
      $endPembayaran = date('d-M-Y');
      $startPulang = '';
      $endPulang = '';
      if (isset($advancedFilter)) {
          if(isset($advancedFilter['tgl_pembayaran']) && !empty($advancedFilter['tgl_pembayaran'])) {
            $explode = explode(" - ", $advancedFilter['tgl_pembayaran']);
            if(count($explode) == 2) {
               $startPembayaran = date('d-M-Y', strtotime($explode[0]));
               $endPembayaran = date('d-M-Y', strtotime($explode[1]));
            }
         }
         if(isset($advancedFilter['tgl_pulang']) && !empty($advancedFilter['tgl_pulang'])) {
            $explode = explode(" - ", $advancedFilter['tgl_pulang']);
            if(count($explode) == 2) {
               $startPulang = date('d-M-Y', strtotime($explode[0]));
               $endPulang = date('d-M-Y', strtotime($explode[1]));
            }
         }   
      }
      $unit_filter = isset($advancedFilter['unit']) ? $advancedFilter['unit']:'-';
      $kelas_filter = isset($advancedFilter['nama_kelas']) ? $advancedFilter['nama_kelas']:'-';
      $kelompok_filter = isset($advancedFilter['kelompok']) ? $advancedFilter['kelompok']:'-';
      $tindakan_filter = isset($advancedFilter['tindakan']) ? $advancedFilter['tindakan']:'-';
      
      $headerExcel = [
          "Tanggal Pembayaran" => $startPembayaran. ' - '.$endPembayaran,
          "Tanggal Pulang" => $startPulang. ' - '.$endPulang,
          "Unit Pelayanan" => $unit_filter,
          "Kelas Tagihan" => $kelas_filter,
          "Kelompok Tindakan" => $kelompok_filter,
          "Nama Tindakan" => $tindakan_filter,
      ];
      
      $header = $this->getheader();
      $data = $this->actionGetData();
      $countData = count($data['data']);
      $totalPerPage =count($data['data']);
      $options = [
          "skipIncrement" => true,
          "customHeader" => [],
      ];
      $uri_kasir = Yii::$app->docoRest->getBaseUri('kasir');
      $params = [
          'sendToUrl' => 'lap-pendapatan-kasir/drop-file',
          'getDataUrl' => 'lap-pendapatan-kasir/get-data-laporan',
          'base_uri' => $uri_kasir,
      ];
      (new InternalService)->sendTo([
          'Sirs' => [
              'DataExportExcel' => [
                  'token' => $auth,
                  'xOwner' => $xOwner,
                  'unique_str' => $randString,
                  'filter' => $get,
                  'params' => $params
              ]
          ]
      ], true);

      (new InternalService)->sendTo([
          'Sirs' => [ 
              'ExportExcel' => [
                  'token' => $auth,
                  'xOwner' => $xOwner,
                  'unique_str' => $randString,
                  'totalPerPage' => $totalPerPage,
                  'countData' => $countData,
                  'filter' => $get,
                  'title' => 'Laporan Rekapitulasi Pendapatan',
                  'headerExcel' => $headerExcel,
                  'footer' => [],
                  'options' => $options,
                  'header' => $header,
              ]
          ]
      ], true);

      (new InternalService)->sendTo([
          'Sirs' => [ 
              'UploadExcel' => [
                  'token' => $auth,
                  'xOwner' => $xOwner,
                  'unique_str' => $randString,
                  'totalPerPage' => $totalPerPage,
                  'countData' => $countData,
                  'params' => $params
              ]
          ]
      ], true);

      return [
          'totalPerPage' => $totalPerPage,
          'unique_str' => $randString,
          'countData' => $countData,
      ];
  }

   public function actionGetDataLaporan()
   {
      try {
         $result = $this->actionGetData();
         $data = isset($result['data']) ? $result['data'] : [];
         $header = isset($result['header']) ? $result['header'] : [];
         $result = [];
         foreach ($data as $key => $val) {
            $kelompoktindakan_nama = isset($val['kelompoktindakan_nama']) ? $val['kelompoktindakan_nama'] : '';
            if(!empty($kelompoktindakan_nama)) {
               $result[$kelompoktindakan_nama][] = $val;
            }
         }
         $row = [];
         $urutData = 0;
         $newRow = [];
         $grandTotal = $qtyGrandTotal = 0;
         foreach ($result as $kelompok => $values) {
            $urutData++;
            $row[] = [
               'daftartindakan_kode' => $kelompok,
               'daftartindakan_nama' => null,
               'qty' => null,
               'total_harga' => null,
            ];
            $subTotal = $qtySubTotal = 0;
            foreach ($values as $key => $detail) {
               $total_harga = isset($detail['total_harga']) ? $detail['total_harga'] : 0;
               $jumlah = isset($detail['qty']) ? $detail['qty'] : 1;
               $kelompoktindakan_nama = isset($val['kelompoktindakan_nama']) ? $val['kelompoktindakan_nama'] : '';
               $daftartindakan_kode = isset($detail['daftartindakan_kode']) ? $detail['daftartindakan_kode'] : '';
               $daftartindakan_nama = isset($detail['daftartindakan_nama']) ? $detail['daftartindakan_nama'] : '';
               $subTotal += $total_harga;
               $grandTotal += $total_harga;
               $qtySubTotal += $jumlah;
               $qtyGrandTotal += $jumlah;
               $newRow = [
                  'daftartindakan_kode' => " $daftartindakan_kode ",
                  'daftartindakan_nama' => $daftartindakan_nama,
                  'qty' => $jumlah,
                  'total_harga' => $total_harga,
               ];
               $row[] = $newRow;
            }
            $row[] = [
               'daftartindakan_kode' => 'SUB TOTAL '. strtoupper($kelompok),
               'daftartindakan_nama' => null,
               'qty' => $qtySubTotal,
               'total_harga' => $subTotal
            ];
         }
         $row[] = [
            'daftartindakan_kode' => 'GRAND TOTAL',
            'daftartindakan_nama' => null,
            'qty' => $qtyGrandTotal,
            'total_harga' => $grandTotal
         ];

         return $row;
      } catch (\yii\db\Exception $e) {
         \Yii::$app->response->statusCode = 500;
         return ['message' => $e->getMessage()];
      } catch (\Exception $e) {
         \Yii::$app->response->statusCode = 500;
         return ['message' => $e->getMessage()];
      }
   }

   public function actionFilters()
   {
      $type = Yii::$app->request->get('type', null);
      $payload = Yii::$app->request->get('payload', []);
      $page = isset($payload['page']) ? $payload['page'] : 1;
      $limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
      $term = isset($payload['term']) ? $payload['term'] : null;
      $result = [];
      if($type == 'kelas') {
         $result = KelasPelayanan::find()
            ->select(['kelaspelayanan_id AS id', 'kelaspelayanan_nama AS text'])
            ->where(['is_active' => true]);

         if(!empty($term)) {
            $result->andWhere(['like', 'LOWER(kelaspelayanan_nama)', strtolower($term)]);
         }

         $result->orderBy(['kelaspelayanan_nama' => SORT_ASC]);
      }
      elseif($type == 'kelompok') {
         $result = KelompokTindakan::find()
            ->select(['kelompoktindakan_nama AS id', 'kelompoktindakan_nama AS text'])
            ->where(['is_active' => true]);

         if(!empty($term)) {
            $result->andWhere(['like', 'LOWER(kelompoktindakan_nama)', strtolower($term)]);
         }

         $result->orderBy(['kelompoktindakan_nama' => SORT_ASC]);
      }
      elseif($type == 'tindakan') {
         $result = DaftarTindakan::find()
               ->select(['daftartindakan_nama AS id', 'daftartindakan_nama AS text'])
               ->where(['is_active' => true]);

         if(!empty($term)) {
            $result->andWhere(['like', 'LOWER(daftartindakan_nama)', strtolower($term)]);
         }

         $result->orderBy(['daftartindakan_nama' => SORT_ASC]);
      }
        
      if(!empty($result)) {
         $result = $result->limit($limit + 1)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
      }
      return $result;
   }
   
   public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;
        
        $filePath = $request->get('filePath', null);
        if ($request->isPost) 
        {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            
            $path = "uploads/";

            $nameFile = $path .'/'. $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'upload file berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        // $dir = $rootPath.'/'.$no_request;
        $fileName = $rootPath.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }
}