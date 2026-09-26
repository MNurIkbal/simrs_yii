<?php

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class LapSensusHarianPasienRajalController extends DocoController
{
    protected $_restRm;
    protected $_title;
    protected $_module = '/rm/lap-sensus-harian-pasien-rajal/';
    const JK = 'jenis_kelamin';
    const POLIKLINIK = 722;
    const PMED = 725;
    const IGD = 724;
    const MCU = 723;
    const TGL_PENDAFTARAN = 'tgl_pendaftaran';
    const JML_HEADER_DAN_SUBTOTAL = 9;

	public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Laporan Sensus Harian Rawat Jalan');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $jk = self::JK;
        $api = $this->_restRm->get('lap-sensus-harian-pasien-rajal/generate-api');
        $api = json_decode($api->getBody(), True);
        $api = $api['response'];
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams['randString'] = $randString;
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        
        $api['tgl'] = count($api['columns']) - 1;
        $api['jenis'] = count($api['columns']) - 2;
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);

        $data = [];
        $tmp = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restRm->get('lap-sensus-harian-pasien-rajal/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $body = $body['response'];
            $result['data'] = $body['data'];
            $result['recordsTotal'] = count($body['data']) - self::JML_HEADER_DAN_SUBTOTAL;
            $result['recordsFiltered'] = count($body['data']) - self::JML_HEADER_DAN_SUBTOTAL;
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter'][self::TGL_PENDAFTARAN])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter'][self::TGL_PENDAFTARAN]);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d', strtotime($tgl_awal));
            $tgl_akhir_format = date('Y-m-d', strtotime($tgl_akhir));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter'][self::TGL_PENDAFTARAN]);
        }
        $path = Yii::getAlias("@download") . "/lap-sensus-harian-pasien-rawat-jalan.pdf";
        try {
            $response = $this->_restRm->get('lap-sensus-harian-pasien-rajal/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return $e->getMessage();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            return $e->getMessage();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/lap-sensus-harian-pasien-rawat-jalan.xlsx";
            $response = $this->_restRm->get('lap-sensus-harian-pasien-rajal/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataSerconn($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $payload = $request->get();
        /* temporary solution to avoid online report */
        // if ($payload['jenis_laporan'] != DocoConstants::J_P_L) {
        //     return [
        //         'status' => false,
        //         'data' => $this->renderAjax('@app/views/site/_underConstruction')
        //     ];
        // }
        $response = $this->_restRm->get('lap-sensus-harian-pasien-rajal/generate-data-serconn',[
            'query' => [
                'tgl_pendaftaran' => $payload['tgl_pendaftaran'],
                'jenis_laporan' => $payload['jenis_laporan'],
                'unique_str' => $randString
            ]
        ]);
        $res = json_decode($response->getBody(), true);
        $data = ArrayHelper::getValue($res, 'response');
        return $data;
    }

    public function actionShowPopupExcel()
    {
        $title = 'Download Excel Laporan Sensus Harian Rawat Jalan';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = [
            'tgl_pendaftaran' => $request->get('tgl_pendaftaran'),
            'jenis_laporan' => $request->get('jenis_laporan')
        ];
        $yiiRestfulParams['randString'] = $randString;
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restRm, [
            'url' => "lap-sensus-harian-pasien-rajal/sync-export-excel-rabbitmq",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'LAP_SENSUS_HARIAN_PASIEN_RAWAT_JALAN.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRm->get('lap-sensus-harian-pasien-rajal/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
}
