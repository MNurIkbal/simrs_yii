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
use app\modules\master\models\SpecimentForm;
use GuzzleHttp\Exception\RequestException;

class SampleLabController extends DocoController
{
    protected $_title = "Sample Laboratorium";
    protected $_module = 'master/sample-lab/';
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
        // $data = $this->getDataApi();
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
            $response = $this->_restMaster->get('sample-lab', 
                [
                    'query' => $yiiRestfulParams
                ]);
            $body = json_decode($response->getBody(), true);

            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['samplelab_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['samplelab_id']);
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

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new SpecimentForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('sample-lab/create', [
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

    public function actionUpdate($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $model = new SpecimentForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        try {
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->put('sample-lab/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);
                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                }else{
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            } else {
                $response = $this->_restMaster->get('sample-lab/view',[
                    'query' => [
                        'id' => $id
                    ]
                ]);
                $response = json_decode($response->getBody(),true);
                $model->attributes = $response['response'];
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
        return $this->renderPartial('form', get_defined_vars());
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->delete('sample-lab/delete?id='.$id);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    private function getDataApi()
    {
        $result = [
            'kode' => [],
            'satuan' => [],
        ];
        try {
            $response = $this->_restMaster->get('sample-lab/generate-api');
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

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        try {
            $path = Yii::getAlias("@download")."/sample_lab.pdf";
            $response = $this->_restMaster->get('sample-lab/export-pdf',[
                'query' => $yiiRestfulParams,
                'save_to' => $path
            ]);
            // Download pdf
            return DocoHelpers::downloadPdf($response, $path, 'sample-lab');
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e) {
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
            $response = $this->_restMaster->get('sample-lab/export-excel?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];

            return DocoHelpers::downloadFile($url);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

}