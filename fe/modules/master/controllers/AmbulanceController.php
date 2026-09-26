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
use app\modules\master\models\AmbulanForm;
use GuzzleHttp\Exception\RequestException;

class AmbulanceController extends DocoController
{
    protected $_title = "Ambulan";
    protected $_module = '/master/ambulance/';
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
        try {
            $title = $this->_title;
            $user_login = Yii::$app->user->identity->loginpemakai_id;
            Yii::$app->cache->delete('tarif-ambulance-'.$user_login);
            Yii::$app->cache->delete('obat-ambulance-'.$user_login);
            $response = $this->getRequest();
            $is_active = $this->_options['status'];
            unset($is_active['']);
            $jenisAmbulan = $response['jenisAmbulan'];
        
        } catch (Exception $e) {
            $jenisAmbulan = [];
            $is_active = [];
        }

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
            $response = $this->_restMaster->get('ambulance/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['ambulan_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['ambulan_id']);
                    $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);

                    $value['rowNum'] = $no;
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
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

    private function getRequest()
    {
        try {
            $request = $this->_restMaster->request('GET', 'ambulance/generate-api');
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];

            return $attributes;
        } catch (RequestException $e) {
            $result['ruangan'] = [];
            $result['status_jenazah'] = [];
            $result['jenis_kelamin'] = [];
            return $result;
        } catch (\Exception $e) {
            $result['ruangan'] = [];
            $result['status_jenazah'] = [];
            $result['jenis_kelamin'] = [];
            return $result;
        }
    }

    private function getDaftarTindakan($daftartindakan_id)
    {
        try {
            $request = $this->_restMaster->request('GET', 'ambulance/get-tindakan?daftartindakan_id='.$daftartindakan_id);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];

            return $attributes;
        } catch (RequestException $e) {
            $result[] = [];
            return $result;
        } catch (\Exception $e) {
            $result[] = [];
            return $result;
        }
    }

    private function getObat($obatalkes_id)
    {
        $request = $this->_restMaster->request('GET', 'ambulance/get-obat?obatalkes_id='.$obatalkes_id);
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $attributes;
    }

    public function actionDetail($id)
    {
        $title = 'Detail Ambulan';
        try {
            $id = DocoHelpers::decrypt($id);
            $response = $this->_restMaster->request('GET', 'ambulance/view', [
                'query' => ['id' => $id ]
            ]);
            $body = json_decode($response->getBody(),true);
            $response = $body['response']; 
            $header = $response['header'];
            $tindakan = $response['tindakan'];
            $obat = $response['obat'];
            return $this->render('view', get_defined_vars());
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actionCetak($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/detail-ambulan.pdf";
        try {
            $response = $this->_restMaster->get('ambulance/print',[
                'query' => [
                        'id' => $id
                ],
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

    public function actionCreate()
    {

        $request = Yii::$app->request;
        $user_login = Yii::$app->user->identity->loginpemakai_id;
        $model = new AmbulanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = 'Tambah Ambulan';
        $response = $this->getRequest();
        $cache = Yii::$app->cache;
        $barang = isset($response['barang']) ? ArrayHelper::map($response['barang'], 'barang_id', 'barang_nama') : [];
        $jenisAmbulan = isset($response['jenisAmbulan']) ? $response['jenisAmbulan'] : [];
        
        $model->is_emergency = DocoConstants::NON_EMERGENCY;
        if($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post($formName);
            $model->attributes = $post;
            $model->is_emergency = ($model->is_emergency == DocoConstants::EMERGENCY) ? true : false;
            
            $cacheTindakan = $cache->get("tarif-ambulance-".$user_login);
            $cacheObat = $cache->get("obat-ambulance-".$user_login);

            if($model->is_emergency == true) {
                if(empty($cacheTindakan)) {
                    $response['response']['text'] = 'Tarif Tidak Boleh Kosong.';
                    $response['response']['title'] = 'Proses Gagal!';
                    return DocoHelpers::response($response, 422);
                }
                else {
                    $listBiayaTetap = [];
                    $is_biaya = false;
                    foreach ($cacheTindakan as $key => $value) {
                        $listBiayaTetap[] = $value['biaya_tetap'];
                    }
                    if(in_array(true, $listBiayaTetap)) {
                        $is_biaya = true;
                    }
                    
                    if($is_biaya == false) {
                        $response['response']['text'] = 'Biaya Tetap Harus di Pilih Salah Satu.';
                        $response['response']['title'] = 'Proses Gagal!';
                        return DocoHelpers::response($response, 422);
                    }
                }

                if(empty($cacheObat)) {
                    $response['response']['text'] = 'Obat Alkes Tidak Boleh Kosong.';
                    $response['response']['title'] = 'Proses Gagal!';
                    return DocoHelpers::response($response, 422);
                }
            }
            else {
                if(empty($cacheTindakan)) {
                    $response['response']['text'] = 'Tarif Tidak Boleh Kosong.';
                    $response['response']['title'] = 'Proses Gagal!';
                    return DocoHelpers::response($response, 422);
                }
                else {
                    $listBiayaTetap = [];
                    $is_biaya = false;
                    foreach ($cacheTindakan as $key => $value) {
                        $listBiayaTetap[] = $value['biaya_tetap'];
                    }
                    if(in_array(true, $listBiayaTetap)) {
                        $is_biaya = true;
                    }
                    
                    if($is_biaya == false) {
                        $response['response']['text'] = 'Biaya Tetap Harus di Pilih Salah Satu.';
                        $response['response']['title'] = 'Proses Gagal!';
                        return DocoHelpers::response($response, 422);
                    }
                }
            }
            if($model->validate()) {
                try {
                    $result = $this->_restMaster->post('ambulance/save',[
                        'form_params' => [
                            'cacheTindakan' => json_encode($cacheTindakan),
                            'cacheObat' => json_encode($cacheObat),
                            'data' => $model,
                        ]
                    ]);
                    $result = json_decode($result->getBody(),true);
                    $status_response = $result['metadata']['status'];
                    if($status_response == 200) {
                        $cache->delete('tarif-ambulance-'.$user_login);
                    }
                    if($status_response  == 200) {
                        $cache->delete('obat-ambulance-'.$user_login);
                    }
                    return DocoHelpers::response($result, false, $formName);
                } catch (RequestException $e) {
                    Yii::info($e->getMessage());
                    $response['response']['text'] = 'Terjadi kesalah pada sistem';
                    $response['response']['message'] = $e->getMessage();
                    return DocoHelpers::response($response, 500);
                }
            }
            else {
                return DocoHelpers::response($model->errors, 422, $formName);
            }
        }
        Yii::$app->cache->delete('obat-ambulance-'.$user_login);
        Yii::$app->cache->delete('tarif-ambulance-'.$user_login);
        return $this->render('form', get_defined_vars());
    }

    private function dataAmbulance( $id ){
        try {
            $yiiRestfulParams['id'] = $id;
            $response = $this->_restMaster->get('ambulance/view?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $resData = $body['response']['obat'];
            
            return $resData;
        } catch (Exception $e) {
            return [];
        }
    }

    public function actionUpdate($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $user_login = Yii::$app->user->identity->loginpemakai_id;
        
        $model = new AmbulanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = 'Edit Ambulan';
        $response = $this->getRequest();
        $cache = Yii::$app->cache;
        $data = $this->getData($id);
        $model->attributes = isset($data['header']) ? $data['header'] : '';
        $model->barang_nama = isset($data['header']) ? $data['header']['barang_nama'] : '';
        $barang = isset($response['barang']) ? ArrayHelper::map($response['barang'], 'barang_id', 'barang_nama') : [];
        $jenisAmbulan = isset($response['jenisAmbulan']) ? $response['jenisAmbulan'] : [];
        
        if($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post($formName);
            $model->attributes = $post;
            $model->is_emergency = ($model->is_emergency == DocoConstants::EMERGENCY) ? true : false;

            $cacheTindakan = $cache->get("tarif-ambulance-".$user_login.'-'.$id);
            $cacheObat = $cache->get("obat-ambulance-".$user_login.'-'.$id);
            if($model->is_emergency == true) {
                if(empty($cacheTindakan)) {
                    $response['response']['text'] = 'Tarif Tidak Boleh Kosong.';
                    $response['response']['title'] = 'Proses Gagal!';
                    return DocoHelpers::response($response, 422);
                }
                else {
                    $listBiayaTetap = [];
                    $is_biaya = 0;
                    foreach ($cacheTindakan as $key => $value) {
                        if (!empty($value['is_default'])) {
                            $is_biaya++;
                        }
                    }
                    if($is_biaya == 0) {
                        $response['response']['text'] = 'Biaya Tetap Harus di Pilih Salah Satu.';
                        $response['response']['title'] = 'Proses Gagal!';
                        return DocoHelpers::response($response, 422);
                    }
                }

                $dataObat = $this->_restMaster->get('ambulance/view?id='.$id, ['form_params' => []]);
                $bodyObat = json_decode($dataObat->getBody(), true);
                $responseObat = $bodyObat['response']['obat'];

                if(empty($cacheObat) && empty($responseObat)) {
                    $response['response']['text'] = 'Obat Alkes Tidak Boleh Kosong.';
                    $response['response']['title'] = 'Proses Gagal!';
                    return DocoHelpers::response($response, 422);
                }
            }
            else {
                if(empty($cacheTindakan)) {
                    $response['response']['text'] = 'Tarif Tidak Boleh Kosong.';
                    $response['response']['title'] = 'Proses Gagal!';
                    return DocoHelpers::response($response, 422);
                }
                else {
                    $listBiayaTetap = [];
                    $is_biaya = 0;
                    foreach ($cacheTindakan as $key => $value) {
                        if (!empty($value['is_default'])) {
                            $is_biaya++;
                        }
                    }
                    if($is_biaya == 0) {
                        $response['response']['text'] = 'Biaya Tetap Harus di Pilih Salah Satu.';
                        $response['response']['title'] = 'Proses Gagal!';
                        return DocoHelpers::response($response, 422);
                    }
                }
            }
            // echo "<pre>";var_dump($cacheTindakan);die();
            if($model->validate()) {
                try {
                    $result = $this->_restMaster->post('ambulance/save-update',[
                        'form_params' => [
                            'cacheTindakan' => json_encode($cacheTindakan),
                            'cacheObat' => json_encode($cacheObat),
                            'data' => json_encode($model->attributes),
                            'ambulan_id' => $id,
                        ]
                    ]);
                    $result = json_decode($result->getBody(),true);
                    if($result['metadata']['status'] == 200) {
                        if($cacheTindakan  == true) {
                            $cache->delete('tarif-ambulance-'.$user_login);
                        }
                        if($cacheObat  == true) {
                            $cache->delete('obat-ambulance-'.$user_login);
                        }
                    }
                    
                    return DocoHelpers::response($result, false);
                } catch (RequestException $e) {
                    Yii::info($e->getMessage());
                    $response['response']['text'] = 'Terjadi kesalah pada sistem';
                    $response['response']['message'] = $e->getMessage();
                    return DocoHelpers::response($response, 500);
                }
            }
        }
        Yii::$app->cache->delete('tarif-ambulance-'.$user_login.'-'.$id);
        Yii::$app->cache->delete('obat-ambulance-'.$user_login.'-'.$id);        
        /* List data Obat*/
        $cacheObat = $cache->get("obat-ambulance-".$user_login.'-'.$id);
         if($cacheObat == false) {
            if (!empty($data['obat'])) {
                $resData = [];
                $no_urut = 0;
                foreach ($data['obat'] as $key => $value) {
                    $no_urut++;
                    $primaryKey = $value['ambulandetail_id'];
                    $resData[$value['obatalkes_id']] = [
                        'rowNum' => $no_urut,
                        'obatalkes_id' => $value['obatalkes_id'],
                        'obatalkes_nama' => $value['obatalkes_nama'],
                        'qty' => $value['qty'].' '.$value['satuanunit_nama'],
                        'ambulan_id' => DocoHelpers::encrypt($id),
                        'aksi' => Html::button(
                            "<i class='fa fa-trash'></i>",[
                                'style' => 'margin-right:5px',
                                'class' => 'btn btn-danger btn-xs delete-cache-obat',
                                'style' => 'margin-right:5px; padding-left:10px !important;',
                                'data-ambulanceid' => DocoHelpers::encrypt($id),
                                'data-id' => $primaryKey,
                                'data-action' => Url::to([$this->_module .'delete-cache-obat','id' => $primaryKey]),
                            ]
                        )
                    ];
                }

                Yii::$app->cache->set("obat-ambulance-".$user_login.'-'.$id, $resData);
            }else{
                Yii::$app->cache->set("obat-ambulance-".$user_login.'-'.$id, []);
            }
        }
        
        /* List data Tindakan*/
        $cacheTindakan = $cache->get("tarif-ambulance-".$user_login.'-'.$id);
        if($cacheTindakan == false) {
            if (!empty($data['tindakan'])) {
                $resData = [];
                $no_urut = 0;
                foreach ($data['tindakan'] as $key => $value) {
                    $no_urut++;
                    $primaryKey = DocoHelpers::encrypt($key);
                    $resData[$value['daftartindakan_id']] = [
                        'is_default' => !empty($value['is_default']) ? 1 : 0,
                        'rowNum' => $no_urut,
                        'ambulan_id' => DocoHelpers::encrypt($id),
                        'daftartindakan_id' => isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : "",
                        'daftartindakan_nama' => isset($value['daftartindakan_nama']) ? $value['daftartindakan_nama'] : "",
                        'biaya_tetap' => Html::checkbox('Tindakan['.$value['daftartindakan_id'].'][biaya_tetap]', $value['is_default'], ['class' => 'chk_tindakan xxxx', 'data-id' => $value['daftartindakan_id']]),
                        'aksi' => Html::button(
                            "<i class='fa fa-trash'></i>",[
                                'style' => 'margin-right:5px',
                                'class' => 'btn btn-danger btn-xs delete-cache-tindakan',
                                'style' => 'margin-right:5px; padding-left:10px !important;',
                                'data-ambulanceid' => DocoHelpers::encrypt($id),
                                'data-id' => $primaryKey,
                                'data-action' => Url::to([$this->_module .'delete-cache-tindakan','id' => $primaryKey]),
                            ]
                        )
                    ];
                }
                Yii::$app->cache->set("tarif-ambulance-".$user_login.'-'.$id, $resData);
            }else{
                Yii::$app->cache->set("tarif-ambulance-".$user_login.'-'.$id, []);
            }
        }
        $ambulan_id = DocoHelpers::encrypt($id);

        return $this->render('form_edit', get_defined_vars());
    }

    private function getData($id)
    {
        try {
            $request = $this->_restMaster->request('GET', 'ambulance/view?id='.$id);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
            return $attributes;
        } catch (RequestException $e) {
            $result[] = [];
            return $result;
        } catch (\Exception $e) {
            $result[] = [];
            return $result;
        }
    }

    public function actionSearchTindakan()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/get-data-tindakan', [
                            'query' => [
                                'term' => $request->get('term'),
                            ]
                        ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['daftartindakan_id'],
                    'text' => $value['daftartindakan_nama'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionSearchObat()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/get-data-obat-alkes', [
                            'query' => [
                                'term' => $request->get('term'),
                            ]
                        ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_nama'],
                    'satuan_kecil' => $value['satuan_kecil'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    private function saveCacheTindakan($cacheAmbulanceTarif, $user_login, $daftartindakan_id, $daftarTindakan, $post){
        if ($cacheAmbulanceTarif == false) {
            Yii::$app->cache->set("tarif-ambulance-".$user_login,[]);
            $cacheAmbulanceTarif = [];
        }
        if (!isset($cacheAmbulanceTarif[$daftartindakan_id])) {
            $cacheAmbulanceTarif[$daftartindakan_id] = [];
        }

        $setItem = [
            'daftartindakan_id' => $daftartindakan_id,
            'daftartindakan_nama' => ($daftarTindakan) ? $daftarTindakan['daftartindakan_nama'] : '',
            'biaya_tetap' => false,
            'is_default' => false,
            'ambulan_id' => isset($post['ambulan_id']) ? $post['ambulan_id'] : '-',
        ];
        $cacheAmbulanceTarif[$daftartindakan_id] = $setItem;
        
        return $cacheAmbulanceTarif;
    }

    public function actionSetCache()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $daftartindakan_id = $request->post('daftartindakan_id');
        $setItem = [];
        try {
            $daftarTindakan = $this->getDaftarTindakan($daftartindakan_id);
            $user_login = Yii::$app->user->identity->loginpemakai_id;
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            /*check if create and update method */
            if (!empty($request->post('ambulan_id')) ) {
                $ambulanceId = $request->post('ambulan_id');
                $ambulanceIds = DocoHelpers::decrypt($ambulanceId);
                
                $cacheAmbulanceTarif = Yii::$app->cache->get("tarif-ambulance-".$user_login.'-'.$ambulanceIds);
                $cacheAmbulanceTarif = $this->saveCacheTindakan($cacheAmbulanceTarif, $user_login, $daftartindakan_id, $daftarTindakan, $post);
                Yii::$app->cache->set("tarif-ambulance-".$user_login.'-'.$ambulanceIds,$cacheAmbulanceTarif);

            }else{
                $cacheAmbulanceTarif = Yii::$app->cache->get("tarif-ambulance-".$user_login);
                $cacheAmbulanceTarif = $this->saveCacheTindakan($cacheAmbulanceTarif, $user_login, $daftartindakan_id, $daftarTindakan, $post);
                Yii::$app->cache->set("tarif-ambulance-".$user_login,$cacheAmbulanceTarif,3600);
            }
            
            if ($cacheAmbulanceTarif == true) {
                $response['response'] = [
                    'title' => 'Proses Berhasil !',
                    'text' => 'Data berhasil di tambah'
                ];
            }else{
                $response['response'] = [
                    'title' => 'Proses Gagal !',
                    'text' => 'Data gagal di tambah'
                ];
            }

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function saveCacheObat($cacheAmbulance, $user_login, $obatalkes_id, $obatalkes, $post){
        if ($cacheAmbulance == false) {
            Yii::$app->cache->set("obat-ambulance-".$user_login,[]);
            $cacheAmbulance = [];
        }
        if (!isset($cacheAmbulance[$obatalkes_id])) {
            $cacheAmbulance[$obatalkes_id] = [];
        }
        
        $setItem = [
            'obatalkes_id' => $obatalkes_id,
            'obatalkes_nama' => ($obatalkes) ? $obatalkes['obatalkes_nama'] : '',
            'qty' => $post['qty'],
            'ambulan_id' => isset($post['ambulan_id']) ? $post['ambulan_id'] : '-',
            'satuan_kecil' => isset($post['satuan_kecil']) ? $post['satuan_kecil'] : '',
        ];

        $cacheAmbulance[$obatalkes_id] = $setItem;
        
        return $cacheAmbulance;
    }

    public function actionSetCacheObat()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $obatalkes_id = $post['obatalkes_id'];
        $qty = $post['qty'];
        $setItem = [];
        try {
            if(empty($obatalkes_id)) {
                $response['response']['text'] = 'Obat Alkes harus diisi.';
                $response['response']['title'] = 'Proses Gagal!';
                return DocoHelpers::response($response, 422);
            }
            elseif(empty($qty)) {
                $response['response']['text'] = 'Qty harus diisi.';
                $response['response']['title'] = 'Proses Gagal!';
                return DocoHelpers::response($response, 422);
            }
            else {
                $obatalkes = $this->getObat($obatalkes_id);
                $user_login = Yii::$app->user->identity->loginpemakai_id;
                /*check if create and update method */
                if (!empty($request->post('ambulan_id')) ) {
                    $ambulanceId = $request->post('ambulan_id');
                    $ambulanceIds = DocoHelpers::decrypt($ambulanceId);

                    $cacheAmbulance = Yii::$app->cache->get("obat-ambulance-".$user_login.'-'.$ambulanceIds);
                    $cacheAmbulance = $this->saveCacheObat($cacheAmbulance, $user_login, $obatalkes_id, $obatalkes, $post);
                    Yii::$app->cache->set("obat-ambulance-".$user_login.'-'.$ambulanceIds,$cacheAmbulance);
                }else{
                    $cacheAmbulance = Yii::$app->cache->get("obat-ambulance-".$user_login);
                    $cacheAmbulance = $this->saveCacheObat($cacheAmbulance, $user_login, $obatalkes_id, $obatalkes, $post);

                    Yii::$app->cache->set("obat-ambulance-".$user_login,$cacheAmbulance,3600);
                }

                if ($cacheAmbulance == true) {
                    $response['response'] = [
                        'title' => 'Proses Berhasil !',
                        'text' => 'Data berhasil di tambah'
                    ];
                }else{
                    $response['response'] = [
                        'title' => 'Proses Gagal !',
                        'text' => 'Data gagal di tambah'
                    ];
                }

                return DocoHelpers::response($response);
            }
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
        
    }

    public function actionGetListTindakan()
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $resetCache = [];
        $no_urut = $request->get('start', 1);

        $user_login = Yii::$app->user->identity->loginpemakai_id;
        /*check if create and update method */
        if (!empty($request->get('ambulan_id')) ) {
            $ambulan_id = DocoHelpers::decrypt($request->get('ambulan_id'));
            $cacheAmbulanceTindakan = Yii::$app->cache->get("tarif-ambulance-".$user_login.'-'.$ambulan_id);
            $result = $this->listDataTindakan($cacheAmbulanceTindakan, $no_urut, $draw);
        }else{
            $cacheAmbulanceTindakan = Yii::$app->cache->get("tarif-ambulance-".$user_login, false);
            $result = $this->listDataTindakan($cacheAmbulanceTindakan, $no_urut, $draw);
        }
        
        return $result;
    }

    private function listDataTindakan($data, $no_urut, $draw){
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $result['draw'] = $draw;
        $result['data'] = [];
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $no_urut++;
                $primaryKey = DocoHelpers::encrypt($key);
                $dataCache = [
                    'rowNum' => $no_urut,
                    'daftartindakan_id' => isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : "",
                    'daftartindakan_nama' => isset($value['daftartindakan_nama']) ? $value['daftartindakan_nama'] : "",
                    'biaya_tetap' => Html::checkbox('Tindakan['.$value['daftartindakan_id'].'][biaya_tetap]', $value['is_default'], ['class' => 'chk_tindakan xxxx', 'data-id' => $value['daftartindakan_id']]),
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete-cache-tindakan',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'data-ambulanceid' => !empty($value['ambulan_id']) ? $value['ambulan_id'] : '-',
                            'data-id' => $primaryKey,
                            'data-action' => Url::to([$this->_module .'delete-cache-tindakan','id' => $primaryKey]),
                        ]
                    )
                ];

                $resetCache[$value['daftartindakan_id']] = [
                    'daftartindakan_id' => $value['daftartindakan_id'],
                    'daftartindakan_nama' => ($value['daftartindakan_nama']) ? $value['daftartindakan_nama'] : '',
                    'biaya_tetap' => !empty($value['is_default']) ? 1 : 0,
                    'is_default' => !empty($value['is_default']) ? 1 : 0,
                ];
                $result['data'][] = $dataCache;
            }
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = $no_urut;
            $result['draw'] = $draw;
        }
        return $result;
    }

    public function actionGetListObat()
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $no_urut = $request->get('start', 1);
        $resetCache = [];
        $user_login = Yii::$app->user->identity->loginpemakai_id;

        /*check if create and update method */
        if (!empty($request->get('ambulan_id')) ) {
            $ambulan_id = DocoHelpers::decrypt($request->get('ambulan_id'));
            $cacheAmbulanceObat = Yii::$app->cache->get("obat-ambulance-".$user_login.'-'.$ambulan_id);

            $result = $this->listDataObat($cacheAmbulanceObat, $no_urut, $draw);
        }else{
            $cacheAmbulanceObat = Yii::$app->cache->get("obat-ambulance-".$user_login);
            $result = $this->listDataObat($cacheAmbulanceObat, $no_urut, $draw);
        }

        return $result;

    }

    private function listDataObat($data, $no_urut, $draw){
        
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $result['draw'] = $draw;
        $result['data'] = [];
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $no_urut++;
                $primaryKey = DocoHelpers::encrypt($key);
                $satuan = isset($value['satuan_kecil']) ? $value['satuan_kecil'] : '';
                $dataCache = [
                    'rowNum' => $no_urut,
                    'obatalkes_id' => $value['obatalkes_id'],
                    'obatalkes_nama' => $value['obatalkes_nama'],
                    'qty' => $value['qty'].' '.$satuan,
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete-cache-obat',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'data-ambulanceid' => !empty($value['ambulan_id']) ? $value['ambulan_id'] : '-',
                            'data-id' => $primaryKey,
                            'data-action' => Url::to([$this->_module .'delete-cache-obat','id' => $primaryKey]),
                        ]
                    )
                ];
                $resetCache[$value['obatalkes_id']] = [
                    'obatalkes_id' => $value['obatalkes_id'],
                    'obatalkes_nama' => ($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '',
                    'qty' => $value['qty'].' '.$satuan,
                ];
                $result['data'][] = $dataCache;
            }
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = $no_urut;
            $result['draw'] = $draw;
        }
        return $result;
    }

    public function actionDeleteCacheTindakan($id = null)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $user_login = Yii::$app->user->identity->loginpemakai_id;
        /*check if create and update method */
        if (!empty($request->get('ambulan_id')) ) {
            $ambulanceId = $request->get('ambulan_id');
            $ambulanceIds = DocoHelpers::decrypt($ambulanceId);

            $cacheAmbulanceTindakan = Yii::$app->cache->get("tarif-ambulance-".$user_login.'-'.$ambulanceIds);
            if ($cacheAmbulanceTindakan !== false) {
                if (isset($cacheAmbulanceTindakan[$id])) {
                    unset($cacheAmbulanceTindakan[$id]);
                    Yii::$app->cache->set("tarif-ambulance-".$user_login.'-'.$ambulanceIds,$cacheAmbulanceTindakan);
                }
                $cacheAmbulanceTindakan = Yii::$app->cache->get("tarif-ambulance-".$user_login.'-'.$ambulanceIds);
            }

        }else{
            $cacheAmbulanceTindakan = Yii::$app->cache->get("tarif-ambulance-".$user_login);
            if ($cacheAmbulanceTindakan !== false) {
                if (isset($cacheAmbulanceTindakan[$id])) {
                    unset($cacheAmbulanceTindakan[$id]);
                    Yii::$app->cache->set("tarif-ambulance-".$user_login,$cacheAmbulanceTindakan);
                }
            }
        }

        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionDeleteCacheObat($id = null)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $user_login = Yii::$app->user->identity->loginpemakai_id;

        /*check if create and update method */
        if (!empty($request->get('ambulan_id')) ) {
            $ambulanceId = $request->get('ambulan_id');
            $ambulanceIds = DocoHelpers::decrypt($ambulanceId);

            $cacheAmbulanceObat = Yii::$app->cache->get("obat-ambulance-".$user_login.'-'.$ambulanceIds);
            if ($cacheAmbulanceObat !== false) {
                if (isset($cacheAmbulanceObat[$id])) {
                    unset($cacheAmbulanceObat[$id]);
                    Yii::$app->cache->set("obat-ambulance-".$user_login.'-'.$ambulanceIds,$cacheAmbulanceObat);
                }
                $cacheAmbulanceObat = Yii::$app->cache->get("obat-ambulance-".$user_login.'-'.$ambulanceIds);
            }
        }else{
            $cacheAmbulanceObat = Yii::$app->cache->get("obat-ambulance-".$user_login);
            if ($cacheAmbulanceObat !== false) {
                if (isset($cacheAmbulanceObat[$id])) {
                    unset($cacheAmbulanceObat[$id]);
                    Yii::$app->cache->set("obat-ambulance-".$user_login,$cacheAmbulanceObat);
                }
                $cacheAmbulanceObat = Yii::$app->cache->get("obat-ambulance-".$user_login);
            }
        }

        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionSearchBarang()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/get-data-barang', [
                            'query' => [
                                'term' => $request->get('term'),
                            ]
                        ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['barang_id'],
                    'text' => $value['barang_nama'],
                    'merek' => $value['barang_merk'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'ambulance/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/Master Ambulan.xlsx";
        try {
            $response = $this->_restMaster->get($url, [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/ambulance.pdf";
            $response = $this->_restMaster->get('ambulance/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionDeleteAmbulan($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->request('DELETE', 'ambulance/delete-ambulan',[
                'query' => ['id' => $id ]
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionDeleteTindakanLama($id,$tindakan)
    {
        try {
            $response = $this->_restMaster->POST('ambulance/delete-tindakan-lama', [
                'query'=> [
                    'id' => $id
                ]]);
            $user_login = Yii::$app->user->identity->loginpemakai_id;
            $cacheAmbulanceTindakan = Yii::$app->cache->get("tarif-ambulance-".$user_login);
            if (isset($cacheAmbulanceTindakan[$tindakan])) {
                unset($cacheAmbulanceTindakan[$tindakan]);
                Yii::$app->cache->set("tarif-ambulance-".$user_login,$cacheAmbulanceTindakan);
            }

            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionDeleteObatLama($id)
    {
        try {
            $response = $this->_restMaster->POST('ambulance/delete-obat-lama', [
                'query'=>['id'=>$id]]);

            $response = json_decode($response->getBody(), true);

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionChangeStatus($id, $status)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->get('ambulance/ubah-status-ambulan', [
                'query' => [
                    'id' => $id,
                    'is_active' => $status
                ]
            ]);

            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionUpdateCacheBiayaTetap()
    {
        $request = Yii::$app->request;
        $biaya_tetap = $request->get('biaya_tetap');
        $daftartindakan_id = $request->get('daftartindakan_id');
        $setItem = [];
        try {
            $user_login = Yii::$app->user->identity->loginpemakai_id;
             /*check if create and update method */
            if (!empty($request->get('ambulan_id')) ) {
                $ambulanceId = $request->get('ambulan_id');
                $ambulanceIds = DocoHelpers::decrypt($ambulanceId);

                $cacheAmbulanceTindakan = Yii::$app->cache->get("tarif-ambulance-".$user_login.'-'.$ambulanceIds);
                if ($cacheAmbulanceTindakan !== false) {
                    if (isset($cacheAmbulanceTindakan[$daftartindakan_id])) {
                        $cacheAmbulanceTindakan[$daftartindakan_id]['is_default'] = $biaya_tetap;
                        $cacheAmbulanceTindakan[$daftartindakan_id]['biaya_tetap'] = 
                        Html::checkbox('Tindakan['.$daftartindakan_id.'][biaya_tetap]', $daftartindakan_id, [
                                    'class' => 'chk_tindakan xxxx', 
                                    'data-id' => $daftartindakan_id,
                                    'value' => $biaya_tetap,
                                    ]);
                        Yii::$app->cache->set("tarif-ambulance-".$user_login.'-'.$ambulanceIds,$cacheAmbulanceTindakan);
                    }
                    $cacheAmbulanceTindakan = Yii::$app->cache->get("tarif-ambulance-".$user_login.'-'.$ambulanceIds);
                }

            }else{
                $cacheAmbulanceTindakan = Yii::$app->cache->get("tarif-ambulance-".$user_login);
                if ($cacheAmbulanceTindakan !== false) {
                    if (isset($cacheAmbulanceTindakan[$daftartindakan_id])) {
                        $cacheAmbulanceTindakan[$daftartindakan_id]['is_default'] = $biaya_tetap;
                        $cacheAmbulanceTindakan[$daftartindakan_id]['biaya_tetap'] = Html::checkbox('Tindakan['.$daftartindakan_id.'][biaya_tetap]', $daftartindakan_id, [
                                    'class' => 'chk_tindakan xxxx', 
                                    'data-id' => $daftartindakan_id,
                                    'value' => $biaya_tetap,
                                    ]);
                        Yii::$app->cache->set("tarif-ambulance-".$user_login,$cacheAmbulanceTindakan);
                    }
                }
            }

            $response['response'] = $cacheAmbulanceTindakan;
            return DocoHelpers::response($response);

        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionGetMerek()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        $barang_id = $get['barang_id'];
        try {
            $request = $this->_restMaster->request('GET', 'ambulance/get-merek?barang_id='.$barang_id);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
            return $attributes;
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}