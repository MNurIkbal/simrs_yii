<?php

namespace Doco\pengadaan\controllers;

use Yii;
use yii\web\Response;
use yii\filters\AccessControl;
use app\models\Model;
use yii\widgets\ActiveForm;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\pengadaan\models\PoManualForm;
use app\modules\pengadaan\models\PoManualDetailForm;
use app\modules\pengadaan\models\CustomPoManualDetail;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class PurchaseOrderManualController extends DocoController
{
    public $_title = "Purchase Order (PO)";
    protected $_module = '/pengadaan/purchase-order-manual';
    public $_restPengadaan;
    protected $_restMaster;
    protected $actionPath = "Doco\pengadaan\actions\PurchaseOrderManual";

    public function init()
    {
        parent::init();
        $this->_restPengadaan = Yii::$app->docoRest->pengadaan;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actions() {
        $actions = parent::actions();
        $action = [
            'index'     => $this->actionPath . '\IndexAction',
            'simpan'    => $this->actionPath . '\SimpanAction',
        ];
        $actions = array_merge($actions, $action);
        return $actions;
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

            $response = $this->_restPengadaan->get('allow/get-ruangan',[
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

    public function getRequest($pegawai_id)
    {
        try {
            $request = $this->_restPengadaan->request('GET', 'purchase-order-manual/generate-api', [
                'query' => [
                    'pegawai_id' => $pegawai_id,
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
        } catch (RequestException $e) {
            $attributes = [
                'instalasi' => [],
                'payterm' => [],
                'pajak' => [],
                'mapValue' => [],
                'pegawaiLogin' => null,
            ];
        }

        return $attributes;
    }

    public function actionSearchPegawai()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/list-pegawai',[
                            'query' => [
                                'term' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionSearchSupplier()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = Yii::$app->docoRest->pengadaan->get('allow/list-supplier',[
                            'query' => [
                                'term' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['supplier_id'],
                    'text' => $value['supplier_nama'],
                    'pajak_id' => $value['pajak_id']
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionSearchItem($instalasi_id)
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restPengadaan->get('allow/list-item',[
                'query' => [
                    'term' => $request->get('term'),
                    'instalasi_id' => $instalasi_id,
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                if($instalasi_id == 15) {
                    $response[] = [
                        'id' => $value['barang_id'].'-B',
                        'text' => $value['barang_nama'],
                    ];
                }
                else {
                    $response[] = [
                        'id' => $value['obatalkes_id'].'-O',
                        'text' => $value['obatalkes_nama'],
                        'is_consigment' => $value['is_consigment'] ? $value['is_consigment'] : false,
                    ];
                }
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionGetSatuanKonversiItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        try {
            if ($request->post()) {
                $depdrop_parents = $request->post('depdrop_parents');
                $parent_label = $depdrop_parents[0];
            }

            $exp = explode('-', $parent_label);
            list($item_id, $tipe) = $exp;
            $result = [];
            $result['output'] = [];
            $result['selected'] = '';
            $response = $this->_restMaster->get('allow/list-satuan-konversi-item', [
                'query' => [
                    'item_id' => $item_id,
                    'tipe' => $tipe,
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value)
                if($tipe == 'B') {
                    $result['output'][] = [
                        'id' => $value['satuankonversibrg_id'],
                        // 'name' => '1 '.$value['besar'].' = '.$value['nilai_konversi'].' '.$value['kecil']
                        'name' => $value['besar'],
                        'options' => [
                            'data-satuan_kecil' => $value['kecil'],
                            'data-nilai_konversi' => $value['nilai_konversi'],
                        ] 
                    ];
                }
                else {
                    $result['output'][] = [
                        'id' => $value['satuankonversi_id'],
                        // 'name' => '1 '.$value['besar'].' = '.$value['nilai_konversi'].' '.$value['kecil']
                        'name' => $value['besar'],
                        'options' => [
                            'data-satuan_kecil' => $value['kecil'],
                            'data-nilai_konversi' => $value['nilai_konversi'],
                        ]
                    ];
                }
                
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            $result['output'] = [];
            $result['selected'] = '';
            return $result;
        }
    }

    public function actionCetak($no_pomanual)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $path = Yii::getAlias("@download") . "/purchase-order-manual.pdf";
            $response = $this->_restPengadaan->get('purchase-order-manual/export-pdf?no_pomanual='.$no_pomanual,
            [
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }
}
