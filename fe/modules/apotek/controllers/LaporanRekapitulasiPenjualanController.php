<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
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

class LaporanRekapitulasiPenjualanController extends DocoController
{
    public $_title = "Laporan Rekapitulasi Penjualan";
    public $_module = '/apotek/laporan-rekapitulasi-penjualan';
    public $_restApotek;

    public function init(){
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    public function actions() {
        return [
            'index'         => 'Doco\apotek\actions\LaporanRekapitulasiPenjualan\IndexAction',
            'get-data'      => 'Doco\apotek\actions\LaporanRekapitulasiPenjualan\GetDataAction',
            'export-excel'  => 'Doco\apotek\actions\LaporanRekapitulasiPenjualan\ExportExcelAction'
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

    public function actionShowPopup()
    {
        $title = 'Laporan Rekapitulasi Penjualan';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $get = DocoDatatableHelper::convertToRestfulParams($request->get());
        $get['tgl_pelayanan'] = $request->get('tgl_pelayanan', null);
        $get['kode_obat'] = $request->get('kode_obat', null);
        $get['nama_obat'] = $request->get('nama_obat', null);
        Yii::$app->session->setFlash($randString, $get);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restApotek, [
            'url' => "lap-rekapitulasi-penjualan/export-excel-bgprocess",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);

        $fileDownloads = 'Laporan Rekapitulasi Penjualan.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restApotek->get('lap-rekapitulasi-penjualan/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path,true);
    }

}
