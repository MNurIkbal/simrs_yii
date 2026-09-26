<?php

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class LaporanLeadTimeResepController extends DocoController
{
    public $_title = "Laporan Lead Time Resep";
    public $_module = 'apotek/laporan-lead-time-resep/';
    public $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actions() {
        return [
            'index'         => 'Doco\apotek\actions\LaporanLeadTimeResep\IndexAction',
            'get-data'      => 'Doco\apotek\actions\LaporanLeadTimeResep\GetDataAction',
            'export-excel'  => 'Doco\apotek\actions\LaporanLeadTimeResep\ExportExcelAction'
        ];
    }

    public function actionShowPopupExcel() {
        $title = Yii::t('fe', 'Cetak Laporan Lead Time Resep');
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advance_filter'] = $request->get('advance_filter');
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSyncExcel() {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restApotek, [
            'url' => 'laporan-lead-time-resep/sync-export-excel',
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel() {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Lead Time Resep.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $this->guzzleExec($this->_restApotek,[
            'url' => 'laporan-lead-time-resep/download-file',
            'method' => 'GET',
            'payload' => [
                'query' => ['no_request' => $filename],
                'save_to' => $path,
            ],
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
    
    public function actionGetDataSerconn()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $payload = $request->get();
        
        $randString = DocoHelpers::generateRandomString();
        
        $yiiRestfulParams['randString'] = $randString;
        $response = $this->_restApotek->get('laporan-lead-time-resep/generate-data-serconn',[
            'query' => [
                'advance_filter' => $payload['advance_filter'],
                'unique_str' => $randString
            ]
        ]);
        $res = json_decode($response->getBody(), true);
        
        $data = ArrayHelper::getValue($res, 'response');
        return $data;
    }
}
