<?php

namespace app\modules\master\components\traits;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\modules\master\models\PencarianPasienForm;

trait DashboardOdooFarmasiTrait
{
	public function actionFarmasiGetData()
	{
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = $cache = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $model = $request->get('model', null);
        try {
            $response = $this->_restGudang->get('integration/get-data?model='.$model.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['sync_id_api']);
                    $value['primary'] = $primaryKey;
                    $disabled = (empty($value['sync_id_api'])) ? false : true;
                    if(isset($value['tipe_rekap'])) {
                        if($value['tipe_rekap'] == 'RESEP_RACIKAN'){
                            $value['tipe_rekap_'] = 'RACIKAN BATAL';
                        }else{
                            $value['tipe_rekap_'] = $value['tipe_rekap'];
                        }
                    }
                    $value['select_item'] = Html::checkbox('select_item', false, [
                        'id' => 'select_item-'.$value['sync_id_api'],
                        'class' => 'select_item',
                        'value' => $value['sync_id_api'],
                    ]);
                    $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=>"/master/dashboard-odoo/farmasi-detail-response?id=".$primaryKey.'&model='.$model,'onclick'=> 'docoHelper.detail(this)']);
                    $value['rowNum'] = $no;
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionPengadaanGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = $cache = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $model = $request->get('model', null);
        try {
            $response = $this->_restPengadaan->get('integration/get-data?model='.$model.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['sync_id_api']);
                    $value['primary'] = $primaryKey;
                    $disabled = (empty($value['sync_id_api'])) ? false : true;
                    $value['select_item'] = Html::checkbox('select_item', false, [
                        'id' => 'select_item-'.$value['sync_id_api'],
                        'class' => 'select_item',
                        'value' => $value['sync_id_api'],
                    ]);
                    $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=>"/master/dashboard-odoo/pengadaan-detail-response?id=".$primaryKey.'&model='.$model,'onclick'=> 'docoHelper.detail(this)']);
                    switch ($model) {
                        case 'purchaseorder':
                            $value['date_order'] = date("d-M-Y H:i:s", strtotime($value['date_order']));
                        break;
                        case 'purchaseorderline':
                            $value['date_planned'] = date("d-M-Y H:i:s", strtotime($value['date_planned']));
                        break;
                        case 'returnpurchaseorder':
                            $value['date_order'] = date("d-M-Y H:i:s", strtotime($value['date_order']));
                        break;
                        case 'returnpurchaseorderline':
                            $value['date_planned'] = date("d-M-Y H:i:s", strtotime($value['date_planned']));
                        break;
                    }
                    $value['rowNum'] = $no;
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;
                return $result;
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

	public function actionFarmasiDetailResponse($id, $model)
	{
		$request = $this->_restGudang->request('GET', 'integration/get-data-transaksi?id='.DocoHelpers::decrypt($id).'&model='.$model);
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];
        if(!empty($attributes)) {
            $response = json_encode($attributes, JSON_PRETTY_PRINT);
        }
        else {
            $response = '<center><h3><strong>Tidak Ada Response</strong><h3></center>';
        }
        
        return $this->renderAjax('_detail', get_defined_vars());
	}

    public function actionPengadaanDetailResponse($id, $model)
    {
        $request = $this->_restPengadaan->request('GET', 'integration/get-data-transaksi?id='.DocoHelpers::decrypt($id).'&model='.$model);
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];
        if(!empty($attributes)) {
            $response = json_encode($attributes, JSON_PRETTY_PRINT);
        }
        else {
            $response = '<center><h3><strong>Tidak Ada Response</strong><h3></center>';
        }
        
        return $this->renderAjax('_detail', get_defined_vars());
    }

    public function actionStockout()
    {
        $request = Yii::$app->request;
        $type = $request->get('type', '');
        $path = ($type == 'picking') ? '_stockout_picking' : '_stockout_move';
        $statusProses = [
            'SUKSES' => 'SUKSES',
            'GAGAL' => 'GAGAL',
            'MENUNGGU PROSES' => 'MENUNGGU PROSES',
            'DALAM PROSES' => 'DALAM PROSES',
        ];
        $tipe = [
            'BMHP' => 'BMHP',
            'RESEP' => 'RESEP',
            'RESEP_RACIKAN' => 'RACIKAN BATAL'
        ];

        return $this->renderAjax($path, [
            'statusProses' => $statusProses,
            'tipe' => $tipe
        ]);
    }

    public function actionStockReturn()
    {
        $request = Yii::$app->request;
        $type = $request->get('type', '');
        $path = ($type == 'picking') ? '_stockreturn_picking' : '_stockreturn_move';
        $statusProses = [
            'SUKSES' => 'SUKSES',
            'GAGAL' => 'GAGAL',
            'MENUNGGU PROSES' => 'MENUNGGU PROSES',
            'DALAM PROSES' => 'DALAM PROSES',
        ];
        $tipe = [
            'BMHP' => 'BMHP',
            'RESEP' => 'RESEP',
            'RESEP_RACIKAN' => 'RACIKAN BATAL'
        ];

        return $this->renderAjax($path, [
            'statusProses' => $statusProses,
            'tipe' => $tipe
        ]);
    }

    public function actionGrn()
    {
        $request = Yii::$app->request;
        $type = $request->get('type', '');

        switch ($type) {
            case 'purchase':
                $path = '_grn_purchase';
                break;
            case 'purchasedetail':
                $path = '_grn_purchasedetail';
                break;
            case 'purchaseretur':
                $path = '_grn_purchaseretur';
                break;
            case 'purchasereturdetail':
                $path = '_grn_purchasereturdetail';
                break;
            
            default:
                $path = '_grn_purchase';
                break;
        }

        $statusProses = [
            'SUKSES' => 'SUKSES',
            'GAGAL' => 'GAGAL',
            'MENUNGGU PROSES' => 'MENUNGGU PROSES',
            'DALAM PROSES' => 'DALAM PROSES',
        ];
        $tipe = [
            'BMHP' => 'BMHP',
            'RESEP' => 'RESEP',
            'RESEP_RACIKAN' => 'RACIKAN BATAL'
        ];

        return $this->renderAjax($path, [
            'statusProses' => $statusProses,
            'tipe' => $tipe
        ]);
    }

    public function actionFarmasiResend()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $response = [];
        try {
            $model = ($request->get('model')) ? $request->get('model') : null;
            $syncIdApi = $post['sync_id_api'];
            $syncIdApi = !is_array($syncIdApi) ? array($syncIdApi) : $syncIdApi;
            $result = $this->_restGudang->post('integration/resync?model='.$model, [
                'form_params' => [
                    'sync_id_api' => $syncIdApi,
                ]
            ]);
            $result = json_decode($result->getBody(),true);
            return DocoHelpers::response($result['response']);
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
            $response['response']['message'] = $e->getMessage();
            return DocoHelpers::response($response, 500);
        }
    }

    public function actionPengadaanResend()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $response = [];
        try {
            $model = ($request->get('model')) ? $request->get('model') : null;
            $syncIdApi = $post['sync_id_api'];
            $syncIdApi = !is_array($syncIdApi) ? array($syncIdApi) : $syncIdApi;
            $result = $this->_restPengadaan->post('integration/resync?model='.$model, [
                'form_params' => [
                    'sync_id_api' => $syncIdApi,
                ]
            ]);
            $result = json_decode($result->getBody(),true);
            return DocoHelpers::response($result['response']);
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
            $response['response']['message'] = $e->getMessage();
            return DocoHelpers::response($response, 500);
        }
    }
}