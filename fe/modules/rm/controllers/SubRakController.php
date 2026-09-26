<?php
// Author : Ardi Pratama

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\rm\models\SubrakForm;
use GuzzleHttp\Exception\RequestException;
 
class SubRakController extends DocoController
{
    protected $_title = "Rm :: Sub Lokasi Rak";
    protected $_module = 'rm/sub-rak/';
    protected $_restRm;

    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;
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
        $response = $this->_restRm->get('sub-rak-rekam-medik/list-rak');
        $body = json_decode($response->getBody(), TRUE);
        $lokasirak = $body['response']['data'];

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
            $response = $this->_restRm->get('sub-rak-rekam-medik/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['subrak_id']);
                unset($value['subrak_id']);

                $value['primary'] = $primaryKey;
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
            echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Lihat Data');
        $model = new SubrakForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restRm->get('sub-rak-rekam-medik/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $title = \Yii::t('fe', 'Tambah data');
            $model = new SubrakForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    try {
                        $response = $this->_restRm->post('sub-rak-rekam-medik/create', [
                            'form_params' => $model->attributes
                        ]);
                        $r = json_decode($response->getBody(), true);
                        return DocoHelpers::response($r, false);
                        // return DocoHelpers::responseJsonString($response->getBody(), $formName);
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
                $response = $this->_restRm->get('lokasi-rak-rekam-medik');
                $body = json_decode($response->getBody(), TRUE);
                $lokasirak = $body['response']['data'];

                return $this->renderPartial('form', get_defined_vars());
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
        $title = \Yii::t('fe', 'Ubah data');
        $model = new SubrakForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restRm->put('sub-rak-rekam-medik/update?id='.$id, [
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
            $response = $this->_restRm->get('lokasi-rak-rekam-medik');
            $body = json_decode($response->getBody(), TRUE);
            $lokasirak = $body['response']['data'];

            $response = $this->_restRm->get('sub-rak-rekam-medik/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restRm->delete('sub-rak-rekam-medik/delete?id='.$id);
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

    public function actionListSubrak()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $lokasirak = $post['depdrop_parents'][0];

        $SubrakRequest = $this->_restRm->get('sub-rak-rekam-medik/list-subrak?lokasi='.$lokasirak);
        $body = json_decode($SubrakRequest->getBody(),TRUE);
        $ddlSubrak = $body['response'];

        $out = [];
        foreach($ddlSubrak as $key => $value) {
            $out[] = [
                        'id' => $key,
                        'name' => $value
                    ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }

    public function actionListSubrakNama()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $lokasirak = $post['depdrop_parents'][0];

        $SubrakRequest = $this->_restRm->get('sub-rak-rekam-medik/list-subrak-by-name?lokasi='.$lokasirak);
        $body = json_decode($SubrakRequest->getBody(),TRUE);
        $ddlSubrak = $body['response'];

        $out = [];
        foreach($ddlSubrak as $key => $value) {
            $out[] = [
                        'id' => $value,
                        'name' => $value
                    ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }
}
