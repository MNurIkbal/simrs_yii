<?php

/**
 * @author Randy Vianda Putra
 * @todo Konfigurasi Farmasi
 * @copyright 23 April 2018 aweutist
 */

namespace Doco\gudang\controllers;

use app\components\DHtml;
use Yii;
use yii\web\Response;
use yii\filters\AccessControl;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\gudang\models\KonfigForm;
use yii\helpers\ArrayHelper;

class KonfigFarmasiController extends DocoController
{
    protected $_title = "Konfigurasi Farmasi";
    protected $_module = '/gudang/konfig-farmasi';
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    public function actionIndex()
    {
        $title = DHtml::getTitleMenu();
        $model = new KonfigForm;
        $docoVars = Yii::$app->docoVars;
        $user_id = DocoHelpers::encrypt($docoVars->user("pegawai_id"));
        $current = [];

        try {
            $metodeAntrian = $metodeHarga = [];
            $lookupList = $this->guzzleExec(Yii::$app->docoRest->gudang, [
                'url' => 'konfig-farmasi/get-all-lookup',
                'method' => 'get'
            ]);

            $metodeAntrian = ArrayHelper::getValue($lookupList, 'lookup_antrian_obat', []);
            $metodeAntrian = ArrayHelper::map($metodeAntrian, 'lookup_value', 'lookup_value');
            $metodeHarga = ArrayHelper::getValue($lookupList, 'lookup_harga', []);
            $metodeHarga = ArrayHelper::map($metodeHarga, 'lookup_value', 'lookup_value');
            $kronisLimit = ArrayHelper::getValue($lookupList, 'kronis_limit');
            
            $current = $this->guzzleExec(Yii::$app->docoRest->gudang, [
                'url' => 'konfig-farmasi/get-konfig',
                'method' => 'get',
                'payload' => [
                    'query' => [ 'id' => 1 ]
                ]
            ]);
        } catch (\Exception $e) {
            $current = $metodeAntrian = $metodeHarga = $rumusHNA = [];
            $kronisLimit = null;
        }

        return $this->render('index', get_defined_vars());
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $model = new KonfigForm;
        $model->load($request->post());

        try {
            if ($model->validate()) {
                try {
                    $response = $this->_restGudang->post("konfig-farmasi/save?", [
                        "form_params" => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(), true);

                    return DocoHelpers::response($body);
                } catch (RequestException $e) {
                    return DocoHelpers::response($e->getMessage());
                }
            }else{
                return DocoHelpers::response($model->errors, 422, "KonfigForm");
            }
        } catch (RequestException $e) {
            return DocoHelpers::response($e->getMessage());
        }
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restGudang->request('get', 'konfig-farmasi/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['konfigfarmasi_id']);
                $value['primary'] = $primaryKey;
                unset($value['konfigfarmasi_id']);
                $value['rowNum'] = $no;
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionCreate()
    {
        $title = $this->_title;
        $model = new KonfigForm;
        $request = Yii::$app->request;
        $post = $request->post();
        if ($post) {
            $model->load($post);
            if ($model->validate()) {
                $response = $this->_restGudang->request('POST', 'konfig-farmasi/save',[
                    'form_params' => $post,
                ]);
                $response = json_decode($response->getBody(), true);
            } else {
                $response = $model->errors;
            }

            return DocoHelpers::response($response, 422, 'KonfigForm');
        } else {
            $response = $this->_restGudang->get('konfig-farmasi/get-all-lookup');
            $body = json_decode($response->getBody(), true);
            $antrian_obat = $harga = [];
            if (!empty($body['response']['lookup_harga'])) {
                $harga = $body['response']['lookup_harga'];
                $harga = ArrayHelper::map($harga, 'lookup_value', 'lookup_value');
            }
            if (!empty($body['response']['lookup_antrian_obat'])) {
                $antrian_obat = $body['response']['lookup_antrian_obat'];
                $antrian_obat = ArrayHelper::map($antrian_obat, 'lookup_value', 'lookup_value');
            }
        }

        return $this->render('form', get_defined_vars());
    }

    public function actionView($id)
    {
        $title = 'Informasi ' . $this->_title;
        $konfig_id = DocoHelpers::decrypt($id);
        $model = new KonfigForm;
        $response = $this->_restGudang->get('konfig-farmasi/get-konfig?id=' . $konfig_id);
        $body = json_decode($response->getBody(), true);
        $data = $body['response'];
        $responseLookup = $this->_restGudang->get('konfig-farmasi/get-all-lookup');
        $bodyLookup = json_decode($responseLookup->getBody(), true);
        $antrian_obat = $harga = [];
        if (!empty($bodyLookup['response']['lookup_harga'])) {
            $harga = $bodyLookup['response']['lookup_harga'];
            $harga = ArrayHelper::map($harga, 'lookup_value', 'lookup_value');
        }
        if (!empty($bodyLookup['response']['lookup_antrian_obat'])) {
            $antrian_obat = $bodyLookup['response']['lookup_antrian_obat'];
            $antrian_obat = ArrayHelper::map($antrian_obat, 'lookup_value', 'lookup_value');
        }
        // dump($data);exit;
        $model->attributes = $data;
        return $this->render('view', get_defined_vars());
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restGudang->request('DELETE', 'konfig-farmasi/delete',[
                'query' => ['id' => $id ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetPenjamin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $payload = $request->get();
        $term = $request->get('q');
        try {
            // if (!is_null($term)) {
                $response = $this->_restGudang->get('allow/search-cara-bayar-penjamin?', ['form_params' => [],
                    'query' => [
                        'term' => $term,
                        'page' => @$payload['page']
                    ],
                ]);
                $body = json_decode($response->getBody(), true);

                $data = $body['response']['data'];


                $list = [];
                foreach ($data as $row_item) {
                    $item = [
                        "id" => $row_item['penjamin_id'],
                        "text" => $row_item['carabayar_nama']. " - " .$row_item['penjamin_nama'],
                        "data" => $row_item
                    ];
                    $list[] = $item;
                }

                return [
                    'list' => $list,
                    'data' => $data,
                    'more' => isset($body['response']['data']) ? count($body['response']['data']) >= 10 : false,
                    'payload' => $request->get(),
                ];
            // }else{
            //     return [
            //         'list' => ['id'=>28,'text'=>'asd'],
            //         'data' => [],
            //         'more' => false,
            //         'payload' => $request->get(),
            //     ];
            // }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}

