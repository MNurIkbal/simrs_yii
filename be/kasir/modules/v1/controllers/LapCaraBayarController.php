<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanCaraBayarView;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use yii\helpers\ArrayHelper;
use Doco\rabbitmq\RabbitBgProcess;
use yii\web\UploadedFile;
use app\modules\v1\models\UploadForm;

class LapCaraBayarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanCaraBayarView';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new LaporanCaraBayarView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pembayaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pembayaran']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        // if($between) {
            $query->andWhere(['between', 'tgl_pembayaran', $start, $end]);
        // }
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    protected $_title = "Laporan Cara Bayar";
    public function actionExportExcel()
    {
        $model = new LaporanCaraBayarView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pembayaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pembayaran']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        // if($between) {
            $query->andWhere(['between', 'tgl_pembayaran', $start, $end]);
        // }
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        
        foreach ($query->asArray()->all() as $key => $value) {
            // Data Selection
            $value['tgl_pembayaran'] = date("j M Y", strtotime($value['tgl_pembayaran']));

            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal pembayaran')] = $value['tgl_pembayaran'];
            $newValue[\Yii::t('app', 'No pembayaran')] = $value['no_pembayaran'];
            $newValue[\Yii::t('app', 'No pendaftaran')] = $value['no_pendaftaran'];
            $newValue[\Yii::t('app', 'No rekam medis')] = $value['no_rekam_medik'];
            $newValue[\Yii::t('app', 'Nama pasien')] = $value['nama_pasien'];
            $newValue[\Yii::t('app', 'Cara bayar')] = $value['carabayar_nama'];
            $newValue[\Yii::t("app", "Penjamin")] = $value['penjamin_nama'];
            $result[$key] = $newValue;
        }

        // Directory Creation
        $header = array(
            Yii::t("app", "Tanggal pembayaran") => date('d M Y', strtotime($start))." - ".date('d M Y', strtotime($end)),
            Yii::t("app", "Cara bayar") => (@$_GET['advanced-filter']['carabayar_nama']),
            Yii::t("app", "Penjamin") => (@$_GET['advanced-filter']['penjamin_nama']),
        );

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, array(
            "uploadPath" => "./uploads", // Optional, default folder "uploads" di root app & root advanced app
        ),[],[],true);
        
        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionExportPdf
    * @attribute #table_data# => table
    * @attribute #tgl_pembayaran# => tanggal pembayaran
    **/
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        try {
            $model = new LaporanCaraBayarView;
            $query = $model::find(true);

            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
            **/
            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');

            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['tgl_pembayaran'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaran']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_pembayaran']); // Unset Advanced Filter  date range
                    $between = true;
                }
            }
            $query->andWhere(['between', 'tgl_pembayaran', $start, $end]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            $print = new DocoPrint();
            $print->attributes = [
                '#table_data#' => $this->renderPartial('print_pdf',['data'=>$query->asArray()->all()]),
                '#tgl_pembayaran#' => date('d M Y', strtotime($start)).' - '.date('d M Y', strtotime($end)),
            ];
            $print->Output();
        } catch (\yii\db\Exception $e) {
            return "Terjadi Kesalahan";
        }
         catch (\yii\db\Exception $e) {
            return "Terjadi Kesalahan";
        }
    }

    public function actionExportExcelBgprocess() 
    {
        $request  = Yii::$app->request;
        $get  = $request->get();

        $randString = ArrayHelper::getValue($get, 'randString');

        if (isset($get['page'])) unset($get['page']);
        if (isset($get['per-page'])) unset($get['per-page']);

        $data = $this->actionGetObjectData();
        $countData = $totalPerPage = count($data);

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $carabayar = $penjamin = '-';

        if (isset($_GET['advanced-filter'])) {
            $filter = $request->get('advanced-filter');
            if (isset($_GET['advanced-filter']['tgl_pembayaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaran']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
            }

            if (isset($_GET['advanced-filter']['carabayar_nama'])) {
                $carabayar = $filter['carabayar_nama'];
            }

            if (isset($_GET['advanced-filter']['penjamin_nama'])) {
                $penjamin = $filter['penjamin_nama'];
            }
        }

        $periode = '' . date('j M Y', strtotime($start)) . ' - ' . date('j M Y', strtotime($end));

        $headerFilter = [
            'Tanggal Pembayaran' => $periode,
            'Cara Bayar' => $carabayar,
            'Penjamin' => $penjamin
        ];
        
        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'filter' => $get,
            'countData' => $countData, 
            'title' => 'Laporan Cara Bayar',
            'manual_excel' => false,
            'sendToUrl' => 'lap-cara-bayar/drop-file',
            'base_uri' => Yii::$app->docoRest->getBaseUri('kasir'),
            'getDataUrl' => 'lap-cara-bayar/get-object-data',
            'headerFilter' => $headerFilter,
        ], 'laporan_cara_bayar_kasir');

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionGetObjectData()
    {
        $request = Yii::$app->request;
        $model = new LaporanCaraBayarView;
        $query = $model::find(true);
        
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_pembayaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaran']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
            }
        }

        $query->andWhere(['between', 'tgl_pembayaran', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return $query->asArray()->all();
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
        $fileName = $rootPath.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }
}