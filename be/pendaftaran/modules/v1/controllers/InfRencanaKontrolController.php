<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-31 11:12
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\InfRencanaKontrol;
use app\modules\v1\models\UploadForm;
use Doco\components\DocoConstants;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use Doco\rabbitmq\RabbitBgProcess;
use Yii;
use yii\data\ActiveDataProvider;

class InfRencanaKontrolController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\inf-rencana-kontrol';

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
        return $actions;
    }

    public function actionInitIndex()
    {
        $result['jenis'] = [];
        $result['statusDaftar'] = [];
        $result['poliklinik'] = [];
        try {
            $poliklinik = Yii::$app->runAction('/v1/allow/list-ruangan', ['instalasi_id' => DocoConstants::VAR_I_RJ]);
            $result['poliklinik'] = ArrayHelper::map($poliklinik['response']['data'], 'ruangan_id', 'ruangan_nama');

            $jenis = AllowController::getLookupByType('transaksi_konsul')->all();
            $result['jenis'] = ArrayHelper::map($jenis, 'lookup_id', 'lookup_name');

            $status = AllowController::getLookupByType('status_daftar_ol')->all();
            $result['statusDaftar'] = ArrayHelper::map($status, 'lookup_id', 'lookup_name');
            return $result;
        } catch (\Exception $e) {
            return $result;
        }
    }

    public function actionIndex()
    {
        $model = new InfRencanaKontrol;
        $query = $model::find();
        $query = $this->getData($model,$query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    protected function getData($model, $query)
    {
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            }
            if (isset($_GET['advanced-filter']['tgl_jadwal'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_jadwal']);
                if (count($explode) == 2) {
                    $startJadwal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $endJadwal = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    $query->andWhere(['between', 'tgl_jadwal', $startJadwal, $endJadwal]);
                }
                unset($_GET['advanced-filter']['tgl_jadwal']);
            }
            if (isset($_GET['advanced-filter']['asalpoliklinikkonsul_id'])) {
                $query->andWhere(['asalpoliklinikkonsul_id' => (int) $_GET['advanced-filter']['asalpoliklinikkonsul_id']]);
                unset($_GET['advanced-filter']['asalpoliklinikkonsul_id']);
            }
            if (isset($_GET['advanced-filter']['ruangan_id'])) {
                $query->andWhere(['ruangan_id' => (int) $_GET['advanced-filter']['ruangan_id']]);
                unset($_GET['advanced-filter']['ruangan_id']);
            }
            if (isset($_GET['advanced-filter']['approval'])) {
                $query->andWhere(['status_approve' => (int) $_GET['advanced-filter']['approval']]);
                unset($_GET['advanced-filter']['approval']);
            }
            if (isset($_GET['advanced-filter']['transaksi_konsul'])) {
                $query->andWhere(['transaksi_konsul' => (int) $_GET['advanced-filter']['transaksi_konsul']]);
                unset($_GET['advanced-filter']['transaksi_konsul']);
            }
            if (isset($_GET['advanced-filter']['no_pendaftaran'])) {
                $query->andWhere(['ILIKE', 'LOWER(no_pendaftaran)', strtolower($_GET['advanced-filter']['no_pendaftaran'])]);
                unset($_GET['advanced-filter']['no_pendaftaran']);
            }
            if (isset($_GET['advanced-filter']['nama_pasien'])) {
                $query->andWhere(['ILIKE', 'LOWER(nama_pasien)', strtolower($_GET['advanced-filter']['nama_pasien'])]);
                unset($_GET['advanced-filter']['nama_pasien']);
            }
            if (isset($_GET['advanced-filter']['no_rekam_medik'])) {
                $query->andWhere(['ILIKE', 'no_rekam_medik', $_GET['advanced-filter']['no_rekam_medik']]);
                unset($_GET['advanced-filter']['no_rekam_medik']);
            }
            if (isset($_GET['advanced-filter']['doktermengkonsul_id'])) {
                $query->andWhere(['doktermengkonsul_id' => $_GET['advanced-filter']['doktermengkonsul_id']]);
                unset($_GET['advanced-filter']['doktermengkonsul_id']);
            }
            if (isset($_GET['advanced-filter']['pegawai_id'])) {
                $query->andWhere(['pegawai_id' => $_GET['advanced-filter']['pegawai_id']]);
                unset($_GET['advanced-filter']['pegawai_id']);
            }
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return $query;
    }

    public function actionSyncExportExcel()
    {
        $model = new InfRencanaKontrol;
        $query = $model::find();

        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $total_data = $this->getData($model, $query)->count();
        $randString = isset($getData['randString']) ? $getData['randString'] : null;

        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'filter' => $getData,
            'totalPerPage' => 10,
            'countData' => $total_data,
            'base_uri' => Yii::$app->docoRest->getBaseUri('pendaftaran'),
            'xOwner' => $xOwner,
            'auth' => $auth
        ], 'import_rencana_kontrol',  'import_rencana_kontrol');

        return [
            'totalPerPage' => $total_data,
            'unique_str' => $randString,
            'countData' => $total_data,
        ];
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;

        $filePath = $request->get('filePath', null);
        if ($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName . '.' . $ext;

            $path = "uploads/" . $filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path . '/' . $model->file;
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
