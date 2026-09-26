<?php
// Author : Dede Herdiana

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoSelect2Trait;
use GuzzleHttp\Exception\RequestException;
use app\modules\master\models\TarifAkomodasiBedahForm;
use app\modules\master\models\SetDefaultForm;
use app\modules\master\models\AddComponent;

class TarifAkomodasiBedahController extends DocoController
{
    use DocoSelect2Trait;

    protected $_title = "Master :: Tarif Akomodasi Bedah";
    protected $_module = 'master/tarif-akomodasi-bedah';
    protected $_restMaster;
    protected $_status;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_status = [  1 => 'Aktif',
                            0=> 'Tidak Aktif',
                        ];
    }

    public function actions()
    {
        $request = Yii::$app->request;
        return [
            'get-data-perda' => [
                'class' => 'app\modules\master\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'perda-tarif/index',
                'module' => $this->_module,
                'keyField' => 'perdatarif_id',
                'requestMethod' => 'GET'
            ],
            'get-data-tarif' => [
                'class' => 'app\modules\master\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'tarif-akomodasi-bedah/index',
                'module' => $this->_module,
                'keyField' => 'tarifbedah_id',
                'requestMethod' => 'GET'
            ],
            'create-tarif' => [
                'class' => 'app\modules\master\components\actions\CreateModalAction',
                'serviceName' => $this->_restMaster,
                'serviceCreateAction' => 'tarif-akomodasi-bedah/create',
                'modelForm' => new TarifAkomodasiBedahForm,
                'viewForm' => 'tarif/form',
                'additional_data' => $this->_packTarif()
            ],
            'update-tarif' => [
                'class' => 'app\modules\master\components\actions\UpdateModalAction',
                'serviceName' => $this->_restMaster,
                'serviceUpdateAction' => 'tarif-akomodasi-bedah/update',
                'serviceViewAction' => 'tarif-akomodasi-bedah/view',
                'module' => $this->_module,
                'modelForm' => new TarifAkomodasiBedahForm,
                'viewForm' => 'tarif/form',
                'additional_data' => $this->_packTarif()
            ],
        ];
    }

    public function actionIndex()
    {
        $status = $this->_status;
        return $this->render('index', get_defined_vars());
    }

    public function actionTarif()
    {
        $status = $this->_status;
        $request = Yii::$app->request;
        try {
            $additional_data = $this->_packTarif();
            $kegiatanoperasi = isset($additional_data['kegiatanoperasi']) ? $additional_data['kegiatanoperasi'] : [];
            $kelaspelayanan = isset($additional_data['kelaspelayanan']) ? $additional_data['kelaspelayanan'] : [];
            $perdatarif = isset($additional_data['perdatarif']) ? $additional_data['perdatarif'] : [];
            return $this->renderAjax('_tarif',get_defined_vars());
        } catch (Exception $e) {
            return [];
        }
    }

    public function actionExportExcel($type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if($type == 'perda'){
            $url = 'perda-tarif/export-excel?'.http_build_query($yiiRestfulParams);
            $path = Yii::getAlias("@download") . "/master-perda-tarif-tindakan.xlsx";
        }else if($type == 'tarif'){
            $url = 'tarif-tindakan/export-excel?'.http_build_query($yiiRestfulParams);
            $path = Yii::getAlias("@download") . "/master-tarif-tindakan.xlsx";
        }else{
            $url = 'komponen-tarif/export-excel?'.http_build_query($yiiRestfulParams);
            $path = Yii::getAlias("@download") . "/master-komponen-tarif-tindakan.xlsx";
        }

        try {
            $response = $this->_restMaster->get($url,[
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

    public function actionExportPdf($type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if($type == 'perda'){
            $url = 'perda-tarif/export-pdf?'.http_build_query($yiiRestfulParams);
            $path = Yii::getAlias("@download") . "/master-perda-tarif-tindakan.pdf";
        }else if($type == 'tarif'){
            $url = 'tarif-tindakan/export-pdf?'.http_build_query($yiiRestfulParams);
            $path = Yii::getAlias("@download") . "/master-tarif-tindakan.pdf";
        }else{
            $url = 'komponen-tarif/export-pdf?'.http_build_query($yiiRestfulParams);
            $path = Yii::getAlias("@download") . "/master-komponen-tarif-tindakan.pdf";
        }
        try {
            $response = $this->_restMaster->get($url,[
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

    
    public function actionFormTarif()
    {
        try {
            $opsiPerda = [];
            $opsipaket = [];
            $opsitindakan = [];
            $counter = 0;
            $tipepaket = '';
            $title = Yii::t('fe', 'Tambah');
            $model = new TarifAkomodasiBedahForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $model->is_active = 1;
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                
                $model->load($post);
                $model->persen_cyto =(float) DocoHelpers::convertToAngka($model->persen_cyto);

                if ($model->validate()) {
                    
                    try {
                        $response = $this->_restMaster->post('tarif-akomodasi-bedah/save-tarif',[
                                        'form_params' => $model->attributes
                                ]);
                        $response = json_decode($response->getBody(),true);
                        return DocoHelpers::response($response);
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
            $additional_data = $this->_packTarif();
            $status_edit = 0;
            $is_akomodasi = 0;
            $kamarruangan_id = $kamar = '';

            return $this->renderAjax('tarif/form',get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionFormTarifEdit()
    {
        try {
            $request = Yii::$app->request;
           
            $opsiPerda = [];
          
            $counter = 0;
            $total_id = '';
            $tipepaket = '';
            $title = Yii::t('fe', 'Ubah');
            $model = new TarifAkomodasiBedahForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $id = DocoHelpers::decrypt($request->get('id'));
            if ($request->post()) {
                $post = $request->post();
                
                $model->load($post);
                if ($model->validate()) {
                    try {
                        $response = $this->_restMaster->put('tarif-akomodasi-bedah/update-tarif?id='.$id,[
                                        'form_params' => $request->post()
                                ]);
                        $response = json_decode($response->getBody(),true);
                        return DocoHelpers::response($response);
                    } catch (RequestException $e) {
                        return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                    } catch (\Exception $e) {
                        return DocoHelpers::responseTemplate(500, $e->getMessage());
                    }
                }else{
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            }else{
                $response = $this->_restMaster->get('tarif-akomodasi-bedah/get-data-tarif?id='.$id, []);
                $body = json_decode($response->getBody(), true);
                $model->attributes = isset($body['response']) ? $body['response']: [];
                $model->is_active = $body['response']['is_active'] ? 1 : 0;
                $model->persen_cyto = str_replace('.', ',', $model->persen_cyto );
                $additional_data = $this->_packTarif();
                $status_edit = 1;
                
                return $this->renderAjax('tarif/form',get_defined_vars());
            }
            
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionDelete($id){
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($request->get('id'));
            try {
                $response = $this->_restMaster->post('tarif-akomodasi-bedah/delete-tarif?id='.$id,[]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response);
            } catch (\Exception $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }
    }

    private function _packTarif()
    {
        try {
            $response = $this->_restMaster->get('tarif-akomodasi-bedah/pack-tarif', ['form_params'=>[]]);
            $body = json_decode($response->getBody(), true);
            $body = $body['response'];
            $kegiatanoperasi = isset($body['kegiatanoperasi']) ? ArrayHelper::map($body['kegiatanoperasi'], 'kegiatanoperasi_id', 'kegiatanoperasi_nama') : [];
            $perdatarif = isset($body['perdatarif']) ? ArrayHelper::map($body['perdatarif'], 'perdatarif_id', 'perdanama_sk') : [];
            $kelaspelayanan = isset($body['kelaspelayanan']) 
                                ? (($body['kelaspelayanan']) 
                                    ? ArrayHelper::map($body['kelaspelayanan'], 'kelaspelayanan_id', 'kelaspelayanan_nama')
                                    : []) 
                                : [];
            
            $result = [
                'kegiatanoperasi'=>$kegiatanoperasi,
                'kelaspelayanan'=>$kelaspelayanan,
                'perdatarif'=>$perdatarif,
            ];
            return $result;
        } catch (RequestException $e) {
            $result = [
                'carabayar'=> [],
                'kelaspelayanan'=> [],
                'jenis_tindakan_paket'=> []
            ];
            return $result;
        } catch (\Exception $e) {
            $result = [
                'carabayar'=> [],
                'kelaspelayanan'=> [],
                'jenis_tindakan_paket'=> []
            ];
            return $result;
        }
    }

}