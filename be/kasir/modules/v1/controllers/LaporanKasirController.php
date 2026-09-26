<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use yii\helpers\ArrayHelper;
use Doco\rabbitmq\RabbitBgProcess;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;
use app\modules\v1\models\KonfigLaporan;
use yii\data\ActiveDataProvider;
use Doco\Services\Cache;

class LaporanKasirController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPasienriView';

    public function verbs()
    {
        $verbs = parent::verbs();
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

    public function actionListKonfigLaporan()
    {
        $request = Yii::$app->request;
        $key_laporan = $request->get('key_laporan', null);

        $laporan = KonfigLaporan::find()->andWhere([
            'key_laporan' => $key_laporan,
            'is_active' => true,
        ])->orderBy(['konfiglaporan_id' => SORT_ASC])->asArray()->all();
        return $laporan;
    }

    public function actionExportExcelBgprocess()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        $range_tanggal = $request->get('range_tanggal');
        $jenis_laporan = $request->get('jenis_laporan');
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:00');
        
        if ($range_tanggal) {
            $explode = explode(" - ", $range_tanggal);
            if (count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
            }
        }

        $source = KonfigLaporan::find()->where(['jenis_laporan' => $jenis_laporan])->asArray()->all();
        $source = isset($source[0]) ? $source[0] : [];
        $source_view = ArrayHelper::getValue($source, 'source_view');
        $fieldWhere = ArrayHelper::getValue($source, 'filter');
        $footer = ArrayHelper::getValue($source, 'footer');
        $title = ArrayHelper::getValue($source, 'jenis_laporan');

        if (!empty(ArrayHelper::getValue($source, 'additional_data', []))) {
            $additional_data = json_decode(ArrayHelper::getValue($source, 'additional_data', []), true);
            if (isset($additional_data['is_function']) && $additional_data['is_function']) {
                $source_view = "{$source_view}(:dateStart, :dateEnd)";
                $query = $this->dbConnection()->createCommand("SELECT COUNT(*) FROM {$source_view}");
            } else {
                $query = $this->dbConnection()->createCommand("SELECT COUNT(*) FROM {$source_view} WHERE {$fieldWhere} BETWEEN :dateStart AND :dateEnd");
            }
        } else {
            $query = $this->dbConnection()->createCommand("SELECT COUNT(*) FROM {$source_view} WHERE {$fieldWhere} BETWEEN :dateStart AND :dateEnd");
        }

        $query->bindValue(':dateStart', $start);
        $query->bindValue(':dateEnd', $end);
        $dataCount = $query->queryScalar();

        $countData = $totalPerPage = $dataCount;
        $headerExcel = [
            'Tanggal' => $request->get('range_tanggal'),
        ];

        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'filter' => $request->get(),
            'totalPerPage' => $countData,
            'headerExcel' => $headerExcel,
            'footerExcel' => $footer,
            'countData' => $countData, 
            'title' => $title,
            'sendToUrl' => 'laporan-kasir/drop-file',
            'base_uri' => Yii::$app->docoRest->getBaseUri('kasir'),
        ], 'laporan_kasir');

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
        if ($request->isPost) {
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
        $fileName = $rootPath.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }

    private function dbConnection()
    {
        return !empty(Yii::$app->dbslave->username) ? Yii::$app->dbslave : Yii::$app->db;
    }
}
