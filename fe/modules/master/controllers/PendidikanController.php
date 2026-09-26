<?php

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoController;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use Doco\master\models\PendidikanForm;

class PendidikanController extends DocoController
{

    protected $_title = "Master :: Pendidikan";
    protected $_module = '/master/pendidikan';
    protected $restMaster;

    public function init()
    {
        parent::init();
        $this->restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $status = $this->_status;
        return $this->render('index',get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->restMaster->request('POST', 'pendidikan/',[
                            'form_params' => $post
                        ]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->post('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendidikan_id']);
                unset($value['pendidikan_id']);
                $value['aksi']  = Html::button(
                        "<i class='fa fa-eye'></i>", [
                        'style' => 'margin-right:5px',
                        'class' => 'btn btn-info btn-xs data-view',
                        'action' => Url::to([$this->_module .'/view','id' => $primaryKey]),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-popup' => "tooltip",
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Lihat')
                    ]
                );
                $value['aksi'] .= Html::button(
                        "<i class='fa fa-pencil'></i>", [
                        'style' => 'margin-right:5px',
                        'style' => 'margin-right:5px ',
                        'class' => 'btn btn-primary btn-xs data-update',
                        'action' => Url::to([$this->_module .'/update','id' => $primaryKey]),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Ubah'),
                    ]
                );
                $value['aksi'] .= Html::button(
                    "<i class='fa fa-trash'></i>",[
                    'style' => 'margin-right:5px',
                    'class' => 'btn btn-danger btn-xs delete',
                    'style' => 'margin-right:5px',
                    'data-popup' => "tooltip",
                    'action' => Url::to([$this->_module .'/delete','id' => $primaryKey]),
                    ]
                );

                $value['status'] = DocoHelpers::isActive($value['is_active']);
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

    public function actionCreate()
    {
        try {
            $title = 'Tambah Pendidikan';
            $model = new PendidikanForm;
            $status = $this->_status;
            $request = Yii::$app->request;
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->restMaster->request('POST', 'pendidikan/create',[
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,'PendidikanForm');
                } else {
                    return DocoHelpers::response($model->errors,422,'PendidikanForm');
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

    public function actionUpdate($id)
    {
        try {
            $title = 'Update Pendidikan';
            $model = new PendidikanForm;
            $status = $this->_status;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->restMaster->request('POST', 'pendidikan/update',[
                                        'query' => ['id' => $id],
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,true);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PendidikanForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                $result = $this->find($id);
                if (isset($result['response'])) {
                    $model->attributes = $result['response'];
                    return $this->renderPartial('form',get_defined_vars());
                } else {
                    return $this->actionCreate();
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionDelete($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $response = $this->restMaster->request('DELETE', 'pendidikan/delete',[
                            'query' => ['id' => $id ]
                        ]);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionView($id)
    {
        try {
            $title = 'View Pendidikan';
            $model = new PendidikanForm;
            $status = $this->_status;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);
            $result = $this->find($id);
                if (isset($result['response'])) {
                    $model->attributes = $result['response'];
                    return $this->renderPartial('view',get_defined_vars());
                } else {
                    return $this->actionCreate();
                }
           
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    /**
    * @param integer $id
    * 
    * @return array|mix
    * @throws GuzzleHttp\Exception\RequestException
    * @throws Exception
    */

    public function find($id)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->restMaster->request('GET', 'pendidikan/view',[
                            'query' => ['id' => $id ]
                        ]);
            return json_decode($response->getBody(),true);
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
