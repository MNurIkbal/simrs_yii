<?php

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Formulir So
 * @copyright 17 January 2018 aweutist
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
use Doco\apotek\models\TransaksiForm;

class TransaksiFormulirController extends DocoController
{

    protected $_title = "Transaksi Formulir Stok Opname";
    protected $_module = '/apotek/transaksi-formulir';
    protected $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $instalasiId = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $instalasi = [];
        $data_periode = [];
        $list_rak = [];
        try {
            $response = $this->_restApotek->get('allow/get-instalasi/',
                [
                    'form_params' => [],
                    'query' => []
                ]);
            
            $response = json_decode($response->getBody(),true);
            $data_periode = isset($response['response']['periode']) ? $response['response']['periode'] : [];
            $instalasi = isset($response['response']['instalasi']) ? $response['response']['instalasi'] : [];

            $list_data = $this->getListRak(1);
            $list_rak = ArrayHelper::map($list_data, 'rakobat_id', 'rakobat_nama');
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        }

        return $this->render('stok-opname', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if(!isset($yiiRestfulParams['advanced-filter']['ruangan_id'])){
            $yiiRestfulParams['advanced-filter']['ruangan_id'] = $ruangan_id;
        }

        if(!isset($yiiRestfulParams['advanced-filter']['instalasi_id'])){
            $yiiRestfulParams['advanced-filter']['instalasi_id'] = $instalasi_id;
        }

        if(!isset($yiiRestfulParams['advanced-filter']['rakobat_id'])){
            $yiiRestfulParams['advanced-filter']['rakobat_id'] = null;
        }
        if(!isset($yiiRestfulParams['advanced-filter']['jenisobatalkes_nama'])){
            $yiiRestfulParams['advanced-filter']['jenisobatalkes_nama'] = null;
        }

        if(isset($yiiRestfulParams['advanced-filter']['rakobat_id']) && $yiiRestfulParams['advanced-filter']['rakobat_id'] == 'Loading ...') {
            $yiiRestfulParams['advanced-filter']['rakobat_id'] = null;
        }

        $list_data = $this->getListRak(null);
        $list_rak = ArrayHelper::map($list_data, 'rakobat_id', 'rakobat_nama');

        $draw = $request->get('draw',1);
        $data = [];

        try {
            $response = $this->_restApotek->get('formulir-stok-opname/', [
                            'form_params' => [],
                            'query' => $yiiRestfulParams
                        ]);

            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['stok_sistem'] = DocoHelpers::formatNumber($value['stok_sistem'], true, false, 3);
                $value['rakobat_nama'] = !is_null($value['rakobat_nama']) ? $value['rakobat_nama'] : 'Tanpa Rak';
                $value['laci'] = !is_null($value['laci']) ? $value['laci'] : 'Tanpa Rak';
                $data[$key] = $value;
            }
            $result['data'] = ($data) ? $data : '' ;
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
            $response = $this->_restApotek->get('allow/get-ruangan',[
                'query' => [
                    'instalasi_id' => $parent_label,
                    'state' => 0,
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

    public function actionGetRakobat()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        $type = 1;

        $result = [];
        $result['output'] = [];
        $result['selected'] = Yii::$app->docoVars->workspace("rakobat_id");

        try {
            $response = $this->_restApotek->get('allow/get-rakobat',[
                'query' => [
                    'type' => $type,
                    'ruangan_id' => $parent_label
                ]
            ]);

            $body = json_decode($response->getBody(), True);
 
            if(!empty($body['response']['data'])){
                foreach ($body['response']['data'] as $value){
                    $result['output'][] = [
                        'id' => $value['rakobat_id'],
                        'name' => $value['rakobat_nama']
                    ];
                }
            }
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetJenisObat()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        $type = 1;

        $result = [];
        $result['output'] = [];
        $result['selected'] = Yii::$app->docoVars->workspace("jenisobatalkes_nama");

        try {
            $response = $this->_restApotek->get('allow/get-jenis-obat-alkes',[
                'query' => [
                    'type' => $type,
                    'ruangan_id' => $parent_label
                ]
            ]);

            $body = json_decode($response->getBody(), True);
 
            if(!empty($body['response']['data'])){
                foreach ($body['response']['data'] as $value){
                    $result['output'][] = [
                        'id' => $value['jenisobatalkes_nama'],
                        'name' => $value['jenisobatalkes_nama']
                    ];
                }
            }
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
        $post = $request->post();
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        if(!isset($filter['advanced-filter']['ruangan_id'])){
            $filter['advanced-filter']['ruangan_id'] = $ruangan_id;
        }

        if(!isset($filter['advanced-filter']['instalasi_id'])){
            $filter['advanced-filter']['instalasi_id'] = $instalasi_id;
        }

        try {
            $periodestokobat_id = empty($post['periodestokobat_id']) ? null : $post['periodestokobat_id'];
            $instalasi_id = empty($post['instalasi_id']) ? null : $post['instalasi_id'];
            $ruangan_id = empty($post['ruangan_id']) ? null : $post['ruangan_id'];
            $totaldata = empty($post['totaldata']) ? 0 : $post['totaldata'];
            $order = empty($filter['order']) ? null : $filter['order'];

            $where['instalasi_id'] = $instalasi_id;
            $where['ruangan_id'] = $ruangan_id;
            $where['totaldata'] = $totaldata;
            $where['order'] = $order;

            $filter['ruangan_id'] = $filter['advanced-filter']['ruangan_id'];

            if (!empty($instalasi_id) && !empty($ruangan_id)) {
                $response = $this->_restApotek->get('formulir-stok-opname/save',
                    [
                        'form_params' => [
                            'kondisi' => $where
                        ],
                        'query' => $filter
                    ]);
                $response = json_decode($response->getBody(),true);
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
            Yii::$app->response->statusCode = 422;
            return ['response' => [
                                'title' => 'Terjadi Kesalahan',
                                'text' => $e->getMessage(),
                                'status' => 422
                            ]
                        ];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 422;
            return ['response' => [
                                'title' => 'Terjadi Kesalahan',
                                'text' => $e->getMessage(),
                                'status' => 422
                            ]
                        ];
        }
    }

    public function actionBeforePrint($id)
    {
        $title = 'Print formulir Stok Opname';
        $id = DocoHelpers::decrypt($id);
        $data = Yii::$app->cache->get("formulit-stokopname-{$id}");
        $data['periode'] = ($data['periode']) ? $data['periode'] : date('d-m-Y',strtotime($data['data'][0]['tglperiodeposting_awal']))." s/d ".date('d-m-Y',strtotime($data['data'][0]['tglperiodeposting_akhir'])) ;
        return $this->renderPartial('before-print',get_defined_vars());
    }

    public function actionPrintPdf($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/transaksi-formulir-stokopname.pdf";
        $data = Yii::$app->cache->get("formulit-stokopname-{$id}");
        $data['no_formulir'] = $request->get('formulir');
        $data['ruangan_nama'] = Yii::$app->docoVars->workspace('ruangan_name');
        $data['periode'] = 'tester';

        try {
            $response = $this->_restApotek->post('formulir-stok-opname/print-formulir-stok-opname?id='.$id, [
                'form_params' => $data,
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            // return DocoHelpers::response($body);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response($e->getMessage());
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function getListRak($type)
    {
        try{
            $request = Yii::$app->request;
            $get = $this->_restApotek->get('allow/get-rakobat', [
                'query' => [
                    'type' => $type
                ]
            ]);
            $body = json_decode($get->getbody(), true);
            $response = $body['response']['data'];

            return $response;
        } catch(RequestException $e){
            return [];
        } catch(\Exception $e){
            return [];
        }
    }
}
