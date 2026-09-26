<?php

namespace Doco\radiologi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\radiologi\models\OrderObatAlkesForm;
use yii\helpers\ArrayHelper;
use app\components\Traits\TindakanPenunjangTrait;
use app\components\DocoSelect2Trait;

class OrderObatAlkesController extends DocoController
{
    use TindakanPenunjangTrait;
    use DocoSelect2Trait;
    
    protected $_title = "Pemakaian Obat / Alkes";
    protected $_module = '/radiologi/order-obat-alkes';
    protected $_restRad;
    protected $backendUrl;
    protected $serviceRest;
    protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->_restRad = Yii::$app->docoRest->radiologi;
        $this->backendUrl = 'obat-alkes';
        $this->serviceRest = Yii::$app->docoRest->radiologi;
    }

    public function actionIndex($id, $pelayananId, $tindakanId)
    {
        $model = new OrderObatAlkesForm;
        $list_pemeriksaan = $list_pegawai = [];
        $status = null;
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restRad->request('GET', 'obat-alkes/get-attributes?id=' . $id);
            $response = json_decode($response->getBody(),true);
            $restApi = isset($response['response']) ? $response['response'] : [];
            $info_pasien = isset($restApi['listInfo']) ? $restApi['listInfo'] : [];
            $list_pemeriksaan = ArrayHelper::map($restApi['list_pemeriksaan'],
                            'tindakanpelayanan_id','daftartindakan_nama');
            $list_pegawai = ArrayHelper::map($restApi['list_pegawai'],'pegawai_id','nama_pegawai');
            $status = isset($restApi['status_periksa']) ? $restApi['status_periksa'] : null;
        } catch (RequestException $e) {}
        return $this->render('index', get_defined_vars());
    }

    public function actionAddObat($id, $pelayananId)
    {
        $model = new OrderObatAlkesForm;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $pelayananId = DocoHelpers::decrypt($pelayananId);
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            $model->stok = $request->post('stok');
            if ($model->validate()) {
                try {
                    $response = $this->_restRad->post('obat-alkes/create', [
                        'form_params' => $model->attributes,
                        'query' => [
                            'id' => $id,
                            'pelayananId' => $pelayananId
                        ]
                    ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response, false, $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::response($e->getMessage(), 422);
                } catch (\Exception $e) {
                    return DocoHelpers::response($e->getMessage(), 422);
                }
            } else {
                return DocoHelpers::response($model->errors, 422,$formName);
            }
        }
    }

    public function actionGetData($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $yiiRestfulParams['id'] = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restRad->get('obat-alkes',
                [
                    'query' => $yiiRestfulParams
                ]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['obatalkespasien_id']);
                $value['primary'] = $primaryKey;
                unset($value['obatalkespasien_id']);

                $value['tglpelayanan'] = date('d-M-Y H:i:s',strtotime($value['tglpelayanan']));
                $value['ditagihkan'] = !empty($value['hargajual_oa']) ? '✓' : null;
                $value['rowNum'] = $no;
                $value['aksi'] = '<i class="fa fa-lock"></i>';
                if (empty($value['obatsudahbayar_id'])) {
                    $value['aksi'] = Html::button(
                            "<i class='fa fa-trash'></i>",[
                                'style' => 'margin-right:5px',
                                'class' => 'btn btn-danger btn-xs delete',
                                'style' => 'margin-right:5px; padding-left:10px !important;',
                                'action' => Url::to([$this->_module .'/delete','id' => $primaryKey]),
                            ]
                        );
                }
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

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restRad->delete('obat-alkes/delete',
                [
                    'query' => [
                        'id' => $id
                    ]
                ]);
            $response = json_decode($response->getBody(),422);
            return DocoHelpers::response($response,false);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'text' => $e->getMessage()
            ],422);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'text' => $e->getMessage()
            ],422);
        }
    }
}