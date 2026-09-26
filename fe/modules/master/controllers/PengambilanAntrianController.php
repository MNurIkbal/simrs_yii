<?php
/**
 * @author : arief saputra
 * @edited : ali.padilah@docotel.com
 * @description: master layar antrian
**/

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\master\models\KonfigantrianForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class PengambilanAntrianController extends DocoController
{
    protected $_title = 'Pengambilan antrian';
    protected $_module = 'pengambilan-antrian/';
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
        $status = $this->_status;
        $title = Yii::t('fe', $this->_title);
        $jenis_antrian = $cara_bayar = $klasifikasi = $fungsi_antrian = [];
        $instalasi = $ruangan  = $pegawai =  [];
        try {
            $getAttribute = $this->_restMaster->get('konfig-antrian/get-attribute-options');
            $getAttribute = json_decode($getAttribute->getBody(),true);
            $getAttribute = $getAttribute['response'];

            $jenis_antrian = $getAttribute['jenis_antrian'];
            $cara_bayar = $getAttribute['cara_bayar'];
            $klasifikasi = $getAttribute['klasifikasi'];
            $fungsi_antrian = $getAttribute['fungsi_antrian'];
            $instalasi = $getAttribute['instalasi'];
            $ruangan = $getAttribute['ruangan'];
            $pegawai = $getAttribute['pegawaiDokter'];
            // $fungsi_antrian = Yii::$app->cache->getOrSet(DocoConstants::CACHE_FUNGSI_ANTRIAN, 
            //         function ($cache) use ($fungsi_antrian) {
            //             $list_fungsi_antrian = [];
            //             foreach ($fungsi_antrian as $value) {
            //                 $list_fungsi_antrian[$value['lookup_kode']][] = $value;
            //             }
            //             return $list_fungsi_antrian;
            //         });
            Yii::$app->cache->getOrSet(DocoConstants::CACHE_RUANGAN,
                    function ($cache) use ($ruangan) {
                        $list_ruangan= [];
                        foreach ($ruangan as $value) {
                            $list_ruangan[$value['instalasi_id']][] = $value;
                        }
                        return $list_ruangan;
                    });

            Yii::$app->cache->getOrSet(DocoConstants::CACHE_PEGAWAI_DOKTER,
                    function ($cache) use ($pegawai) {
                        $list_pegawai= [];
                        foreach ($pegawai as $value) {
                            $list_pegawai[$value['ruangan_id']][] = $value;
                        }
                        return $list_pegawai;
                    });
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
        }
        $ddlstatus = ['1'=>Yii::t('fe','Aktif'),'0'=>Yii::t('fe','Tidak aktif')];
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData($is_action = false, $jenis_antrian = null)
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if ($jenis_antrian) {
            $yiiRestfulParams['advanced-filter']['jenisantrian_id'] = $jenis_antrian;
        }

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('konfig-antrian?'.http_build_query($yiiRestfulParams), 
                                [
                                    'form_params' => []
                                ]);
            $body = json_decode($response->getBody(), True);
            $dataKonfigAntrian = $body['response']['data'];

            usort($dataKonfigAntrian, function($a, $b) {
                if ($a['jenisantrian_id'] > $b['jenisantrian_id']) {
                    return 1;
                } elseif ($a['jenisantrian_id'] < $b['jenisantrian_id']) {
                    return -1;
                }
                return 0;
            });

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['konfigantrian_id']);
                $value['jenisantrian_id'] = DocoHelpers::encrypt($value['jenisantrian_id']);
                $value['primary'] = $primaryKey;
                if ($is_action) {
                    $value['aksi'] = Html::a(
                            "<i class='fa fa-pencil'></i>",
                            Url::to([
                                $this->_module .'update',
                                'id' => $primaryKey,
                                'jenisantrian_id' => $value['jenisantrian_id']
                            ]), [
                            'style' => 'margin-right:5px;padding-left:7px !important;',
                            'class' => 'btn btn-primary btn-xs data-update',
                            'data-placement' => 'bottom',
                            'data-original-title' => Yii::t('fe', 'Ubah'),
                        ]
                    );
                }
                unset($value['konfigantrian_id']);
                $value['status'] = ($value['is_default'] == true) ? Yii::t('fe','Aktif') : Yii::t('fe','Tidak aktif');
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->delete('konfig-antrian/delete?id='.$id);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response(true);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionCreate()
    {
        $title = "Tambah Detail Antrian";
        $request = Yii::$app->request;
        $jenisantrian_id = null;
        $model = new KonfigantrianForm;
        $model->scenario = 'pendaftaran';

        if ($request->post()) {
            $post = $request->post();
            $model->load($request->post());
            $model->pegawai_id = empty($post["KonfigantrianForm"]["pegawai_id"]) ? null : $post["KonfigantrianForm"]["pegawai_id"];

            if (!empty($model->jenisantrian_id)) {
                switch ($model->jenisantrian_id) {
                    case DocoConstants::JA_KSR :
                        $model->scenario = 'kasir';
                        break;

                    case DocoConstants::JA_PNG :
                        $model->scenario = 'penunjang';
                        break;

                    case DocoConstants::JA_FAR :
                        $model->scenario = 'farmasi';
                        break;

                    case DocoConstants::JA_POL :
                        $model->scenario = 'poliklinik';
                        break;
                    
                    default:
                        $model->scenario = 'pendaftaran';
                        break;
                }
            }

            // dump($model->attributes);exit;
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('konfig-antrian/create',[
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);
                } catch (RequestException $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()]);
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()]);
                }
            } else {
                $response = $model->errors;
            }
            return DocoHelpers::response($response, 422, 'KonfigantrianForm');
        } else {
            $jenis_antrian = $cara_bayar = $klasifikasi = $fungsi_antrian = [];
            $instalasi = $ruangan  = [];
            try {
                
                $getAttribute = $this->_restMaster->get('konfig-antrian/get-attribute-options');
                $getAttribute = json_decode($getAttribute->getBody(),true);
                $getAttribute = $getAttribute['response'];
                $jenis_antrian = $getAttribute['jenis_antrian'];
                $cara_bayar = $getAttribute['cara_bayar'];
                $klasifikasi = $getAttribute['klasifikasi'];
                $fungsi_antrian = $getAttribute['fungsi_antrian'];
                $instalasi = $getAttribute['instalasi'];
                // dump($instalasi);exit;
                $ruangan = $getAttribute['ruangan'];
                $pegawai = $getAttribute['pegawai_dokter'];
                $fungsi_antrian = Yii::$app->cache->getOrSet(DocoConstants::CACHE_FUNGSI_ANTRIAN, 
                        function ($cache) use ($fungsi_antrian) {
                            $list_fungsi_antrian = [];
                            foreach ($fungsi_antrian as $value) {
                                $list_fungsi_antrian[$value['lookup_kode']][] = $value;
                            }
                            return $list_fungsi_antrian;
                        });
                Yii::$app->cache->getOrSet(DocoConstants::CACHE_RUANGAN,
                        function ($cache) use ($ruangan) {
                            $list_ruangan= [];
                            foreach ($ruangan as $value) {
                                $list_ruangan[$value['instalasi_id']][] = $value;
                            }
                            return $list_ruangan;
                        });
                Yii::$app->cache->getOrSet(DocoConstants::CACHE_PEGAWAI_DOKTER,
                    function ($cache) use ($pegawai) {
                        $list_pegawai= [];
                        foreach ($pegawai as $value) {
                            $list_pegawai[$value['ruangan_id']][] = $value;
                        }
                        return $list_pegawai;
                    });
                // $fungsi_antrian = isset($fungsi_antrian[$jenisantrian_id]) ? $fungsi_antrian[$jenisantrian_id] : [];
                // $fungsi_antrian = [];
                $model->jenisantrian_nama = isset($jenis_antrian[$jenisantrian_id]) 
                                                ? $jenis_antrian[$jenisantrian_id] : '';
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
            } catch (\Exception $e) {
                Yii::info($e->getMessage());
            }
        }

        $status = ['1'=>Yii::t('fe','Aktif'),'0'=>Yii::t('fe','Tidak aktif')];
        return $this->render('form', get_defined_vars());
    }

    public function actionDetail($id, $id_konfig = null)
    {
        $title = "Detail Pengambilan Antrian";
        $request = Yii::$app->request;
        $jenisantrian_id = DocoHelpers::decrypt($id);
        $model = new KonfigantrianForm;

        if ($request->post()) {
            $model->load($request->post());
            $model->jenisantrian_id = $jenisantrian_id;
            $fungsi_id = $model->fungsi_antrian_id;
            if (in_array($fungsi_id, DocoConstants::$show_instalasi)) {
                $model->scenario = 'instalasi';
            } else if (in_array($fungsi_id, DocoConstants::$show_cara_bayar)) {
                $model->scenario = 'cara-bayar';
            }

            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('konfig-antrian/create',[
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);
                } catch (RequestException $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()]);
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()]);
                }
            } else {
                $response = $model->errors;
            }
            return DocoHelpers::response($response, 422, 'KonfigantrianForm');
        } else {
            $jenis_antrian = $cara_bayar = $klasifikasi = $fungsi_antrian = [];
            $instalasi = $ruangan  = [];
            try {
                
                $getAttribute = $this->_restMaster->get('konfig-antrian/get-attribute-options');
                $getAttribute = json_decode($getAttribute->getBody(),true);
                $getAttribute = $getAttribute['response'];
                
                $jenis_antrian = $getAttribute['jenis_antrian'];
                $cara_bayar = $getAttribute['cara_bayar'];
                $klasifikasi = $getAttribute['klasifikasi'];
                $fungsi_antrian = $getAttribute['fungsi_antrian'];
                $instalasi = $getAttribute['instalasi'];
                $ruangan = $getAttribute['ruangan'];
                $fungsi_antrian = Yii::$app->cache->getOrSet(DocoConstants::CACHE_FUNGSI_ANTRIAN, 
                        function ($cache) use ($fungsi_antrian) {
                            $list_fungsi_antrian = [];
                            foreach ($fungsi_antrian as $value) {
                                $list_fungsi_antrian[$value['lookup_kode']][] = $value;
                            }
                            return $list_fungsi_antrian;
                        });
                Yii::$app->cache->getOrSet(DocoConstants::CACHE_RUANGAN,
                        function ($cache) use ($ruangan) {
                            $list_ruangan= [];
                            foreach ($ruangan as $value) {
                                $list_ruangan[$value['instalasi_id']][] = $value;
                            }
                            return $list_ruangan;
                        });
                $fungsi_antrian = isset($fungsi_antrian[$jenisantrian_id]) ? $fungsi_antrian[$jenisantrian_id] : [];
                $model->jenisantrian_nama = isset($jenis_antrian[$jenisantrian_id]) 
                                                ? $jenis_antrian[$jenisantrian_id] : '';
                $model->jenisantrian_id = $jenisantrian_id;
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
            } catch (\Exception $e) {
                Yii::info($e->getMessage());
            }
        }
        $status = ['1'=>Yii::t('fe','Aktif'),'0'=>Yii::t('fe','Tidak aktif')];
        return $this->render('form', get_defined_vars());
    }

    public function actionListFungsiantrian() {
        $request = Yii::$app->request;
        $post = $request->post();
        $jenisantrian_id = $post['depdrop_parents'][0];

        $KonfigantrianRequest = $this->_restMaster->get('allow/get-fungsiantrian-by-jenis?jenisantrian_id='.$jenisantrian_id);
        $body = json_decode($KonfigantrianRequest->getBody(),TRUE);
        $dataKonfig = $body['response'];

        $out = [];
        foreach($dataKonfig as $key => $value) {
            $out[] = [
                        'id' => $key,
                        'name' => $value
                    ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }

    public function actionUpdate($id, $jenisantrian_id)
    {
        $title = "Detail Pengambilan Antrian";
        $request = Yii::$app->request;
        $jenisantrian_id = DocoHelpers::decrypt($jenisantrian_id);
        $model = new KonfigantrianForm;

        if ($request->post()) {
            $model->load($request->post());
            $model->jenisantrian_id = $jenisantrian_id;
            $post = $request->post();
            $model->pegawai_id = empty($post["KonfigantrianForm"]["pegawai_id"]) ? null : $post["KonfigantrianForm"]["pegawai_id"];
            if (!empty($model->jenisantrian_id)) {
                switch ($model->jenisantrian_id) {
                    case DocoConstants::JA_KSR :
                        $model->scenario = 'kasir';
                        break;

                    case DocoConstants::JA_PNG :
                        $model->scenario = 'penunjang';
                        break;

                    case DocoConstants::JA_FAR :
                        $model->scenario = 'farmasi';
                        break;

                    case DocoConstants::JA_POL :
                        $model->scenario = 'poliklinik';
                        break;
                    
                    default:
                        $model->scenario = 'pendaftaran';
                        break;
                }
            }

            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('konfig-antrian/update',[
                        'query' => [
                            'id' => DocoHelpers::decrypt($id)
                        ],
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);
                } catch (RequestException $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()]);
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()]);
                }
            } else {
                $response = $model->errors;
            }
            return DocoHelpers::response($response, 422, 'KonfigantrianForm');
        } else {
            $jenis_antrian = $cara_bayar = $klasifikasi = $fungsi_antrian = [];
            $instalasi = $ruangan  = [];
            try {
                
                $getAttribute = $this->_restMaster->get('konfig-antrian/get-attribute-options',[
                    'query' => [
                        'id' => DocoHelpers::decrypt($id)
                    ]
                ]);
                $getAttribute = json_decode($getAttribute->getBody(),true);
                $getAttribute = $getAttribute['response'];
                
                $jenis_antrian = $getAttribute['jenis_antrian'];
                $cara_bayar = $getAttribute['cara_bayar'];
                $klasifikasi = $getAttribute['klasifikasi'];
                $fungsi_antrian = $getAttribute['fungsi_antrian'];
                $instalasi = $getAttribute['instalasi'];
                $ruangan = $getAttribute['ruangan'];
                $pegawai = $getAttribute['pegawai_dokter'];
                $model->attributes = $getAttribute['data'];
                $model->fungsi_antrian_id = isset($getAttribute['data']['fungsiantrian_id']) 
                                                ? $getAttribute['data']['fungsiantrian_id'] : null;
                $fungsi_antrian = Yii::$app->cache->getOrSet(DocoConstants::CACHE_FUNGSI_ANTRIAN, 
                        function ($cache) use ($fungsi_antrian) {
                            $list_fungsi_antrian = [];
                            foreach ($fungsi_antrian as $value) {
                                $list_fungsi_antrian[$value['lookup_kode']][] = $value;
                            }
                            return $list_fungsi_antrian;
                        });
                Yii::$app->cache->getOrSet(DocoConstants::CACHE_RUANGAN,
                        function ($cache) use ($ruangan) {
                            $list_ruangan= [];
                            foreach ($ruangan as $value) {
                                $list_ruangan[$value['instalasi_id']][] = $value;
                            }
                            return $list_ruangan;
                        });
                $fungsi_antrian = isset($fungsi_antrian[$jenisantrian_id]) ? $fungsi_antrian[$jenisantrian_id] : [];
                $model->jenisantrian_nama = isset($jenis_antrian[$jenisantrian_id]) 
                                                ? $jenis_antrian[$jenisantrian_id] : '';
                $model->jenisantrian_id = $jenisantrian_id;
                $model->is_active = $model->is_active == true ? 1 : 0;
                $model->pegawai_id = empty($getAttribute['data']['pegawai_id']) ? null : $getAttribute['data']['pegawai_id'];
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
            } catch (\Exception $e) {
                Yii::info($e->getMessage());
            }
        }
        $status = ['1'=>Yii::t('fe','Aktif'),'0'=>Yii::t('fe','Tidak aktif')];
        return $this->render('form', get_defined_vars());
    }

    public function actionGetFungsi($id = null)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = [
            'output'=>[], 
            'selected'=>$id,
            'kode' => []
        ];
        if (!empty($post['depdrop_parents'][0])) {
            $jenisantrian_id = $post['depdrop_parents'][0];
            $listFungsi = Yii::$app->cache->get(DocoConstants::CACHE_FUNGSI_ANTRIAN);
            if (isset($listFungsi[$jenisantrian_id])) {
                foreach ($listFungsi[$jenisantrian_id] as $val) {
                    $result['output'][] = [
                        'id' => $val['lookup_id'],
                        'name' => $val['lookup_name'],
                    ];
                    $result['kode'][$val['lookup_id']] = $val;
                }
            }
        }
        return DocoHelpers::response($result);
    }

    public function actionGetRuangan($id = null)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = ['output'=>[], 'selected'=>$id];
        if (isset($post['depdrop_parents'][0])) {
            $instalasi_id = $post['depdrop_parents'][0];
            $listRuangan = Yii::$app->cache->get(DocoConstants::CACHE_RUANGAN);
            if (isset($listRuangan[$instalasi_id])) {
                foreach ($listRuangan[$instalasi_id] as $val) {
                    $result['output'][] = [
                        'id' => $val['ruangan_id'],
                        'name' => $val['ruangan_nama'],
                    ];
                }
            }
        }
        return DocoHelpers::response($result);
    }

    public function actionGetFungsiRuangan($id = null)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = ['output'=>[], 'selected'=>$id];
        if (isset($post['depdrop_parents'][0])) {
            $jenisantrian_id = $post['depdrop_parents'][0];
            $listFungsi = Yii::$app->cache->get(DocoConstants::CACHE_FUNGSI_ANTRIAN);
            if (isset($listFungsi[$jenisantrian_id])) {
                foreach ($listFungsi[$jenisantrian_id] as $val) {
                    $result['output'][] = [
                        'id' => $val['lookup_id'],
                        'name' => $val['lookup_name'],
                    ];
                }
            }
        }
        return DocoHelpers::response($result);
    }

    public function actionListRuangan() 
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $fungsiantrian_id = $post['depdrop_parents'][0];
        if($fungsiantrian_id == ''){
            echo json_encode(['output'=>[], 'selected'=>'']);
            return;
        }

        $KonfigantrianRequest = $this->_restMaster->get('allow/get-ruangan-by-fungsi?fungsiantrian_id='.$fungsiantrian_id);
        $body = json_decode($KonfigantrianRequest->getBody(),TRUE);
        $dataKonfig = $body['response'];

        $out = [];
        foreach($dataKonfig as $key => $value) {
            $out[] = [
                        'id' => $key,
                        'name' => $value
                    ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }
    // Export excel
    public function actionExportExcel()
    {
        // Convert to json format
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        // Try catch
        try {
            // Response
            $response = $this->_restMaster->get('konfig-antrian/export-excel?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];

            return DocoHelpers::downloadFile($url);
        } catch (\Exception $e) {
            // Get message
            $result['error'] = $e->getMessage();

            // Return
            return $result;
        } catch (RequestException $e) {
            // Get message
            $result['error'] = $e->getMessage();
            
            // Return
            return $result;
        }
    }

    public function actionExportPdf(){
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/laporan-pengambilan-antrian.pdf";
        try {
            $response = $this->_restMaster->get('konfig-antrian/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {       
        var_dump($e->getMessage());exit();     
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {            
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetPegawai($id = null)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = ['output'=>[], 'selected'=>$id];
        if (isset($post['depdrop_parents'][0])) {
            $ruangan_id = $post['depdrop_parents'][0];
            $listPegawai = Yii::$app->cache->get(DocoConstants::CACHE_PEGAWAI_DOKTER);
            if (isset($listPegawai[$ruangan_id])) {
                foreach ($listPegawai[$ruangan_id] as $val) {
                    $result['output'][] = [
                        'id' => $val['pegawai_id'],
                        'name' => $val['nama_pegawai'],
                    ];
                }
            }
        }
        return DocoHelpers::response($result);
    }
}