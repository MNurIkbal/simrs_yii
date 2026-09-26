<?php
// Author : Ramdhan Nurrachman

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoConstants;
use app\components\DHtml;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\DokumenForm;

class DokumenController extends DocoController
{
    protected $allowAction = [ '*' ];
    protected $_title = "Master Dokumen Upload";
    protected $_module = 'master/dokumen/';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        $title = DHtml::getTitleMenu();
        $title = !empty($title) ? $title : $this->_title;
        $status = $this->_status; $options = $this->_options;
        $jenisDokumen = $this->getJenisDokumen();
        if($jenisDokumen) {
            $jenisDokumen = ArrayHelper::map($jenisDokumen, 'lookup_id', 'lookup_name');
        }
        
        return $this->render('index', get_defined_vars());
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
            $response = $this->_restMaster->get('dokumen/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $dokumenId = ArrayHelper::getValue($value, 'dokumen_id');
                $primaryKey = DocoHelpers::encrypt($dokumenId);
                unset($dokumenId);

                $isActive = ArrayHelper::getValue($value, 'is_active', true);
                $value['is_active'] = $isActive ? "Aktif" : "Tidak Aktif";
                $value['primary'] = $primaryKey;
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
           
            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $title = DHtml::getTitleMenu();
        $title = !empty($title) ? $title : $this->_title;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $title);
        $model = new DokumenForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $jenisDokumen = $this->getJenisDokumen();
        if($jenisDokumen) {
            $jenisDokumen = ArrayHelper::map($jenisDokumen, 'lookup_id', 'lookup_name');
        }
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->_restMaster->put('dokumen/simpan', [
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response,false, $formName);
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ],422);
            }
        } else {
            $model->is_active = 1;
            return $this->renderAjax('form', get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        $title = DHtml::getTitleMenu();
        $title = !empty($title) ? $title : $this->_title;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $title);
        $request = Yii::$app->request;
        $status = $this->_status; 
        $options = $this->_options;
        $jenisDokumen = $this->getJenisDokumen();
        if($jenisDokumen) {
            $jenisDokumen = ArrayHelper::map($jenisDokumen, 'lookup_id', 'lookup_name');
        }
        
        $model = new DokumenForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->_restMaster->put('dokumen/simpan?id='.$id, [
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response,false, $formName);
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ],422);
            }
        } else {
            $response = $this->_restMaster->get('dokumen/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            $isActive = ArrayHelper::getValue($attributes, 'is_active', true);
            $model->is_active = $isActive ? 1 : 0;
            return $this->renderAjax('form', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->delete('dokumen/delete?id='.$id);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response,false);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message, [
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    private function getJenisDokumen()
    {
        return $this->guzzleExec($this->_restMaster, [
			'url' => 'dokumen/get-jenis-dokumen',
			'method' => 'get',
			'payload' => []
		]);
    }
}
