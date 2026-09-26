<?php

namespace Doco\master\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\RakForm;
use GuzzleHttp\Exception\RequestException;

class RakController extends DocoController
{
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
        $ruangan = [];
        $request = $this->_restMaster->get('allow/list-ruangan');
        $response = json_decode($request->getBody(), true);
        // var_dump($response);exit;
        $ruangan = ArrayHelper::map($response['response']['data'],'ruangan_id','ruangan_nama');
        return $this->render('index', get_defined_vars());
    }

    public function actionCreate()
    {
        $model = new RakForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('app', 'Tambah Rak');
        $response =  $this->_restMaster->get('rak/get-list-ruangan', []);
        $data = json_decode($response->getBody(), true);

        foreach($data['response'] as $key => $value){
            $primaryKey = DocoHelpers::encrypt($value['ruangan_id']);
            $value['primary'] = $primaryKey;
            $data[$key] = $value;
        }
        
        $result['data'] = $data;
        $parentList = [];
        
        try{
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());

                if ($model->validate()) {
                    $request = $this->_restMaster->post('rak/create', [
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($request->getBody(), true);
                    return DocoHelpers::response($response);
                } else {
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            } else {
                return $this->render('form', get_defined_vars());
            }
        }catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage()); 
     }
  }
  
  public function actionUpdate($id = null)
  {
      try{
        $model = new RakForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('app', 'Ubah Rak');

        $encryptedId = $id;
        $id = DocoHelpers::decrypt($id);
        $request = $this->_restMaster->get('rak/rak-by-id?id='.$id);
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response']['query'];
        $model->attributes = $attributes;
        
        $parentList = !empty($response['response']['parentRak']) ? $response['response']['parentRak'] : [];
        $parentList = !empty($parentList) ? [$parentList['rakobat_id'] => $parentList['rakobat_nama']] : [];
        foreach($response['response']['listRuangan'] as $key => $value){
            $primaryKey = DocoHelpers::encrypt($value['ruangan_id']);
            $value['primary'] = $primaryKey;
            $result[$key] = $value;
        }
        $result['data'] = $response['response']['listRuangan'];
        
        if(Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            if ($model->validate()) {
                $request = $this->_restMaster->post('rak/update?id='.$id, [
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($request->getBody(), true);

                return DocoHelpers::response($response);
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }else {
            return $this->render('form_update', get_defined_vars());
        }
      }catch(RequestException $e){
        return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
      }catch (\Exception $e) {
        return DocoHelpers::responseTemplate(500, $e->getMessage());
    }  
  }

  public function actionDelete($id)
  {
      $id = DocoHelpers::decrypt($id);
      try{
          $request = $this->_restMaster->delete('rak/delete?id'.$id);
          $request = json_decode($request->getBody(),true);
          return DocoHelpers::response($request);
      }catch (RequestException $e) {
        return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
    } catch (\Exception $e) {
        return DocoHelpers::responseTemplate(500, $e->getMessage());
    }
  }

  public function actionGetDataRuangan()
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $params = Yii::$app->request->get();

    try{
        $request = $this->_restMaster->get('rak/get-list-ruangan');
      
        $response = json_encode($request->getBody(), true);

        $no = Yii::$app->request->get('start', 1);

        }catch(RequestException $e)
        {
            $result['error'] = $e->getMessage();
        }
    }
  

  public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->get();

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params);

        $draw = Yii::$app->request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $request = $this->_restMaster->get('rak/get-data?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $response = json_decode($request->getBody(), true);

            $no = Yii::$app->request->get('start', 1);

            if (!empty($response['response']['data'])) {
                foreach ($response['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['rakobat_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['rakobat_id']);
                    $value['rowNum'] = $no;
                    $value['empty'] = '';
                    $data[$key] = $value;

                }

                $result['data'] = $data;
                $result['recordsTotal'] = $response['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $response['response']['_meta']['totalCount'];

                return $result;
            }
            else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;

                return $result;
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
        } catch (\Exception $e) {
            // Error result
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
        }
    }

    public function actionGetParentRak()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        $rakobat_id = !empty($depdrop_parents[1]) ? $depdrop_parents[1] : null;
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';
        try {
            $response =  $this->_restMaster->get('rak/get-list-parent-rak', [
                'query' => [
                    'ruangan_id' => $parent_label,
                    'rakobat_id' => $rakobat_id
                ]
            ]);
            $data = json_decode($response->getBody(), true);
            foreach ($data['response'] as $value) 
                $result['output'][] = [
                    'id' => $value['rakobat_id'], 
                    'name' => $value['rakobat_nama']
                ];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}

?>