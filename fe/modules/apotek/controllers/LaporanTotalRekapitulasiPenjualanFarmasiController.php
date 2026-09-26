<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class LaporanTotalRekapitulasiPenjualanFarmasiController extends DocoController
{
    public $_title = "Laporan Total Rekapitulasi Penjualan Farmasi";
    public $_module = '/apotek/laporan-total-rekapitulasi-penjualan-farmasi';
    public $_restApotek;
    protected $allowAction = ['*'];

    public function init(){
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    public function actions() {
        return [
            'index'         => 'Doco\apotek\actions\LaporanTotalRekapitulasiPenjualanFarmasi\IndexAction',
            'get-data'      => 'Doco\apotek\actions\LaporanTotalRekapitulasiPenjualanFarmasi\GetDataAction',
            'export-excel'  => 'Doco\apotek\actions\LaporanTotalRekapitulasiPenjualanFarmasi\ExportExcelAction'
        ];
    }

    public function checkAksesMenu()
    {
        $m = Yii::$app->controller->module->id;
        $c = Yii::$app->controller->id;
        $controller = "/$m/$c";
        $route = "export-excel";

        $menu = Yii::$app->session->get("akses_menu");
        if(!empty($menu)) {
            if(!empty($menu[$controller])) {
                if(in_array($route, $menu[$controller])) {
                    return true;
                }
            }
        }
        return false;
    }

    public function actionShowPopupExcel()
    {
        $title = 'Cetak Laporan Total Rekapitulasi Penjualan Farmasi';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restApotek, [
            'url' => "lap-total-rekapitulasi-penjualan-farmasi/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Total Rekapitulasi Penjualan Farmasi.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restApotek->get('lap-total-rekapitulasi-penjualan-farmasi/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }    
}
