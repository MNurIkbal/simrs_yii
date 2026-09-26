<?php
// Author : Ramdhan Nurrachman

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoConstants;
use yii\helpers\ArrayHelper;
use app\modules\kasir\models\KontrakPenjaminForm;
use app\modules\kasir\models\PenjaminGradeForm;
use app\modules\kasir\models\KontrakPenjaminDetail;


class KontrakManajemenController extends DocoController
{
    public $_title = "Kontrak Manajemen";
    public $_module = 'kontrak-manajemen/';
    public $_restKasir;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; 
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
        $title = $this->_title;
        
        $resMaster = $this->getRequest();
        return $this->render('index', get_defined_vars());
    }

    private function getRequest()
    {
        try {
            $request = $this->_restKasir->get('kontrak-manajemen/generate-api');
            $body = json_decode($request->getBody(), TRUE);
            return $body['response'];
        } catch (RequestException $e) {
            $body['kontrak_penjamin_id'] = [];
            $body['penjamin_id'] = [];
            $body['penjamin_nama'] = [];
            return $body;
        } catch (\Exception $e) {
            $body['kontrak_penjamin_id'] = [];
            $body['penjamin_id'] = [];
            $body['penjamin_nama'] = [];
            return $body;
        }
    }

    private function checkKontrak($nama_kontrak)
    {            
            $request = $this->_restKasir->post('kontrak-manajemen/check-kontrak',[
            'form_params' => [
                'no_kontrak' => $nama_kontrak,
             ]
        ]);           
            $body = json_decode($request->getBody(), TRUE);
            return $body['response'];
    }

    public function actionGetListGrade()
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

                if (!empty($request->get('kontrak_id')) ) {
                    $kontrak_id = $request->get('kontrak_id');
                    $cacheGrade = $this->getDataGrades($kontrak_id);
                    if (!empty($cacheGrade)){
                        $result = $this->listDataGrade($cacheGrade, $no_urut, $draw);
                    }                    
                }else{
                    $cacheGrade = Yii::$app->cache->get("grade-".$user_login);
                    $result = $this->listDataGrade($cacheGrade, $no_urut, $draw);
                }

                

        return $result;       

    }

    private function listDataGrade($data, $no_urut, $draw){
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
                    'grade_nameID' => $value['grade'].$value['lookup_id'],
                    'grade' => $value['grade'],
                    'lookup_name' => $value['lookup_name'],
                    'lookup_id' => $value['lookup_id'],
                    'tipediskon_id' => $value['tipediskon_id'],
                    'tipediskon_nama' => $value['tipediskon_nama'],
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete-cache',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'data-id' => $primaryKey,
                            'data-action' => Url::to([$this->_module.'delete-cache','id' => $primaryKey]),
                        ]
                    ),
                ];

                $result['data'][] = $dataCache;
            }
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = $no_urut;
            $result['draw'] = $draw;
        }
        return $result;
    }

    public function actionDeleteCache($id = null)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $user_login = Yii::$app->user->identity->loginpemakai_id;

            $cacheGrade = Yii::$app->cache->get("grade-".$user_login);
            if ($cacheGrade !== false) {
                if (isset($cacheGrade[$id])) {
                    unset($cacheGrade[$id]);
                    Yii::$app->cache->set("grade-".$user_login,$cacheGrade);
                }
                $cacheGrade = Yii::$app->cache->get("grade-".$user_login);
            }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionCreateGrade(){
        $model = new PenjaminGradeForm;
        try {
            $request = Yii::$app->request;
            $post = $request->get('penjamin_ids');
            $resMaster = $this->getMappingGrades($post);
            if(!empty($resMaster)){
              $grade_map = ArrayHelper::map($resMaster,'penjamingrade_id','grade');
            } else $grade_map =[];
            $title = 'Tambah Grade';
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $request = Yii::$app->request;            

                return $this->renderPartial('modal-grade',get_defined_vars());
                
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    private function getMappingGrades($id)
    {
        try {
            $request = $this->_restKasir->request('GET', 'kontrak-manajemen/mapping-grades?penjamin_id='.$id);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];       
            if(!ArrayHelper::keyExists(0, $attributes, false)){
                $result = [];
            } else{
                $result = $attributes;
            };
            return $result;
        } catch (RequestException $e) {
            $result[] = [];
            return $result;
        } catch (\Exception $e) {
            $result[] = [];
            return $result;
        }
    }

    public function actionCreateMapGrade(){
        try {          
            $model = new PenjaminGradeForm;           
            $request = Yii::$app->request;
            Yii::$app->response->format = Response::FORMAT_JSON;
            $post = $request->post();
            $model->grade =  $post['grade_value'];     
                
                if($model->validate()){
                    $result = $this->_restKasir->post('kontrak-manajemen/save-map-grade',[
                        'form_params' => [
                           'grade' => $model->grade,
                           'penjamin' => $post['penjamin_value']
                        ]
                    ]);
                    $result = json_decode($result->getBody(),true);
                    $response ['penjamingrade_id'] = $result['response']['penjamingrade_id'];
                    $response ['grade'] = $model->grade;

                    return DocoHelpers::response([
                        'result' => $response,
                    ]);
                }
                else {
                        $errors =  DocoHelpers::parseError($model->errors, 'PenjaminGradeForm');
                        return DocoHelpers::responseTemplate(422, 'Error', $errors);
                    }
                                                 
           
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['response']['text'] = 'Terjadi kesalahan pada sistem';
            $response['response']['message'] = $e->getMessage();
            return DocoHelpers::response($response, 500);
        }

    }

    public function actionCreate()
    {
        $user_login = Yii::$app->user->identity->loginpemakai_id;
        $model = new KontrakPenjaminForm;        
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = 'Tambah Kontrak Manajemen';
        $cache = Yii::$app->cache;
        $response = $this->getRequest();       
        $resMaster = $response;         
        $penjamin = isset($resMaster['penjamin']) ? ArrayHelper::map($resMaster['penjamin'], 'penjamin_id', 'penjamin_nama') : [];

        if($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post($formName);

            $model->attributes = $post;
            $message_error = [];
            $check_kontrak = $this->checkKontrak($model->no_kontrak);
            $valdate= strtotime($model->tgl_selesai) - strtotime($model->tgl_mulai);
            if($check_kontrak){
                $message_error =  [
                                    'no_kontrak' => [
                                        'Nomor kontrak sudah pernah dibuat sebelumnya. Silahkan masukkan lagi',
                                    ]];
                $errors = DocoHelpers::parseError($message_error, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
                
            } 

            // if (!empty($message_error)) {
            //     $errors = DocoHelpers::parseError($message_error, $formName);
            //     return DocoHelpers::responseTemplate(422, 'Error', $errors);
            // };
            // elseif (strlen($model->no_kontrak) > 20){
            //     $message_error =  [
            //         'no_kontrak' => [
            //             'Nomor kontrak terlalu panjang. Silahkan masukkan lagi',
            //         ]];
            // } elseif (strlen($model->nama_kontrak) > 100){
            //     $message_error =  [
            //         'nama_kontrak' => [
            //             'Nama kontrak terlalu panjang. Silahkan masukkan lagi',
            //         ]];
            // } 
            // elseif(empty($model->tgl_mulai)){
            //     $message_error =  [
            //         'tgl_mulai' => [
            //             'Tanggal Mulai belum dimasukkan. Silahkan masukkan terlebih dahulu',
            //         ]];
            // } elseif(empty($model->tgl_selesai)){
            //     $message_error =  [
            //         'tgl_selesai' => [
            //             'Tanggal Selesai belum dimasukkan. Silahkan masukkan terlebih dahulu',
            //         ]];
            // };
            // if (!empty($message_error)) {
            //     $errors = DocoHelpers::parseError($message_error, $formName);
            //     return DocoHelpers::responseTemplate(422, 'Error', $errors);
            // };

            if(!empty($model->tgl_mulai) || (!empty($model->tgl_selesai))){
                $model->tgl_mulai = date('Y-m-d',strtotime($model->tgl_mulai));
                $model->tgl_selesai = date('Y-m-d',strtotime($model->tgl_selesai));
            };

            $valdate= strtotime($model->tgl_selesai) - strtotime($model->tgl_mulai);
             if($valdate < 0 ) {
                $message_error =  [
                    'tgl_mulai' => [
                        'Tanggal mulai tidak dapat lebih akhir daripada tanggal selesai.',
                    ]];
                    $errors = DocoHelpers::parseError($message_error, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);

                };            

             if($model->validate()) {
                $cacheGrade = Yii::$app->cache->get("grade-".$user_login);
                try {                             
                        $cacheGrade = Yii::$app->cache->get("grade-".$user_login);
                        $result = $this->_restKasir->post('kontrak-manajemen/save',[
                         'form_params' => [
                            'cacheGrade' => json_encode($cacheGrade),
                            'data' => $model
                         ]                        
                         
                ]);
                    
                    $result = json_decode($result->getBody(),true);
                    $status_response = $result['metadata']['status'];
                    if($status_response == 200) {
                        $cache->delete('grade-'.$user_login);
                    };
                    return DocoHelpers::response($result);
                } catch (RequestException $e) {
                    Yii::info($e->getMessage());
                    $response['response']['text'] = 'Terjadi kesalahan pada sistem';
                    $response['response']['message'] = $e->getMessage();
                    return DocoHelpers::response($response, 500);
                }
             }
            else {
                $errors =  DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        
        $cache->delete('grade-'.$user_login);
        return $this->render('form', get_defined_vars());
    }

    public function actionDetail($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $user_login = Yii::$app->user->identity->loginpemakai_id;
        $model = new KontrakPenjaminForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = 'Detail Kontrak Manajemen';
        $response = $this->getRequest();
        $data = $this->getDataUpdates($id);
        $nama_kontrak = isset($data['header']) ? $data['header']['nama_kontrak'] : '';
        $no_kontrak = isset($data['header']) ? $data['header']['no_kontrak'] : '';
        $tgl_mulai = isset($data['header']) ? date('d-M-Y',strtotime($data['header']['tgl_mulai'],0)) : '';
        $tgl_selesai = isset($data['header']) ? date('d-M-Y',strtotime($data['header']['tgl_selesai'],0)) : '';
        $penjamin_nama = isset($data['header']) ? $data['header']['penjamin_nama'] : '';       
        $kontrak_id = $id;
        return $this->render('view', get_defined_vars());
    }

    private function getDataUpdates($id)
    {
        try {
            $request = $this->_restKasir->request('GET', 'kontrak-manajemen/view-update?id='.$id);
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
    private function getDataGrades($id)
    {
        try {
            $request = $this->_restKasir->request('GET', 'kontrak-manajemen/view-grade?id='.$id);
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

    public function actionGetListLob()
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
        $lookup_id = Yii::$app->docoVars->workspace("lookup_id");
        $yiiRestfulParams['lookup_id'] = $lookup_id;

        try{
            $response = $this->_restKasir->get('kontrak-manajemen/get-list-lob');
            $response_diskon = $this->_restKasir->get('kontrak-manajemen/get-tipe-diskon');
            $body = json_decode($response->getBody(), true);
            $body_diskon = json_decode($response_diskon->getBody(), true);
            $no = $request->get('start',1);            
            $diskon_val = ArrayHelper::map($body_diskon['response']['data'], 'tipediskon_id', 'tipediskon_nama');
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = $value['lookup_id'];
                    $value['primary_key'] = Html::checkbox($value['lookup_id'], false, ['class' => 'chk_pilihan xxxx', 'data-id' => $value['lookup_name']]); 
                    $value['lookup_id'] = $primaryKey;
                    $value['rowNum'] = $value['lookup_id'];
                    $value['diskon'] = Html::dropDownList('diskon', '',$diskon_val, 
                    [
                        'class' => 'select2 form-control input-xs diskongrade',
                        'prompt' => \Yii::t('fe', '--Pilih Tipe Diskon--'),
                ]);
                    $data[$key] = $value;
                };

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

    public function actionSetCacheGrade()
    {
        $request = Yii::$app->request;
        $post = $request->post();

        try {  
                if(empty($post['data_grader'])){
                    $response['text'] = 'LOB Kosong. Silahkan cek inputan.';
                    $response['title'] = 'Proses Gagal!';
                    return DocoHelpers::response($response, 422);
                }

            $grade_diskon = $post['data_grader'];
            $setItem = [];
            for ($i= 0; $i < count($grade_diskon); $i++){
                if(empty($grade_diskon[$i]['grade'])) {
                    $response['text'] = 'Grade perlu diisi.';
                    $response['title'] = 'Proses Gagal!';
                    return DocoHelpers::response($response, 422);
                }
            };

                    for ($i= 0; $i < count($grade_diskon); $i++){
                        if(empty($grade_diskon[$i]['tipediskon_nama'])) {

                            $response['text'] = 'Tipe Diskon perlu diisi.';
                            $response['title'] = 'Proses Gagal!';
                            return DocoHelpers::response($response, 422);
                        }
                    };

                    $user_login = Yii::$app->user->identity->loginpemakai_id;                    
                    $cacheGrade = Yii::$app->cache->get("grade-".$user_login);
                    $cacheGrade = $this->saveCacheGrade($cacheGrade, $grade_diskon);
                    
                    if ($cacheGrade  && empty($cacheGrade['look_up'])) {
                        $response= [
                            'title' => 'Proses Berhasil !',
                            'text' => 'Data berhasil di tambah'
                        ];
                    }else if($cacheGrade['look_up']){
                        $list = implode(', ', $cacheGrade['look_up']);

                        return DocoHelpers::response([
                            'response' => [
                                'text' => 'Ada Duplikat Data pada '.$list.'. Silahkan cek inputan.',
                            ]
                        ], 422);
                        // return $response;                    
                    }else{
                        $response = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Data gagal di tambah'
                        ];
                    }
                // }                              
                  
                return DocoHelpers::response($response);

        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
        
    }


    private function saveCacheGrade($cacheGrade, $grade_diskon){
        $lookup_redundant =[];
        if (!empty($cacheGrade)) {
            $tempGrades = $cacheGrade;
            $grade_temp = $grade_diskon;            
            foreach ($tempGrades as $key => $value) {
                foreach($grade_diskon as $key2 => $value2){
                    if (($value['lookup_id'] == $value2['lookup_id']) && ($value['grade'] == $value2['grade'])){
                        $lookup_redundant[$key] = $value2['lookup_name'];
                        unset($grade_temp[$key]);
                    }
                }                
            };
            $cacheGrade = array_merge($cacheGrade, $grade_temp);                
        } else{
            $cacheGrade =$grade_diskon;
        };
        
        $keys = array_column($cacheGrade, 'grade');
        array_multisort($keys, SORT_ASC, $cacheGrade);
        $user_login = Yii::$app->user->identity->loginpemakai_id;
        Yii::$app->cache->set("grade-".$user_login,$cacheGrade);
        $grade =
        [
            'grade' => $cacheGrade,
            'look_up' => $lookup_redundant,
        ];

        return $grade;
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
        $kontrakpenjamin_id = Yii::$app->docoVars->workspace("kontrakpenjamin_id");
        $yiiRestfulParams['kontrakpenjamin_id'] = $kontrakpenjamin_id;
        try {
            $response = $this->_restKasir->get('kontrak-manajemen/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kontrakpenjamin_id']);
                $value['tgl_mulai'] = date("j M Y", strtotime($value['tgl_mulai']));
                $value['tgl_selesai'] = date("j M Y", strtotime($value['tgl_selesai']));
                $value['is_active'] = ($value['is_active'] == true) ? 'Aktif' : 'Tidak Aktif'; 
                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
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
}
