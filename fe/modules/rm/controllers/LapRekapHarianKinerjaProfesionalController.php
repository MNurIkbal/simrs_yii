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

class LapRekapHarianKinerjaProfesionalController extends DocoController
{
    protected $_restRm;
    protected $_title;
    protected $_module = 'lap-rekap-harian-kinerja-profesional';
    const JUMLAH = 'jumlah';

	public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Laporan Rekapitulasi harian Perbandingan Data Kinerja Profesional');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $opts = $this->guzzleExec($this->_restRm, [
            'url' => $this->_module.'/get-options',
            'payload' => [
                'query' =>[]
            ]
        ]);
        $listRuangan = ArrayHelper::map($opts['ruangan'], 'ruangan_id', 'ruangan_nama');
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
            $response = $this->_restRm->get($this->_module.'/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $data = $body['response'];

            array_walk($data, function(&$a) { // kebutuhan sementara untuk filter
                  $a['tgl_sensus'] = null;
                  $a['ruangan_id'] = null;
            });

            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportExcel() 
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") ."/". $this->_title .".xlsx";

            $restRm = $this->_restRm->get($this->_module.'/export-excel?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionShowPopupExcel()
    {
        $title = 'Cetak Laporan Rekapitulasi harian Perbandingan Data Kinerja Profesional';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;

        $url = '/rm/lap-rekap-harian-kinerja-profesional/';
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('@app/views/site/_modalExcel', compact('title', 'randString', 'url'));
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restRm, [
            'url' => "lap-rekap-harian-kinerja-profesional/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Rekapitulasi harian Perbandingan Data Kinerja Profesional.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRm->get('lap-rekap-harian-kinerja-profesional/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

}