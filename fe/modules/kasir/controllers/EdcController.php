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
use app\modules\kasir\models\EdcForm;

class EdcController extends DocoController
{

	protected $_title = "Mesin EDC";
    protected $_module = 'kasir/edc/';
    protected $_restKasir;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir;
    }

    public function actionIndex()
    {
        $status = $this->getStatusActive();
        $bank = $this->getDataBank();
        $title = $this->_title;
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
            $response = $this->_restKasir->get('edc/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['edclist_id']);
                unset($value['edclist_id']);
                $value['primary'] = $primaryKey;
                $value['is_active'] = DocoHelpers::isActive($value['is_active']);
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['count'];
            $result['recordsFiltered'] = $body['response']['count'];
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
        $model = new EdcForm;
        $status = $this->getStatusActive();
        $bank = $this->getDataBank();
        $model->is_active = true;
        $request = Yii::$app->request;
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->_restKasir->request('POST', 'edc/create',[
                                    'form_params' => $model->attributes
                            ]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response);
            } else {
                $errors = DocoHelpers::parseError($model->errors,'EdcForm');
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
        $model = new EdcForm;
        $status = $this->getStatusActive();
        $bank = $this->getDataBank();
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->_restKasir->put('edc/update?id='.$id, [
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response);
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {                
            $response = $this->_restKasir->get('edc/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            $model->is_active = ($model->is_active == false) ? 0 : 1;
            return $this->renderPartial('form', get_defined_vars());
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

    private function getDataBank()
    {
        $response = $this->_restKasir->get('allow/get-data-bank');
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        
        return $attributes;
    }
}
