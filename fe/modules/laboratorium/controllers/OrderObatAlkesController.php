<?php

namespace Doco\laboratorium\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\laboratorium\models\OrderObatAlkesForm;
use yii\helpers\ArrayHelper;
use app\components\Traits\TindakanPenunjangTrait;
class OrderObatAlkesController extends DocoController
{
    use TindakanPenunjangTrait;
    protected $_title = "Pemakaian Obat / Alkes";
    protected $_module = '/laboratorium/order-obat-alkes';
    protected $_restLab;
    protected $backendUrl;
    protected $serviceRest;
    protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->_restLab = Yii::$app->docoRest->laboratorium;
        $this->backendUrl = 'obat-alkes';
        $this->serviceRest = Yii::$app->docoRest->laboratorium;
    }

    public function actionIndex($id)
    {
        $model = new OrderObatAlkesForm;
        $list_pemeriksaan = $list_pegawai = [];
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restLab->request('GET', 'obat-alkes/get-attributes', [
                'query'=>[
                    'id' => $id,
                    'ruangan_id'=>Yii::$app->docoVars->workspace('ruangan_id')
                ]
            ]);
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

    public function actionAddObat($id)
    {
        $model = new OrderObatAlkesForm;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            $model->stok = $request->post('stok');
            if ($model->validate()) {
                try {
                    $response = $this->_restLab->post('obat-alkes/create', [
                        'form_params' => $model->attributes,
                        'query' => [
                            'id' => $id
                        ]
                    ]);
                    $response = json_decode($response->getBody(),true);
                    // return DocoHelpers::response($response);
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
            $response = $this->_restLab->get('obat-alkes', 
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
                $value['ditagihkan'] = !empty($value['harganetto_oa']) ? '✓' : null;
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
            $response = $this->_restLab->delete('obat-alkes/delete', 
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