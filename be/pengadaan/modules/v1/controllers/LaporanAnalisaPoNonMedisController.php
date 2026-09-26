<?php

/**
 * @author : Budi(budi@sirs.co.id)
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\LaporanAnalisaPoNonMedisView;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoActiveController;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;

class LaporanAnalisaPoNonMedisController extends DocoActiveController
{
   public $modelClass = '';
   public $namespace = 'app\modules\v1\actions\LaporanAnalisaPoNonMedis';

   public function verbs()
   {
      $verbs = parent::verbs();
      return $verbs;
   }

   public function actions()
   {
      return [
         'get-data' => $this->namespace . '\GetDataAction',
         'export-excel' => $this->namespace . '\ExportExcelAction',
         'sync-export-excel' => $this->namespace . '\SyncExportExcelAction',
      ];
   }

   public function dateFilter($query, $request)
   {
      $advancedFilter = $request->get('advanced-filter');
      $start = $end = '01-01-01';
      if (empty(ArrayHelper::getValue($advancedFilter, 'tgl_pr')) && empty(ArrayHelper::getValue($advancedFilter, 'tgl_po'))) {
         $query->andWhere(['between', 'tgl_pr', $start, $end]);
      } else {
         if (!empty(ArrayHelper::getValue($advancedFilter, 'tgl_pr'))) {
            $explodePr = explode(" - ", $advancedFilter['tgl_pr']);
            if (count($explodePr) == 2) {
               $start = date('Y-m-d 00:00:00', strtotime($explodePr[0]));
               $end = date('Y-m-d 23:59:59', strtotime($explodePr[1]));
            }
            $query->andWhere(['between', 'tgl_pr', $start, $end]);
         } 
         if (!empty(ArrayHelper::getValue($advancedFilter, 'tgl_po'))) {
            $explodePo = explode(" - ", $advancedFilter['tgl_po']);
            if (count($explodePo) == 2) {
               $start = date('Y-m-d 00:00:00', strtotime($explodePo[0]));
               $end = date('Y-m-d 23:59:59', strtotime($explodePo[1]));
            }
            $query->andWhere(['between', 'tgl_po', $start, $end]);
         }
      }
   }

   public function getDataDummy()
   {
      return [
         'data' => [
            0 => [
               'kode_barang' => 'BRG001',
               'nama_barang' => 'Kursi',
               'no_pr' => 'PR001',
               'tgl_pr' => '2021-03-12',
               'qty_pr' => '1 Box',
               'uom_pr' => '1 Box = 100 Tab',
               'catatan' => 'Test Catatan',
               'no_po' => 'PO002',
               'tgl_po' => '2021-03-05',
               'tgl_validasi_po' => '2021-03-05',
               'tgl_batal_po' => '',
               'catatan_batal_po' => '',
               'qty_po' => '1 Box',
               'uom_po' => '1 Box = 100 Tab',
               'harga' => '50000',
               'diskon' => '0',
               'ppn' => '10',
               'subtotal' => '5500000',
               'harga_total' => '5500000',
               'no_penerimaan' => 'PN001',
               'tgl_penerimaan' => '2021-03-10',
               'qty_penerimaan' => '1 Box',
               'uom_penerimaan' => '1 Box = 100 Tab',
               'sisa_penerimaan' => '0',
               'uom_sisa_penerimaan' => '0',
               'nofaktur_penerimaan' => 'FAK001',
               'tgl_verifikasi_penerimaan' => '2021-03-10',
               'pr_jarak_po' => '5 Hari',
               'po_jarak_tgl_penerimaan' => '3 Hari',
               'pr_jarak_tgl_penerimaan' => '4 Hari',
               'po_jarak_validasi_po' => '2 Hari',
               'po_validasi_tgl_penerimaan' => '1 Hari',
               'kode_supplier' => 'SUP001',
               'nama_supplier' => 'Gajah Mada',
               'catatan_po' => '',
            ],
            1 => [
               'kode_barang' => 'BRG002',
               'nama_barang' => 'Meja',
               'no_pr' => 'PR002',
               'tgl_pr' => '2021-03-15',
               'qty_pr' => '2 Box',
               'uom_pr' => '2 Box = 200 Tab',
               'catatan' => '',
               'no_po' => 'PO001',
               'tgl_po' => '2021-03-01',
               'tgl_validasi_po' => '2021-03-01',
               'tgl_batal_po' => '',
               'catatan_batal_po' => '',
               'qty_po' => '1 Box',
               'uom_po' => '1 Box = 100 Tab',
               'harga' => '25000',
               'diskon' => '0',
               'ppn' => '15',
               'subtotal' => '2500000',
               'harga_total' => '2500000',
               'no_penerimaan' => 'PN001',
               'tgl_penerimaan' => '2021-03-10',
               'qty_penerimaan' => '1 Box',
               'uom_penerimaan' => '1 Box = 100 Tab',
               'sisa_penerimaan' => '0',
               'uom_sisa_penerimaan' => '0',
               'nofaktur_penerimaan' => 'FAK001',
               'tgl_verifikasi_penerimaan' => '2021-03-10',
               'pr_jarak_po' => '5 Hari',
               'po_jarak_tgl_penerimaan' => '3 Hari',
               'pr_jarak_tgl_penerimaan' => '4 Hari',
               'po_jarak_validasi_po' => '2 Hari',
               'po_validasi_tgl_penerimaan' => '1 Hari',
               'kode_supplier' => 'SUP001',
               'nama_supplier' => 'Gajah Mada',
               'catatan_po' => '',
            ]
         ],
         '_meta' => [
            'currentPage' => 1,
            'totalCount' => 2,
            'pageCount' => 1,
            'perPage' => 10,
         ]
      ];
   }

   public function getDataLaporanExcel($request)
   {
      $model = new LaporanAnalisaPoNonMedisView;
      $query = $model::find();
      if (count($request) > 0) {
         $this->dateFilter($query, $request);
      }
      $query = DocoRestActiveFilter::advancedFilter($model, $query);
      $query = $query->all();
      return [
         'count' => count($query),
         'data' => $query
      ];
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
            
            $path = "uploads/".$filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

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
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/Laporan PO Analisa Non Medis.xlsx';

        if (file_exists($fileName)) 
        {
            $file = basename($fileName);
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($fileName);
            die();
        }
    }

}
