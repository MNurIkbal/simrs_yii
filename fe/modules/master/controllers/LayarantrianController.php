<?php
/**
 * @author: arief saputra
 * @description: master layar antrian
**/

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\LayarantrianForm;
use GuzzleHttp\Exception\RequestException;

class LayarantrianController extends DocoController
{
    protected $_title = 'Layar Antrian';
    protected $_module = 'layarantrian/';
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
        $status = $this->_status;
        $title = Yii::t('fe', $this->_title);

        return $this->render('index', get_defined_vars());
    }

    public function actionPengambilanAntrian()
    {
        $status = $this->_status;
        $title = Yii::t('fe', 'Master Pengambilan Antrian');

        return $this->render('pengambilan-antrian', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();

            $response = $this->_restMaster->request('POST', 'layarantrian/',[
                            'form_params' => $post
                        ]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->post('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['layarantrian_id']);
                $value['primary'] = $primaryKey;
                unset($value['layarantrian_id']);
                
                /*$value['aksi'] = Html::button('<i class="fa fa-pencil" aria-hidden="true"></i>',[
                    'class' => 'btn btn-success btn-xs',
                    'style' => 'margin-right:5px',
                    'data-toggle' => 'modal',
                    'action' => Url::to([$this->_module .'update-pengambilan-antrian','id' => $primaryKey]),
                    'data-target' => '#modal_backdrop',
                    'data-popup' => "tooltip",
                    'title' => "Update"
                ]);
                $value['aksi'] .= Html::button('<i class="fa fa-trash" aria-hidden="true"></i>',[
                    'class' => 'btn btn-danger btn-xs delete',
                    'style' => 'margin-right:5px',
                    'data-popup' => "tooltip",
                    'action' => Url::to([$this->_module .'delete-pengambilan-antrian','id' => $primaryKey]),
                    'title' => "Hapus"
                ]);*/

                $value['status'] = DocoHelpers::isActive2($value['is_active'], [$primaryKey, "master/".$this->_module]);
                $value['rowNum'] = $no;
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->post('draw'),
                'recordsTotal' => $body['response']['count'],
                'recordsFiltered' => $body['response']['count']
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = 'Lihat Data';
        $model = new LayarantrianForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restMaster->get('layarantrian/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }

    public function actionCreate()
    {
        try {
            $title = 'Tambah Layar Antrian';
            $model = new LayarantrianForm;
            $status = $this->_status;
            $request = Yii::$app->request;

            $responseRequest = $this->_restMaster->get('layarantrian/list-type-screen');
            $body = json_decode($responseRequest->getBody(),TRUE);
            $ddlTypeScreen = $body['response'];

            $responseRequest = $this->_restMaster->get('layarantrian/list-function-screen');
            $body = json_decode($responseRequest->getBody(),TRUE);
            $ddlFunctionScreen = $body['response'];

            if ($request->post()) {
                die('masuk sini if');
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'layarantrian/create',[
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,true);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'layarantrianForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                return $this->renderPartial('form',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionCreatePengambilanAntrian()
    {
        try {
            $title = 'Tambah Pengambilan Antrian';
            $model = new LayarantrianForm;
            $status = $this->_status;
            $request = Yii::$app->request;

            $responseRequest = $this->_restMaster->get('layarantrian/list-type-screen');
            $body = json_decode($responseRequest->getBody(),TRUE);
            $ddlTypeScreen = $body['response'];

            $responseRequest = $this->_restMaster->get('layarantrian/list-function-screen');
            $body = json_decode($responseRequest->getBody(),TRUE);
            $ddlFunctionScreen = $body['response'];

            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'layarantrian/create',[
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,true);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'layarantrianForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                return $this->renderPartial('form-pengambilan-antrian',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        $title = 'Ubah Data';
        $model = new LayarantrianForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        $status = $this->_status;

        $responseRequest = $this->_restMaster->get('layarantrian/list-type-screen');
        $body = json_decode($responseRequest->getBody(),TRUE);
        $ddlTypeScreen = $body['response'];

        $responseRequest = $this->_restMaster->get('layarantrian/list-function-screen');
        $body = json_decode($responseRequest->getBody(),TRUE);
        $ddlFunctionScreen = $body['response'];
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('layarantrian/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);

                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restMaster->get('layarantrian/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionUpdatePengambilanAntrian($id = null)
    {
        $request = Yii::$app->request;
        $title = 'Ubah Data';
        $model = new LayarantrianForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        $status = $this->_status;

        $responseRequest = $this->_restMaster->get('layarantrian/list-type-screen');
        $body = json_decode($responseRequest->getBody(),TRUE);
        $ddlTypeScreen = $body['response'];

        $responseRequest = $this->_restMaster->get('layarantrian/list-function-screen');
        $body = json_decode($responseRequest->getBody(),TRUE);
        $ddlFunctionScreen = $body['response'];
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('layarantrian/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);

                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restMaster->get('layarantrian/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            return $this->renderPartial('form-pengambilan-antrian', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->delete('layarantrian/delete?id='.$id);
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

    public function actionChangeStatus(){
        $request = Yii::$app->request;
        $model = new LayarantrianForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $post = $request->post();

        if ($post) {

            $id = DocoHelpers::decrypt($post['id']);

            $model->load($request->post());

            try {
                $response = $this->_restMaster->put('layarantrian/update?id='.$id, [
                    'form_params' => ['is_active' => $post['is_active']]
                ]);

                return DocoHelpers::responseJsonString($response->getBody(), $formName);
            } catch (RequestException $e) {
                return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
            } catch (\Exception $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }
        }
    }

    public function actionExport($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrint($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionExportAll()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrintAll()
    {
        return $this->render('index', get_defined_vars());
    }
}
