<?php

/**
** @author yaya
** service : 
** - Gudang lap-formulir-so-barang v1
**/

namespace Doco\gudang\controllers;

use Yii;
use yii\web\Response;
use yii\filters\AccessControl;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\Html;
use yii\helpers\Url;

class LapFormulirSoBarangController extends DocoController
{
    protected $_title = "Laporan Formulir Stok Opname";
    protected $_module = '/gudang/lap-formulir-so-barang';
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $instalasiId = Yii::$app->docoVars->workspace("instalasi_id");
        $instalasi = [];
        try {
            $response = $this->_restGudang->get('allow/get-instalasi-active/', 
                [
                    'form_params' => [],
                    'query' => []
                ]);
            $response = json_decode($response->getBody(),true);
            $instalasi = isset($response['response']) ? $response['response'] : [];
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        }
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw',1);
        $data = [];
        try {
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");

            $response = $this->_restGudang->get('lap-formulir-so-barang/', 
                [
                    'form_params' => [],
                    'query' => $yiiRestfulParams
                ]);

            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['periode_stok'] = date('d-M-Y',strtotime($value['periode_awal'])) 
                                        . ' <b>s/d</b> ' . date('d-M-Y',strtotime($value['periode_akhir']));
                $value['total_harganetto'] = DocoHelpers::formatNumber($value['total_harganetto']);
                $value['tglformulir'] = date('d-M-Y',strtotime($value['tglformulir']));
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
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
        $result['selected'] = Yii::$app->docoVars->workspace("ruangan_id");

        try {

            $response = $this->_restGudang->get('allow/get-ruangan',[
                'query' => [
                    'instalasi_id' => $parent_label
                ]
            ]);

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

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/laporan-formulir-stokopname-barang.pdf";
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $response = $this->_restGudang->get('lap-formulir-so-barang/export-pdf', [
                'query' => $yiiRestfulParams,
                'save_to' => $path
            ]);

            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/lap-formulir-so-barang.xlsx";
            $response = $this->_restGudang->get('lap-formulir-so-barang/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}