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
use app\modules\master\models\KlasifikasiKamarForm;
use app\components\Services\BpjsAplicaresService;

class KlasifikasiKamarController extends DocoController
{
    protected $_title = "Klasifikasi";
    protected $_module = 'master/klasifikasi-kamar/';
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
        $response = $this->_restMaster->get('klasifikasi-kamar/get-data-select');
        $body = json_decode($response->getBody(), TRUE);
        $sirs = $body['response']['sirs'];
        $eis = $body['response']['eis'];
        $applicare = $body['response']['applicare'];
        $spdgt = $body['response']['spdgt'];

        $listReferensiAplicare = (new BpjsAplicaresService)->getReferensiKamarAplicare();
        $listReferensiAplicare = ArrayHelper::map($listReferensiAplicare, 'namakelas', 'namakelas');
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
            if (isset($yiiRestfulParams['advanced-filter'])){
                $filter = $yiiRestfulParams['advanced-filter'];

                if (isset($filter['sirsonline_nama'])){
                    $sirsonline_id = $filter['sirsonline_nama'];
                    $yiiRestfulParams['advanced-filter']['sirsonline_id'] = $sirsonline_id;
                    unset($yiiRestfulParams['advanced-filter']['sirsonline_nama']);
                }

                if (isset($filter['eiscovid_nama'])){
                    $eiscovid_id = $filter['eiscovid_nama'];
                    $yiiRestfulParams['advanced-filter']['eiscovid_id'] = $eiscovid_id;
                    unset($yiiRestfulParams['advanced-filter']['eiscovid_nama']);
                }

                if (isset($filter['applicare_nama'])){
                    $applicare_id = $filter['applicare_nama'];
                    $yiiRestfulParams['advanced-filter']['applicare_id'] = $applicare_id;
                    unset($yiiRestfulParams['advanced-filter']['applicare_nama']);
                }

                if (isset($filter['spgdt_nama'])){
                    $spgdt_id = $filter['spgdt_nama'];
                    $yiiRestfulParams['advanced-filter']['spgdt_id'] = $spgdt_id;
                    unset($yiiRestfulParams['advanced-filter']['spgdt_nama']);
                }
            }
            $response = $this->_restMaster->get('klasifikasi-kamar/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['klasifikasikamar_id']);
                $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);
                    
                $value['primary'] = $primaryKey;
                unset($value['klasifikasikamar_id']);
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


    public function actionCreate()
    {
        // Init
        $status = $this->_status; $options = $this->_options;

        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_title);
        $model = new KlasifikasiKamarForm;

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('klasifikasi-kamar/create-klasifikasi', [
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(), True);
                    return DocoHelpers::response($body);

                } catch (RequestException $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'KlasifikasiKamarForm');
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restMaster->get('klasifikasi-kamar/get-data-select');
            $body = json_decode($response->getBody(), TRUE);

            $sirs = $body['response']['sirs'];
            $eis = $body['response']['eis'];
            $applicare = $body['response']['applicare'];
            $spdgt = $body['response']['spdgt'];

            $listReferensiAplicare = (new BpjsAplicaresService)->getReferensiKamarAplicare();
            $listReferensiAplicare = ArrayHelper::map($listReferensiAplicare, 'kodekelas', 'namakelas');

            return $this->renderAjax('form', get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        // Init
        $status = $this->_status; 
        $options = $this->_options;
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new KlasifikasiKamarForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('klasifikasi-kamar/update-klasifikasi?id='.$id, [
                        'query' => ['id' => $id ],
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(), True);
                    return DocoHelpers::response($body);

                } catch (RequestException $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'KlasifikasiKamarForm');
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restMaster->get('klasifikasi-kamar/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            $model->klasifikasikamar_id = $attributes['klasifikasikamar_id'];

            $response = $this->_restMaster->get('klasifikasi-kamar/get-data-select');
            $body = json_decode($response->getBody(), TRUE);
            $sirs = $body['response']['sirs'];
            $eis = $body['response']['eis'];
            $applicare = $body['response']['applicare'];
            $spdgt = $body['response']['spdgt'];

            $listReferensiAplicare = (new BpjsAplicaresService)->getReferensiKamarAplicare();
            $listReferensiAplicare = ArrayHelper::map($listReferensiAplicare, 'kodekelas', 'namakelas');
            
            return $this->renderAjax('form', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->get('klasifikasi-kamar/delete-klasifikasi',
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

        $path = Yii::getAlias("@download") . "/klasifikasi-kamar.pdf";
        try {
            $response = $this->_restMaster->get('klasifikasi-kamar/export-pdf?'.http_build_query($yiiRestfulParams),[
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
        $path = Yii::getAlias("@download") . "/master-klasifikasi-kamar.xlsx";
        try {
            $response = $this->_restMaster->get('klasifikasi-kamar/export-excel', [
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

    public function actionChangeStatus()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $id = DocoHelpers::decrypt($id);
        $status = $request->get('status');

        $response = $this->_restMaster->post('klasifikasi-kamar/change-status', [
            'query' => ['id' => $id ],
            'form_params' => ["is_active" => $status]
        ]);
        $response = json_decode($response->getBody(),true);
        
        return DocoHelpers::response($response);
    }

    public function actionShowPopupExcel()
    {
        $request = Yii::$app->request;
        $title = 'Laporan Excel Klasifikasi Kamar';
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSyncExcel($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        $data = $this->guzzleExec($this->_restMaster, [
            'url' => "klasifikasi-kamar/export-excel-bgproses",
            'payload' => [
                'query' => $session
            ],
        ]);
        return $data;
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'Laporan Excel Klasifikasi Kamar '.date("dmY").'.xlsx';
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $this->_restMaster->get('klasifikasi-kamar/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path,true);
    }

}
