<?php

namespace Doco\master\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\master\models\ModulExternalForm;

class ModulExternalController extends DocoController
{
    protected $_title = "Modul External";
    protected $_module = '/master/modul-external';
    protected $_restDcms;

    public function init()
    {
        parent::init();
        $this->_restDcms = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $status = $this->_status;
        $title = $this->_title;
        return $this->render('index',get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw',1);
            $data = [];
            $response = $this->_restDcms->request('GET', 'modul-external/',[
                            'query' => $yiiRestfulParams
                        ]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['modul_id']);
                unset($value['modul_id']);
                $value['primary'] = $primaryKey;
                $value['status'] = DocoHelpers::isActive($value['is_active']);
                $value['rowNum'] = $no;
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $draw,
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionCreate()
    {
        try {
            $title = 'Tambah Modul External';
            $model = new ModulExternalForm;
            $status = [
                Yii::t('fe', 'Tidak aktif'),
                Yii::t('fe', 'Aktif'),
            ];
            $newtab = [
                Yii::t('fe', 'Tidak'),
                Yii::t('fe', 'Ya'),
            ];
            $request = Yii::$app->request;
            $model->is_active = 1;
            $model->open_newtab = 1;
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restDcms->request('POST', 'modul-external/create',[
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,'ModulExternalForm');
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'ModulExternalForm');
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

    public function actionUpdate($id)
    {
        try {
            $title = 'Update Modul External';
            $model = new ModulExternalForm;
            $status = [
                Yii::t('fe', 'Tidak aktif'),
                Yii::t('fe', 'Aktif'),
            ];
            $newtab = [
                Yii::t('fe', 'Tidak'),
                Yii::t('fe', 'Ya'),
            ];
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restDcms->request('POST', 'modul-external/update',[
                                        'query' => ['id' => $id],
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,true);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'ModulExternalForm');
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
                    throw new \yii\web\HttpException(404, 'The requested Item could not be found.');
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
            $response = $this->_restDcms->request('DELETE', 'modul-external/delete',[
                            'query' => ['id' => $id ]
                        ]);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => $response['response']['title'],
                'text' => $response['response']['message']
            ];
            return DocoHelpers::response($response,true);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
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
            $response = $this->_restDcms->request('GET', 'modul-external/view',[
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