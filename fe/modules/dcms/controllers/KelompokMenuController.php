<?php


namespace Doco\dcms\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use Doco\dcms\models\KelompokMenuForm;


class KelompokMenuController extends DocoController
{
    
    protected $_title = "Kelompok Menu";
    protected $_module = '/dcms/kelompok-menu';
    protected $restMaster;

    public function init()
    {
        parent::init();
        $this->restMaster = Yii::$app->docoRest->master;
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
            $response = $this->restMaster->request('GET', 'kelompok-menu/',[
                            'form_params' => [],
                            'query' => $yiiRestfulParams
                        ]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->get('start', 0);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kelmenu_id']);
                $value['primary'] = $primaryKey;
                unset($value['kelmenu_id']);
                
                $value['is_active'] = DocoHelpers::isActive($value['is_active']);
                $value['rowNum'] = $no;
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->post('draw'),
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
            $title = 'Tambah Kelompok Menu';
            $model = new KelompokMenuForm;
            $status = $this->_status;
            $request = Yii::$app->request;
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->restMaster->request('POST', 'kelompok-menu/create',[
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,true);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KelompokMenuForm');
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
            $title = 'Update Kelompok Menu';
            $model = new KelompokMenuForm;
            $status = $this->_status;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->restMaster->request('POST', 'kelompok-menu/update',[
                                        'query' => ['id' => $id],
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,true);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KelompokMenuForm');
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
            $response = $this->restMaster->request('DELETE', 'kelompok-menu/delete',[
                            'query' => ['id' => $id ]
                        ]);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => $response['response']['title'],
                'text' => $response['response']['message']
            ];
            return DocoHelpers::response($response);
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
            $response = $this->restMaster->request('GET', 'kelompok-menu/view',[
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