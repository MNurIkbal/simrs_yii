<?php

/**
** @author yaya
** service : 
** - Gudang formulir-stok-opname v1
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

class TransaksiFormulirController extends DocoController
{
    protected $_title = "Transaksi Formulir Stok Opname";
    protected $_module = '/gudang/transaksi-formulir';
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
        return $this->render('stok-opname', get_defined_vars());
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
            
            $response = $this->_restGudang->get('formulir-stok-opname/', 
                [
                    'form_params' => [],
                    'query' => $yiiRestfulParams
                ]);

            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
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

    public function actionSave()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            if (isset($get['advanced-filter']['instalasi_id']) 
                    && isset($get['advanced-filter']['ruangan_id']) && isset($get['advanced-filter']['periodestokbarang_id'])) {
                $get['ruangan_id'] = $get['advanced-filter']['ruangan_id'];
                $response = $this->_restGudang->get('formulir-stok-opname/save', 
                    [
                        'form_params' => [],
                        'query' => $get
                    ]);
                $response = json_decode($response->getBody(),true);
                $idParent = $response['response']['id_parent'];
                $response['response']['id_parent'] = DocoHelpers::encrypt($response['response']['id_parent']);
                Yii::$app->cache->set("formulit-stokopname-{$idParent}",$response['response'],3600);
                return DocoHelpers::response($response);
            } else {
                Yii::$app->response->statusCode = 422;
                return [
                    'response' => [
                        'text' => 'Periode, Instalasi dan Ruangan harus di isi',
                        'title' => 'Proses Gagal !'
                    ]
                ];
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionBeforePrint($id)
    {
        $title = 'Print formulir Stok Opname';
        $id = DocoHelpers::decrypt($id);
        $data = Yii::$app->cache->get("formulit-stokopname-{$id}");
        return $this->renderPartial('before-print',get_defined_vars());
    }

    public function actionPrintPdf($id)
    {
        $id = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/transaksi-formulir-stokopname.pdf";
        $data = Yii::$app->cache->get("formulit-stokopname-{$id}");

        try {
            $response = $this->_restGudang->post('formulir-stok-opname/print-formulir-stok-opname', [
                'form_params' => $data,
                'save_to' => $path
            ]);

            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}