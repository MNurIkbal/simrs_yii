<?php
/**
 *  Author : Dede Herdiana
 */

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\master\components\DetailKontrakPenjamin;
use app\modules\master\models\TipeDiskonForm;
use app\modules\master\models\TipeDiskonDetailForm;

class TipeDiskonController extends DocoController
{
    protected $_restMaster;
    
    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        try {
            $title = "Master Tipe Diskon";
            return $this->render('index',get_defined_vars());
        } catch (Exception $e) {
            Yii::error([$e]);
            return 'failed';
        }
    }

    public function actionTipeDiskon(){
        return $this->renderAjax('_tipediskon',get_defined_vars());
    }

    public function actionGetTipeDiskon(){
        $request = Yii::$app->request;
		$no = $request->get('start',1);
        
        $draw = $request->get('draw', 1);
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restMaster->get('tipe-diskon/get-tipe-diskon?'.http_build_query($yiiRestfulParams), ['form_params'=>[]]);

            $body = json_decode($response->getBody(), true);
            $data = $body['response']['data'];
            foreach($data as $i => $row){
                $no++;
                $primaryKey = DocoHelpers::encrypt($row['tipediskon_id']);
                $data[$i]['rowNum'] = $no;
                $data[$i]['primary'] = $primaryKey;
                $data[$i]['status'] = !empty($row['is_active']) ? ($row['is_active'] ? "Aktif" : "Tidak Aktif") : 'Tidak Aktif';
            }
            $result['data'] = $data;
            $result['recordsTotal'] = isset($body['response']['_meta']['totalCount']) ? $body['response']['_meta']['totalCount'] : 0;
            $result['recordsFiltered'] = isset($body['response']['_meta']['totalCount']) ? $body['response']['_meta']['totalCount'] : 0;
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            Yii::error(['message' => $e]);
            return 'failed';
        } catch (\Exception $e) {
            return 'failed';
        }
    }

    public function actionCreate(){
        $title = Yii::t('fe', 'Form Komponen Diskon');
        $request = Yii::$app->request;
        $action = $request->get('stat', 'add'); 
        $model = new TipeDiskonForm;
        $model->is_active = true;
        $is_edit = false;
        $this->clearCache();

        return $this->renderAjax('_tabform',get_defined_vars());
    }

    public function actionCreateDetail(){
        $title = Yii::t('fe', 'Form Detail');
        $model = new TipeDiskonDetailForm;
        $is_edit = false;
        $additional_data = $this->_packTarif();
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $request = Yii::$app->request;
        $action = $request->get('stat', 'add'); 
        $unique = DocoHelpers::decrypt($request->get('jl')); 
        $cacheName = $this->prefixCache($unique);
        $label = "";
        $jenis_layanan = "";

        if($unique == DocoConstants::TD_KELAS){
            $additional_data = $this->getKelasPelayanan();
            $label = "Kelas";
            $jenis_layanan = DocoConstants::TD_KELAS;
        }else if($unique == DocoConstants::TD_KELOMPOK){
            $additional_data = $this->getKelompokTindakan();
            $label = "Kategori";
            $jenis_layanan = DocoConstants::TD_KELOMPOK;
        }else if($unique == DocoConstants::TD_TINDAKAN){
            $additional_data = $this->getDaftarTindakan();
            $label = "Tindakan";
            $jenis_layanan = DocoConstants::TD_TINDAKAN;
        }

        if ($request->post()) {
            $post = $request->post();
            $model->load($post);
            if ($model->validate() && $model->validateCustomRequired()) {
                $model->max_dijamin = DocoHelpers::convertToAngka($model->max_dijamin);
                $data = $model->attributes;
                $unique = $model->jenislayanan_id; 
                $key = $model->layanan_id; 
                $cacheName = $this->prefixCache($unique);
                $keyName = $this->prefixCache($model->layanan_id);
                $row[$keyName] = $data;
                $result = $this->saveToCache($cacheName, $row, $key);
                if(!empty($result['status']) && $result['status'] ){
                    return DocoHelpers::response($result);
                }else{
                    $message = !empty($result['message']) ? $result['message'] : []; 
                    $errors = DocoHelpers::parseError($message, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }

            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        return $this->renderAjax('components/_form',get_defined_vars());
    }

    public function actionEdit(){
        $request = Yii::$app->request;
        $tipediskon_id = $request->get('id');
        $model = new TipeDiskonForm;
        $action = "edit";
        $this->clearCache();
        
        $response = $this->_restMaster->get('tipe-diskon/get-tipe-diskon?',[
            'form_params'=>[
                'tipediskon_id' => DocoHelpers::decrypt($tipediskon_id),
            ]
        ]);
        
        $body = json_decode($response->getBody(), true);
        $tipediskon_id = !empty($body['response']['data'][0]['tipediskon_id']) ? $body['response']['data'][0]['tipediskon_id'] : null;
        $tipediskon_nama = !empty($body['response']['data'][0]['tipediskon_nama']) ? $body['response']['data'][0]['tipediskon_nama'] : null;
        $is_active = !empty($body['response']['data'][0]['is_active']) ? $body['response']['data'][0]['is_active'] : false;
        $model->tipediskon_id = $tipediskon_id;
        $model->tipediskon_nama = $tipediskon_nama;
        $model->is_active = $is_active;
        $is_edit = true;

        return $this->renderAjax('_tabform', get_defined_vars() );
    }

    private function prefixCache($unique){
        return 'layanan-' . $unique;
    }
    
    private function prefixDelete($key=null){
        return 'deleted-tipe-diskon';
    }

    private function getKelasPelayanan(){
        try {
            $response = $this->_restMaster->get('tipe-diskon/get-kelas-pelayanan', ['form_params'=>[]]);
            $body = json_decode($response->getBody(), true);
            $body = $body['response'];
            $kelaspelayanan = ArrayHelper::map($body['kelaspelayanan'], 'kelaspelayanan_id', 'kelaspelayanan_nama');
            return $kelaspelayanan;
        } catch (RequestException $e) {
            $result = [];
            return $result;
        } catch (\Exception $e) {
            $result = [];
            return $result;
        }
    }

    private function getKelompokTindakan(){
        try {
            $response = $this->_restMaster->get('tipe-diskon/get-kelompok-tindakan', ['form_params'=>[]]);
            $body = json_decode($response->getBody(), true);
            $body = $body['response'];
            $kelompoktindakan = ArrayHelper::map($body['kelompoktindakan'], 'kelompoktindakan_id', 'kelompoktindakan_nama');
            return $kelompoktindakan;
        } catch (RequestException $e) {
            $result = [];
            return $result;
        } catch (\Exception $e) {
            $result = [];
            return $result;
        }
    }

    private function getDaftarTindakan(){
        try {
            $response = $this->_restMaster->get('tipe-diskon/get-daftar-tindakan', ['form_params'=>[]]);
            $body = json_decode($response->getBody(), true);
            $body = $body['response'];
            $daftartindakan = ArrayHelper::map($body['daftartindakan'], 'daftartindakan_id', 'daftartindakan_nama');
            return $daftartindakan;
        } catch (RequestException $e) {
            $result = [];
            return $result;
        } catch (\Exception $e) {
            $result = [];
            return $result;
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
    
    public function actionGetTipeDiskonDetail(){
        $request = Yii::$app->request;
		$no = $request->get('start',1);
        $tipediskon_id = $request->get('id', null);
        $tipediskon_id = DocoHelpers::decrypt($tipediskon_id);
        $action = $request->get('stat', 'show'); 
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $_jenisLayananId = 0;
        $_jenisLayanan =  "Kelompok Tindakan";

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restMaster->post('tipe-diskon/get-tipe-diskon-detail?'.http_build_query($yiiRestfulParams), 
                    [
                        'form_params'=>[
                            'id' => $tipediskon_id,
                            'action' => $action
                        ]
                    ]);

            $body = json_decode($response->getBody(), true);
            $data = $body['response']['data'];
            /** rebuild data */
            $tmpData = [];
            $newData = [];
            foreach($data as $i => $value ){
                $no++;
                $disc_persen  = !empty($value['disc_persen']) ? $value['disc_persen'] : 0;
                $max_dijamin  = !empty($value['max_dijamin']) ? $value['max_dijamin'] : 0;
                $form_disc_persen  = $disc_persen;
                $form_max_dijamin  = $max_dijamin;
                $jenis_layanan  = !empty($value['jenis_layanan']) ? $value['jenis_layanan'] : 0;
                $jenislayanan_id  =  !empty($value['jenislayanan_id']) ? $value['jenislayanan_id'] : 0;
                $jenis_layanan  =  !empty($value['jenis_layanan']) ? $value['jenis_layanan'] : '';
                $layanan  = !empty($value['layanan']) ? $value['layanan'] :  null;
                $layanan_id = !empty($value['layanan_id']) ? $value['layanan_id'] : null;
                $primaryKey = DocoHelpers::encrypt(!empty($value['tipediskondetail_id']) ? $value['tipediskondetail_id'] : '' );
                $tipediskondetail_id = !empty($value['tipediskondetail_id']) ? $value['tipediskondetail_id'] : null ;
                $tipediskon_id = DocoHelpers::encrypt(!empty($value['tipediskon_id']) ? $value['tipediskon_id'] : 0 );
                if($action != 'show'){
                    $form_disc_persen = '
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group highlight-addon has-size-sm">
                                    <div class="input-group">
                                        <input type="text" 
                                                class="form-control input-sm text-center doco-number percent-discount-parent" 
                                                autocomplete="off" 
                                                value="'.$disc_persen.'" data-layananid = "'.$layanan_id.'"  data-layanan = "'.$layanan.'" data-jenislayananid = "'.$jenislayanan_id.'" data-kpd = "'.$tipediskondetail_id.'" data-pk = "'.$tipediskon_id.'">
                                    </div>
                                </div>
                            </div>
                        </div>
                        ';
                    $form_max_dijamin = '
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group highlight-addon has-size-sm field-persen">
                                    <div class="input-group">
                                        <input type="text" 
                                                class="form-control input-sm text-center doco-number max-dijamin-parent" 
                                                autocomplete="off" 
                                                value="'.$max_dijamin.'" data-layananid = "'.$layanan_id.'" data-layanan = "'.$layanan.'" data-jenislayananid = "'.$jenislayanan_id.'" data-kpd = "'.$tipediskondetail_id.'" data-pk = "'.$tipediskon_id.'">
                                    </div>
                                </div>
                            </div>
                        </div>
                        ';
                }

                $data[$i]['layanan'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success btn-detail', 'id'=>"btn-".$layanan_id, 'data-id'=> $layanan_id, 'data-source'=>"/master/tarif-tindakan/kontrak-penjamin-detail-child?id=".$tipediskon_id."&stat=".$action."&jenis=".DocoHelpers::encrypt($jenislayanan_id)."&layanan=".DocoHelpers::encrypt($layanan_id),'onclick'=> 'docoHelper.detail(this)']) . ' ' .$layanan;
                $data[$i]['rowNum'] = $no;
                $data[$i]['disc_persen'] = $disc_persen; 
                $data[$i]['form_disc_persen'] = $form_disc_persen; 
                $data[$i]['max_dijamin'] = $max_dijamin;
                $data[$i]['form_max_dijamin'] = $form_max_dijamin;
                $data[$i]['jenislayanan_id'] = $jenislayanan_id;
                $data[$i]['jenis_layanan'] = $jenis_layanan;
                $data[$i]['layanan_id'] = $layanan_id;
            }
            if($action == "add"){
                $data =[];
            }
            $result['data'] = $data;
            $result['recordsTotal'] = isset($body['response']['_meta']['totalCount']) ? $body['response']['_meta']['totalCount'] : 0;
            $result['recordsFiltered'] = isset($body['response']['_meta']['totalCount']) ? $body['response']['_meta']['totalCount'] : 0;
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return '';
        } catch (\Exception $e) {
            return '';
        }
    }
    
    public function saveToCache($cacheName, $data, $key)
    {
        $cache = Yii::$app->cache;
        $cacheData = $cache->get($cacheName);
        $keyName = $this->prefixCache($key);
        $is_change = !empty($data[$keyName]['is_change']) ? ($data[$keyName]['is_change'] == 'true' ? true : false) : false;
        if (count($data) < 1) {
            return true;
        }
        if ($cacheData == false) {
            if (!isset($data[$keyName])) {
                $data[$keyName] = [$data];
            }
            $cache->set($cacheName, $data);
        } else {
            $arr = $cacheData;
            if(!empty($arr[$keyName]) && $is_change){
                return [
                    'status' => false,
                    'message' =>[
                                    'layanan_id' => [
                                        'Layanan tersebut sudah digunakan, silakan pilih layanan lain.',
                                    ],
                                ]
                
                ];
            }else{
                $arr[$keyName] = $data[$keyName];
            }
            $cache->set($cacheName, $arr);
        }
        $cacheData = $cache->get($cacheName);
        return [
            'status' => true
        ];
    }

    public function clearCache($cacheName = []){
        $cache = Yii::$app->cache;
        if(!empty($cacheName)){
            $listCacheName = $cacheName;
        }else{
            $listCacheName = [ DocoConstants::TD_KELAS, DocoConstants::TD_KELOMPOK, DocoConstants::TD_TINDAKAN];
        }
        if(!empty($listCacheName)){
            foreach($listCacheName as $val){
                $cacheName = $this->prefixCache($val);
                $cache->set($cacheName, []);
            }
        }
        $cache->set($this->prefixDelete(), []);
    }

    public function actionGetCache($cacheName)
    {
        $cache = Yii::$app->cache;
        $cacheData = $cache->get($cacheName);
        $cacheDataDelete = $cache->get($this->prefixDelete());

        $request = Yii::$app->request;
        $tipediskon_id = $request->get('id',null);
        $result = [];
        $data = [];
        $draw = $request->get('draw', 1);
        $action = $request->get('stat', 'add');
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $explodeCache = explode('-', $cacheName);

            if ((!$cacheData || empty($cacheData)) && empty($cacheDataDelete) && $tipediskon_id != null && $action != 'add') {
                $getDataCache = $this->helper->guzzleExec($this->_restMaster, [
                    'url' => 'tipe-diskon/get-tipe-diskon-detail',
                    'method' => 'GET', 
                    'payload' => [
                        'query' => [
                            'tipediskon_id' => $tipediskon_id
                        ]
                    ]
                ]);

                if (!isset($getDataCache['data']) || !$getDataCache['data']) {
                    return $this->helper->response($result);
                }
                if(!empty($getDataCache['data'])){
                    $tmpData = [];
                    /** Grouping data */
                    foreach($getDataCache['data'] as $val){
                        $jenislayanan_id = !empty($val['jenislayanan_id']) ? $val['jenislayanan_id'] : null;
                        $tmpData[$jenislayanan_id][]= $val;
                    }


                    /**populating for cache */
                    foreach($tmpData as $key => $val){
                        $tmpCache = [];
                        $newCacheName = $this->prefixCache($key);
                        foreach($val as $row){
                            $layanan_id = !empty($row['layanan_id']) ? $row['layanan_id'] : null;
                            $keyDetail = $this->prefixCache($layanan_id);
                            $tmpCache[$keyDetail] = $row; 
                        }
                        $cache->set($newCacheName, $tmpCache);
                    }

                }

                $cacheData = $cache->get($cacheName);
            }

            if (empty($cacheData)) {
                return $this->helper->response($result);
            }
            $no = 0;
            foreach ($cacheData as $key => $value) {
                $no++;
                $jenislayanan_id = !empty($value['jenislayanan_id']) ? $value['jenislayanan_id'] : '';
                if(!empty($value)){
                    $value['rowNum'] = $no;
                    $value['aksi'] = '<button class="btn btn-info edit-cache" data-key="' . DocoHelpers::encrypt($key) . '" data-cache="' . DocoHelpers::encrypt($cacheName) . '" data-jl="' . DocoHelpers::encrypt($jenislayanan_id) . '"><i class="fa fa-edit"></i></button> ';
                    $value['aksi'] .= '<button class="btn btn-danger delete-cache" data-key="' . DocoHelpers::encrypt($key) . '" data-cache="' . DocoHelpers::encrypt($cacheName) . '" data-jl="' . DocoHelpers::encrypt($jenislayanan_id) . '"><i class="fa fa-trash"></i></button>';
                }

                $data[] = $value;
            }

        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = count($data);
        $result['recordsFiltered'] = count($data);
        return DocoHelpers::response($result);
    }

    public function actionUnsetCache($key, $cacheName)
    {
        $cache = Yii::$app->cache;
        $key = DocoHelpers::decrypt($key);
        $cacheName = DocoHelpers::decrypt($cacheName);
        $cacheData = $cache->get($cacheName);
        if ($cacheData) {
            $arr = $cacheData;

            /** populate for delete */
            $cacheNameDelete = $this->prefixDelete($key);
            $cacheDataDelete = $cache->get($cacheNameDelete);
            $deletedTmp = !empty($arr[$key]) ? $arr[$key] : [];
            if (count($deletedTmp) > 0) {
                $cacheDataDelete[] = $deletedTmp;
                $cache->set($cacheNameDelete, $cacheDataDelete);
            }
            /** -------------------- */
            
            unset($arr[$key]);
            $newArr = [];
            foreach ($arr as $key => $value) {
                $newArr[] = $value;
            }

            $cache->set($cacheName, $newArr);
            return DocoHelpers::response(['response' => ['title' => 'Proses berhasil!', 'text' => 'Data berhasil dihapus']]);
        }
        return DocoHelpers::response(['response' => ['title' => 'Proses berhasil!', 'text' => 'Data berhasil dihapus']]);
    }

    public function actionSave(){
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $model = new TipeDiskonForm;
        $postData = $request->post('TipeDiskonForm'); 

        /** Populate Data detail */
        $jenislayanan_id = [DocoConstants::TD_KELAS, DocoConstants::TD_KELOMPOK, DocoConstants::TD_TINDAKAN];
        $detail = [];
        if(!empty($jenislayanan_id) && is_array($jenislayanan_id)){
            foreach($jenislayanan_id as $row){
                $cacheData = $cache->get($this->prefixCache($row));
                if($cacheData){
                    foreach($cacheData as $key => $val){
                        $detail[] = $val;
                    }
                }
            }
        }

        /** Populate data for delete  */
        $deleted_data = [];
        $cacheDataDelete = $cache->get($this->prefixDelete());
        if($cacheDataDelete){
            $deleted_data = $cacheDataDelete;
        }
        $postData['deleted_data'] = $deleted_data;

        $postData['detail'] = $detail;
        $model->tipediskon_nama = !empty($postData['tipediskon_nama']) ? $postData['tipediskon_nama'] : '';
        $model->is_active = !empty($postData['is_active']) && $postData['is_active'] ? true : false;
        try {
            if($model->validate()){
                $result = $this->guzzleExec($this->_restMaster, [
                    'url' => 'tipe-diskon/save',
                    'method' => 'POST',
                    'payload' => [
                        'form_params' => $postData,
                    ],
                    'returnResponse' => true,
                ]);
                $result = !empty($result['data']) ? $result['data'] : '';
                return $result;
            }else{
                $errors = DocoHelpers::parseError($model->errors, 'TipeDiskonForm');
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } catch (RequestException $e) {
            return 'failed';
        } catch (\Exception $e) {
            Yii::error(["dada" => $e]);
            return 'failed';
        }
    }
}
