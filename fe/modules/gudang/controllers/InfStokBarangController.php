<?php

/**
* @author Ramdhan Nurrachman
* @edited Yaya
* Pemindahan Service Ke Gudang
* 24-Maret-2018
**/


namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoConstants;

class InfStokBarangController extends DocoController
{
    protected $_title = "Informasi Stok Barang";
    protected $_module = 'gudang/inf-stok-barang/';
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        try {
            $response = $this->_restGudang->get('inf-stok-barang/get-instalasi');
            $body = json_decode($response->getBody(), true);
            $instalasi = $body['response'];
            $ruangan_aktif = [];
            $instalasi_aktif = "";
            $visibility = false;

            $ruangan_aktif = [Yii::$app->docoVars->workspace("ruangan_id") => Yii::$app->docoVars->workspace("ruangan_name")];
            $instalasi_aktif = Yii::$app->docoVars->workspace("instalasi_id");
            $visibility = false;

        } catch (RequestException $e) {
            $instalasi = [];
        }

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        if (!isset($filters['advanced-filter']['instalasi_id'])) {
            $filters['advanced-filter']['instalasi_id'] = Yii::$app->docoVars->workspace("instalasi_id");
            $filters['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        }

        if (!isset($filters['advanced-filter']['periodestok_nama'])) {
            $filters['advanced-filter']['periodestok_nama'] = date('Y-m-d');
        }

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restGudang->get('inf-stok-barang/',[
                'query' => $filters
            ]);
            $body = json_decode($response->getBody(), true);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['periodestok_id']);
                unset($value['periodestok_id']);

                $value['qty_dipesan'] = DocoHelpers::formatNumber($value['qty_dipesan']);
                $value['qty_tersedia'] = DocoHelpers::formatNumber($value['qty_tersedia']);
                $value['qty_stok'] = DocoHelpers::formatNumber($value['qty_stok']);

                $value['tglperiodestok_awal'] = date("j M Y", strtotime($value['tglperiodestok_awal']));
                $value['tglperiodestok_akhir'] = date("j M Y", strtotime($value['tglperiodestok_akhir']));
                $value['barang_kode'] = isset($value['barang_kode']) ? $value['barang_kode'] : "";
                $value['kelompokbarang_nama'] = isset($value['kelompokbarang_nama']) ? $value['kelompokbarang_nama'] : "";

                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
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
        $request = Yii::$app->request;
        $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
        if(!isset($filters['advanced-filter']['instalasi_id'])){
            $filters['advanced-filter']['instalasi_id'] = Yii::$app->docoVars->workspace("instalasi_id");
            $filters['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        }
        if(!isset($filters['advanced-filter']['periodestok_nama'])){
            $filters['advanced-filter']['periodestok_nama'] = date('Y-m-d');
        }
        $path = Yii::getAlias("@download") . "/informasi-stok-dan-ketersedian-barang.xlsx";
        try {
            $response = $this->_restGudang->get('inf-stok-barang/export-excel',[
                'query' => $filters,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e){
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restGudang->get('allow/get-ruangan?instalasi_id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'name' => $value['ruangan_nama']
                ];
            return $result;
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
        $title = 'Download Informasi Stok Barang';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if(!isset($yiiRestfulParams['advanced-filter']['instalasi_id'])){
            $yiiRestfulParams['advanced-filter']['instalasi_id'] = Yii::$app->docoVars->workspace("instalasi_id");
            $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        }
        if(!isset($yiiRestfulParams['advanced-filter']['periodestok_nama'])){
            $yiiRestfulParams['advanced-filter']['periodestok_nama'] = date('Y-m-d');
        }
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restGudang, [
            'url' => "inf-stok-barang/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $date = date('dmY');
        $fileDownloads = 'Informasi Stok Barang '.$date.'.xlsx';
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restGudang->get('inf-stok-barang/download-file-excel', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }    

}
