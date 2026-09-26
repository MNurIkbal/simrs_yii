<?php
/**
 * @author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\kasir\models\JenisNonTunaiForm;

class JenisPembayaranNonTunaiController extends DocoController
{

	protected $_title = "Jenis Pembayaran Non Tunai";
    protected $_module = 'kasir/jenis-pembayaran-non-tunai/';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actions()
    {
        return [
            'get-data-bank' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'master-api/get-data-bank',
                'data_name' => [
                    'nama_bank'
                ],
                'keyField' => 'bank_id'
            ],
        ];
    }

    public function actionIndex()
    {
        $status = $this->getStatusActive();
        $title = $this->_title;
        $module = $this->_module;
        return $this->render('index',get_defined_vars());
    }

    public function actionGetData()
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

        try {
            $response = $this->_restMaster->get('jenis-pembayaran-non-tunai/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['jenisnontunai_id']);
                unset($value['jenisnontunai_id']);
                $value['primary'] = $primaryKey;
                $value['is_active'] = DocoHelpers::isActive($value['is_active']);
                $value['rowNum'] = $no;
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

    public function actionCreate()
    {
        $title = Yii::t('fe', 'Tambah ').$this->_title;
        $model = new JenisNonTunaiForm;
        $status = $this->getStatusActive();
        $model->is_active = true;
        $request = Yii::$app->request;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $nama_bank = '';
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->_restMaster->request('POST', 'jenis-pembayaran-non-tunai/create',
                    [
                        'form_params' => $model->attributes
                    ]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response);
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::response([
                        'response' => [
                            'data' => $errors
                        ]
                    ],422);
            }
        } else {
            return $this->renderPartial('form',get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
       	$title = Yii::t('fe', 'Ubah Data ').$this->_title;
        $model = new JenisNonTunaiForm;
        $status = $this->getStatusActive();
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->_restMaster->put('jenis-pembayaran-non-tunai/update?id='.$id, [
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response);
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {                
            $response = $this->_restMaster->get('jenis-pembayaran-non-tunai/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            $model->is_active = ($model->is_active == false) ? 0 : 1;
            $nama_bank = isset($body['response']['bank']) ? $body['response']['bank']['nama_bank'] : '';

            return $this->renderPartial('form', get_defined_vars());
        }
    }  

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->delete('jenis-pembayaran-non-tunai/delete?id='.$id);
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(), 
                "OK", [
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message, [
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    private function getStatusActive()
    {
        $status = $this->_options['status'];
        if(!empty($status)) {
            foreach ($status as $key => $value) {
                if($key === "") {
                    unset($status[$key]);
                }
            }
        }

        return $status;
    }



}
