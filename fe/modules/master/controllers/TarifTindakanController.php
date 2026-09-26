<?php
// Author : Ardi Pratama

namespace Doco\master\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoSelect2Trait;
use GuzzleHttp\Exception\RequestException;
use app\modules\master\models\KomponenTarifForm;
use app\modules\master\models\PerdaTarifForm;
use app\modules\master\models\JenisTarifForm;
use app\modules\master\models\TarifTindakanForm;
use app\modules\master\models\SetDefaultForm;
use app\modules\master\models\AddComponent;

class TarifTindakanController extends DocoController
{
    use DocoSelect2Trait;

    protected $_title = "Master :: Tarif Tindakan";
    protected $_module = 'master/tarif-tindakan';
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
        // dump($request->get());die;
        return [
            'get-data-komponen' => [
                'class' => 'app\modules\master\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'komponen-tarif/index',
                'module' => $this->_module,
                'keyField' => 'komponentarif_id',
                'requestMethod' => 'GET'
            ],
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
                'serviceAction' => 'tarif-tindakan/index',
                'module' => $this->_module,
                'keyField' => 'tariftindakan_id',
                'requestMethod' => 'GET'
            ],
            'create-komponen' => [
                'class' => 'app\modules\master\components\actions\CreateModalAction',
                'serviceName' => $this->_restMaster,
                'serviceCreateAction' => 'komponen-tarif/create',
                'modelForm' => new KomponenTarifForm,
                'viewForm' => 'komponen/form',
            ],
            'create-perda' => [
                'class' => 'app\modules\master\components\actions\CreateModalAction',
                'serviceName' => $this->_restMaster,
                'serviceCreateAction' => 'perda-tarif/create',
                'modelForm' => new PerdaTarifForm,
                'viewForm' => 'perda/form'
            ],
            'create-tarif' => [
                'class' => 'app\modules\master\components\actions\CreateModalAction',
                'serviceName' => $this->_restMaster,
                'serviceCreateAction' => 'tarif-tindakan/create',
                'modelForm' => new TarifTindakanForm,
                'viewForm' => 'tarif/form',
                'additional_data' => $this->_packTarif()
            ],
            'update-komponen' => [
                'class' => 'app\modules\master\components\actions\UpdateModalAction',
                'serviceName' => $this->_restMaster,
                'serviceUpdateAction' => 'komponen-tarif/update',
                'serviceViewAction' => 'komponen-tarif/view',
                'requestUpdateMethod' => 'POST',
                'module' => $this->_module,
                'modelForm' => new KomponenTarifForm,
                'viewForm' => 'komponen/form'
            ],
            'update-perda' => [
                'class' => 'app\modules\master\components\actions\UpdateModalAction',
                'serviceName' => $this->_restMaster,
                'serviceUpdateAction' => 'perda-tarif/update',
                'serviceViewAction' => 'perda-tarif/view',
                'requestUpdateMethod' => 'POST',
                'module' => $this->_module,
                'modelForm' => new PerdaTarifForm,
                'viewForm' => 'perda/form'
            ],
            'update-tarif' => [
                'class' => 'app\modules\master\components\actions\UpdateModalAction',
                'serviceName' => $this->_restMaster,
                'serviceUpdateAction' => 'tarif-tindakan/update',
                'serviceViewAction' => 'tarif-tindakan/view',
                'module' => $this->_module,
                'modelForm' => new TarifTindakanForm,
                'viewForm' => 'tarif/form',
                'additional_data' => $this->_packTarif()
            ],
            'delete-komponen' => [
                'class' => 'app\modules\master\components\actions\DeleteModalAction',
                'serviceName' => $this->_restMaster,
                'serviceDeleteAction' => 'komponen-tarif/delete',
            ],
            'delete-perda' => [
                'class' => 'app\modules\master\components\actions\DeleteModalAction',
                'serviceName' => $this->_restMaster,
                'serviceDeleteAction' => 'perda-tarif/delete',
                'serviceMethod'=>'POST'
            ],
            'delete-tarif' => [
                'class' => 'app\modules\master\components\actions\DeleteModalAction',
                'serviceName' => $this->_restMaster,
                'serviceDeleteAction' => 'tarif-tindakan/delete',
                // 'serviceMethod'=>'POST'
            ],
            'get-data-kamar' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'tarif-tindakan/get-data-kamar',
                'data_name' => [
                    'ruangan_nama',
                    'kamarruangan_nokamar'
                ],
                'keyField' => 'kamarruangan_id'
            ],
            'get-list-komponen' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'tarif-tindakan/get-list-komponen',
                'data_name' => [
                    'komponentarif_kode',
                    'komponentarif_nama',
                ],
                'keyField' => 'komponentarif_id'
            ],
        ];
    }

    public function _getJenisKomponen()
    {
        $jenisKomponen = ['is_dokter' => 'Dokter',
                        'is_perawat' => 'Perawat',
                        'is_fisioterapis' => 'Fisioterapis',
                        'is_dietisien' => 'Dietisien',
                        'is_radiografer' => 'Radiografer'
                    ];
        return $jenisKomponen;
    }

    public function _getStatus()
    {
        return $this->_status;
    }

    public function _getOptions()
    {
        return $this->_options;
    }

    public function actionIndex()
    {
        $status = $this->_status;
        return $this->render('index', get_defined_vars());
    }

    public function actionKomponen()
    {
        $status = $this->_status; $options = $this->_options;
        $request = Yii::$app->request;
        try {
            return $this->renderAjax('_komponen',get_defined_vars());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400,Yii::t("fe","Terdapat kesalahan"));
        }
    }

    public function actionPerda()
    {
        $status = $this->_status; $options = $this->_options;
        $request = Yii::$app->request;
        try {
            return $this->renderAjax('_perda',get_defined_vars());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400,Yii::t("fe","Terdapat kesalahan"));
        }
    }

    public function actionJenisTarif()
    {
        $status = $this->_status; $options = $this->_options;
        $request = Yii::$app->request;
        try {
            return $this->renderAjax('_jenis_tarif',get_defined_vars());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400,Yii::t("fe","Terdapat kesalahan"));
        }
    }

    public function actionTarif()
    {
        $status = $this->_status; $options = $this->_options;
        $request = Yii::$app->request;
        $jenis_tarif = [
            1 => 'Semua Tarif',
            2 => 'Tarif Kamar',
            3 => 'Tarif Dokter'
        ];
        try {
            $additional_data = $this->_packTarif();
            $kamar = isset($additional_data['kamar']) ? $additional_data['kamar'] : [];
            return $this->renderAjax('_tarif',get_defined_vars());
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getListPenjamin()
    {
        try{
            $response = $this->_restMaster->get('allow/list-penjamin');
            $response = json_decode($response->getBody(),true);
            $list_penjamin = $response['response'];
            return $list_penjamin;
        } catch(RequestException $e){
            return null;
        }
    }

    public function actionCetakTarif()
    {
        // $id = DocoHelpers::decrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        
        $path = Yii::getAlias("@download") . "/cetak-tarif-tindakan.pdf";
        try {
            $response = $this->_restMaster->get('tarif-tindakan/print-tarif-tindakan',[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            // var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            // var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
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

    public function actionGetPenjamin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restMaster->get('allow/list-penjamin?carabayar_id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $key => $value) 
                $result['output'][] = [
                    'id' => $key, 
                    'name' => $value
                ];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }
    public function actionGetCarabayar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restMaster->get('allow/list-carabayar?penjamin_id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $key => $value) 
                if(isset($value['caraBayar']) && count($value['caraBayar']) > 0){
                    $result['output'][] = [
                        'id' => $value['caraBayar']['carabayar_id'], 
                        'name' => $value['caraBayar']['carabayar_nama']
                    ];
                }
                
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }
    public function actionGetPerda()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restMaster->request('POST', 'tarif-tindakan/get-perda',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['perdatarif_id'],'text'=>$value['perda_no'].' - '.$value['perdanama_sk']];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }

    public function actionGetListKomponen()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 10;
        $offset = ($page-1)*5;
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $result = $this->_restMaster->get('tarif-tindakan/get-list-komponen',[
                'query' => [
                    // 'term' => $request->get('term'),
                    'term' => $request->get('q'),
                    'page'=>$page,
                    'offset'=>$offset,
                    'limit'=>$limit
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                            'id'=>$value['komponentarif_id'],
                            'text'=>$value['komponentarif_kode']. ' - '. $value['komponentarif_nama'], 
                            'datavalue'=>$value 
                        ];
            }
        } catch (RequestException $e) {
            $response['message'] = $e->getMessage();
        }
        return DocoHelpers::response([
            'result' => $response,
            'pagination' => [ 'more' => !empty($data)?true:false ]
        ]);
    }

    public function actionGetKomponen()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restMaster->request('POST', 'tarif-tindakan/get-komponen',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                            'id'=>$value['komponentarif_id'],
                            'text'=>$value['komponentarif_kode']. ' - '. $value['komponentarif_nama'], 
                            'datavalue'=>$value 
                        ];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetPaket()
    {
        $request = Yii::$app->request;
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $term = $request->get('q')['term'];
            $response = $this->_restMaster->request('POST', 'tarif-tindakan/get-paket',[
                'form_params'=>['term'=>$term]
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id'=>$value['tipepaket_id'],
                    'text'=>$value['tipepaket_kode']. ' - '. $value['tipepaket_nama'], 
                    'datavalue'=>$value 
                ];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetTindakan()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $response = $this->_restMaster->get('tarif-tindakan/get-tindakan', [
            'query' => $get
        ]);
        $body = json_decode($response->getBody(), true);
        $data = [];
        foreach ($body['response'] as $key => $value) {
            $data[] = [
                        'id'=>$value['daftartindakan_id'],
                        'text'=>$value['daftartindakan_kode']. ' - '. $value['daftartindakan_nama'], 
                        'datavalue'=>$value, 
                        'kelompoktindakan_persencyto'=>$value['kelompoktindakan_persencyto'], 
                        'kelompoktindakan_persendiskon'=>$value['kelompoktindakan_persendiskon'], 
                        'is_akomodasi' => $value['is_akomodasi'],
                    ];
        }
        $total = count($body['response']);
        $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
        return DocoHelpers::response($return);
    }
    public function actionGetTindakanPaket()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restMaster->request('POST', 'tarif-tindakan/get-tindakan-paket',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['nama_tindakan_paket'],'text'=>$value['nama_tindakan_paket'] ];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    
    public function actionGetDataPaketDetail($id = null, $is_mcu = null, $kelaspelayanan_id = null, $penjamin_id = null, $perdatarif_id = null, $status_edit = false)
    {
        $id = DocoHelpers::encrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $url = 'tarif-tindakan/detail-paket-tindakan?tipepaket_id='.$id.'&is_mcu='.$is_mcu.'&kelaspelayanan_id='.$kelaspelayanan_id.'&penjamin_id='.$penjamin_id.'&perdatarif_id='.$perdatarif_id.'&status_edit='.$status_edit;
        $response = $this->_restMaster->get($url);
        $body = json_decode($response->getBody(), true);
        return $body['response'];
    }

    public function actionGetDataPaketDetailEdit($id = null, $is_mcu = null)
    {
        $id = DocoHelpers::encrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $url = 'tarif-tindakan/detail-paket-tindakan?tipepaket_id='.$id.'&is_mcu='.$is_mcu;
        $response = $this->_restMaster->get($url);
        $body = json_decode($response->getBody(), true);
        return $body['response'];
    }
    
    public function actionFormTarif()
    {
        try {
            $komponen = [];
            $opsiPenjamin = [];
            $opsiPerda = [];
            $opsipaket = [];
            $opsitindakan = [];
            $opsiKelas = [];
            $opsiCaraBayar = [];
            $opsiDokter = [];
            $counter = 0;
            $tipepaket = '';
            $title = Yii::t('fe', 'Tambah');
            $model = new TarifTindakanForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $model->is_active = 1;
            $model->is_persentase = 1;
            $request = Yii::$app->request;
            $id = "";
            $response = $this->_restMaster->get('tarif-tindakan/get-default-komponen', []);
            $response = json_decode($response->getBody(), true);
            $response = ArrayHelper::getValue($response, 'response', []);
            $komponenRs = [
                'komponentarif_id' => ArrayHelper::getValue($response, 'komponentarif_id'),
                'komponentarif_nama' => ArrayHelper::getValue($response, 'komponentarif_nama'),
                'komponentarif_kode' => ArrayHelper::getValue($response, 'komponentarif_kode'),
            ];
            $komponenRs = json_encode($komponenRs);
            if ($request->post()) {
                $post = $request->post();
                $model->load($post);
                $model->persencyto_tindakan =(float) $this->helper->convertToAngka($model->persencyto_tindakan);
                $model->persen_penyulit =(float) $this->helper->convertToAngka($model->persen_penyulit);
                $is_akomodasi = ((int) $post['is_akomodasi'] == 1) ? true : false;
                $isPersentase = ($model->is_persentase == 1) ? false : true;
                $model->is_persentase = $isPersentase;
                if($is_akomodasi) {
                    $model->scenario = $model::AKOMODASI;
                }
                if ($model->validate()) {
                    if (!empty($model->daftartindakan_id)) {
                        if (is_array($model->komponentarif_id)) {
                            foreach ($model->komponentarif_id as $key => $value) {
                                $tmp_nominal = isset($model->harga_tariftindakan[$key]) ? $model->harga_tariftindakan[$key] : 0;
                                $tmp_persentase = isset($model->persentase[$key]) ? $model->persentase[$key] : 0;
                                $model->list_komponen[] = [
                                    'komponen_id' => $value,
                                    'nominal' => $tmp_nominal,
                                    'persentase' => $tmp_persentase,
                                    'is_persentase' => $isPersentase
                                ];
                            }
                        }
                    }else{
                        $model->list_komponen = json_decode($post['list_komponen'],true);
                        $tmp_list_komponen = [];
                        if (is_array($model->list_komponen)) {
                            foreach ($model->list_komponen as $key => $value) {
                                $row = [
                                    'daftartindakan_id' => $value['daftartindakan_id'],
                                    'komponen' => [],
                                    'ruangan_id' => $value['ruangan_id'],
                                ];
                                if (is_array($value['komponen'])) {
                                    foreach ($value['komponen'] as $key1 => $value1) {
                                        $row['komponen'][] = [
                                            'ruangan_id' => ArrayHelper::getValue($value,'ruangan_id'),
                                            'komponen_id' => ArrayHelper::getValue($value1,'komponen_id'),
                                            'nominal' => ArrayHelper::getValue($value1,'nominal_angka'),
                                            'persentase' => ArrayHelper::getValue($value1,'persentase_komponen'),
                                        ];
                                    }
                                }
                                $tmp_list_komponen[] = $row;
                            }
                        }
                        $model->list_komponen = $tmp_list_komponen;
                    }
                    try {
                        $response = $this->_restMaster->post('tarif-tindakan/save-tarif',[
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
            $jsonKomponen = json_encode($komponen);
            $additional_data = $this->_packTarif();
            $status_edit = 0;
            $is_akomodasi = 0;
            $kamarruangan_id = $kamar = '';
            $daftartindakanId = $penjaminId = $kelasId = $tipepaketId = $dokterId = $perdaId = '';
            $urlLog = 'daftartindakan_id='.$daftartindakanId.'&penjamin_id='.$penjaminId.'&kelaspelayanan_id='.$kelasId.'&tipepaket_id='.$tipepaketId.'&dokter_id='.$dokterId.'&perda_id='.$perdaId;
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
            $komponen = [];
            $opsiPenjamin = [];
            $opsiPerda = [];
            $opsipaket = [];
            $opsitindakan = [];
            $opsiKelas = [];
            $opsiCaraBayar = [];
            $opsiDokter = [];
            $counter = 0;
            $total_id = '';
            $tipepaket = '';
            $title = Yii::t('fe', 'Ubah');
            $model = new TarifTindakanForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $id = $this->helper->decrypt($request->get('id'));
            $response = $this->_restMaster->get('tarif-tindakan/get-data-tarif-tindakan?id='.$id, []);
            $body = json_decode($response->getBody(), true);
            $header = isset($body['response']['header']) ? $body['response']['header'] : [];
            $penjaminId = !empty($header['penjamin_id']) ? $header['penjamin_id'] : '';
            $kelasId = !empty($header['kelaspelayanan_id']) ? $header['kelaspelayanan_id'] : '';
            $dokterId = !empty($header['dokter_id']) ? $header['dokter_id'] : '';
            $perdaId = !empty($header['perdatarif_id']) ? $header['perdatarif_id'] : '';
            $kamarruangan_id = isset($header['kamarruangan_id']) ? $header['kamarruangan_id'] : null;
            $komponenRs = json_encode([]);
            if ($request->post()) {
                $post = $request->post();
                $model->load($post);
                $model->persencyto_tindakan =(float) $this->helper->convertToAngka($model->persencyto_tindakan);
                $model->persen_penyulit =(float) $this->helper->convertToAngka($model->persen_penyulit);
                $is_akomodasi = ((int) $post['is_akomodasi'] == 1) ? true : false;
                if($is_akomodasi) {
                    $model->scenario = $model::AKOMODASI;
                }
                $model->kelaspelayanan_id = $kelasId;
                $model->carabayar_id = $header['carabayar_id'];
                $model->penjamin_id = $penjaminId;
                $model->dokter_id = $dokterId;
                $model->perdatarif_id = $perdaId;
                $model->kamar_ruangan_id = $kamarruangan_id;
                $isPersentase = ($model->is_persentase == 1) ? false : true;
                $model->is_persentase = $isPersentase;
                if ($model->validate()) {
                    if (!empty($model->daftartindakan_id)) {
                        if (is_array($model->komponentarif_id)) {
                            foreach ($model->komponentarif_id as $key => $value) {
                                $tmp_nominal = isset($model->harga_tariftindakan[$key]) ? $model->harga_tariftindakan[$key] : 0;
                                $tmp_persentase = isset($model->persentase[$key]) ? $model->persentase[$key] : 0;
                                $model->list_komponen[] = [
                                    'komponen_id' => $value,
                                    'nominal' => $tmp_nominal,
                                    'persentase' => $tmp_persentase,
                                    'is_persentase' => $isPersentase,
                                ];
                            }
                        }
                    }else{
                        $model->list_komponen = json_decode($post['list_komponen'],true);
                        $tmpListKomponen = [];
                        if (is_array($model->list_komponen)) {
                            foreach ($model->list_komponen as $key => $value) {
                                $tindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
                                $ruanganId = ArrayHelper::getValue($value, 'ruangan_id');
                                $harga = ArrayHelper::getValue($value, 'harga', 0);
                                $hargaOrigin = ArrayHelper::getValue($value, 'harga_origin', 0);
                                $row = [
                                    'daftartindakan_id' => $tindakanId,
                                    'ruangan_id' => $ruanganId,
                                    'komponen' => []
                                ];
                                if (is_array($value['komponen'])) {
                                    $totalHargaKomponen = 0;
                                    foreach ($value['komponen'] as $key1 => $value1) {
                                        $komponenId = ArrayHelper::getValue($value1, 'komponen_id');
                                        $nominalAngka = ArrayHelper::getValue($value1, 'nominal_angka', 0);
                                        $persentase = ArrayHelper::getValue($value1,'persentase_komponen');
                                        $row['komponen'][] = [
                                            'komponen_id' => $komponenId,
                                            'nominal' => $nominalAngka,
                                            'ruangan_id' => $ruanganId,
                                            'persentase' => $persentase,
                                        ];
                                        $totalHargaKomponen += (float)$nominalAngka;
                                    }
                                    if(($persentase && $hargaOrigin) && round(($hargaOrigin*($persentase/100)) != round($totalHargaKomponen))){
                                        $response = [
                                            'response' => [
                                                'title' => 'Proses Gagal.',
                                                'text' => 'Salah satu tindakan/pemeriksaan nilai penyesuiannya tidak valid!',
                                                'status' => 422,
                                            ]
                                        ];
                                        return DocoHelpers::response($response, 422, false);
                                    }
                                }
                                $tmpListKomponen[] = $row;
                            }
                        }
                        $model->list_komponen = $tmpListKomponen;
                    }
                    try {
                        $response = $this->_restMaster->put('tarif-tindakan/update-tarif?id='.$id,[
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
                    $msg =  'Error';
                    if($model->tipepaket_id){
                        /** GET ERROR MESSAFE VALIDASI */
                        if(is_array($model->errors)){
                            foreach($model->errors as $k => $v){
                                $msg = !empty($v[0]) ? $v[0] : 'Error';
                                $response = [
                                    'response' => [
                                        'title' => 'Proses Gagal.',
                                        'text' => 'Nilai Tarif paket tidak boleh Nol!',
                                        'status' => 422,
                                    ]
                                ];
                            return DocoHelpers::response($response, 422, false);
                            }
                        }
                    }
                    return DocoHelpers::responseTemplate(422, $msg, $errors);
                }
            } else{ 
                $daftartindakanId = !empty($header['daftartindakan_id']) ? $header['daftartindakan_id'] : '';
                $tipepaketId = !empty($header['tipepaket_id']) ? $header['tipepaket_id'] : '';
                $urlLog = 'daftartindakan_id='.$daftartindakanId.'&penjamin_id='.$penjaminId.'&kelaspelayanan_id='.$kelasId.'&tipepaket_id='.$tipepaketId.'&dokter_id='.$dokterId.'&perda_id='.$perdaId;
                $is_akomodasi = isset($body['response']['is_akomodasi']) ? $body['response']['is_akomodasi'] : false;
                $total = isset($body['response']['total']) ? $body['response']['total'] : 0;
                $model->attributes = $header;
                $komponen = isset($body['response']['komponen']) ? $body['response']['komponen'] : [];
                $counter = count($komponen);
                $jsonKomponen = json_encode($komponen);
                $is_akomodasi = ($is_akomodasi) ? 1 : 0;
                $model->is_akomodasi = $is_akomodasi;
                $model->kamar_ruangan_id = $kamarruangan_id;
                $kamar = isset($header['kamar']) ? $header['kamar'] : null;
                $model->tindakanpaket = isset($header['jenis_tindakan_paket']) ? $header['jenis_tindakan_paket'] : '';
                $tipepaket = isset($header['jenis_tindakan_paket']) ? $header['jenis_tindakan_paket'] : '';
                $model->is_active = $header['is_active'] ? 1 : 0;
                $model->persencyto_tindakan = str_replace('.', ',', $model->persencyto_tindakan );
                $model->persendiskon_tindakan = str_replace('.', ',', $model->persendiskon_tindakan );
                $model->persen_penyulit = str_replace('.', ',', $model->persen_penyulit );
                $model->total_harga_tindakan = DocoHelpers::formatNumber($header['harga_tariftindakan']);
                $model->is_persentase = ($model->is_persentase == true) ? 0 : 1;
                $additional_data = $this->_packTarif();
                $status_edit = 1;
                $opsiPenjamin = isset($header['penjamin_id']) ? ['id'=>$header['penjamin_id'], 'text'=> $header['penjamin_nama']] : [];
                $opsiPerda = isset($header['perdatarif_id']) ? ['id'=>$header['perdatarif_id'], 'text'=> $header['perdanama_sk']] : [];
                $tipepaket_kode = isset($header['tipepaket_kode']) ? $header['tipepaket_kode'] : '';
                $opsitindakan = isset($header['tindakan_paket_id']) ? 
                    [ 
                        'id' => $header['tindakan_paket_id'], 
                        'text'=>  (isset($header['daftartindakan_kode']) ? $header['daftartindakan_kode'] : $tipepaket_kode) ." - ". $header['nama_tindakan_paket']
                    ] : [];
                $opsiKelas = isset($header['kelaspelayanan_id']) ? ['id'=>$header['kelaspelayanan_id'], 'text'=> $header['kelaspelayanan_nama']] : [];
                $opsiCaraBayar = isset($header['carabayar_id']) ? ['id'=>$header['carabayar_id'], 'text'=> $header['carabayar_nama']] : [];
                $opsiDokter = isset($header['dokter_id']) ? ['id'=>$header['dokter_id'], 'text'=> $header['dokter']] : [];
                $model->penjamin_id = 1;
                $tmpPenjaminId = !empty($header['penjamin_id']) ? $header['penjamin_id'] : null;
                $tmpKelasPelayanan = !empty($header['kelaspelayanan_id']) ? $header['kelaspelayanan_id'] : null;
                
                return $this->renderAjax('tarif/form',get_defined_vars());
            }
            
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionSetDefault()
    {
        try {
            $title = Yii::t('fe', 'Set default');
            $model = new SetDefaultForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                $model->load($post);
                if ($model->validate()) {
                    // return DocoHelpers::response($model->attributes);
                    try {
                        $response = $this->_restMaster->request('POST', 'tarif-tindakan/set-default',[
                                        'form_params' => $model->attributes
                                ]);
                        $response = json_decode($response->getBody(),true);
                        return DocoHelpers::response($response,false,true);
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
            $penjamin = $this->getListPenjamin();
            return $this->renderAjax('_setdefault',get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    private function _packTarif()
    {
        try {
            $response = $this->_restMaster->get('allow/pack-tarif', ['form_params'=>[]]);
            $body = json_decode($response->getBody(), true);
            $body = $body['response'];
            $jenisTindakanPaket = ['TINDAKAN'=>'TINDAKAN', 'PAKET'=>'PAKET'] ;
            $carabayar = isset($body['carabayar']) ? ArrayHelper::map($body['carabayar'], 'carabayar_id', 'carabayar_nama') : [];
            $perdatarif = isset($body['perdatarif']) ? ArrayHelper::map($body['perdatarif'], 'perdatarif_id', 'perdanama_sk') : [];
            // $komponen = isset($body['komponen']) ? ArrayHelper::map($body['komponen'], 'komponentarif_id', 'komponentarif_nama') : [];
            $kelaspelayanan = isset($body['kelaspelayanan']) 
                                ? (($body['kelaspelayanan']) 
                                    ? ArrayHelper::map($body['kelaspelayanan'], 'kelaspelayanan_id', 'kelaspelayanan_nama')
                                    : []) 
                                : [];
            $resKomponen = [];
            if (isset($body['komponen'])) {
                foreach ($body['komponen'] as $key => $value) {
                    $resKomponen[$value['komponentarif_id']] = $value['komponentarif_kode'].' - '.$value['komponentarif_nama'];
                }
            }
            $resKamar = isset($body['kamar']) 
                                ? (($body['kamar']) 
                                    ? ArrayHelper::map($body['kamar'], 'kamarruangan_id', 'kamarruangan_nokamar')
                                    : []) 
                                : [];
            
            $all_option = [0 => 'Semua Kamar'];
            $resKamar = $all_option + $resKamar;
            $result = [
                'carabayar'=>$carabayar,
                'kelaspelayanan'=>$kelaspelayanan,
                'perdatarif'=>$perdatarif,
                'jenis_tindakan_paket'=>$jenisTindakanPaket,
                'komponen'=>$resKomponen,
                'kamar'=>$resKamar
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

    public function actionUpdateActived()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        try {
            $perdatarif_id_before = $request->get('perdatarif_id_before');
            $perdatarif_id_now = $request->get('perdatarif_id_now');
            $data = [
                    'perdatarif_id_before' => $perdatarif_id_before,
                    'perdatarif_id_now' => $perdatarif_id_now,
                    ];
            $response = $this->_restMaster->get('perda-tarif/update-actived',[
                            'query' => $data
                    ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionAddComponent($id = null, $is_mcu = false)
    {
        $title = Yii::t('fe', 'Tambah Komponen');
        $model = new AddComponent;
        $getIDKomponen = explode('-', $id);
        $tipepaket_id = DocoHelpers::encrypt($getIDKomponen[0]);
        $tindakan_paket_id = DocoHelpers::encrypt($getIDKomponen[1]);
        $list_id = $getIDKomponen[2];
        if($is_mcu){
            $title = Yii::t('fe', 'Detail Komponen');
            return $this->renderAjax('tarif/add_component_mcu', get_defined_vars()); // untuk saat ini komponen mcu dipisah, karena komponennya tidak bisa diedit /ditambah
        }
        return $this->renderAjax('tarif/add_component', get_defined_vars());
    }

    public function actionViewLog()
    {
        $request = Yii::$app->request;
        $daftartindakan_id = $request->get('daftartindakan_id', null);
        $penjamin_id = $request->get('penjamin_id', null);
        $kelaspelayanan_id = $request->get('kelaspelayanan_id', null);
        $tipepaket_id = $request->get('tipepaket_id', null);
        $dokter_id = $request->get('dokter_id', null);
        $perda_id = $request->get('perda_id', null);
        $urlLog = 'daftartindakan_id='.$daftartindakan_id.'&penjamin_id='.$penjamin_id.'&kelaspelayanan_id='.$kelaspelayanan_id.'&tipepaket_id='.$tipepaket_id.'&dokter_id='.$dokter_id.'&perda_id='.$perda_id;
        return $this->renderAjax('tarif/_log',get_defined_vars());
    }

    public function actionGetDataLog()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $daftartindakan_id = $request->get('daftartindakan_id', null);
        $penjamin_id = $request->get('penjamin_id', null);
        $kelaspelayanan_id = $request->get('kelaspelayanan_id', null);
        $tipepaket_id = $request->get('tipepaket_id', null);
        $dokter_id = $request->get('dokter_id', null);
        $perda_id = $request->get('perda_id', null);
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['daftartindakan_id'] = $daftartindakan_id;
        $payload['penjamin_id'] = $penjamin_id;
        $payload['kelaspelayanan_id'] = $kelaspelayanan_id;
        $payload['tipepaket_id'] = $tipepaket_id;
        $payload['dokter_id'] = $dokter_id;
        $payload['perda_id'] = $perda_id;
        $response = $this->guzzleExec($this->_restMaster, [
            'url' => 'tarif-tindakan/get-data-log',
            'method' => 'get',
            'payload' => [
                'query' => $payload,
            ]
        ]);
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }

    public function actionShowPopupExcel()
    {
        $title = 'Download Excel Master Tarif Tindakan';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('komponen/_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restMaster, [
            'url' => "tarif-tindakan/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Tarif Tindakan.xlsx';
        $path = Yii::getAlias("@download") . '/' . $fileDownloads;
        $response = $this->_restMaster->get('tarif-tindakan/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path, true);
    }
}