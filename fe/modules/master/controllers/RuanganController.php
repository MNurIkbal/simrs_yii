<?php
// Author : Ramdhan Nurrachman

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoConstants;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\RuanganForm;
use yii\helpers\FileHelper;

class RuanganController extends DocoController
{
    protected $_title = "Ruangan";
    protected $_module = 'master/ruangan/';
    protected $_restMaster;
    const SINGKATAN_RJ = 'RJ';

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
            $response = $this->_restMaster->get('ruangan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
           
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['ruangan_id']);
                if ($value['is_active'] == "t") {
                    $value['is_active'] = "Aktif";
                }else{
                    $value['is_active'] = "Tidak Aktif";
                }
                $value['primary'] = $primaryKey;
                unset($value['ruangan_id']);
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
           
            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Lihat').' '.\Yii::t('fe', $this->_title);
        $model = new RuanganForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restMaster->get('ruangan/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }


    private function uploadFile($file, $path, $data){
        if (!empty($file)) {
            $webroot = \Yii::getAlias('@webroot');
            $size = $file->size;
            $ext = end(explode(".", $file->name));
            if ($size > DocoConstants::MAX_UPLOAD_LAB) {
                $result['response']['title'] = Yii::t('fe', 'Proses gagal !');
                $result['response']['text'] = Yii::t('fe', 'File maksimal 100 mb !');
                return DocoHelpers::response($result, 422);
            }
            if (($ext == 'jpg') OR ($ext == 'png') OR ($ext == 'pdf') OR ($ext == 'mp3')) {
                // success
            } else {
                $result['response']['title'] = Yii::t('fe', 'Proses gagal !');
                $result['response']['text'] = Yii::t('fe', 'File harus berbentuk jpg, png dengan maksimal 100 mb');

                return DocoHelpers::response($result, 422);
            }

            $ext = end(explode(".", $file->name));
            $dataFileName = !empty($data->id) ? $data->id : $file->name ;
            $encdataFileName = DocoHelpers::encrypt($dataFileName);
            $fileName = date('Ymd-his', strtotime('NOW')).Yii::$app->security->generateRandomString(6).".{$encdataFileName}".'.'.$ext;
            if (!file_exists($webroot.$path)) {
                mkdir($webroot.$path, 0777, true);
            }
            $file->saveAs($webroot.$path.$fileName);
            $path = $webroot.$path.$fileName;

            if (!file_exists($path)) {
                return ['file_name'=> '',
                    'blob'  => '',
                    'status' => false
                    ];
            }

            $mimetype = FileHelper::getMimeType($path);
            
            $source = file_get_contents($path);
            $base64 = base64_encode($source);
            $blob = 'data:'.$mimetype.';base64,'.$base64;
            return ['file_name'=> $fileName,
                    'blob'  => $blob,
                    'status' => true
                    ];
        } else {
            return ['file_name'=> '',
                    'blob'  => '',
                    'status' => false
                    ];
        }
    }

    public function actionCreate()
    {
        // Init
        $status = $this->_status; $options = $this->_options;

        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_title);
        $model = new RuanganForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $FileRuanganImage = UploadedFile::getInstance($model, "ruangan_image");
                $FileRuanganFilesuara = UploadedFile::getInstance($model, "ruangan_filesuara");

                $resRuanganImage = $this->uploadFile($FileRuanganImage, '/media/ruangan/', $model->attributes);
                $resRuanganFilesuara = $this->uploadFile($FileRuanganFilesuara, '/media/ruangan/', $model->attributes);
                if($resRuanganImage['status'] == true){
                    $model->ruangan_image = $resRuanganImage['file_name'];
                    $model->ruangan_image_blob = $resRuanganImage['blob'];
                }

                if($resRuanganFilesuara['status'] == true){
                    $model->ruangan_filesuara = $resRuanganFilesuara['file_name'];
                    $model->ruangan_filesuara_blob = $resRuanganFilesuara['blob'];
                }
                try {
                    $response = $this->guzzleExec($this->_restMaster, [
                        'url' => 'ruangan/create-ruangan',
                        'method' => 'post',
                        'payload' => [
                            'form_params' => $model->attributes
                        ],
                    ]);
                    if(isset($response['metadata']['status']) && isset($response['metadata']['message']) &&
                        $response['metadata']['status'] == 422) { // untuk mengeluarkan response validasi BE
                        $errors = DocoHelpers::parseError($response['metadata']['message'], $formName);
                        return DocoHelpers::responseTemplate(422, 'Error', $errors);
                    }
                    return DocoHelpers::response($response);
                } catch (RequestException $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {

            $response = $this->_restMaster->get('instalasi?advanced-filter[is_active]=1&per-page=100');
            $body = json_decode($response->getBody(), TRUE);
            $instalasi = $body['response']['data'];
            $instalasi = ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama');
            asort($instalasi);

            $response = $this->_restMaster->get('unit-kerja?advanced-filter[is_active]=1&per-page=100');
            $body = json_decode($response->getBody(), TRUE);
            $unitKerja = $body['response']['data'];

            $response = $this->_restMaster->get('ruangan/list-lantai');
            $body = json_decode($response->getBody(), TRUE);
            $lantai = $body['response'];
            
            
            return $this->renderAjax(Yii::$app->docoPlugin->execute($this,'form_ruangan'), get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        // Init
        $status = $this->_status; 
        $options = $this->_options;
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new RuanganForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        $lantai_ruangan = '';

        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $FileRuanganImage = UploadedFile::getInstance($model, "ruangan_image");
                $FileRuanganFilesuara = UploadedFile::getInstance($model, "ruangan_filesuara");

                $resRuanganImage = $this->uploadFile($FileRuanganImage, '/media/ruangan/', $model->attributes);
                $resRuanganFilesuara = $this->uploadFile($FileRuanganFilesuara, '/media/ruangan/', $model->attributes);
                if($resRuanganImage['status'] == true){
                    $model->ruangan_image = $resRuanganImage['file_name'];
                    $model->ruangan_image_blob = $resRuanganImage['blob'];
                }

                if($resRuanganFilesuara['status'] == true){
                    $model->ruangan_filesuara = $resRuanganFilesuara['file_name'];
                    $model->ruangan_filesuara_blob = $resRuanganFilesuara['blob'];
                }
                try {

                    $response = $this->guzzleExec($this->_restMaster, [
                        'url' => 'ruangan/update-ruangan',
                        'method' => 'post',
                        'payload' => [
                            'form_params' => $model->attributes,
                            'query' => ['id' => $id ],

                        ],
                    ]);
                    if(isset($response['metadata']['status']) && isset($response['metadata']['message']) &&
                        $response['metadata']['status'] == 422) { // untuk mengeluarkan response validasi BE
                        $errors = DocoHelpers::parseError($response['metadata']['message'], $formName);
                        return DocoHelpers::responseTemplate(422, 'Error', $errors);
                    }
                    return DocoHelpers::response($response);
                } catch (RequestException $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {

            $response = $this->_restMaster->get('instalasi?advanced-filter[is_active]=1&per-page=100');
            $body = json_decode($response->getBody(), TRUE);
            $instalasi = $body['response']['data'];
            $instalasi = ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama');
            asort($instalasi);

            $response = $this->_restMaster->get('unit-kerja?advanced-filter[is_active]=1&per-page=100');
            $body = json_decode($response->getBody(), TRUE);
            $unitKerja = $body['response']['data'];

            $response = $this->_restMaster->get('ruangan/list-lantai?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $lantai = $body['response']['list_lantai'];

            if (!empty($body['response']['get_lantai'])){
                $cekLantai = $body['response']['get_lantai'];
                $lantai_ruangan = $cekLantai['jenisantriandetail_id'];
            }

            $response = $this->_restMaster->get('ruangan/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            $model->lantairuangan_id = $lantai_ruangan;
            $model->satusehat_ruangan = $attributes['satusehat_ruangan_id'];
            return $this->renderAjax(Yii::$app->docoPlugin->execute($this,'form_ruangan'), get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->get('ruangan/delete-ruangan',
                ['query'=>['id'=>$id]]
            );
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);

        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $path = Yii::getAlias("@download") . "/ruangan.pdf";
        try {
            $response = $this->_restMaster->get('ruangan/export-pdf?'.http_build_query($yiiRestfulParams),[
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

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/laporan-ruangan.xlsx";
        try {
            $response = $this->_restMaster->get('ruangan/export-excel', [
                "query" => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
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

    public function actionChangeStatus($id, $status)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->put('ruangan/update?id='.$id, [
                'form_params' => ["is_active" => $status]
            ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Status berhasil diubah.")
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(),
                "OK",
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ')." !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
            ];
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(),
                json_decode($e->getResponse()->getBody()->getContents())->message,
                [],
                $data
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
    * @author Rizal
    * @since
    * @param
    * @return
    * @desc
    */
    public function actionListRuangan() {
        $request = Yii::$app->request;
        $post = $request->post();
        $instalasi_id = $post['depdrop_parents'][0];

        $ruanganRequest = $this->_restMaster->get('ruangan/list-ruangan?instalasi_id='.$instalasi_id);
        $body = json_decode($ruanganRequest->getBody(),TRUE);
        $responses = $body['response'];

        $out = [];
        foreach($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response
            ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }

    public function actionDepListRuangan() {
        $request = Yii::$app->request;
        $post = $request->post();
        $instalasi_id = $post['depdrop_parents'][0];
        if ($instalasi_id) {
            $ruanganRequest = $this->_restMaster->get('allow/list-ruangan?instalasi_id='.$instalasi_id);
            $body = json_decode($ruanganRequest->getBody(),TRUE);
            $responses = $body['response']['data'];
            $responses = ArrayHelper::map($responses, 'ruangan_id', 'ruangan_nama');
        } else {
            $responses = [];
        }

        $out = [];
        foreach($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response
            ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }

    public function actionListByPenyakit() {
        $request = Yii::$app->request;
        $obj = $request->post('obj');
        $value = $request->post('value');
        $response = [];

        switch ($obj) {
            case 'jeniskasuspenyakit_id':
            $penyakitRequest = $this->_restMaster->get('ruangan/list-by-penyakit?penyakit='.$value);
            $body = json_decode($penyakitRequest->getBody(),TRUE);
            $response = $body['response'];
            break;
        }

        $tagOptions = ['prompt' => "Pilih ... "];

        return Html::renderSelectOptions([], ArrayHelper::map($response, 'ruangan_id', 'ruangan_nama'), $tagOptions);
    }
}
