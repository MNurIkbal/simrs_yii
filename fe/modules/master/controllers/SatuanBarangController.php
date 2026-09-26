<?php

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\SatuanUnitForm;
use GuzzleHttp\Exception\RequestException;

class SatuanBarangController extends DocoController
{
    protected $_title = "Satuan";
    protected $_module = 'master/satuan-barang/';
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
        $title = 'Satuan';
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
            $response = $this->_restMaster->get('satuan-barang', 
                [
                    'query' => $yiiRestfulParams
                ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['satuanunit_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['satuanunit_id']);
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
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new SatuanUnitForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('satuan-barang/create', [
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
        }
        $model->is_active = 1;
        return $this->renderPartial('form', get_defined_vars());
    }

    // public function actionUpdate($id)
    // {
    //     $id = DocoHelpers::decrypt($id);
    //     $request = Yii::$app->request;
    //     $model = new SatuanForm;
    //     $formName = substr(strrchr(get_class($model), "\\"), 1);

    //     try {
    //         if ($request->post()) {
    //             $model->load($request->post());
    //             if ($model->validate()) {
    //                 $response = $this->_restMaster->put('satuan/update?id='.$id, [
    //                     'form_params' => $model->attributes
    //                 ]);
    //                 return DocoHelpers::responseJsonString($response->getBody(), $formName);
    //             }else{
    //                 $errors = DocoHelpers::parseError($model->errors, $formName);
    //                 return DocoHelpers::responseTemplate(422, 'Error', $errors);
    //             }
    //         } else {
    //             $response = $this->_restMaster->get('satuan/view',[
    //                 'query' => [
    //                     'id' => $id
    //                 ]
    //             ]);
    //             $response = json_decode($response->getBody(),true);
    //             $model->attributes = $response['response'];
    //         }
    //     } catch (RequestException $e) {
    //         return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
    //     } catch (\Exception $e) {
    //         return DocoHelpers::responseTemplate(500, $e->getMessage());
    //     }
    //     return $this->renderPartial('form', get_defined_vars());
    // }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->delete('satuan-barang/delete?id='.$id);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    private function getDataApi()
    {
        try {
            $response = $this->_restMaster->get('satuan/generate-api');
            $body = json_decode($response->getBody(), true);

            $result = [
                'kode' => $body['response']['kode'],
                'satuan' => $body['response']['satuan'],
            ];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }

    // Export excel
    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        try {
            $response = $this->_restMaster->get('satuan/export-excel?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];

            return DocoHelpers::downloadFile($url);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetSatuan($tipe)
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){            
            $response = $this->_restMaster->request('POST', 'satuan-barang/data-satuan?tipe='.$tipe, [
                'form_params' => [
                    'term' => $_GET['q']['term'], 
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            $data = [];                                    
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => ($tipe == 1) ? $value['satuanunit_nama'] : $value['satuanunit_namalain'],
                    'text' => ($tipe == 1) ? $value['satuanunit_nama'] : $value['satuanunit_namalain'],
                ];
            }          
            $total = count($body['response']);                  
            $return = ['result' => $data, 'total_count' => $total,'incomplete_results' =>false];
            return DocoHelpers::response($return);
        }
    }

    public function actionCheckTransaction()
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($request->get('id'));
        try {
            $response = $this->_restMaster->get('satuan-barang/check-transaction?id='.$id, 
                            ['form_params' => []
                        ]);

            $body = json_decode($response->getBody(), true);
            $resResponse = $body['response']['title'];
            $resmetadata = $body['metadata']['status'];
            return $resmetadata;
            
        } catch (Exception $e) {
            $return = 422;
            return $return;
        }
    }

}