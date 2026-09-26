<?php
// Author : Ramdhan Nurrachman

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\ProfilRumahSakitForm;
use app\modules\master\models\InfoSliderForm;
use GuzzleHttp\Exception\RequestException;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

class InfoSliderRumahSakitController extends DocoController
{
    protected $_title = "Profil rumah sakit";
    protected $_module = 'master/profil-rumah-sakit/';
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
        // Init
        $status = $this->_status; $options = $this->_options;
        
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->controller->enableCsrfValidation = false;
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
        $path = \Yii::getAlias('@webroot');


        try {
            $response = $this->_restMaster->get('info-slider-rumah-sakit/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);

            $rootPath = Url::base(true);
            

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['infoslider_id']);
                unset($value['infoslider_id']);
                
                $value['primary'] = $primaryKey;
                // $value['primaryKey'] = $primaryKey;
                $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);

                $preview_file_gambar = $rootPath . '/media/img/info-slider/' .$value['file_gambar'];
                $cek_gambar = $path . '/media/img/info-slider/' . $value['file_gambar'];
                $value['img_show'] = '';

                if (file_exists($cek_gambar)) {
                    $value['img_show'] = '<img src="' . $preview_file_gambar . '" width="150px" height = "150px" />';
                }else{
                    $value['img_show'] = '';
                }
                
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

    public function actionTambah()
    {
        ini_set('post_max_size', '256M');
        ini_set('upload_max_size', '2M');

        // Init
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah') . ' ' . \Yii::t('fe', $this->_title);
        $model = new InfoSliderForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        

        if ($request->post()) {

            $tgl_mulai = DocoHelpers::convertIndoToEnglish($request->post('InfoSliderForm')['tgl_mulai'], false, true);
            $tgl_selesai = DocoHelpers::convertIndoToEnglish($request->post('InfoSliderForm')['tgl_selesai'], false, true);

            $model->load($request->post());
            $model->tgl_mulai = $tgl_mulai;
            $model->tgl_selesai = $tgl_selesai;
            $model->file_gambar = UploadedFile::getInstance($model, "file_gambar");

            if ($model->validate()) {
                try {
                    // fitur upload
                    $image = $model->file_gambar;
                    
                    if (isset($image->name)) {
                        $ext = end(explode(".", $image->name));
                        $model->file_gambar = $image->name;
                        
                        $path = \Yii::getAlias('@webroot');

                        $cek_dir = $path . '/media/img/info-slider';
                        if (!is_dir($cek_dir)) {
                            mkdir($cek_dir, 0777,true);
                        }
                        
                        if ($image->saveAs($path . '/media/img/info-slider/' . $model->file_gambar)) {
                            $response = $this->_restMaster->post('info-slider-rumah-sakit/created',[
                                'form_params' => $model->attributes,
                            ]);
                        } else {
                            $response = $this->_restMaster->post('info-slider-rumah-sakit/created',[
                                'form_params' => $model->attributes,
                            ]);
                        }
                    } else {
                        // return $model->attributes;
                        
                        $response = $this->_restMaster->post('info-slider-rumah-sakit/created',[
                            'form_params' => $model->attributes,
                        ]);
                    }
                    $tmpGetBody = json_decode($response->getBody(), true);
                    return DocoHelpers::response($tmpGetBody,false,'InfoSliderForm');
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
            // return $this->renderPartial('form', get_defined_vars());
            return $this->render('form-tambah', get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        // Init
        ini_set('post_max_size', '256M');
        ini_set('upload_max_size', '2M');
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new InfoSliderForm;
        $model->scenario = 'update';
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        $id_encrypt = DocoHelpers::encrypt($id);
        $path = \Yii::getAlias('@webroot');

        if ($request->post()) {
            $tgl_mulai = DocoHelpers::convertIndoToEnglish($request->post('InfoSliderForm')['tgl_mulai'], false, true);
            $tgl_selesai = DocoHelpers::convertIndoToEnglish($request->post('InfoSliderForm')['tgl_selesai'], false, true);

            $model->load($request->post());

            $model->tgl_mulai = $tgl_mulai;
            $model->tgl_selesai = $tgl_selesai;
            $model->file_gambar = UploadedFile::getInstance($model, "file_gambar");
            if ($model->validate()) {
                try {

                    // fitur upload
                    $image = $model->file_gambar;
            
                    if (isset($image->name)) {
                        $ext = end(explode(".", $image->name));
                        $model->file_gambar = $image->name;
                        
                        $cek_dir = $path . '/media/img/info-slider';
                        if (!is_dir($cek_dir)){
                            mkdir($cek_dir, 0777,true);
                        }

                        if ($image->saveAs($path . '/media/img/info-slider/' . $model->file_gambar)) {
                            $response = $this->_restMaster->post('info-slider-rumah-sakit/updated?id='.$id, [
                                'form_params' => $model->attributes,
                            ]);
                        } else {
                            
                            $response = $this->_restMaster->post('info-slider-rumah-sakit/updated?id='.$id, [
                                'form_params' => $model->attributes,
                            ]);
                        }
                    } else {
                        $response = $this->_restMaster->post('info-slider-rumah-sakit/updated?id='.$id, [
                            'form_params' => $model->attributes,
                        ]);

                    }

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

            $response = $this->_restMaster->get('info-slider-rumah-sakit/detail?id=' . $id);
            $body = json_decode($response->getBody(), true);
            $attributes = $body['response']['data'];
            if(isset($attributes['tgl_mulai'])){
                $attributes['tgl_mulai'] = date('d-M-Y',strtotime($attributes['tgl_mulai']));
            }
            if (isset($attributes['tgl_selesai'])) {
                $attributes['tgl_selesai'] = date('d-M-Y', strtotime($attributes['tgl_selesai']));
            }

            $model->attributes = $attributes;

            $rootPath = Url::base(true);
            $preview_file_gambar = isset($model->file_gambar) ? [$rootPath .'/media/img/info-slider/' . @$model->file_gambar] : false;

            return $this->render('form-update', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        // echo 'info-slider-rumah-sakit/deleted?id='.$id;
        try {
            $response = $this->_restMaster->delete('info-slider-rumah-sakit/deleted?id='.$id);
            $response = json_decode($response->getBody(),true);

            return DocoHelpers::response($response,false);
        } catch (RequestException $e) {
            return DocoHelpers::response($e->getMessage(), 500);
        } catch (\Exception $e) {
            return DocoHelpers::response($e->getMessage(), 500);
        }

        /*
         try {
            $response = $this->_restMaster->delete('golongan-operasi/delete?id='.$id);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response,false);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message, [
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
         */
    }

}
