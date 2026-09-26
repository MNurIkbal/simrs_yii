<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\KonfigGudang;
use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoMessages;
use app\modules\v1\models\LaporanHasilSoBarangView;
use app\modules\v1\models\Ruangan;
use yii\helpers\ArrayHelper;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;

class LaporanHasilStokOpnameBarangController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanHasilSoBarangView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["detail"] = ["GET"];
        $verbs["index"] = ["POST", "GET"];
        $verbs["export-excel"] = ["GET"];
        $verbs["get-konfig-ruangan"] = ["GET"];
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
        $model = new LaporanHasilSoBarangView;
        $query = $model::find(true);
        
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_form_so'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_form_so']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_form_so']);
            }
        }
        $query->andWhere(['between', 'tgl_form_so', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetKonfigRuangan() {
        $konfigFarmasi = KonfigGudang::find()->one();
        return [
            'konfig_gudang' => $konfigFarmasi,
            'ruangan' => ArrayHelper::map(Ruangan::find()->orderBy(["ruangan_nama" => SORT_ASC])->all(), 'ruangan_nama', 'ruangan_nama')
        ];
    }

    public function getDataExcel($param){
        $model = new LaporanHasilSoBarangView;
        $query = $model::find(true);
        
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if (isset($param['advanced-filter'])) {
            if(isset($param['advanced-filter']['tgl_form_so'])){
                $explode = explode(" - ", $param['advanced-filter']['tgl_form_so']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($param['advanced-filter']['tgl_form_so']);
            }
        }

        $query->andWhere(['between', 'tgl_form_so', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model,$query);
        return $query;
    }

    public function actionSyncExportExcel() {
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

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanHasilSOBarangExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportLaporanHasilSOBarang' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadLaporanHasilSOBarangExcel' => [
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

    public function actionDropFile() {
        $request = Yii::$app->request;
        $model = new UploadForm;

        $filePath = $request->get('filePath', null);
        if ($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $ext = $files->getExtension();
            $model->file = $filePath.'.'.$ext;

            $path = "uploads/";
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

    public function actionDownloadFile() {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads/';
        $fileName = $rootPath.$no_request.".xlsx";

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
            unlink($fileName);
            die();
        }
    }
}
