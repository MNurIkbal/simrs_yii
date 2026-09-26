<?php

/**
 * @Author: Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * @Date:   2021-01-22 11:13:50
 */

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use GuzzleHttp\Exception\RequestException;

use app\modules\master\models\ManufakturForm;

class ManufakturController extends DocoController
{
    protected $_restMaster;
    protected $allowAction = ['*'];
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
        return $this->render('index', get_defined_vars());
    }

    public function actionCreate()
    {
        $model = new ManufakturForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('app', 'Tambah Manufaktur');
        try {
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());

                if ($model->validate()) {
                    $request = $this->_restMaster->post('manufaktur/create', [
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
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    // Action update
    public function actionUpdate($id = null)
    {
        try {
            $model = new manufakturForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $title = Yii::t('app', 'Ubah Manufaktur');

            $encryptedId = $id;
            $id = DocoHelpers::decrypt($id);

            $request = $this->_restMaster->get('manufaktur/view?id='.$id);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
            $model->attributes = $attributes;
            
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());

                if ($model->validate()) {
                    $request = $this->_restMaster->post('manufaktur/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($request->getBody(), true);

                    return DocoHelpers::response($response);
                } else {
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            }
            else {
                return $this->render('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $request = $this->_restMaster->delete('manufaktur/delete?id='.$id);
            $request = json_decode($request->getBody(),true);
            return DocoHelpers::response($request);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    // Action get data
    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->get();

        if (isset($params['order']) && $params['order'] != '') {
            if (isset($params['order'][0]['column'])) {
                $params['order'][0]['column'] = 2;
            }
        }

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params);
        $draw = Yii::$app->request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $request = $this->_restMaster->get('manufaktur/get-data?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $response = json_decode($request->getBody(), true);

            $no = Yii::$app->request->get('start', 1);

            if (!empty($response['response']['data'])) {
                foreach ($response['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['manufaktur_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['manufaktur_id']);
                    $value['rowNum'] = $no;
                    $value['empty'] = '';
                    $value['status_aktif'] = $value['is_active'] ? "Aktif" : "Tidak Aktif";
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
}
?>
