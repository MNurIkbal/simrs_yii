<?php

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\JenisObatAlkesForm;
use GuzzleHttp\Exception\RequestException;

class JenisObatAlkesController extends DocoController
{
    protected $_title = "Jenis Obat Alkes";
    protected $_module = 'master/jenis-obat-alkes/';
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


    public function getAttributes()
    {
        try {
            $response = $this->_restMaster->get('jenis-obat-alkes/get-attributes',[]);
            $getResponse = json_decode($response->getBody(), true);
            $res = $getResponse['response'];
            $result = [
                'group_jenisobat' => ArrayHelper::map($res['group_jenisobat'], 'lookup_id', 'lookup_name'),
                'service_group' => $res['service_group'],
                'service_category' => $res['service_category']
            ];
            return $result;
        } catch (Exception $e) {
            $result = [
                'group_jenisobat' => [],
                'service_group' => [],
                'service_category' => []
            ];
        }
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $getAttr = $this->getAttributes();
        $group_jenisobat = $getAttr['group_jenisobat'];
        $service_group = $getAttr['service_group'];
        $service_category = $getAttr['service_category'];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
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
        try {
            $response = $this->_restMaster->get('jenis-obat-alkes',
                [
                    'query' => $yiiRestfulParams
                ]);

            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['jenisobatalkes_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['jenisobatalkes_id']);
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

    public function actionCreateJenisObat()
    {
        $request = Yii::$app->request;
        $model = new JenisObatAlkesForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $getAttr = $this->getAttributes();
        $group_jenisobat = $getAttr['group_jenisobat'];
        $service_group = $getAttr['service_group'];
        $service_category = $getAttr['service_category'];

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->_restMaster->post('jenis-obat-alkes/save', [
                    'form_params' => $model->attributes
                ]);
                $result = json_decode($response->getBody(),true);

                return DocoHelpers::response($result);
            } else {
                $errors = DocoHelpers::parseError($model->errors,'JenisObatAlkesForm');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ],422);
            }
        }
        $model->is_active = 1;
        return $this->renderPartial('form', get_defined_vars());
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->delete('jenis-obat-alkes/delete?id='.$id);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionGetDataSelect2()
    {
        return $this->guzzleExec($this->_restMaster, [
            'url' => 'jenis-obat-alkes/get-data-select2',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }
}