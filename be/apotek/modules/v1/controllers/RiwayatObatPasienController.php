<?php


namespace app\modules\v1\controllers;

use app\modules\v1\models\UploadForm;
use Doco\components\DocoHelpers;
use Doco\Services\InternalService;
use yii\data\ActiveDataProvider;
use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\InfoRiwayatResep;
use app\modules\v1\models\PasienView;
use app\modules\v1\models\ObatAlkes;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoSpout;
use yii\web\UploadedFile;

class RiwayatObatPasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoRiwayatResep';
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
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
        // $this->konfig_farmasi = DocoConstants::konfigFarmasi();
        parent::init();
    }

    public function actionIndex($pasien_id = null)
    {
        $request = Yii::$app->request;
        $model = new InfoRiwayatResep;
        try {;
            $start = date('Y-m-d 00:00:00', strtotime('-30 days'));
            $end = date('Y-m-d 23:59:59');
            if(isset($_GET['advanced-filter']['tgl_transaksi']) && $_GET['advanced-filter']['tgl_transaksi'] != '') {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_transaksi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_transaksi']);
            }
            if (empty($_GET['advanced-filter']['pasien_id'])) {
                return [
                    'data' => [],
                    '_meta' => [
                        'totalCount' => 0
                    ],
                ];
            }
            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->andWhere(['between', 'tgl_transaksi', $start, $end]);
            return new ActiveDataProvider([
                'query' => $query
            ]);
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function getInfoObatAlkes($pasien_id, $where = [])
    {
        $obat_alkes = InfoRiwayatResep::find()
            ->where(['pasien_id' => $pasien_id])
            ->andWhere($where)
            ->all();
        return $obat_alkes;
    }

    public function actionSearchPasien()
    {
        $request = Yii::$app->request;
        $term = $request->get('term', '');
        if (empty($term)) {
            return ['data' => []];
        }
        $data = PasienView::find()
            ->where(['ilike', 'LOWER(nama_pasien)', strtolower($term)])
            ->orWhere(['ilike', 'LOWER(no_rekam_medik)', strtolower($term)]);
        return ['data' => $data->limit(20)->all()];
    }

    public function actionSearchObat()
    {
        $request = Yii::$app->request;
        $term = $request->get('term', '');
        if (empty($term)) {
            return ['data' => []];
        }
        $data = ObatAlkes::find()->where(['like', 'LOWER(obatalkes_nama)', strtolower($term)]);
        return ['data' => $data->all()];
    }

    public function getDataExcel($param)
    {
        $model = new InfoRiwayatResep;
        $start = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $end = date('Y-m-d 23:59:59');
        if(isset($_GET['advanced-filter']['tgl_transaksi']) && $_GET['advanced-filter']['tgl_transaksi'] != '') {
            $explode = explode(" - ", $_GET['advanced-filter']['tgl_transaksi']);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
            }
            unset($_GET['advanced-filter']['tgl_transaksi']);
        }
        $query = $model::find();
        $query->andWhere(['between', 'tgl_transaksi', $start, $end]);

        return DocoRestActiveFilter::advancedFilter($model, $query);
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

        (new InternalService)->sendTo([
            'Sirs' => [
                'RiwayatObatPasienExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportRiwayatObatPaien' => [
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
                'UploadRiwayatObatPasienExcel' => [
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
        $fileName = $dir.'/riwayat-obat-pasien.xlsx';

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
            unlink($filename);
            die();
        }
    }
}

?>