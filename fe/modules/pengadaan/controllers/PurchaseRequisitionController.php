<?php

namespace Doco\pengadaan\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\modules\pengadaan\models\PurchaseRequisitionForm;

class PurchaseRequisitionController extends DocoController
{
    public    $_restpengadaan;
    public    $_title = "Purchase Requisition";
    protected $_module = '/pengadaan/purchase-requisition/';
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();
        $this->_restpengadaan = Yii::$app->docoRest->pengadaan;
        $this->_restMaster = Yii::$app->docoRest->master;

    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actions()
    {
        return [
            'edit'                  => 'Doco\pengadaan\actions\PurchaseRequisition\EditAction',
            'create'                => 'Doco\pengadaan\actions\PurchaseRequisition\CreateAction',
            'search-obat'           => 'Doco\pengadaan\actions\PurchaseRequisition\SearchObatAction',
            'get-stok'              => 'Doco\pengadaan\actions\PurchaseRequisition\GetStokAction',
            'get-satuan-konversi'   => 'Doco\pengadaan\actions\PurchaseRequisition\GetSatuanKonversiAction',
            'generate-po-partial'   => 'Doco\pengadaan\actions\PurchaseRequisition\GeneratePoPartialAction',
            'pr-recommendation'     => 'Doco\pengadaan\actions\PurchaseRequisition\PRRecommendationAction',
            'save'                  => 'Doco\pengadaan\actions\PurchaseRequisition\SaveAction',
            'update'                => 'Doco\pengadaan\actions\PurchaseRequisition\UpdateAction',
            'cancel'                => 'Doco\pengadaan\actions\PurchaseRequisition\CancelAction',
            'create-barang'         => 'Doco\pengadaan\actions\PurchaseRequisition\CreateBarangAction',
            'search-item'           => 'Doco\pengadaan\actions\PurchaseRequisition\SearchItemAction',
            'edit-barang'           => 'Doco\pengadaan\actions\PurchaseRequisition\EditBarangAction',
            'get-stock-boundary'    => 'Doco\pengadaan\actions\PurchaseRequisition\GetStockBoundaryAction',
            'recommendation-order'  => 'Doco\pengadaan\actions\PurchaseRequisition\RecommendationOrderAction',
            'list-jenis-obat-alkes' => 'Doco\pengadaan\actions\PurchaseRequisition\ListJenisObatAlkesAction',
            'get-po-outstanding'    => 'Doco\pengadaan\actions\PurchaseRequisition\GetPoOutstandingAction',
            'get-pemakaian'         => 'Doco\pengadaan\actions\PurchaseRequisition\GetPemakaianAction',
            'get-stok-barang' => 'Doco\pengadaan\actions\PurchaseRequisition\GetStokBarangAction',
            'search-item-barang' => 'Doco\pengadaan\actions\PurchaseRequisition\SearchItemBarangAction',
            'ruangan-stok-obat' => 'Doco\pengadaan\actions\PurchaseRequisition\RuanganStokObatAction',
            'list-pemakaian' => 'Doco\pengadaan\actions\PurchaseRequisition\ListPemakaianAction',
            'get-data-list-pemakaian' => 'Doco\pengadaan\actions\PurchaseRequisition\GetDataListPemakaianAction',
        ];
    }

    public function getKonfigFarmasi()
    {
        $get = Yii::$app->docoRest->pengadaan->get('allow/get-konfig-farmasi', [
            'query' => ['column' => 'is_large_unit_pr']
        ]);
        
        $response = json_decode($get->getBody(), true);
        return $response['response']['is_large_unit_pr'] == 'true' ? 1 : 0;
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

    public function getListJenisObatAlkes($is_consignment = false) {
        try {
            $response = Yii::$app->docoRest->master->get('allow/get-list-jenis-obat', [
                'query' => [
                    'is_consignment' => $is_consignment
                ]
            ]);
            $getResponse = json_decode($response->getBody(), true);

            $result = [
                'non_group' => $getResponse['response']['list_jenis_obat'],
                'data_obat' => $getResponse['response']['data_obat'],
                'data_alkes' => $getResponse['response']['data_alkes']
            ];

        } catch (RequestException $e) {
            $result = [
                'non_group' => [],
                'data_obat' => [],
                'data_alkes' => []
            ];
        }

        return $result;
    }

    public function getNarkotikaId() {
        try {
            $response = Yii::$app->docoRest->master->get('allow/get-narkotika-id', []);
            $getResponse = json_decode($response->getBody(), true);
            $result = $getResponse['response']['narkotika_id'];

        } catch (RequestException $e) {
            $result = null;
        }

        return $result;
    }
}
