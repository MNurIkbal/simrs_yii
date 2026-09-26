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
use app\modules\master\models\KonfigSystemForm;
use GuzzleHttp\Exception\RequestException;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

use function GuzzleHttp\json_decode;

class KonfigSystemController extends DocoController
{
    protected $allowAction = [ '*' ];
    
    protected $_title = "Konfig System";
    protected $_module = 'master/konfig-system/';
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
    
    public function actionIndex($id = null)
    {
        // Check Module Fisioterapi
        $isFisioterapi  = Yii::$app->hasModule('fisioterapi');

        // Init
        $status = $this->_status; 
        $options = $this->_options;
        
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new KonfigSystemForm;

        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        if(empty($id)){
            $id = 1;
        }

        $id_encrypt = DocoHelpers::encrypt($id);
        
        if ($request->post()) {
            $post = $request->post();
            $model->load($request->post());

            // pengecekan apakah akan ditambahkan persentase tindakan atau tidak
            if ($model->default_biaya == 1) {
                $model->scenario = 'addBiaya';
            }

            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('konfig-system/update?id='.$id, [
                        'form_params' => $model->attributes,
                    ]);

                    $parseResponse = json_decode($response->getBody(),TRUE);
                    return DocoHelpers::response($parseResponse,false,$formName);
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
            // bundle request data to BE
            $listRequest = [
                'KelasPelayanan'=>'getKelasPelayanan',
                'resProfil'=> ['actionView', $id],
            ];
            $response = $this->_restMaster->get('konfig-system/multi-req', ['form_params'=>$listRequest]);
            $body = json_decode($response->getBody(), true);
            $body = $body['response'];
            
            $resProfil = (isset($body['resProfil']['data']) && count($body['resProfil']['data']) > 0) ? $body['resProfil']['data'] : [];
            $model->attributes = $resProfil;

            // add list kelas pelayanan default
            $getKelas = (isset($body['KelasPelayanan']) && count($body['KelasPelayanan']) > 0) ? ArrayHelper::map($body['KelasPelayanan'], 'kelaspelayanan_id', 'kelaspelayanan_nama') : [];
            $ListKelas = $getKelas;

            // set multiple kelas default
            if (!empty($model->kelas_pelayanan)) {
                $model->kelas_pelayanan = json_decode($model->kelas_pelayanan, true);
            }
            
            // set value radio button tipe pembayaran
            if ($model->pembayaran_langsung) {
                $model->pembayaran_langsung = 1;
            }else{
                $model->pembayaran_langsung = 0;
            }

            if ($model->is_validasi_pembayaran) {
                $model->is_validasi_pembayaran = 1;
            }else{
                $model->is_validasi_pembayaran = 0;
            }

            // set value for show/hide filed
            if ($model->adm_persen) {
                $showField = 1;
            }else{
                $showField = 0;
            }

            if ($model->is_print_automatic) {
                $model->is_print_automatic = 1;
            }else{
                $model->is_print_automatic = 0;
            }

            // get data tindakan by id
            $idTindakan = !empty($model->adm_tindakan_id) ? $model->adm_tindakan_id : 0;

            $setValTindakan = [];
            // cek data tindakan id
            if (!empty($model->adm_tindakan_id)) {
                // bundle request data to BE
                $listRequest = [
                    'getTindakan'=>['actionTindakan', $idTindakan],
                ];
                $response = $this->_restMaster->get('konfig-system/multi-req', ['form_params'=>$listRequest]);
                $body = json_decode($response->getBody(), true);
                $body = $body['response'];
                $ListTindakan = (isset($body['getTindakan']) && count($body['getTindakan']) > 0) ? $body['getTindakan'] : [];
                $labelTindakan = $ListTindakan['daftartindakan_kode'] . ' - ' . $ListTindakan['daftartindakan_nama'];
                $setValTindakan = [
                    $idTindakan => $labelTindakan,
                ];
            }
            return $this->render('form-update', get_defined_vars());
        }
    }

    /**
     * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
     * dropdown pagination infinity scroll - konfig data
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionListTindakan()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 10;
        $offset = ($page-1)*5;
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $result = $this->_restMaster->get('konfig-system/list-tindakan-infinity',[
                'query' => [
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
                            'id'=>$value['daftartindakan_id'],
                            'text'=>$value['daftartindakan_kode']. ' - '. $value['daftartindakan_nama'], 
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

    public function actionListKelompokTindakan()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 10;
        $offset = ($page-1)*5;
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $result = $this->_restMaster->get('konfig-system/list-kelompok-tindakan-infinity',[
                'query' => [
                    'term' => $request->get('q'),
                    'page'=>$page,
                    'offset'=>$offset,
                    'limit'=>$limit
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            $response[] = [
                'id'=>'0 : Pilih Semua',
                'text'=>'Pilih Semua', 
                'datavalue'=>'', 
            ];
            foreach ($data as $key => $value) {
                $response[] = [
                            'id'=>$value['kelompoktindakan_id'].':'.$value['kelompoktindakan_kode']. ' - '. $value['kelompoktindakan_nama'],
                            'text'=>$value['kelompoktindakan_kode']. ' - '. $value['kelompoktindakan_nama'], 
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

    public function actionKonfigBilling($id = null)
    {
        $request = Yii::$app->request;
        $model = new KonfigSystemForm;

        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        if (empty($id)) {
            $id = 1;
        }

        $id_encrypt = DocoHelpers::encrypt($id);
        $getPembulatan = [];

        $listRequest = [
            'KelasPelayanan'=>'getKelasPelayanan',
            'resProfil'=> ['actionView', $id],
            'listPembulatan'=> ['actionGetLookup', 'nilai_pembulatan'],
        ];
        $response = $this->_restMaster->get('konfig-system/multi-req', ['form_params'=>$listRequest]);
        $body = json_decode($response->getBody(), true);
        $body = $body['response'];
        $resProfil = (isset($body['resProfil']['data']) && count($body['resProfil']['data']) > 0) 
                            ? $body['resProfil']['data'] : [];
        $model->attributes = $resProfil;
        $model->is_pembulatankeatas = $resProfil['is_pembulatankeatas'] ? 1 : 0;
        $model->is_set_plafon = $resProfil['is_set_plafon'] ? 1 : 0;
        $model->is_show_obat_form_penatajasa = !$model->is_show_obat_form_penatajasa;

        if ($request->post()) {
            $post = $request->post();
            $konfigKelompokTindakan = json_encode($post['KonfigSystemForm']['konfig_kelompok_tindakan']);

            $konfigKelompokTindakan = $post['KonfigSystemForm']['konfig_kelompok_tindakan'];
            $konfigKlp = [];
            $konfigKlp['is_check_all'] = false;
            $konfigKlp['konfig_kelompok_tindakan'] = [];
            if(!empty($konfigKelompokTindakan)){
                foreach($konfigKelompokTindakan as $value){
                    $tmp = explode(':',$value);
                    if((int)$tmp[0] == 0){
                        $konfigKlp['is_check_all'] = true;
                        $konfigKlp['konfig_kelompok_tindakan'][]=[];
                        break;
                    }
                    $konfigKlp['konfig_kelompok_tindakan'][]=[(int)$tmp[0] => $tmp[1]];
                };
            }

            $model->load($request->post());
            // pengecekan apakah akan ditambahkan persentase tindakan atau tidak
            if ($model->default_biaya == 1) {
                $model->scenario = 'addBiaya';
            }else{
                $model->adm_persen=null;
                $model->adm_tindakan_id=null;
            }
            $model->is_show_obat_form_penatajasa = !$model->is_show_obat_form_penatajasa;
            $model->konfig_kelompok_tindakan = json_encode($konfigKlp);
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('konfig-system/update?id='.$id, [
                        'json' => $model->getAttributes(),
                    ]);

                    $parseResponse = json_decode($response->getBody(),TRUE);
                    return DocoHelpers::response($parseResponse,false,$formName);
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
            // bundle request data to BE

            $resRounded = isset($body['listPembulatan']) ? $body['listPembulatan'] : [];
            $getPembulatan = ArrayHelper::map($resRounded, 'lookup_value', 'lookup_name');
            $valKlpTindakan  = isset($model->konfig_kelompok_tindakan) ? json_decode($model->konfig_kelompok_tindakan,true) : [];
            $setValKlpTindakan = [];
            if(!empty($valKlpTindakan['is_check_all']) && $valKlpTindakan['is_check_all']){
                $setValKlpTindakan['0 : Pilih Semua'] = 'Pilih Semua';
            } else {
                if(!empty($valKlpTindakan['konfig_kelompok_tindakan'])){
                    foreach($valKlpTindakan['konfig_kelompok_tindakan'] as $key => $values){
                        foreach($values as $k=>$v){
                            $setValKlpTindakan[$k.' : '.$v] = $v;
                        }
                    }
                }                
            }
            // add list kelas pelayanan default
            $getKelas = (isset($body['KelasPelayanan']) && count($body['KelasPelayanan']) > 0) 
                                ? ArrayHelper::map($body['KelasPelayanan'], 'kelaspelayanan_id', 'kelaspelayanan_nama') : [];
            $ListKelas = $getKelas;

            $showField = 0;
            if ($model->adm_persen) {
                $showField = 1;
            }

            $idTindakan = !empty($model->adm_tindakan_id) ? $model->adm_tindakan_id : 0;

            $setValTindakan = [];
            if (!empty($model->adm_tindakan_id)) {
                $listRequest = [
                    'getTindakan'=>['actionTindakan', $idTindakan],
                ];
                $response = $this->_restMaster->get('konfig-system/multi-req', ['form_params'=>$listRequest]);
                $body = json_decode($response->getBody(), true);
                $body = $body['response'];
                $ListTindakan = (isset($body['getTindakan']) && count($body['getTindakan']) > 0) ? $body['getTindakan'] : [];
                $labelTindakan = $ListTindakan['daftartindakan_kode'] . ' - ' . $ListTindakan['daftartindakan_nama'];
                $setValTindakan = [
                    $idTindakan => $labelTindakan,
                ];
            }
            return $this->renderAjax('_konfig_billing',get_defined_vars());
        }
    }

    public function actionTarifDefault()
    {
        $status = $this->_status;
        $options = $this->_options;
        $this->_title = 'Konfigurasi Tarif Default';
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        try {
            return $this->renderAjax('_tarif_default',get_defined_vars());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400,Yii::t("fe","Terdapat kesalahan"));
        }
    }

    public function actionBilling()
    {
        $status = $this->_status;
        $options = $this->_options;
        $this->_title = 'Konfig Billing';
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        try {
            return $this->render('form-kasir', get_defined_vars());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400,Yii::t("fe","Terdapat kesalahan"));
        }
    }

    public function actionGetKonfigTarifDefault()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restMaster->request('get', 'konfig-system/get-konfig-tarif-default?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['carabayar_id']);
                $value['rowNum'] = $no;
                $value['primary'] = $primaryKey;
                $value['btn_action'] = "
                    <button class='btn btn-info btn-labeled btn-xs edit-konfig-tarif' data-carabayar_id='".$value['carabayar_id']."'><b><i class='fa fa-edit'></i></b> Edit</button>
                    <button class='btn btn-danger btn-labeled btn-xs cancel-konfig-tarif hidden' data-carabayar_id='".$value['carabayar_id']."'><b><i class='fa fa-trash'></i></b> Cancel</button>
                    ";
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetPenjaminDefault()
    {
        return $this->guzzleExec($this->_restMaster, [
            'url' => 'konfig-system/get-penjamin',
            'method' => 'get',
            'payload' => [
                'query' => array_merge(Yii::$app->request->get('payload', []))
            ],
            'returnResponse' => true
        ]);
    }

    public function actionUpdatePenjaminDefault()
    {
        $payload = $this->validatePayload([
            'payloadKey' => [
                'carabayar_id' => 'required',
                'penjamindefault_id' => 'required',
            ]
        ]);
        if (isset($payload['errors'])){
            return $this->responseJson(422, 'Silakan cek kembali input', ['errors' => $payload['errors']]);
        } else {
            return $this->guzzleExec($this->_restMaster, [
                'url' => 'konfig-system/update-penjamin-default',
                'method' => 'POST',
                'returnResponse' => true,
                'payload' => [
                    'form_params' => $payload
                ]
            ]);
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
