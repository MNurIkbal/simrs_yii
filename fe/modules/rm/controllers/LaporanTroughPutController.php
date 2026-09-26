<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class LaporanTroughPutController extends DocoController
{
    protected $allowAction = ['*']; // remove when done
    protected $_module = '/rm/laporan/';
    public $_title;
    protected $_restRm;
    
    public function init()
    {
        parent::init();
        $this->_title = 'Laporan Trough Put';
        $this->_restRm = Yii::$app->docoRest->rm;
    }
    
    public function actionIndex()
    {
        $title = $this->_title;

        return $this->render('index', get_defined_vars());
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $month = $request->get('bulan');
        $year = $request->get('tahun');

        $url = 'laporan-trough-put/export-excel?month=' . $month . '&year=' . $year;
        $path = Yii::getAlias("@download") . "/laporan_thruput.xlsx";

        try {
            $response = Yii::$app->docoRest->rm->get($url, [
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionShowPopupExcel()
    {
        $title = 'Cetak Laporan Trough Put';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        $yiiRestfulParams['randString'] = $randString;
        $yiiRestfulParams['month'] = $request->get('bulan');
        $yiiRestfulParams['year'] = $request->get('tahun');

        $url = '/rm/laporan-trough-put/';
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('@app/views/site/_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restRm, [
            'url' => "laporan-trough-put/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Troughput.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRm->get('laporan-trough-put/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

}
