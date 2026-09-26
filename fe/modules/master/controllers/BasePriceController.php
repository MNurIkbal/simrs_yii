<?php

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

use app\modules\master\models\BasePrice;
use app\modules\master\models\UploadForm;
use yii\web\UploadedFile;
use GuzzleHttp\Exception\RequestException;

class BasePriceController extends DocoController
{
    protected $_title = "Master Base Price Harga";
    protected $_module = '/master/base-price/';
    protected $_restMaster;
    protected $_ruangan_id;
    protected $_pegawai_id;
    
    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $this->_ruangan_id = !empty($ruangan_id) ? $ruangan_id : 0;
        $id_pegawai = Yii::$app->docoVars->user('id_pegawai');
        $this->_pegawai_id = !empty($id_pegawai) ? $id_pegawai : 0;
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
        $controller = Yii::$app->controller;
        $roleUbahBtn = DHtml::cekHakAkses('ubah-base-price');
        $roleUbahBtn = ($roleUbahBtn) ? '' : 'display:none';

        $roleDetailHistoryBtn = DHtml::cekHakAkses('detail-history-obat');
        $roleDetailHistoryBtn = ($roleDetailHistoryBtn) ? '' : 'display:none';

        $roleExportTemplateBtn = DHtml::cekHakAkses('download-excel');
        $roleExportTemplateBtn = ($roleExportTemplateBtn) ? '' : 'display:none';

        $roleImportBtn = DHtml::cekHakAkses('upload-base-price');
        $roleImportBtn = ($roleImportBtn) ? '' : 'display:none';

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
        try {
            $response = $this->_restMaster->get('base-price/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $responData = $body['response']['data'];
            foreach ($responData as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['obatalkes_id']);
                $value['primary'] = $primaryKey;
                unset($value['obatalkes_id']);
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

    public function actionEdit()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $obatalkes_id = DocoHelpers::decrypt($id);
        $obatalkes = $this->getObat($obatalkes_id);

        $title = \Yii::t('fe', 'Ubah Base Price Harga');
        $model = new BasePrice;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        
        if ($request->post()) {
            $model->load($request->post());
            $model->attributes = $request->post();
            // $model->scenario = 'upload_file';
            $model->ket_ubah_harga = DocoConstants::PERUBAHAN_MANUAL;
            $errors = DocoHelpers::parseError($model->errors, $formName);
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('base-price/ubah-base-price?id='.$id, [
                        'query' => ['id' => $id ],
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(), True);
                    return DocoHelpers::response($body);
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

        $model->obatalkes_id = $obatalkes_id;
        $model->obatalkes_nama = $obatalkes['obatalkes_nama'];
        $model->hn_last = DocoHelpers::formatNumber($obatalkes['harganetto_ygdipakai']) . ' / ' . $obatalkes['satuankecil_nama'];
        $model->harga_sugesstion = DocoHelpers::formatNumber($obatalkes['harga_sugesstion']);
        $model->catatan = "";

        return $this->renderAjax('form', get_defined_vars());
    }

    public function actionGetDataObatImport()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $user_login = Yii::$app->user->identity->loginpemakai_id;
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        try {
            $getData = Yii::$app->cache->get("upload-base-price-".$user_login);
            $no = $request->get('start', 1);
            foreach ($getData as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['obatalkes_id']);
                $value['primary'] = $primaryKey;
                $value['hn_last'] = !empty($value['hn_last']) ? DocoHelpers::formatNumber($value['hn_last']) : 0;
                $value['harga_sugesstion'] = !empty($value['harga_sugesstion']) ? DocoHelpers::formatNumber($value['harga_sugesstion']) : 0;
                $value['last_harganetto'] = !empty($value['last_harganetto']) ? DocoHelpers::formatNumber($value['last_harganetto']) : 0;
                $value['harganetto'] = !empty($value['harganetto']) ? DocoHelpers::formatNumber($value['harganetto']) : 0;
                unset($value['obatalkes_id']);

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }
            
            $result['data'] = $data;
            $result['recordsTotal'] = count($getData);
            $result['recordsFiltered'] = $no;
            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionUploadBasePrice()
    {
        $request = Yii::$app->request;
        $user_login = Yii::$app->user->identity->loginpemakai_id;
        $model = new BasePrice;
        $formName = substr(strrchr(get_class($model), "\\"), 1);        
        $getData = Yii::$app->cache->get("upload-base-price-".$user_login);
        if (empty($getData)) {
            $response['response']['text'] = 'Tidak Data Obat yang akan di simpan.';
            $response['response']['title'] = 'Proses Gagal!';
            return DocoHelpers::response($response, 422);
        }

        try {
            $data = ['data_obat' => $getData
                    ];
            $response = $this->_restMaster->post('base-price/upload-base-price', [
                'query' => [],
                'form_params' => $data
            ]);
            $body = json_decode($response->getBody(), True);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
       
       
    }

    public function actionImportData()
    {
        try {
            $user_login = Yii::$app->user->identity->loginpemakai_id;
            $request = Yii::$app->request;
            $getData = Yii::$app->cache->get("upload-base-price-".$user_login);
            if ($getData) {
                Yii::$app->cache->delete("upload-base-price-".$user_login);
            }
            return $this->renderAjax('_import_form',get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }


    public function actionUpload()
    {
        $user_login = Yii::$app->user->identity->loginpemakai_id;
        $request = Yii::$app->request;
        $getData = Yii::$app->cache->get("upload-base-price-".$user_login);
        if ($getData) {
            Yii::$app->cache->delete("upload-base-price-".$user_login);
        }
        $model = new UploadForm();
        $model->upload_file = UploadedFile::getInstance($model, 'upload_file');
        if ($model->upload_file == NULL) {
            Yii::$app->cache->set("upload-base-price-".$user_login, []);
            $result = [];

            $response['response']['file'] = '';
            $response['response']['data'] = $result;
            $response['response']['status'] = 422;
            $response['response']['title'] = 'Proses Gagal';
            $response['response']['text'] = 'Format yang di Upload tidak sesuai, harus berupa xls, xlsx';

            return DocoHelpers::response($response, 200);
        }
        $fileName = $model->upload_file->name;
        $file = $model->upload_file->tempName;
        if (isset($model->upload_file)) {
            $getDataImport=  DocoHelpers::getUploadFileExcel($file);
            $data = [];
            for($row=1; $row <= $getDataImport['highestRow']; $row++){
                $rowData = $getDataImport['sheet']->rangeToArray('B'.$row.':'.$getDataImport['highestColumn'].$row,Null,true, false);
                if($row==1){
                    continue;
                }

                $model = new BasePrice();
                $model->obatalkes_id = $rowData[0][0];
                $model->obatalkes_nama = $rowData[0][1];
                $model->hn_last = $rowData[0][2];
                $model->harga_sugesstion = $rowData[0][3];
                $model->last_harganetto = $rowData[0][4];
                $model->harganetto =  $rowData[0][5];
                $model->ket_ubah_harga = DocoConstants::PERUBAHAN_MANUAL;
                $data[] = $model->attributes;
            }
            unset($data[0]);
            unset($data[1]);
            unset($data[2]);
            $result = [];
            $nomor = 0;
            foreach ($data as $key => $value) {
                if(  is_float($value['harganetto']) ) {
                    $status = true;
                    if (is_float($value['harganetto'])) {
                        $harganetto = !empty($value['harganetto']) ? DocoHelpers::formatNumber($value['harganetto']) : 0;
                    }else{
                        $harganetto = NULL;
                        $status = false;
                    }

                    if ($value['obatalkes_id'] == NULL) {
                        $status = false;
                    }

                    // continue;
                    $nomor ++;
                    $value['nomor'] = $nomor;
                    $value['obatalkes_id'] = $value['obatalkes_id'];
                    $value['hn_last'] = !empty($value['hn_last']) ? DocoHelpers::formatNumber($value['hn_last']) : 0;
                    $value['harga_sugesstion'] = !empty($value['harga_sugesstion']) ? DocoHelpers::formatNumber($value['harga_sugesstion']) : 0;
                    $value['last_harganetto'] = !empty($value['last_harganetto']) ? DocoHelpers::formatNumber($value['last_harganetto']) : 0; 
                    $value['harganetto'] = $harganetto;
                    $value['ket_ubah_harga'] = DocoConstants::PERUBAHAN_MANUAL;
                    $value['status'] = $status;
                    $result[] = $value;
                }
            }
            Yii::$app->cache->set("upload-base-price-".$user_login, $result);
            $getData = Yii::$app->cache->get("upload-base-price-".$user_login);

            $response['response']['file'] = $fileName;
            $response['response']['data'] = $result;
            $response['response']['status'] = 200;
            $response['response']['title'] = 'Proses Berhasil';
            $response['response']['text'] = 'Data Berhasil di upload!';

            return DocoHelpers::response($response ,200);
        }
        else {
            Yii::$app->cache->set("upload-base-price-".$user_login, []);
            $response['response']['title'] = 'Proses Gagal!';
            $response['response']['text'] = 'File gagal di upload.';
            return DocoHelpers::response($response, 422);
        }
    }

    private function getObat($id)
    {
        try {
            $request = $this->_restMaster->get('base-price/get-obat?id='.$id);
            $response = json_decode($request->getBody(), true);
            $result = $response['response'];

            return $result;
        } catch (Exception $e) {
            return [];
        }
    }

    public function actionDetail()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $obatalkes_id = DocoHelpers::decrypt($id);
            $encryptObatalkes_id = DocoHelpers::encrypt($obatalkes_id);
            $obatalkes = $this->getObat($obatalkes_id);
       
            return $this->renderAjax('_detail_form',get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetDataObat()
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
        try {
            $response = $this->_restMaster->get('base-price/get-data-obat?obatalkes_id='.$request->get('obatalkes_id').'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $responData = $body['response']['data'];
            foreach ($responData as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['obatalkes_id']);
                $value['primary'] = $primaryKey;
                $value['harga_dasar'] = !empty($value['harga_dasar']) ? DocoHelpers::formatNumber($value['harga_dasar']) : 0;
                $value['tgl_obathistory'] = date('d F Y H:i:s', strtotime($value['tgl_obathistory']));
                unset($value['obatalkes_id']);

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

    public function getDetailHistoryObat($obatalkes_id = null)
    {
        try {
             $response = $this->_restMaster->get('base-price/detail-history-obat', [
                            'query' => ['obatalkes_id' => $obatalkes_id]
                        ]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $return = $body['response'];

            return $return;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/master-base-price.xlsx";
            $response = $this->_restMaster->get('base-price/export-excel?' .http_build_query($yiiRestfulParams),[
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

    public function actionExportDownloadExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/template-master-base-price.xlsx";
            $response = $this->_restMaster->get('base-price/download-excel?'.http_build_query($yiiRestfulParams),[
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
        
        $path = Yii::getAlias("@download") . "/base-price.pdf";
        try {
            $response = $this->_restMaster->get('base-price/export-pdf?'.http_build_query($yiiRestfulParams),[
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

    public function actionCetakDetailObat()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = $request->get('obatalkes_id');
        $getHistoryObat= $this->getDetailHistoryObat($id);
        
        try {
            if (empty($getHistoryObat)) {
                $response['response']['title'] = 'Proses Gagal!';
                $response['response']['text'] = 'Tidak Ada Data .';
                return $this->redirect('/master/base-price/#');
            }else{
                $path = Yii::getAlias("@download") . "/cetak-detail-base-price.xlsx";
                $response = $this->_restMaster->get('base-price/export-excel-detail?obatalkes_id='.$id,[
                    'save_to' => $path,
                ]);
                $body = json_decode($response->getBody(), true);
                $url = $body['response'];
                
                return DocoHelpers::downloadFile($path,true);
            }
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e){
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}
