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
use GuzzleHttp\Exception\RequestException;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

class ProfilRumahSakitController extends DocoController
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

    /*public function actionIndex()
    {
        // Init
        $status = $this->_status; $options = $this->_options;
        
        return $this->render('index', get_defined_vars());
    }*/

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

        try {
            $response = $this->_restMaster->get('profil-rumah-sakit/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['profilrs_id']);
                unset($value['profilrs_id']);

                $value['primaryKey'] = $primaryKey;
                $value['aksi']  = '<table class="no-border" align="center"><tr>';
                $value['aksi'] .= '<td>&nbsp;'.Html::button(
                    '<i class="fa fa-eye" aria-hidden="true"></i>', [
                        'class' => 'btn btn-info btn-xs data-view',
                        'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Lihat'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '<td>&nbsp;'.Html::a(
                    '<i class="fa fa-file-excel-o" aria-hidden="true"></i>', '#', [
                        'class' => 'btn btn-green btn-xs data-export',
                        'action' => Url::home().$this->_module.'export?id='.$primaryKey,
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Ekspor'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '<td>&nbsp;'.Html::a(
                    '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>', '#', [
                        'class' => 'btn btn-crimson btn-xs data-print',
                        'action' => Url::home().$this->_module.'print?id='.$primaryKey,
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Cetak'),
                    ]
                ).'&nbsp;</td>';
                
              

                $value['aksi'] .= '<td>&nbsp;' . Html::a(
                    '<i class="fa fa-pencil" aria-hidden="true"></i>',
                    Url::home() . $this->_module . 'update?id=' . $primaryKey,
                    [
                        'class' => 'btn btn-dark-turquise btn-xs',
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Ubah'),
                    ]
                ) . '&nbsp;</td>';

                $value['aksi'] .= '<td>&nbsp;'.Html::a(
                    '<i class="fa fa-trash" aria-hidden="true"></i>', '#', [
                        'class' => 'btn btn-danger btn-xs data-delete',
                        'action' => Url::home().$this->_module.'delete?id='.$primaryKey,
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Hapus'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '</tr></table>';
                $value['aksi'] .= '</div>';

                $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);
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
        // Init
        $status = $this->_status;
        $options = $this->_options;

        $id_encrypt = DocoHelpers::encrypt(1);

        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah') . ' ' . \Yii::t('fe', $this->_title);
        $model = new ProfilRumahSakitForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        if ($request->post()) {
            $model->load($request->post());
            $model->logo_rumahsakit  = UploadedFile::getInstance($model, 'logo_rumahsakit');
            $model->gambar_login     = UploadedFile::getInstance($model, 'gambar_login');
            $model->background_login = UploadedFile::getInstance($model, 'background_login');
            $model->logo_header      = UploadedFile::getInstance($model, 'logo_header');
            if ($model->validate()) {
                try {
                    if(!$model->uploadGambar()){
                        return DocoHelpers::responseTemplate(500, "Terjadi Kesalahan Saat Upload");
                    }
                    $response = $this->_restMaster->put('profil-rumah-sakit/update-profile?id='.$id, [
                        'form_params' => $model->attributes,
                    ]);

                    $parseResponse = json_decode($response->getBody(),TRUE);
                    if(isset($parseResponse['response']['data-profil'])){
                        $cacheApp = $parseResponse['response']['data-profil'];
                        Yii::$app->cache->set("app",$cacheApp);
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

            $requests = $this->_restMaster->get('allow/get-api');
            $response_api = json_decode($requests->getBody(), true);
            $response_api = $response_api['response']['lookup'];

            $jenis_rumahsakit = $response_api['jenisrs_profilrs'];
            $jenis_rumahsakit = ArrayHelper::map($jenis_rumahsakit, 'lookup_id', 'lookup_name');

            $kelas_rumahsakit = $response_api['kelas_rumahsakit'];
            $kelas_rumahsakit = ArrayHelper::map($kelas_rumahsakit,'lookup_id','lookup_name');

            $propinsi = $response_api['provinsi'];
            $propinsi = ArrayHelper::map($propinsi, 'propinsi_id', 'propinsi_nama');
            /*$response = $this->_restMaster->get('propinsi?advanced-filter[is_active]=1');
            $body = json_decode($response->getBody(), true);
            $propinsi = $body['response']['data'];*/

            $response = $this->_restMaster->get('kabupaten?advanced-filter[is_active]=1');
            $body = json_decode($response->getBody(), true);
            $kabupaten = $body['response']['data'];

            $response = $this->_restMaster->get('kecamatan?advanced-filter[is_active]=1');
            $body = json_decode($response->getBody(), true);
            $kecamatan = $body['response']['data'];

            $response = $this->_restMaster->get('kelurahan?advanced-filter[is_active]=1');
            $body = json_decode($response->getBody(), true);
            $kelurahan = $body['response']['data'];

            // return $this->renderPartial('form', get_defined_vars());
            return $this->render('form-tambah', get_defined_vars());
        }
    }

    public function actionIndex($id = null)
    {
        // Init
        $status = $this->_status; $options = $this->_options;
        
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new ProfilRumahSakitForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        if(empty($id)){
            $id = 1;
        }
        $id_encrypt = DocoHelpers::encrypt($id);
        $resProfil = $this->_restMaster->get('profil-rumah-sakit/view?id='.$id);
        $bodyresProfil = json_decode($resProfil->getBody(), TRUE);
        $attributesProfil = $bodyresProfil['response'];
        $model->attributes = $attributesProfil;
        if ($request->post()) {
            $post = $request->post();
            $model->load($request->post());
            $model->logo_rumahsakit  = UploadedFile::getInstance($model, 'logo_rumahsakit');
            $model->gambar_login     = UploadedFile::getInstance($model, 'gambar_login');
            $model->background_login = UploadedFile::getInstance($model, 'background_login');
            $model->logo_header      = UploadedFile::getInstance($model, 'logo_header');
            if ($model->validate()) {
                try {
                    
                    if(!$model->uploadGambar()){
                        return DocoHelpers::responseTemplate(500, "Terjadi Kesalahan Saat Upload");
                    }

                    if(isset($attributesProfil['logo_header']) && is_null($model->logo_header) && $post['is_deleted_logo_header'] == 1){
                        $model->logo_header = 'setnull';
                    }
                    if(isset($attributesProfil['logo_rumahsakit']) && is_null($model->logo_rumahsakit) && $post['is_deleted_logo_rumahsakit'] == 1){
                        $model->logo_rumahsakit = 'setnull';
                    }
                    if(isset($attributesProfil['gambar_login']) && is_null($model->gambar_login) && $post['is_deleted_gambar_login'] == 1){
                        $model->gambar_login = 'setnull';
                    }
                    if(isset($attributesProfil['background_login']) && is_null($model->background_login) && $post['is_deleted_background_login'] == 1){
                        $model->background_login = 'setnull';
                    }

                    // echo "<pre>";
                    // print_r($model->attributes);
                    // echo "</pre>";
                    // die();

                    $response = $this->_restMaster->put('profil-rumah-sakit/update-profile?id='.$id, [
                        'form_params' => $model->attributes,
                    ]);

                    $parseResponse = json_decode($response->getBody(),TRUE);
                    if(isset($parseResponse['response']['data-profil'])){
                        $cacheApp = $parseResponse['response']['data-profil'];
                        if (Yii::$app->cache->get("app") != false) {
                            Yii::$app->cache->delete("app");
                        }
                        Yii::$app->cache->set("app",$cacheApp);
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


            $requests = $this->_restMaster->get('allow/get-api');
            $response_api = json_decode($requests->getBody(), true);
            $response_api = $response_api['response']['lookup'];

            $jenis_rumahsakit = $response_api['jenisrs_profilrs'];
            $jenis_rumahsakit = ArrayHelper::map($jenis_rumahsakit, 'lookup_id', 'lookup_name');

            $kelas_rumahsakit = $response_api['kelas_rumahsakit'];
            $kelas_rumahsakit = ArrayHelper::map($kelas_rumahsakit, 'lookup_id', 'lookup_name');

            $propinsi = $response_api['provinsi'];
            $propinsi = ArrayHelper::map($propinsi, 'propinsi_id', 'propinsi_nama');
            
            $response = $this->_restMaster->get('kabupaten?advanced-filter[is_active]=1');
            $body = json_decode($response->getBody(), TRUE);
            $kabupaten = $body['response']['data'];
            
            $response = $this->_restMaster->get('kecamatan?advanced-filter[is_active]=1');
            $body = json_decode($response->getBody(), TRUE);
            $kecamatan = $body['response']['data'];
            
            $response = $this->_restMaster->get('kelurahan?advanced-filter[is_active]=1');
            $body = json_decode($response->getBody(), TRUE);
            $kelurahan = $body['response']['data'];

            $rootPath = Url::base(true);
            $imgPath = Url::base(true). '/media/img/profil-rs/';
            $preview_logo_rumahsakit = isset($model->logo_rumahsakit) ? [$rootPath.@$model->path_logorumahsakit.@$model->logo_rumahsakit] : false;
            $preview_gambar_login = isset($model->gambar_login) ? [$rootPath.@$model->path_gambar_login.@$model->gambar_login] : false;
            $preview_background_login = isset($model->background_login) ? [$rootPath.@$model->path_background_login.@$model->background_login] : false;
            $preview_logo_header = isset($model->logo_header) ? [$rootPath.@$model->path_logo_header.@$model->logo_header] : false;
            return $this->render('form-update', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->delete('profil-rumah-sakit/delete?id='.$id);
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

    public function actionExport($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrint($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionExportAll()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrintAll()
    {
        return $this->render('index', get_defined_vars());
    }
}
