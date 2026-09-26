<?php
// Author : Rizal Faidin

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\KelasPelayananForm;
use app\modules\master\models\KelasRuanganForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class KelasController extends DocoController
{
    // protected $_title = Yii::t("fe", "Kelas pelayanan");
    protected $_title = 'Kelas';
    protected $_titlePelayanan = 'Kelas pelayanan';
    protected $_titleRuangan = 'Kelas ruangan';
    protected $_module = '/master/kelas/';
    protected $_restMaster;
    protected $_statusAktif = [
        0 => 'Tidak Aktif',
        1 => 'Aktif',
    ];

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
        // $status = $this->_status;
        // $options = $this->_options;
        return $this->render('index', get_defined_vars());
    }

    public function actionPageKelasPelayanan() 
    {
        // $status = $this->_status;
        $status = $this->_statusAktif;
        $listJenisKelas = $listRuangan = [];
        try {
            $kelasPelayananRequest = $this->_restMaster->get('kelas-pelayanan/list-jenis-kelas');
            $body = json_decode($kelasPelayananRequest->getBody(),true);

            $listJenisKelas = $body['response']['jenis_kelas'];
            $listRuangan = $body['response']['ruangan'];
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }

        return $this->renderPartial('_kelasPelayanan', get_defined_vars());
    }

    public function actionPageKelasRuangan() 
    {
        $status = $this->_status;
        $listJenisKelas = $listRuangan = [];
        try {
            $KelasPelayananRequest = $this->_restMaster->get('kelas-pelayanan/list-jenis-kelas');
            $body = json_decode($KelasPelayananRequest->getBody(),true);
            $listKelasPelayanan = $body['response']['kelas_pelayanan'];
            $listJenisKelas = $body['response']['jenis_kelas'];
            $listRuangan = $body['response']['ruangan'];
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }

        return $this->renderPartial('_kelasRuangan', get_defined_vars());
    }

    public function actionSetListPelayanan()
    {
        $request = Yii::$app->request;
        $model = new KelasPelayananForm;
        $model->scenario = 'set-list';
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $post = $request->post();
            $model->load($post);
            if ($model->validate()) {
                $dataPelayanan = Yii::$app->cache->get("data-pelayanan-{$instalasi}");
                if ($dataPelayanan === false) {
                    Yii::$app->cache->set("data-pelayanan-{$instalasi}",[]);
                    $dataPelayanan = [];
                }

                $dataPelayanan[] = [
                    'jeniskelas_id' => isset($model->jeniskelas_id) ? $model->jeniskelas_id : null,
                    'kelaspelayanan_nama' => $model->kelaspelayanan_nama,
                    'kelaspelayanan_namalainnya'  => $model->kelaspelayanan_namalainnya
                ];
                Yii::$app->cache->set("data-pelayanan-{$instalasi}",$dataPelayanan,3600);
                $response['response'] = [
                    'title' => 'Proses Berhasil !',
                    'text' => 'Data berhasil di tambah'
                ];
                return DocoHelpers::response($response);
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response,422,$formName);
            }
        }
    }

    public function actionGetListPelayanan()
    {
        $request = Yii::$app->request;

        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $dataPelayanan = Yii::$app->cache->get("data-pelayanan-{$instalasi}");

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;

        if ($dataPelayanan !== false) {
            $no = $request->get('start',1);
            foreach ($dataPelayanan as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($key);
                $data[] = [
                    'rowNum' => $no,
                    'kelaspelayanan_nama' => $value['kelaspelayanan_nama'],
                    'kelaspelayanan_namalainnya' => $value['kelaspelayanan_namalainnya'],
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'action' => Url::to([$this->_module .'delete-list-pelayanan','id' => $primaryKey]),
                        ]
                    )
                ];
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $no;
            $result['recordsFiltered'] = $no;
        }

        return DocoHelpers::response($result);
    }

    public function actionDeleteListPelayanan($id)
    {
        $id = DocoHelpers::decrypt($id);
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $dataPelayanan = Yii::$app->cache->get("data-pelayanan-{$instalasi}");
        if ($dataPelayanan !== false) {
            if (isset($dataPelayanan[$id])) {
                unset($dataPelayanan[$id]);
                Yii::$app->cache->set("data-pelayanan-{$instalasi}",$dataPelayanan);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    // pelayanan
    public function actionCreatePelayanan()
    {
        // Init
        $model = new KelasPelayananForm;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_titlePelayanan);
        $status = $this->_status;
        $options = $this->_options;

        $request = Yii::$app->request;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $post = $request->post();
            $i = 0;
            $dataKelasPelayanan = [];
            
            $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
            $dataPelayanan = Yii::$app->cache->get("data-pelayanan-{$instalasi}");
            if ($dataPelayanan !== false) {
                foreach($dataPelayanan as $row) {
                     $dataKelasPelayanan[] = array(
                            'jeniskelas_id' => isset($post['jeniskelas_id']) ? $post['jeniskelas_id'] : null,
                            'kelaspelayanan_nama' => $row['kelaspelayanan_nama'],
                            'kelaspelayanan_namalainnya' => $row['kelaspelayanan_namalainnya']
                        );

                     $i++;
                }
            }

            $post['list_kelas_pelayanan'] = $dataKelasPelayanan;
            $model->attributes = $post;
            $model->scenario = 'save-all';
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('kelas-pelayanan/create', [
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);
                    if ($response['metadata']['status'] == 200) {
                        Yii::$app->cache->set("data-pelayanan-{$instalasi}",[]);
                    }
                    return DocoHelpers::response($response,false,$formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response,422,$formName);
            }
        } else {
            $KelasPelayananRequest = $this->_restMaster->get('kelas-pelayanan/list-jenis-kelas');
            $body = json_decode($KelasPelayananRequest->getBody(),TRUE);
            $listJenisKelas = $body['response']['jenis_kelas'];
            return $this->render('form_create_kelasPelayanan', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->delete('kelas-pelayanan/delete?id='.$id);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
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

    public function actionDeleteRuangan($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $json = json_decode($id, true);
            $kelaspelayanan_id = $json[0];
            $ruangan_id = $json[1];
            $response = $this->_restMaster->request('DELETE', 'kelas-ruangan/delete-ruangan',[
                            'query' => [
                                'kelaspelayanan_id' => $kelaspelayanan_id,
                                'ruangan_id' => $ruangan_id,
                            ]
                        ]);
            $response = json_decode($response->getBody(),true);
            if ($response['metadata']['status'] == 200) {
                # code...
                Yii::$app->cache->delete("get-kelas-pelayanan-{$ruangan_id}");
            }
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus',
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUpdatePelayanan($id)
    {
        try {
            $title = Yii::t('fe', 'Ubah') . ' ' . Yii::t('fe', $this->_titlePelayanan);
            $model = new KelasPelayananForm;
            // $status = $this->_status;
            $status = $this->_statusAktif;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'kelas-pelayanan/update',[
                                        'query' => ['id' => $id],
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,$formName);
                } else {
                    $response = $model->errors;
                    return DocoHelpers::response($response,422,$formName);
                }
            } else {
                $KelasPelayananRequest = $this->_restMaster->get('kelas-pelayanan/list-jenis-kelas',[
                    'query' => [
                        'id' => $id
                    ]
                ]);
                $body = json_decode($KelasPelayananRequest->getBody(),TRUE);
                
                if (isset($body['response'])) {
                    $listJenisKelas = $body['response']['jenis_kelas'];
                    $listPelayanan = $body['response']['data_pelayanan'];
                    $model->attributes = $listPelayanan;
                    $model->is_active = $model->is_active ? 1 : 0;
                    return $this->renderPartial('form_kelasPelayanan',get_defined_vars());
                } else {
                    return $this->actionCreate();
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $date = date("Y-m-d");
        $path = Yii::getAlias("@download") . "/kelas-pelayanan.pdf";
        $params = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $response = $this->_restMaster->get('kelas-pelayanan/export-excel',[
                    'query' => $params
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];
            return DocoHelpers::downloadFile($url);
       } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
       } catch (RequestException $e){
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
       }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $date = date("Y-m-d");
        $path = Yii::getAlias("@download") . "/kelas-pelayanan.pdf";
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $response = $this->_restMaster->get('kelas-pelayanan/cetak-kelas-pelayanan', [
                'query' => $yiiRestfulParams,
                'save_to' => $path
            ]);

            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdfRuangan()
    {
        $request = Yii::$app->request;
        $date = date("Y-m-d");
        $path = Yii::getAlias("@download") . "/kelas-ruangan.pdf";
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $response = $this->_restMaster->get('kelas-ruangan/cetak-kelas-ruangan', [
                'query' => $yiiRestfulParams,
                'save_to' => $path
            ]);

            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcelRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());        
        try {
            $response = $this->_restMaster->get('kelas-ruangan/export-excel-ruangan?'.http_build_query($yiiRestfulParams));    
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];                     
        
            return DocoHelpers::downloadFile($url);           
       } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
       } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
       }
    }

    public function actionGetDataKelasPelayanan()
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

            $response = $this->_restMaster->get('kelas-pelayanan', [
                'form_params' => [],
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kelaspelayanan_id']);
                $value['primary'] = $primaryKey;
                unset($value['kelaspelayanan_id']);

                $value['rowNum'] = $no;
                $value['is_active'] = DocoHelpers::isActive($value['is_active']);
                
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionChangeStatus(){
        $request = Yii::$app->request;
        $model = new KelasPelayananForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $post = $request->post();

        if ($post) {

            $id = DocoHelpers::decrypt($post['id']);

            $model->load($request->post());

            try {
                $response = $this->_restMaster->put('kelas-pelayanan/update?id='.$id, [
                    'form_params' => ['is_active' => $post['is_active']]
                ]);

                return DocoHelpers::responseJsonString($response->getBody(), $formName);
            } catch (RequestException $e) {
                return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
            } catch (\Exception $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }
        }
    }

    public function actionChangeStatusPelayanan($id, $status)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            // $response = $this->_restMaster->put('cara-bayar/update?id='.$id, [
            //     'form_params' => ["is_active" => $status]
            // ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Status berhasil diubah.")
            ];
            return DocoHelpers::responseTemplate(
                // $response->getStatusCode(), 
                200, 
                "OK", 
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ')." !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
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


    // kelas ruangan

    public function actionGetDataKelasRuangan()
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
            $response = $this->_restMaster->get('kelas-ruangan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primary = json_encode([$value['kelaspelayanan_id'],$value['ruangan_id']]);
                $value['primary'] = DocoHelpers::encrypt($primary);
                $value['rowNum'] = $no;
                $value['is_active'] = ($value['is_active'])? \Yii::t('fe', 'Aktif') : \Yii::t('fe', 'Non Aktif');
                
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['count'];
            $result['recordsFiltered'] = $body['response']['count'];
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCreateRuangan()
    {
        // Init
        try {
            $model = new KelasRuanganForm;
            $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_titleRuangan);
            $status = $this->_status; $options = $this->_options;

            $kelasPelayananRequest = $this->_restMaster->get('kelas-pelayanan/list-kelas-pelayanan');
            $bodyKelasPelayanan = json_decode($kelasPelayananRequest->getBody(),true);
            $listPelayanan = $bodyKelasPelayanan['response'];
            
            $RuanganRequest = $this->_restMaster->get('kelas-pelayanan/list-ruangan');
            $body = json_decode($RuanganRequest->getBody(),TRUE);
            $listRuangan = $body['response'];

            $request = Yii::$app->request;
            if ($request->post())
            {
                $post = $request->post();
                $ruangan_key = !isset($post['KelasRuanganForm']['list_ruangan_id']) ? $post['KelasRuanganForm']['list_ruangan_id'] : null ;
                $model->attributes = $post['KelasRuanganForm'];
                $model->list_ruangan_id = $post['KelasRuanganForm']['list_ruangan_id'];
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'kelas-ruangan/create-ruangan',[
                                        'form_params' => $post
                                ]);
                    $response = json_decode($response->getBody(),true);
                    if ($response['metadata']['status'] == 200 && $ruangan_key != null) {
                        # code...
                        foreach ($ruangan_key as $value) {
                            # code...
                            Yii::$app->cache->delete("get-kelas-pelayanan-{$value}");
                        }
                    }
                    return DocoHelpers::response($response);
                    return DocoHelpers::response($response,false,true);
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'KelasRuanganForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                return $this->renderPartial('form_kelasRuangan', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUpdateRuangan($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $json = json_decode($id, true);
            $kelaspelayanan_id = $json[0];
            $ruangan_id = $json[1];
            $title = Yii::t('fe', 'Ubah') . ' ' . Yii::t('fe', $this->_titleRuangan);
            $model = new KelasRuanganForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                $ruangan_key = !isset($post['KelasRuanganForm']['list_ruangan_id']) ? $post['KelasRuanganForm']['list_ruangan_id'] : null ;
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'kelas-ruangan/update-ruangan',[
                                        'query' => ['kelaspelayanan_id' => $kelaspelayanan_id, 'ruangan_id' => $ruangan_id],
                                        'form_params' => $post
                                ]);
                    $response = json_decode($response->getBody(),true);
                    if ($response['metadata']['status'] == 200 && $ruangan_key != null) {
                        # code...
                        foreach ($ruangan_key as $value) {
                            # code...
                            Yii::$app->cache->delete("get-kelas-pelayanan-{$value}");
                        }
                    }
                    // return DocoHelpers::response($response);
                    return DocoHelpers::response($response,false,$formName);
                } else {
                    $response = $model->errors;
                    return DocoHelpers::response($response,422,$formName);
                }
            } else {
                $result = $this->findRuangan($kelaspelayanan_id, $ruangan_id);
                $kelasPelayananRequest = $this->_restMaster->get('kelas-pelayanan/list-kelas-pelayanan');
                $bodyKelasPelayanan = json_decode($kelasPelayananRequest->getBody(),true);
                $listPelayanan = $bodyKelasPelayanan['response'];
                
                $RuanganRequest = $this->_restMaster->get('kelas-pelayanan/list-ruangan');
                $body = json_decode($RuanganRequest->getBody(),TRUE);
                $listRuangan = $body['response'];

                if (isset($result['response'])) {
                    $model->attributes = $result['response'];
                    $model->list_ruangan_id = $model->ruangan_id;
                    return $this->renderPartial('form_kelasRuangan',get_defined_vars());
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function findRuangan($kelaspelayanan_id, $ruangan_id)
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restMaster->request('GET', 'kelas-ruangan/edit',
                [
                    'query' => ['kelaspelayanan_id' => $kelaspelayanan_id, 'ruangan_id' => $ruangan_id]
                ]
            );
            return json_decode($response->getBody(),true);
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actionGetKelasPelayanan()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){        

            $response = $this->_restMaster->request('POST', 'kelas-pelayanan/list-kelas-pelayanan',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                                
            foreach ($body['response'] as $key => $value) {                
                $data[] = ['id'=>$value['kelaspelayanan_id'],'text'=>$value['kelaspelayanan_nama']];
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
    }

    public function actionListKelasPelayanan($q = null)
    {   
        $response = $this->_restMaster->request('GET', 'kelas-ruangan/list-kelas-pelayanan?q='.$q);
        $body = json_decode($response->getBody(),TRUE);
        $out = [];
        foreach ($body['response'] as $v) {
            $out[] = ['value' => $v['kelaspelayanan_nama']];
        }

        echo Json::encode($out);
    }

}
