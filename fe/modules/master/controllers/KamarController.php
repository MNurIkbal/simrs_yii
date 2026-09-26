<?php 

/**
 * @author Randy Vianda Putra
 * @todo Master Kamar
 * @copyright 21 Mei 2018 aweutist
 */

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Doco\master\models\KamarForm;
use Doco\master\models\MappinganKlasifikasiKamarForm;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class KamarController extends DocoController
{

    protected $_title = "Kamar";
    protected $_module = '/master/kamar';
    protected $_restMaster;
    protected $_subMenuTitleKamar = "Kamar";
    protected $_subMenuTitleKlasifikasiKamar = "Mappingan Klasifikasi Kamar";


    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        return Yii::$app->docoPlugin->execute($this,'kamar_index');
    }

    public function actionCreate()
    {
        try {
            $title = Yii::t('fe', 'Tambah Kamar');
            $subtitle = Yii::t('fe', 'Form Tambah Kamar');
            $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
            $model = new KamarForm;
            $request = Yii::$app->request;
            $post = $request->post();
            $scenario = 'create';
            if ($post) {
                $model->load($post);
                $model->kamarruangan_nokamar = trim($model->kamarruangan_nokamar);
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'kamar/save',[
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(), true);
                    Yii::$app->cache->delete("data-kamar-{$instalasi}");
                    return DocoHelpers::response($response,false,'KamarForm');
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KamarForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                $data = $this->getData();
                $data_ruangan = !empty($data['data_ruangan']) 
                    ? ArrayHelper::map($data['data_ruangan'], 'ruangan_id', 'ruangan_nama')
                    : [];
                $data_jenis_kamar = !empty($data['data_jenis_kamar']) ? ArrayHelper::map($data['data_jenis_kamar'], 'lookup_id', 'jenis_kamar'): [];
                $data_kelas_pelayanan = !empty($data['data_pelayanan']) 
                    ? ArrayHelper::map($data['data_pelayanan'], 'kelaspelayanan_id', 'kelaspelayanan_nama')
                    : [];
                $data_jenis_kasus_penyakit = !empty($data['data_kasus_penyakit']) ? ArrayHelper::map($data['data_kasus_penyakit'], 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama'): [];
            }
            $klasifikasi_exists = false;
            return $this->render('form', get_defined_vars());
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restMaster->request('get', 'kamar/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = $cache = [];
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kamarruangan_id']);
                $value['primary'] = $primaryKey;
                $cache[] = [
                    'kamarruangan_id' => $value['kamarruangan_id'],
                    'kamarruangan_nokamar' => $value['kamarruangan_nokamar'],
                ];
                if ($value['is_active']) {
                    $value['is_active'] = "Aktif";
                }else{
                    $value['is_active'] = "Tidak Aktif";
                }
                $value['is_dashboard'] = DocoHelpers::switchStatus($value['is_dashboard'], $primaryKey,'change-status','Ya','Tidak');
                unset($value['kamarruangan_id']);
                $value['rowNum'] = $no;
                $row[$key] = $value;
            }
            $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
            $data_kamar = Yii::$app->cache->get("data-kamar-{$instalasi}");
            if ($data_kamar === false) {
                Yii::$app->cache->set("data-kamar-{$instalasi}", $cache);
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

    public function actionGetDataPelayanan($ruangan_id='')
    {
        try{
            $dataKelasPelayanan = [];
            $dataJenisPenyakit = [];

            if(isset($ruangan_id)){
                $response = $this->_restMaster->get('kamar/get-kelas-pelayanan-and-jenis-penyakit-ruangan?ruangan_id='.$ruangan_id);
                $body = json_decode($response->getBody(), true);

                if (isset($body['response']['kelas_pelayanan']) && !empty($body['response']['kelas_pelayanan'])) {
                    foreach ($body['response']['kelas_pelayanan'] as $value) {
                        $dataKelasPelayanan[] = ['id' => $value['kelaspelayanan_id'], 'text' => $value['kelaspelayanan_nama']];
                    }
                }

                if (isset($body['response']['jenis_penyakit']) && !empty($body['response']['jenis_penyakit'])) {
                    foreach ($body['response']['jenis_penyakit'] as $value) {
                        $dataJenisPenyakit[] = ['id' => $value['jeniskasuspenyakit_id'], 'text' => $value['jeniskasuspenyakit_nama']];
                    }
                }

                $return = [
                    'kelas_pelayanan' => $dataKelasPelayanan,
                    'jenis_penyakit' => $dataJenisPenyakit
                ];

                return json_encode($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionCekDataKamar($kamarruangan_nokamar='')
    { 
        try{
            $data = [];
            if(isset($kamarruangan_nokamar)){
                $response = $this->_restMaster->get('kamar/cek-data-kamar?kamarruangan_nokamar='.$kamarruangan_nokamar);
                $body = json_decode($response->getBody(), True);
                return $body['response'];
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    private function getData($id='')
    {
        try {
            if ($id) {
                $response = $this->_restMaster->request('GET', 'kamar/generate-api?id='.$id);
            }else{
                $response = $this->_restMaster->request('GET', 'kamar/generate-api');
            }
            $body = json_decode($response->getBody(),TRUE);
            $return = [
                'data_ruangan' => $body['response']['data-ruangan'],
                'data_jenis_kamar' => $body['response']['data-jenis-kamar'],
                'data_pelayanan' => $body['response']['data-pelayanan'],
                'data_kasus_penyakit' => $body['response']['data-kasus-penyakit'],
            ];

            return $return;
        } catch (RequestException $e) {
            // dump($e);exit;
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetKelasPelayanan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restMaster->get('kamar/get-kelas-pelayanan-ruangan?ruangan_id=' . $parent_label);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                'id' => $value['kelaspelayanan_id'],
                'name' => $value['kelaspelayanan_nama']
            ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetKasusPenyakit()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restMaster->get('kamar/get-kasus-penyakit-ruangan?ruangan_id=' . $parent_label);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                'id' => $value['jeniskasuspenyakit_id'],
                'name' => $value['jeniskasuspenyakit_nama']
            ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetKamar()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restMaster->request('POST', 'kamar/data-kamar',[
                            'form_params' => ['term' => $_GET['q']['term']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['kamarruangan_nokamar'],
                    'text' => $value['kamarruangan_nokamar']
                ];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];

            return DocoHelpers::response($return);
        }
    }

    public function actionDelete($id) {
        $id = DocoHelpers::decrypt($id);
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $response = $this->guzzleExec($this->_restMaster, [
            'url' => 'kamar/delete',
            'method' => 'delete',
            'payload' => [
                'query' => [
                    'id' => $id
                ],
            ],
        ]);
        $res_status = isset($response['res_status']) ? $response['res_status'] : 200;

        if($res_status == 422) {
            $resPayload = [
                "metadata" => [
                    "status" =>  422,
                    "message" =>  "Unprocessable Entity"
                ],
                "response" => [
                    "title" => $response['title'],
                    "text" => $response['message']
                ]
            ];
            return DocoHelpers::response($resPayload);
        } 
        Yii::$app->cache->delete("data-kamar-{$instalasi}");
        return DocoHelpers::response($response);
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['nama_rs'] = Yii::$app->docoVars->identity('nama_rumahsakit');
        $path = Yii::getAlias("@download") . "/kamar.pdf";
        try {
            $response = $this->_restMaster->get('kamar/cetak-pdf?' . http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionUpdate($id)
    {
        $title = Yii::t('fe', 'Ubah Kamar');
        $subtitle = Yii::t('fe', 'Form Ubah Kamar');
        $id = DocoHelpers::decrypt($id);
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $model = new KamarForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $scenario = 'update';
        if ($post) {

            $model->load($post);
            $model->kamarruangan_nokamar = trim($model->kamarruangan_nokamar);
            if ($model->validate()) {
                $model->is_active = $post['KamarForm']['is_active'];
                $response = $this->_restMaster->request('POST', 'kamar/update?id=' . $id, [
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($response->getBody(), true);
                $res_status = isset($response['metadata']['status']) ? $response['metadata']['status'] : '';

                if ($res_status == 200) {
                    $data_dashboard['reload'] = 1;
                    $mode = Yii::$app->params->mode;
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'display-dashboard-kamar-'.$mode,
                        'message' => json_encode(['data' => $data_dashboard])
                    ]);
                }
                Yii::$app->cache->delete("data-kamar-{$instalasi}");
                return DocoHelpers::response($response,false,'KamarForm');
            } else {
                $errors = DocoHelpers::parseError($model->errors,'KamarForm');
                return DocoHelpers::response([
                        'response' => [
                            'data' => $errors
                        ]
                    ],422);
            }
        } else {
            $response = $this->_restMaster->get('kamar/view-data?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $model->attributes = $body['response'];
            $model->is_active = $body['response']['is_active'];
            $model->is_active = ($model->is_active) ? '1' : '0' ;
            $klasifikasi_exists = $body['response']['klasifikasi'];
            $data = $this->getData();
            $data_ruangan = !empty($data['data_ruangan']) 
                    ? ArrayHelper::map($data['data_ruangan'], 'ruangan_id', 'ruangan_nama')
                    : [];
            $data_jenis_kamar = !empty($data['data_jenis_kamar']) ? ArrayHelper::map($data['data_jenis_kamar'], 'lookup_id', 'jenis_kamar'): [];
            $data_kelas_pelayanan = !empty($data['data_pelayanan']) 
                ? ArrayHelper::map($data['data_pelayanan'], 'kelaspelayanan_id', 'kelaspelayanan_nama')
                : [];
            $data_jenis_kasus_penyakit = !empty($data['data_kasus_penyakit']) ? ArrayHelper::map($data['data_kasus_penyakit'], 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama'): [];
        }

        return $this->render('form', get_defined_vars());
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['nama_rs'] = Yii::$app->docoVars->identity('nama_rumahsakit');
        $path = Yii::getAlias("@download") . "/Master Kamar.xlsx";

        try {
            $response = $this->_restMaster->get('kamar/export-excel?'.http_build_query($yiiRestfulParams), [
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

    public function actionGetNamaRuangan()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $response = $this->_restMaster->request('POST', 'kamar/data-nama-ruangan',[
                    'form_params'=>['term'=>$_GET['q']['term']],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                // print_r($body['response']); die;
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['ruangan_id'], 'text' => $value['ruangan_nama']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    /*dashboar*/
    public function actionDashboard(){
        $namaRS = Yii::$app->docoVars->identity("nama_rumahsakit");
        $requests = Yii::$app->request;
        $post = $requests->post();
        
        if ($requests->post()) {
            $kelas_pelayanan = DocoHelpers::decrypt($post['kelas_pelayanan']);
            $ruangan = DocoHelpers::decrypt($post['ruangan']);
            // echo "<pre>";var_dump($kelas_pelayanan);die();
            $request = $this->_restMaster->post('allow/get-dashboard-kamar-ranap', [
                        'form_params' => ['kelas_pelayanan'=> $kelas_pelayanan,
                                        'ruangan'=>$ruangan
                        ]
                    ]);
            $body = json_decode($request->getBody(), true);
            $metadata = $body['metadata'];
            $response = $body['response'];
            
        } else {
            $request = $this->_restMaster->post('allow/get-dashboard-kamar-ranap', [
                            'form_params' => ['kelas_pelayanan'=> 0,
                                        'ruangan'=>0]
                        ]);
            $body = json_decode($request->getBody(), true);
            $metadata = $body['metadata'];
            $response = $body['response'];            
        }

        $getTempatTidur = $response['warna_tempat_tidur'];
        $getKelasPelayanan = $response['kelas_pelayanan'];
        $getRuanganFilter = $response['ruangan_filter'];
        $getKelasPelayananHeader = $response['kelas_pelayanan_header'];
        $getKelasPelayananFilter = $response['kelas_filter'];
        $getDasboarKamar = $response['dashboard_kamar'];
        // echo "<pre>";var_dump($ruangan);die();
        
        $imgQueue = Url::to('@web/media/img/icon-antrian/queue.png');
        $imgDisplay = Url::to('@web/media/img/icon-antrian/display-icon.png');

        $list_jenis_kelas_first = [
                'name_header' => '<br><b>'.Yii::t('fe', 'Kelas Pelayanan').'</b><br>'.Yii::t('fe', 'Semua Kelas'),
                'name' => Yii::t('fe', 'Semua Kelas'),
                'icon' => '<img src="'.$imgDisplay.'" >',
                'url' => Url::to([$this->_module .'/kelas-pelayanan','jenis_id' => 'all-kelas' ]),
            ];
        $ls_display = [];
        foreach ($getKelasPelayanan as $key => $value) {
            $keyJenisAntrian = DocoHelpers::encrypt($value['kelaspelayanan_id']);
            $list_jenis_kelas[] = [
                'name_header' => '<br><b>'.Yii::t('fe', 'Kelas Pelayanan').'</b><br>'.Yii::t('fe', $value['kelaspelayanan_nama']),
                'name' => Yii::t('fe', $value['kelaspelayanan_nama']),
                'icon' => '<img src="'.$imgDisplay.'" >',
                'url' => Url::to([$this->_module .'/kelas-pelayanan','jenis_id' => $keyJenisAntrian ]),
            ];
            $ls_display[$value['kelaspelayanan_id']] = $value['kelaspelayanan_nama'];

        }
        
        array_push($list_jenis_kelas, $list_jenis_kelas_first);
        $count = 0;
        $idx = 0;
        $newList = [];
        foreach ($list_jenis_kelas as $key => $value) {
            $newList[$idx][] = $value;
            if ($count == 2) {
                $count = -1;
                $idx++;
            }
            $count++;
        }
        $list_jenis_kelas = $newList;
        
        $filterRuangan = [];
        foreach ($getRuanganFilter as $k => $v) {
            $idk = DocoHelpers::encrypt($k);
            $filterRuangan[$idk] = $v;
        }

        $filterKelasPelayananHeader = [];
        foreach ($getKelasPelayananFilter as $x => $y) {
            $idx = DocoHelpers::encrypt($x);
            $filterKelasPelayananHeader[$idx] = $y;
        }
        //echo "<pre>";var_dump($getKelasPelayananHeader);die();
        return $this->render('dashboard', get_defined_vars());
    }

    /*dashboar Bhayangkara*/
    public function actionDashboardBhayangkara(){
        $namaRS = Yii::$app->docoVars->identity("nama_rumahsakit");
        $listGroupKelas = array(
                      array(
                        "kamars" => array( 
                                            array('kamarDetails' => array('id_kamar_detail' => 1,
                                                                    'name' => 'zzz',
                                                                    'kamarDetails' =>  array('id_kamar_detail' => 1
                                                                                            )
                                                                    ),

                                                ),
                        ),
                        "id_kamar_group_kelas" => "Alfred Hitchcockx",
                        "nama_group_kelas" => 'Super Vip',
                        "id_kamar_detail" => array('id' => 1,
                                                    'name' => 'zzz'
                                                    ),
                      ),
                    
                    );
        // echo "<pre>";var_dump($listGroupKelas);
        // die();
        /*$listGroupKelas = ['kamars'=>['id_kamar_group_kelas'=>1,
                                            'namea'=>'xxx',
                                            'nama_group_kelas'=>'123'
                                        ],
                                        ['id_kamar_group_kelas'=>1,
                                            'namea'=>'xxx',
                                            'nama_group_kelas'=>'123'
                                            ],
                        ];*/
                          /*                  ],
                          'z'=>'kamarsz',['id_kamar_group_kelas'=>1,
                                            'namea'=>'xxx',
                                            'nama_group_kelas'=>'123'
                                            ],                                    
                          'n'=>'kamarsn',['id_kamar_group_kelas'=>2,
                                            'namea'=>'zzz',
                                            'nama_group_kelas'=>'456'
                                            ]  */                                   
        // $data_identity = Yii::$app->cache->get('app');
        /*foreach ($listGroupKelas as $key => $value) {
            # code...
        }
        */
        return $this->render('dashboard_bhayangkara', get_defined_vars());
    }

    public function actionChangeStatus($id, $status)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->put('kamar/update-dashboard?id='.$id.'&is_dashboard='.$status);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Tampil di dashboard berhasil diubah."),
                'response' => $response
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
                'text' => \Yii::t('fe', "Tampil di dashboard tidak berhasil dubah.")
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

    public function actionRenderPageKamar()
    {
        $title = $this->_title;
        $data = $this->getDataIndex();
        $data_ruangan = !empty($data['data_ruangan'])? ArrayHelper::map($data['data_ruangan'], 'ruangan_nama', 'ruangan_nama'): [];
        $data_jenis_kamar = !empty($data['data_jenis_kamar'])? ArrayHelper::map($data['data_jenis_kamar'], 'jenis_kamar', 'jenis_kamar'): [];
        $data_pelayanan = !empty($data['data_pelayanan'])? ArrayHelper::map($data['data_pelayanan'], 'kelaspelayanan_nama', 'kelaspelayanan_nama'): [];
        $data_kasus_penyakit = !empty($data['data_kasus_penyakit'])? ArrayHelper::map($data['data_kasus_penyakit'], 'jeniskasuspenyakit_nama', 'jeniskasuspenyakit_nama'): [];
        $status = ['true'=>'Aktif', 'false'=>'Tidak Aktif'];
        return $this->renderPartial('_kamar', get_defined_vars());
    }

    public function actionRenderPageKlasifikasiKamar()
    {
        $data = $this->getDataIndex();
        $title = $this->_title;
        $data_ruangan = !empty($data['data_ruangan'])? ArrayHelper::map($data['data_ruangan'], 'ruangan_nama', 'ruangan_nama'): [];
        $data_pelayanan = !empty($data['data_pelayanan'])? ArrayHelper::map($data['data_pelayanan'], 'kelaspelayanan_nama', 'kelaspelayanan_nama'): [];
        $data_klasifikasi = !empty($data['data_klasifikasi'])? ArrayHelper::map($data['data_klasifikasi'], 'klasifikasikamar_id', 'klasifikasikamar_nama'): [];
        return $this->renderPartial('_klasifikasiKamar', get_defined_vars());
    }

    public function actionGetDataKlasifikasiKamar()
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

        $response = $this->guzzleExec($this->_restMaster, [
            'url' => 'kamar/index-klasifikasi-kamar',
            'payload' => [
                'query' => $yiiRestfulParams
            ]
        ]);

        $no = $request->get('start', 1);
        foreach ($response['data'] as $key => $value) {
            $no++;
            $primaryKey = $this->helper->encrypt($value['kamarruangan_id']);
            $value['primary'] = $primaryKey;
            $value['rowNum'] = $no;
            $data[$key] = $value;
            $data[$key]['namakelas_aplicare'] = $response['data'][$key]['namakelas_aplicare'] == "Pilih" ? " " : $response['data'][$key]['namakelas_aplicare'];
            $data[$key]['namatt_rsonline'] = $response['data'][$key]['namatt_rsonline'] == "Pilih" ? " " : $response['data'][$key]['namatt_rsonline'];
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $response['_meta']['totalCount'];
        $result['recordsFiltered'] = $response['_meta']['totalCount'];

        return $result;
    }

    public function actionCreateKlasifikasiKamar()
    {
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_subMenuTitleKlasifikasiKamar);
        $request = Yii::$app->request;
        $model = new MappinganKlasifikasiKamarForm;
        $model->scenario = 'create';
        if($request->post()) {
            $post = $request->post();
            $model->load($post);
            if ($model->validate()) {
                try {
                    $response = $this->guzzleExec($this->_restMaster, [
                        'url' => 'kamar/upsert-klasifikasi-kamar',
                        'method' => 'post',
                        'payload' => [
                            'form_params' => $model->attributes,
                            'query' => [
                                'type' => 'create'
                            ]
                        ]
                    ]);
                    return DocoHelpers::response($response);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), 'MappinganKlasifikasiKamarForm');
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, 'MappinganKlasifikasiKamarForm');
            }
        } else {
            $response = $this->guzzleExec($this->_restMaster, [
                'url' => 'kamar/api-klasifikasi-kamar',
                'payload' => [
                    'query' => [
                        'type' => 'create'
                    ]
                ]
            ]);

            $klasifikasi = ArrayHelper::getValue($response,'klasifikasi');
            $kamar = ArrayHelper::getValue($response,'listKamar');
            return $this->renderPartial('form_klasifikasi_kamar', get_defined_vars());
        }
    }

    public function actionUpdateKlasifikasiKamar($id)
    {
        try {
            $title = Yii::t('fe', 'Ubah') . ' ' . Yii::t('fe', $this->_subMenuTitleKlasifikasiKamar);
            $model = new MappinganKlasifikasiKamarForm;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);
            $model->scenario = 'update';
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->guzzleExec($this->_restMaster, [
                        'url' => 'kamar/upsert-klasifikasi-kamar',
                        'method' => 'post',
                        'payload' => [
                            'form_params' => $model->attributes,
                            'query' => [
                                'type' => 'update'
                            ]
                        ]
                    ]);
                    return DocoHelpers::response($response);
                } else {
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, 'MappinganKlasifikasiKamarForm');
                }
            } else {
                $response = $this->guzzleExec($this->_restMaster, [
                    'url' => 'kamar/api-klasifikasi-kamar',
                    'payload' => [
                        'query' => [
                            'id' => $id,
                            'type' => 'update'
                        ]
                    ]
                ]);

                $klasifikasi = ArrayHelper::getValue($response,'klasifikasi');
                $kamar = ArrayHelper::getValue($response,'listKamar');
                $model->klasifikasikamar_id = ArrayHelper::getValue($response,'dataKamar.klasifikasikamar_id');
                $model->kamarruangan_id = ArrayHelper::getValue($response,'dataKamar.kamarruangan_id');
                $model->kamarruangan_nokamar = ArrayHelper::getValue($response,'dataKamar.kamarruangan_id');
                return $this->renderPartial('form_klasifikasi_kamar', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionDeleteKlasifikasiKamar($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->guzzleExec($this->_restMaster, [
                'url' => 'kamar/delete-klasifikasi-kamar',
                'method' => 'post',
                'payload' => [
                    'query' => [
                        'id' => $id
                    ]
                ]
            ]);
            
            $res_status = isset($response['httpStatusCode']) ? $response['httpStatusCode'] : 200;

            if($res_status == 422) {
                $resPayload = [
                    "metadata" => [
                        "status" =>  422,
                        "message" =>  "Unprocessable Entity"
                    ],
                    "response" => [
                        "title" => 'Proses Gagal!',
                        "text" => 'Data Sudah Dimappingkan dengan master tempat tidur'
                    ]
                ];
                return DocoHelpers::response($resPayload);
            } 
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message, [
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionExportPdfKlasifikasiKamar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['nama_rs'] = Yii::$app->docoVars->identity('nama_rumahsakit');
        $path = Yii::getAlias("@download") . "/klasifikasiKamar.pdf";
        try {
            $response = $this->guzzleExec($this->_restMaster, [
                'url' => 'kamar/export-pdf-klasifikasi-kamar?',
                'payload' => [
                    'query' => $yiiRestfulParams,
                    'save_to' => $path,
                ]
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcelKlasifikasiKamar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['nama_rs'] = Yii::$app->docoVars->identity('nama_rumahsakit');
        $path = Yii::getAlias("@download") . "/klasifikasiKamar.xlsx";

        try {
            $response = $this->guzzleExec($this->_restMaster, [
                'url' => 'kamar/export-excel-klasifikasi-kamar?',
                'payload' => [
                    'query' => $yiiRestfulParams,
                    'save_to' => $path,
                ]
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

    private function getDataIndex($id='')
    {
        try {
            if ($id) {
                $response = $this->_restMaster->request('GET', 'kamar/generate-api?id='.$id);
            }else{
                $response = Yii::$app->docoRest->master->request('GET', 'kamar/generate-api');
            }
            $body = json_decode($response->getBody(),TRUE);
            $return = [
                'data_ruangan' => $body['response']['data-ruangan'],
                'data_jenis_kamar' => $body['response']['data-jenis-kamar'],
                'data_pelayanan' => $body['response']['data-pelayanan'],
                'data_kasus_penyakit' => $body['response']['data-kasus-penyakit'],
                'data_klasifikasi' => $body['response']['data-klasifikasi'],
            ];

            return $return;
        } catch (RequestException $e) {
            // dump($e);exit;
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
