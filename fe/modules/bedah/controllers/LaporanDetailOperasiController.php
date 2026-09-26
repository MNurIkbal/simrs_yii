<?php 

namespace Doco\bedah\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\assets\CalenderAssets;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use Doco\bedah\models\JadwalOperasiForm;
use app\components\DHtml;

class LaporanDetailOperasiController extends DocoController
{
    protected $_title = "Laporan Operasi";
    protected $_module = '/bedah/laporan-detail-operasi';
    protected $_restBedah;

    public function init()
    {
        parent::init();
        $this->_restBedah = Yii::$app->docoRest->bedahsentral;
    }

    public function actionIndex()
    {
        $title = DHtml::getTitleMenu();
        $title = !empty($title) ? $title : $this->_title;

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $response = $this->guzzleExec($this->_restBedah, [
            'url' => 'laporan-detail-operasi/index',
            'method' => 'get',
            'payload' => [
                'query' => $payload,
            ]
        ]);
        $response['data'] = isset($response['data']) ? $response['data'] : [];
        $response['recordsTotal'] = isset($response['data']) ? count($response['data']) : 0;
        $response['recordsFiltered'] = isset($response['data']) ? count($response['data']) : 0;
        return $response;
    }

    public function actionShowPopup()
    {
        $request = Yii::$app->request;
        $type = $request->get('type', 1);
        $typeOperasi = ($type == 1) ? 'Detail Operasi' : 'Rekapitulasi Operasi';
        $title = 'Unduh Excel '.$typeOperasi;
        $randString = DocoHelpers::generateRandomString();
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['type'] = $type;
        $payload['typeOperasi'] = $typeOperasi;
        Yii::$app->session->setFlash($randString, $payload);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restBedah, [
            'url' => "laporan-detail-operasi/export-excel",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $date = date('dmY');
        $filename = $request->get('fileName', null);
        $type = $request->get('type', 1);
        $typeOperasi = ($type == 1) ? 'Laporan Detail Operasi' : 'Laporan Rekapitulasi Operasi';
        $fileDownloads = $typeOperasi." ".$date.'.xlsx';
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restBedah->get('laporan-detail-operasi/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionFilters($type = null)
    {
        return $this->guzzleExec($this->_restBedah, [
            'url' => 'laporan-detail-operasi/filters',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }
}