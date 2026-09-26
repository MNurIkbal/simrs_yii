<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\KonfigFarmasi;
use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoMessages;
use app\modules\v1\models\LaporanHasilSoView;
use Doco\Services\InternalService;
use app\modules\v1\models\Ruangan;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\UploadForm;
use Doco\rabbitmq\RabbitBgProcess;
use Doco\Repositories\KonfigRepositories;
use yii\web\UploadedFile;
use yii\db\Expression;

class LaporanHasilStokOpnameController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanHasilSoView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["detail"] = ["GET"];
        $verbs["index"] = ["GET"];
        $verbs["sync-export-excel"] = ["GET"];
        $verbs["download-file"] = ["GET"];
        $verbs["get-data-excel"] = ["GET"];
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
        $model = new LaporanHasilSoView;
        $query = $model::find(true);
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_form_so'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_form_so']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_form_so']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($_GET['advanced-filter']['instalasi_ruangan'])) {
                $ruangan_id = $_GET['advanced-filter']['instalasi_ruangan'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                unset($_GET['advanced-filter']['instalasi_ruangan']); // Unset Advanced Filter  instalasi_ruangan
            }
        }
        $query->andWhere(['between', 'tgl_form_so', $start, $end]);
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetKonfigRuangan() {
        try {
            $konfigFarmasi = KonfigRepositories::getKonfigFarmasi();
            return [
                'konfig_farmasi' => $konfigFarmasi,
                'ruangan' => ArrayHelper::map(Ruangan::find()->orderBy(["ruangan_nama" => SORT_ASC])->all(), 'ruangan_id', 'ruangan_nama')
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getDataExcel()
    {
        $date = date('Y-m-d');
        $model = new LaporanHasilSoView;
        $query = $model::find(true);

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_form_so'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_form_so']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_form_so']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($_GET['advanced-filter']['instalasi_ruangan'])) {
                $ruangan_id = $_GET['advanced-filter']['instalasi_ruangan'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                unset($_GET['advanced-filter']['instalasi_ruangan']); // Unset Advanced Filter  instalasi_ruangan
            }
        }
        $query->andWhere(['between', 'tgl_form_so', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $data = $this->getDataExcel($getData)->asArray()->all();
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);
        $footer = $this->actionGenerateTotal();
        
        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanHasilStokOpnameExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                    'footer' => $footer,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportLaporanHasilStokOpname' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                    'footer' => [],
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadLaporanHasilStockOpnameExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionSyncExportExcelRabbitmq()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $data = $this->getDataExcel($getData)->asArray()->all();
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);
        $footer = $this->actionGenerateTotal();

        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'filter' => $getData,
            'totalPerPage' => $countData,
            'countData' => $countData,
            'footer' => $footer,
            'sendToUrl' => 'laporan-hasil-stok-opname/drop-file',
            'base_uri' => Yii::$app->docoRest->getBaseUri('apotek'),
        ], 'laporan_hasil_stok_opname',  'import_data_hasil_stok_opname');

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
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
        $fileName = $rootPath.'/' . $no_request . '.xlsx';

        DocoHelpers::downloadFileExcel($fileName);
    }

    public function actionGenerateTotal()
    {
        $model = new LaporanHasilSoView;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $query = $model::find()->select([
            new Expression("SUM(total_harga_selisi) AS  total_selisih"),
            new Expression("SUM(weighted_avg*stok_fisik) AS  total_fisik"),
            new Expression("SUM(total_harga_sistem) AS  total_sistem"),
        ]);
        
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_form_so'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_form_so']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_form_so']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($_GET['advanced-filter']['no_form_so'])){
                $form = $_GET['advanced-filter']['no_form_so'];
                $query->andWhere(['ILIKE', 'no_form_so', $form]);
            }

            if(isset($_GET['advanced-filter']['instalasi_ruangan'])) {
                $ruangan_id = $_GET['advanced-filter']['instalasi_ruangan'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                unset($_GET['advanced-filter']['instalasi_ruangan']);
            }
        }
        $query->andWhere(['between', 'tgl_form_so', $start, $end]);
        $data = $query->asArray()->one();

        return $this->responseJson(200, 'success', $data);
    }
}
