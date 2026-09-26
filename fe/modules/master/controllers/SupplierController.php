<?php

/**
 * @Author: Sigit
 * @Date:   2018-06-06 09:06:50
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-11-06 14:22:01
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

use app\modules\master\models\SupplierForm;

class SupplierController extends DocoController
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
        return $this->render('index', get_defined_vars());
    }

    public function actionCreate()
    {
        $model = new SupplierForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('app', 'Tambah Supplier');
        try {

            $model->scenario = 'master';
            $request = $this->_restMaster->get('supplier/get-source-data');
            $response = json_decode($request->getBody(), true);
            $sourceData = $response['response'];

            $propinsi = $sourceData['propinsi'];
            $kabupaten = $sourceData['kabupaten'];
            $selectedKabupaten = $sourceData['selectedKabupaten'];
            $bank = $sourceData['bank'];
            $pajak = $sourceData['pajak'];
            $kodeSupplier = $sourceData['kode_supplier'];
            $model->supplier_kode = $kodeSupplier;

            if (!empty($kabupaten)) {
                foreach ($kabupaten as $key => $value) {
                    $kabupaten[$key]['ruangan_nama'] = $value['kabupaten_nama'];
                }
            }
            
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                if ($model->validate()) {
                    $request = $this->_restMaster->post('supplier/create', [
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
            $model = new SupplierForm;
            $model->scenario = 'master';
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $title = Yii::t('app', 'Ubah Supplier');

            $encryptedId = $id;
            $id = DocoHelpers::decrypt($id);

            $request = $this->_restMaster->get('supplier/view?id='.$id);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
            $model->attributes = $attributes;

            $request = $this->_restMaster->get('supplier/get-source-data?id='.$model->propinsi_id);
            $response = json_decode($request->getBody(), true);
            $sourceData = $response['response'];

            $propinsi = $sourceData['propinsi'];
            $kabupaten = $sourceData['kabupaten'];
            $selectedKabupaten = $sourceData['selectedKabupaten'];
            $bank = $sourceData['bank'];
            $pajak = $sourceData['pajak'];
            
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());

                if ($model->validate()) {
                    $request = $this->_restMaster->post('supplier/update?id='.$id, [
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
            $request = $this->_restMaster->delete('supplier/delete?id='.$id);
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
            $request = $this->_restMaster->get('supplier/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $response = json_decode($request->getBody(), true);

            $no = Yii::$app->request->get('start', 1);

            if (!empty($response['response']['data'])) {
                foreach ($response['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['supplier_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['supplier_id']);
                    $value['row'] = $no;
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

    // Get propinsi
    public function actionGetPropinsi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $params = '';

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?id='.$parent_label;
        }

        try {
            $request = $this->_restMaster->get('supplier/get-propinsi'.$params);
            $body = json_decode($request->getBody(), true);

            if (isset($body['response']['data']) && !empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $value) {
                    $result['output'][] = [
                        'id' => $value['propinsi_id'],
                        'name' => $value['propinsi_nama']
                    ];
                }
            }

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    // Get kabupaten
    public function actionGetKabupaten()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $params = '';

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?id='.$parent_label;
        }

        try {
            $request = $this->_restMaster->get('supplier/get-kabupaten'.$params);
            $body = json_decode($request->getBody(), true);

            if (isset($body['response']['data']) && !empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $value) {
                    $result['output'][] = [
                        'id' => $value['kabupaten_id'],
                        'name' => $value['kabupaten_nama']
                    ];
                }
            }

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();

            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportPrint()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params = Yii::$app->request->get();

            if (isset($params['order']) && $params['order'] != '') {
                if (isset($params['order'][0]['column'])) {
                    $params['order'][0]['column'] = 2;
                }
            }
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params);
            $request = $this->_restMaster->get('supplier/print?'.http_build_query($yiiRestfulParams));
            $response = json_decode($request->getBody(), true);
            $data = $response['response'];

            return $this->render('print', get_defined_vars());
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/supplier.xlsx";
            $response = $this->_restMaster->get('supplier/export-excel?' .http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];
            
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e){
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        $path = Yii::getAlias("@download") . "/supplier.pdf";
        try {
            $response = $this->_restMaster->get('supplier/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);

        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetKode() {
        try{
            $response = $this->_restMaster->request('get', 'supplier/get-last-kode-supplier');
            $body = json_decode($response->getBody(), true);
            $nomor = $body['response'];
        } catch (RequestException $e) {
            $nomor = '';
        } catch (\Exception $e) {
            $nomor = '';
        }
        return $nomor;
    }
}
?>
