<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-25 10:50:14
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-02-21 14:31:58
 */


namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\master\models\ExpertiseForm;

class ExpertiseController extends DocoController
{
    protected $_title = "Master Expertise";
    protected $_module = 'master/expertise/';
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
        $title = $this->_title;
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
        $result['recordsFiltered'] = 0;
        try {
            $response = $this->_restMaster->get('expertise/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['expertise_id']);
                unset($value['expertise_id']);
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
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
    public function actionCreate()
    {
        // Init
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_title);
        $model = new ExpertiseForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $options = $optionsDokter = [];
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('expertise/create', [
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
        return $this->renderAjax('form', compact('title','model','options','optionsDokter'));
    }
    public function actionUpdate($id){
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new ExpertiseForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        $options = $optionsDokter = [];
        if ($request->post()) {
            $model->load($request->post());
            $post = $request->post('ExpertiseForm');
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('expertise/update', [
                        'form_params' => $model->attributes,
                        'query'=>['id'=>$id]
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
        try {
            $response = $this->_restMaster->get('expertise/view?id='.$id);
            $body = json_decode($response->getBody(), true);
            $attributes = $body['response'];
            $options[$body['response']['pemeriksaanrad_id']] = $body['response']['pemeriksaanrad_nama'];
            $optionsDokter[$body['response']['pegawai_id']] = $body['response']['nama_pegawai'];
            $model->attributes = $attributes;
        } catch (Exception $e) {
            $model->attributes = [];
        }

        return $this->renderAjax('form', compact('title','model','options','optionsDokter'));
    }
    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->delete('expertise/delete?id='.$id);
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
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $url = 'expertise/export-pdf?ruangan_id='.$ruangan_id.'&'.http_build_query($yiiRestfulParams);
            $path = Yii::getAlias("@download") . "/cetak-data-master-expertise.pdf";
            $response = $this->_restMaster->get($url,[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'expertise/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/Master Expertise.xlsx";
        try {
            $response = $this->_restMaster->get($url,[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
    public function actionGetPemeriksaanRad()
    {
        $result = [];
        try {
            if(isset($_GET['term']) && !empty($_GET['term'])){
                $response = $this->_restMaster->request('get', 'expertise/get-pemeriksaan-rad',[
                                'query'=>['pemeriksaanrad_nama'=>$_GET['term']],
                            ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id'=>$value['pemeriksaanradiologi_id'],'text'=>$value['pemeriksaanrad_nama']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        } catch (Exception $e) {
            $result = [];
        }
        return $result;
    }
}