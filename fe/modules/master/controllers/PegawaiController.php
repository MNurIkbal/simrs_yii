<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-21 11:47:03
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-04-10 15:00:01
 */

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use Doco\master\models\PegawaiRuanganForm;
use Doco\master\models\PegawaiForm;
use Doco\master\models\EsignRegForm;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

class PegawaiController extends DocoController
{
	protected $_title = "Master Pegawai";
    protected $_module = '/master/pegawai';
    protected $_restMaster;
    protected $_workspace;
    protected $_ruangan_id;
    protected $_ruangan_nama;
    protected $_request;
    protected $_session;
    protected $_instalasi;
    protected $_backUrl;
    protected $_uid;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $this->_backUrl = 'pegawai/';
        $this->_uid = Yii::$app->docoVars->user('uid');
    }

    public function actionListJabatan()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 10;
        $offset = ($page-1)*5;
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $term = $request->get('q');
            $q = isset($term['term']) ? $term['term'] : null;
            $result = $this->_restMaster->get('allow/list-jabatan',[
                'query' => [
                    'term' => $q,
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
                            'id'=>$value['jabatan_id'],
                            'text'=>$value['jabatan_nama'],
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

    public function actionListPangkat()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 10;
        $offset = ($page-1)*5;
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $term = $request->get('q');
            $q = isset($term['term']) ? $term['term'] : null;
            $result = $this->_restMaster->get('allow/list-pangkat',[
                'query' => [
                    'term' => $q,
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
                            'id'=>$value['pangkat_id'],
                            'text'=>$value['pangkat_nama'],
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

    public function actionIndex()
    {
        $title = $this->_title;
        try {
            $status_arr = [1 => Yii::t('fe','Pegawai Aktif'), Yii::t('fe','Pegawai Tidak aktif')];
            return $this->render('index', get_defined_vars());
        } catch (\Exception $e){
            $status_arr = [];
            return $status_arr;
        }
    }

    public function actionDetail($id)
    {
        $title = "Detail pegawai ruangan";
        $request = Yii::$app->request;
        $rawId = $id;
        $id = json_decode(DocoHelpers::decrypt($id));
        $pegawai_id = $id[0];
        $ruangan_id = $id[1];
        $model = new PegawaiRuanganForm;
        $instalasi = $this->_restMaster->get('pegawai-ruangan/get-list-instalasi',['query'=>['user_id'=>$this->_uid],'form_params'=>[]]);
        $instalasi = json_decode($instalasi->getBody(), true);
        $instalasi = ArrayHelper::map($instalasi['response']['data'],'instalasi_id','instalasi_nama');

        $response = $this->_restMaster->get('pegawai-ruangan/index?user_id='.$this->_uid.'&advanced-filter[pegawai_id]='.$pegawai_id.'&advanced-filter[ruangan_id]='.$ruangan_id, ['form_params' => []]);
        $data = json_decode($response->getBody(), true);
        $data = $data['response']['data'][0];
        $instalasi_id = $this->_instalasi;
        $data['is_active'] = $data['status'];
        $model->attributes = $data;

        // Get kelompok pegawai nama
        $response = $this->_restMaster->get('pegawai-ruangan/get-kelompok-pegawai-by-id?id='.$model->kelompokpegawai_id, ['form_params' => []]);
        $kelompokPegawai = json_decode($response->getBody(), true);
        $kelompokPegawai = isset($kelompokPegawai['response']['kelompokpegawai_nama']) ? $kelompokPegawai['response']['kelompokpegawai_nama'] : '';

        if($request->post()){
            $post = $request->post();
            $post['user_id'] = Yii::$app->docoVars->user("id");
            $response = $this->_restMaster->request('POST', 'pegawai-ruangan/update', ['form_params'=>$post]);
            $body = json_decode($response->getBody(), true);
            $return = ['response'=>$body['response']];
            return DocoHelpers::response($return);
        }
        $model->instalasi_id = null;
		return $this->render('detail', get_defined_vars());
    }


    public function actionCreate()
    {
        $request = Yii::$app->request;
        $title = "Tambah Pegawai";
        $preview_file_gambar = false;
        $preview_file_ttd = false;

        $gelar_depan = $gelar_belakang = $status_perkawinan = $agama = $golongan_darah = $warga_negara = $suku = $provinsi = $kabupaten = $kecamatan = $kelurahan = $jenis_kelamin = $pendidikan = $jabatan = $kualifikasi_pendidikan = $pangkat = $kelompok_pegawai = $warnakulit = $status_pegawai = $nama_bank = [];

        $requests = $this->_restMaster->get('allow/get-api');
        $response = json_decode($requests->getBody(), true);
        $response = $response['response']['lookup'];

        $gelar_depan = $response['gelar_depan'];
        $gelar_depan= ArrayHelper::map($gelar_depan,'lookup_id','lookup_name');

        $gelar_belakang = $response['gelar_belakang'];
        $gelar_belakang = ArrayHelper::map($gelar_belakang, 'gelarbelakang_id', 'gelarbelakang_nama');

        $jenis_kelamin = $response['jenis_kelamin'];
        $jenis_kelamin = ArrayHelper::map($jenis_kelamin, 'lookup_id', 'lookup_name');

        $status_perkawinan = $response['status_perkawinan'];
        $status_perkawinan = ArrayHelper::map($status_perkawinan, 'lookup_id', 'lookup_name');

        $agama = $response['agama'];
        $agama = ArrayHelper::map($agama, 'lookup_id', 'lookup_name');

        $golongan_darah = $response['golongan_darah'];
        $golongan_darah = ArrayHelper::map($golongan_darah, 'lookup_id', 'lookup_name');

        $warga_negara = $response['warga_negara'];
        $warga_negara = ArrayHelper::map($warga_negara, 'lookup_id', 'lookup_name');

        $suku = $response['suku'];
        $suku = ArrayHelper::map($suku, 'suku_id', 'suku_nama');

        $provinsi = $response['provinsi'];
        $provinsi = ArrayHelper::map($provinsi, 'propinsi_id', 'propinsi_nama');

        $pendidikan = $response['pendidikan'];
        $pendidikan = ArrayHelper::map($pendidikan, 'pendidikan_id', 'pendidikan_nama');

        $kualifikasi_pendidikan = $response['kualifikasi_pendidikan'];
        $kualifikasi_pendidikan = ArrayHelper::map($kualifikasi_pendidikan, 'pendkualifikasi_id', 'pendkualifikasi_nama');

        $jabatan = $response['jabatan_m'];
        $jabatan = ArrayHelper::map($jabatan, 'jabatan_id', 'jabatan_nama');

        $pangkat = $response['pangkat'];
        $pangkat = ArrayHelper::map($pangkat, 'pangkat_id', 'pangkat_nama');

        $warna_kulit = $response['warna_kulit'];
        $warna_kulit = ArrayHelper::map($warna_kulit, 'lookup_id', 'lookup_name');

        $kelompok_pegawai = $response['kelompok_pegawai'];
        $kelompok_pegawai = ArrayHelper::map($kelompok_pegawai, 'kelompokpegawai_id', 'kelompokpegawai_nama');

        $status_pegawai = $response['kategori_pegawai'];
        $status_pegawai = ArrayHelper::map($status_pegawai, 'lookup_id', 'lookup_name');

        $nama_bank = $response['nama_bank'];
        $nama_bank = ArrayHelper::map($nama_bank, 'bank_id', 'nama_bank');

        $kemampuan_bahasa = $response['bahasa'];
        $kemampuan_bahasa = ArrayHelper::map($kemampuan_bahasa, 'lookup_id', 'lookup_name');

        $status_aktif = [1=>"Aktif", "Tidak Aktif"];

        $model = new PegawaiForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model->tgl_lahirpegawai = date('d-M-Y');
        if ($request->post()) {
            $post = $request->post();
            $model->load($post);
            $file = UploadedFile::getInstance($model, 'photopegawai');
            $file_ttd = UploadedFile::getInstance($model, 'tanda_tangan');
            $model->tgl_lahirpegawai = date('Y-m-d', strtotime($post['PegawaiForm']['tgl_lahirpegawai']));
            $post['PegawaiForm']['photopegawai'] = !empty($file) ? $file->name : null ;
            //fungsi upload gambar
            if (!empty($file)) {
                $webroot = \Yii::getAlias('@webroot');
                $path = '/media/img/foto-dokter/';
                $size = $file->size;
                $ext = end(explode(".", $file->name));
                $fileName = $file->name;
                $cek_dir = $webroot.$path;
                if (!is_dir($cek_dir)){
                    mkdir($cek_dir, 0777, true);
                }
                $file->saveAs($webroot.$path.$fileName);

                $path_blob = $webroot.$path.$fileName;

                $mimetype = FileHelper::getMimeType($path_blob);

                $source = file_get_contents($path_blob);
                $base64 = base64_encode($source);
                $blob = 'data:'.$mimetype.';base64,'.$base64;
                $post['PegawaiForm']['photopegawai_blob'] = $blob;
            }

            if (!empty($file_ttd)) {
                $webroot = \Yii::getAlias('@webroot');
                $path = '/uploads/signature/';
                $size = $file_ttd->size;
                $ext = end(explode(".", $file_ttd->name));
                $fileName = $file_ttd->name;
                $cek_dir = $webroot.$path;
                if (!is_dir($cek_dir)){
                    mkdir($cek_dir, 0777, true);
                }
                $file_ttd->saveAs($webroot.$path.$fileName);
                $post['PegawaiForm']['tanda_tangan'] = !empty($file_ttd) ? $file_ttd->name : null ;
            }

            if ($model->validate()) {
                $post['user_id'] = Yii::$app->docoVars->user("id");
                $response = $this->_restMaster->post('pegawai/create-pegawai', [
                    'form_params'=>$post
                ]);
                $body = json_decode($response->getBody(), True);
                return DocoHelpers::response($body);
            } else {
                $errors = DocoHelpers::parseError($model->errors,'PegawaiForm');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ],422);
            }
        }

        return $this->render('form', get_defined_vars());
    }

    public function actionListRuanganByInstalasi()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $instalasi_id = $post['depdrop_parents'][0];

        $RuanganRequest = $this->_restMaster->get('pegawai-ruangan/get-list-ruangan',['query'=>['user_id'=>$this->_uid,'instalasi_id'=>$instalasi_id],'form_params'=>[]]);
        $ruangan = json_decode($RuanganRequest->getBody(), true);
        $ruangan = $ruangan['response']['data'];
        $ddlRuangan = ArrayHelper::map($ruangan, 'ruangan_id','ruangan_nama');

        $out = [];
        foreach($ddlRuangan as $key => $value) {
            $out[] = [
                        'id' => $key,
                        'name' => $value
                    ];
        }
        return DocoHelpers::response(['output'=>$out, 'selected'=>'']);
    }

    public function actionGetDataPegawaiRuangan()
    {
    	Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw',1);
        $data = [];
        try {
            $response = $this->_restMaster->get('pegawai-ruangan/index?instalasi_id='.$this->_instalasi.'&user_id='.$this->_uid.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primary = json_encode([$value['pegawai_id'],$value['ruangan_id']]);
                $value['primary'] = DocoHelpers::encrypt($primary);
                $value['rowNum'] = $no;
                $value['is_active'] = ($value['status']) ? Yii::t('fe','Aktif') : Yii::t('fe','Tidak aktif');
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['totalCount'];
            $result['recordsFiltered'] = $body['response']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetRuangan()
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $id = $parents[0];
                $data = [];
                $ruangan = $this->_restMaster->get('ruangan?advanced-filter[is_active]=1&advanced-filter[instalasi_id]='.$id,['form_params'=>[]]);
                $ruangan = json_decode($ruangan->getBody(), true);
                $ruangan = $ruangan['response']['data'];
                //$ruangan = ArrayHelper::map($ruangan, 'ruangan_id','ruangan_nama');
                $no = 0;
                foreach ($ruangan as $value) {
                    $data[$no] = ['id'=>$value['ruangan_id'],'name'=>$value['ruangan_nama']];
                    $no++;
                }
                $result = ['output'=>$data, 'selected'=>''];
                return DocoHelpers::response($result);
            }
        }
    }
    //get data pegawai buat depdrop

    public function actionGetPegawai()
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $id = $parents[0];
                $data = [];
                $ruangan = $this->_restMaster->get('pegawai?advanced-filter[is_active]=1&advanced-filter[kelompokpegawai_id]='.$id,['form_params'=>[]]);
                $ruangan = json_decode($ruangan->getBody(), true);
                $ruangan = $ruangan['response']['data'];
                //$ruangan = ArrayHelper::map($ruangan, 'ruangan_id','ruangan_nama');
                $no = 0;
                foreach ($ruangan as $value) {
                    $data[$no] = ['id'=>$value['pegawai_id'],'name'=>$value['nomorindukpegawai'].' - '.$value['nama_pegawai']];
                    $no++;
                }
                $result = ['output'=>$data, 'selected'=>''];
                return DocoHelpers::response($result);
            }
        }else{
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
                $response = $this->_restMaster->get('pegawai/index-view?sel_wrap=.filter-form'.http_build_query($yiiRestfulParams));
                $body = json_decode($response->getBody(), True);

                $no = $request->get('start',1);
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                        'class' => 'btn btn-success btn-xs data-check',
                        'data-sel_wrap' =>'.filter-form',
                        'data-key' => $value['pegawai_id'],
                        'data-label' => $value['nama_pegawai'],
                        'title' => \Yii::t('fe', 'Klik'),
                    ]);
                    $value['rowNum'] = $no;
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


    public function actionGetKelompokpegawai()
    {
        if(isset($_GET['q']) && !empty($_GET['q'])){
            $response = $this->_restMaster->request('POST', 'pegawai-ruangan/data-kelompokpegawai',[
                            'form_params'=>['term'=>$_GET['q']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['kelompokpegawai_id'],'text'=>$value['kelompokpegawai_nama']];
            }
            // Return
            return Json::encode(['results' => $data]);
        }
    }
    //get data pegawai buat data di modal
    public function actionGetDataPegawai()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){

            $response = $this->_restMaster->request('POST', 'pegawai-ruangan/data-pegawai',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['nama_pegawai'],'text'=>$value['nomorindukpegawai'] . ' - ' . $value['nama_pegawai']];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }

    public function actionGetDataPegawaiTabel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        try {
            $response = $this->_restMaster->get('pegawai/index-view?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pegawai_id']);
                unset($value['pegawai_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['aktif'] = ($value['is_active']) ? Yii::t('fe','Aktif') : Yii::t('fe','Tidak aktif');
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionModal(){
        $type = $_GET['tipe'];
        if($type == 'pegawai-nama'){
            $view = 'modal-pegawai';
        }else{
            $view = '';
        }
        return $this->renderAjax($view, get_defined_vars());
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/pegawai.xlsx";
            $response = $this->_restMaster->get('pegawai/export-excel?' .http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            // dump($body); die();
            $url = $body['response'];

            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e){
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $path = Yii::getAlias("@download") . "/pegawai.pdf";
        try {
            $response = $this->_restMaster->get('pegawai/export-pdf?'.http_build_query($yiiRestfulParams),[
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

    private function getDataApi($instalasi_id = null, $default = null)
    {
        try {
            $response = $this->_restMaster->get('allow/get-api?instalasi_id=' . $instalasi_id . '&default=' . $default);
            $body = json_decode($response->getBody(), true);

            $result = [
                'response' => $body['response']['lookup'],
                'lookup' => $body['response']['lookup'],
                'master' => $body['response']['master'],
                'ruangan' => $body['response']['ruangan'],
                'cara_bayar' => $body['response']['cara_bayar'],
                'asal_rujukan' => $body['response']['asal_rujukan'],
                'kelas_pelayanan' => $body['response']['kelas_pelayanan'],
                'karcis' => $body['response']['karcis'],
                'jeniskasus' => $body['response']['jeniskasus'],
                'dokter' => $body['response']['dokter'],
                'penjamin' => $body['response']['penjamin'],
                'instalasi' => isset($body['response']['instalasi']['data']) ? $body['response']['instalasi']['data'] : [],
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

    public function actionUpdate($id)
    {
        $title = Yii::t('fe', 'Ubah Pegawai');
        $id = DocoHelpers::decrypt($id);
        $model = new PegawaiForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $request = Yii::$app->request;
        $rootPath = Url::base(true);
        $preview_file_gambar = '';
        $preview_file_ttd = '';
        $path = '/media/img/foto-dokter/';
        $path_ttd = '/uploads/signature/';

        if ($request->post()) {
            $model->load($request->post());
            $file = UploadedFile::getInstance($model, 'photopegawai');
            $file_ttd = UploadedFile::getInstance($model, 'tanda_tangan');
            try {
                $post = $request->post();
                $model->load($post);
                $model->tgl_lahirpegawai = date('Y-m-d', strtotime($post['PegawaiForm']['tgl_lahirpegawai']));
                $post['PegawaiForm']['photopegawai'] = !empty($file) ? $file->name : null ;
                $post['PegawaiForm']['tanda_tangan'] = !empty($file_ttd) ? $file_ttd->name : null ;

                //fungsi upload gambar
                if (!empty($file)) {
                    $webroot = \Yii::getAlias('@webroot');
                    $size = $file->size;
                    $ext = end(explode(".", $file->name));
                    $fileName = $file->name;
                    $cek_dir = $webroot.$path;
                    if (!is_dir($cek_dir)){
                        mkdir($cek_dir, 0777, true);
                    }
                    $file->saveAs($webroot.$path.$fileName);

                    $path_blob = $webroot.$path.$fileName;

                    $mimetype = FileHelper::getMimeType($path_blob);

                    $source = file_get_contents($path_blob);
                    $base64 = base64_encode($source);
                    $blob = 'data:'.$mimetype.';base64,'.$base64;
                    $post['PegawaiForm']['photopegawai_blob'] = $blob;
                }
                
                if (!empty($file_ttd)) {
                    $webroot = \Yii::getAlias('@webroot');
                    $path = '/uploads/signature/';
                    $size = $file_ttd->size;
                    $ext = end(explode(".", $file_ttd->name));
                    $fileName = $file_ttd->name;
                    $cek_dir = $webroot.$path;
                    if (!is_dir($cek_dir)){
                        mkdir($cek_dir, 0777, true);
                    }
                    $file_ttd->saveAs($webroot.$path.$fileName);
                }

                if ($model->validate()) {
                    $response = $this->_restMaster->post('pegawai/update?id=' . $id, [
                                                            'form_params' => $post
                                                        ]);
                    $body = json_decode($response->getBody(), True);
                    return DocoHelpers::response($body);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PegawaiForm');
                    return DocoHelpers::response([
                        'response' => [
                            'data' => $errors
                        ]
                    ],422);
                }
            } catch (Exception $e) {
                return DocoHelpers::response([
                    'response' => [
                        'data' => $e->getMessage()
                    ]
                ],422);
            }
        } else {
            $response = $this->_restMaster->get('pegawai/view-data?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $model->attributes = $data_pegawai =  $body['response'];
            $model->aktif = $data_pegawai["is_active"] ? 1 : 2;
            $model->warganegara_pegawai = $data_pegawai["warganegara"];
            $model->bank_id = $body['response']['bank_id'];
            $model->tgl_lahirpegawai = date('d-M-Y', strtotime($model->tgl_lahirpegawai));
            $gelar_depan = $gelar_belakang = $status_perkawinan = $agama = $golongan_darah = $warga_negara = $suku = $provinsi = $kabupaten = $kecamatan = $kelurahan = $jenis_kelamin = $pendidikan = $jabatan = $kualifikasi_pendidikan = $pangkat = $kelompok_pegawai = $warnakulit = $status_pegawai = $nama_bank = [];

            $requests = $this->_restMaster->get('allow/get-api');
            $response = json_decode($requests->getBody(), true);
            $response = $response['response']['lookup'];

            $gelar_depan = !empty($response['gelar_depan'])
                ? ArrayHelper::map($response['gelar_depan'],'lookup_id','lookup_name')
                : [];

            $gelar_belakang = !empty($response['gelar_belakang'])
                ? ArrayHelper::map($response['gelar_belakang'], 'gelarbelakang_id', 'gelarbelakang_nama')
                : [];

            $jenis_kelamin = !empty($response['jenis_kelamin'])
                ? ArrayHelper::map($response['jenis_kelamin'], 'lookup_id', 'lookup_name')
                : [];

            $status_perkawinan = !empty($response['status_perkawinan'])
                ? ArrayHelper::map($response['status_perkawinan'], 'lookup_id', 'lookup_name')
                : [];

            $agama = !empty($response['agama'])
                ? ArrayHelper::map($response['agama'], 'lookup_id', 'lookup_name')
                : [];

            $golongan_darah = !empty($response['golongan_darah'])
                ? ArrayHelper::map($response['golongan_darah'], 'lookup_id', 'lookup_name')
                : [];

            $warga_negara = !empty($response['warga_negara'])
                ? ArrayHelper::map($response['warga_negara'], 'lookup_id', 'lookup_name')
                : [];

            $suku = !empty($response['suku'])
                ? ArrayHelper::map($response['suku'], 'suku_id', 'suku_nama')
                : [];

            $provinsi = !empty($response['provinsi'])
                ? ArrayHelper::map($response['provinsi'], 'propinsi_id', 'propinsi_nama')
                : [];

            $pendidikan = !empty($response['pendidikan'])
                ? ArrayHelper::map($response['pendidikan'], 'pendidikan_id', 'pendidikan_nama')
                : [];

            $kualifikasi_pendidikan = !empty($response['kualifikasi_pendidikan'])
                ? ArrayHelper::map($response['kualifikasi_pendidikan'], 'pendkualifikasi_id', 'pendkualifikasi_nama')
                : [];

            $jabatan = !empty($response['jabatan_m'])
                ? ArrayHelper::map($response['jabatan_m'], 'jabatan_id', 'jabatan_nama')
                : [];

            $pangkat = !empty($response['pangkat'])
                ? ArrayHelper::map($response['pangkat'], 'pangkat_id', 'pangkat_nama')
                : [];

            $warna_kulit = !empty($response['warna_kulit'])
                ? ArrayHelper::map($response['warna_kulit'], 'lookup_id', 'lookup_name')
                : [];

            $kelompok_pegawai = !empty($response['kelompok_pegawai'])
                ? ArrayHelper::map($response['kelompok_pegawai'], 'kelompokpegawai_id', 'kelompokpegawai_nama')
                : [];

            $status_pegawai = !empty($response['kategori_pegawai'])
                ? ArrayHelper::map($response['kategori_pegawai'], 'lookup_id', 'lookup_name')
                : [];

            $nama_bank = !empty($response['nama_bank'])
                ? ArrayHelper::map($response['nama_bank'], 'bank_id', 'nama_bank')
                : [];

            $kemampuan_bahasa = !empty($response['bahasa'])
                ? ArrayHelper::map($response['bahasa'], 'lookup_id', 'lookup_name')
                : [];
            
            $status_aktif = [1=>"Aktif", "Tidak Aktif"];
            $rootPath = Url::base(true);
            $preview_file_gambar = isset($body['response']['photopegawai']) ? $rootPath.$path.$body['response']['photopegawai'] : false;
            $preview_file_ttd = isset($body['response']['tanda_tangan']) ? $rootPath.$path_ttd.$body['response']['tanda_tangan'] : false;
        
        }

        return $this->render('form', get_defined_vars());
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $request = $this->_restMaster->delete('pegawai/delete',[
                            'query' => ['id' => $id ]
                        ]);
            $request = json_decode($request->getBody(),true);
            return DocoHelpers::response($request);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionGetNamaPegawai()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restMaster->request('POST', 'pegawai/data-pegawai',[
                'form_params' => ['term' => $_GET['q']['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai']
                ];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];

            return DocoHelpers::response($return);
        }
    }

    public function actionGetNip()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restMaster->request('POST', 'pegawai/data-nip',[
                'form_params' => ['term' => $_GET['q']['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['nomorindukpegawai'],
                    'text' => $value['nomorindukpegawai']
                ];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];

            return DocoHelpers::response($return);
        }
    }

    public function actionGetJabatan()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restMaster->request('POST', 'pegawai/data-jabatan',[
                'form_params' => ['term' => $_GET['q']['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['jabatan_id'],
                    'text' => $value['jabatan_nama']
                ];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];

            return DocoHelpers::response($return);
        }
    }

    public function actionGetPangkat()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restMaster->request('POST', 'pegawai/data-pangkat',[
                'form_params' => ['term' => $_GET['q']['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['pangkat_id'],
                    'text' => $value['pangkat_nama']
                ];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];

            return DocoHelpers::response($return);
        }
    }

    public function actionGetKualifikasi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restMaster->get('pegawai/get-kualifikasi?id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['pendkualifikasi_id'],
                    'name' => $value['pendkualifikasi_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }

    public function actionFormEsignRegis($id) {

        $request = Yii::$app->request;
        $model = new EsignRegForm;
        $model->scenario = EsignRegForm::SCENARIO_REGIS;
        if(empty($request->post())) {
            $title = "Pendaftaran Sertifikat Tanda Tangan Digital";
            $error = "";
            try {
                $response = $this->_restMaster->get('pegawai/get-esign-regis?id=' . DocoHelpers::decrypt($id) . '&type=regis');
                $body = json_decode($response->getBody(), true);
                $data = $body['response']['data']['pegawai'];
                $additional_esign_data = json_decode($data['additional_esign_data'], true);
                $model->attributes = [
                    'email' => isset($additional_esign_data['registration_data']['email']) ? 
                        $additional_esign_data['registration_data']['email'] : $data['alamatemail'],
                    'name' => isset($additional_esign_data['registration_data']['name']) ? 
                        $additional_esign_data['registration_data']['name'] : $data['nama_pegawai'],
                    'nik' => isset($additional_esign_data['registration_data']['nik']) ? 
                            $additional_esign_data['registration_data']['nik'] : 
                            ($data['jenisidentitas'] == DocoConstants::JENIS_KTP ? $data['noidentitas'] : null),
                    'nationality_type' => 'WNI',
                    'identity_type' => 'PASSPORT',
                ];
            } catch (RequestException $e) {
                $body = json_decode($e->getResponse()->getBody()->getContents(), true);
                $error = $body['metadata']['message'];
            } catch (\Exception $e) {
                $result['status'] = 500;
                $error = $e->getMessage();
            }
            return $this->render('form-esign', [
                'title' => $title,
                'id' => $id,
                'model' => $model,
                'error' => $error,
                'type' => 'regis',
                'message_tolak' => (isset($additional_esign_data['cert_status']['status']) && $additional_esign_data['cert_status']['status'] == 4 &&
                    isset($additional_esign_data['cert_status']['message']['info'])) ? $additional_esign_data['cert_status']['message']['info'] : "",
            ]);
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $post = $request->post();
            $tnc = $request->post('tnc');

            $model->load($post);
            $model->photo_ktp = UploadedFile::getInstance($model, 'photo_ktp');
            $model->passport_file = UploadedFile::getInstance($model, 'passport_file');
            $model->company_supporting_document = UploadedFile::getInstance($model, 'company_supporting_document');

            if($model->nationality_type == 'WNI') {
                $model->nationality_type = null;
                $model->identity_type = null;
                $model->country_code = null;
                $model->passport_number = null;
                $model->passport_file = null;
                $model->passport_date_expire = null;
                $model->company_supporting_document = null;
                $model->identity_date_expire = null;
            } else if ($model->identity_type != 'KITAS' && $model->identity_type != 'KITAP') {
                $model->company_supporting_document = null;
                $model->identity_date_expire = null;
            }
            if (!$model->validate()) {
                $errors = DocoHelpers::parseError($model->errors,'EsignRegForm');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ],422);
            }
            try {
                $data = $model->attributes;
                if(!empty($data['photo_ktp'])) {
                    $content = file_get_contents($data['photo_ktp']->tempName);
                    $type = $data['photo_ktp']->type;
                    $data['photo_ktp'] = 'data:' . $type . ';base64,' . base64_encode($content);
                }
                if(!empty($data['passport_file'])) {
                    $content = file_get_contents($data['passport_file']->tempName);
                    $type = $data['passport_file']->type;
                    $data['passport_file'] = 'data:' . $type . ';base64,' . base64_encode($content);
                }
                if(!empty($data['company_supporting_document'])) {
                    $content = file_get_contents($data['company_supporting_document']->tempName);
                    $type = $data['company_supporting_document']->type;
                    $data['company_supporting_document'] = 'data:' . $type . ';base64,' . base64_encode($content);
                }
                $data['consent_text'] = $tnc;
                $response = $this->_restMaster->post('pegawai/esign-regis?id=' . DocoHelpers::decrypt($id), [
                    'form_params'=> [
                        'data' => $data,
                    ],
                ]);
                $result = [
                    'status' => 200,
                    'message' => 'Sukses Registrasi',
                    'data' => [],
                ];
            } catch (RequestException $e) {
                $body = json_decode($e->getResponse()->getBody()->getContents(), true);
                $result['status'] = 500;
                $result['message'] = $body['metadata']['message'];
            } catch (\Exception $e) {
                $result['status'] = 500;
                $result['message'] = $e->getMessage();
            }
            \Yii::$app->response->statusCode = $result['status'];
            return $result;
        }
    }

    public function actionFormReEnroll($id) {

        $request = Yii::$app->request;
        $model = new EsignRegForm;
        $model->scenario = EsignRegForm::SCENARIO_REENROLL;
        if(empty($request->post())) {
            $title = "Pendaftaran Ulang Tanda Tangan Digital";
            $error = "";
            try {
                $response = $this->_restMaster->get('pegawai/get-esign-regis?id=' . DocoHelpers::decrypt($id) . '&type=reenroll');
                $body = json_decode($response->getBody(), true);
                $data = $body['response']['data']['pegawai'];
                $additional_esign_data = json_decode($data['additional_esign_data'], true);
                $model->attributes = [
                    'email' => isset($additional_esign_data['registration_data']['email']) ? 
                        $additional_esign_data['registration_data']['email'] : $data['alamatemail'],
                    'name' => isset($additional_esign_data['registration_data']['name']) ? 
                        $additional_esign_data['registration_data']['name'] : $data['nama_pegawai'],
                    'nik' => isset($additional_esign_data['registration_data']['nik']) ? 
                            $additional_esign_data['registration_data']['nik'] : 
                            ($data['jenisidentitas'] == DocoConstants::JENIS_KTP ? $data['noidentitas'] : null),
                    'nationality_type' => 'WNI',
                    'identity_type' => 'PASSPORT',
                ];
            } catch (RequestException $e) {
                $body = json_decode($e->getResponse()->getBody()->getContents(), true);
                $error = $body['metadata']['message'];
            } catch (\Exception $e) {
                $result['status'] = 500;
                $error = $e->getMessage();
            }
            return $this->render('form-esign', [
                'title' => $title,
                'id' => $id,
                'model' => $model,
                'error' => $error,
                'type' => 'reenroll',
                'message_tolak' => (isset($additional_esign_data['cert_status']['status']) && $additional_esign_data['cert_status']['status'] == 4 &&
                    isset($additional_esign_data['cert_status']['message']['info'])) ? $additional_esign_data['cert_status']['message']['info'] : "",
            ]);
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $post = $request->post();
            $tnc = $request->post('tnc');

            $model->load($post);
            $model->photo_ktp = UploadedFile::getInstance($model, 'photo_ktp');
            $model->passport_file = UploadedFile::getInstance($model, 'passport_file');
            $model->company_supporting_document = UploadedFile::getInstance($model, 'company_supporting_document');

            if($model->nationality_type == 'WNI') {
                $model->nationality_type = null;
                $model->identity_type = null;
                $model->country_code = null;
                $model->passport_number = null;
                $model->passport_file = null;
                $model->passport_date_expire = null;
                $model->company_supporting_document = null;
                $model->identity_date_expire = null;
            } else if ($model->identity_type != 'KITAS' && $model->identity_type != 'KITAP') {
                $model->company_supporting_document = null;
                $model->identity_date_expire = null;
            }
            if (!$model->validate()) {
                $errors = DocoHelpers::parseError($model->errors,'EsignRegForm');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ],422);
            }
            try {
                $data = $model->attributes;
                if(!empty($data['photo_ktp'])) {
                    $content = file_get_contents($data['photo_ktp']->tempName);
                    $type = $data['photo_ktp']->type;
                    $data['photo_ktp'] = 'data:' . $type . ';base64,' . base64_encode($content);
                }
                if(!empty($data['passport_file'])) {
                    $content = file_get_contents($data['passport_file']->tempName);
                    $type = $data['passport_file']->type;
                    $data['passport_file'] = 'data:' . $type . ';base64,' . base64_encode($content);
                }
                if(!empty($data['company_supporting_document'])) {
                    $content = file_get_contents($data['company_supporting_document']->tempName);
                    $type = $data['company_supporting_document']->type;
                    $data['company_supporting_document'] = 'data:' . $type . ';base64,' . base64_encode($content);
                }
                $data['consent_text'] = $tnc;
                $response = $this->_restMaster->post('pegawai/re-enroll?id=' . DocoHelpers::decrypt($id), [
                    'form_params'=> [
                        'data' => $data,
                    ],
                ]);
                $result = [
                    'status' => 200,
                    'message' => 'Sukses Registrasi',
                    'data' => [],
                ];
            } catch (RequestException $e) {
                $body = json_decode($e->getResponse()->getBody()->getContents(), true);
                $result['status'] = 500;
                $result['message'] = $body['metadata']['message'];
            } catch (\Exception $e) {
                $result['status'] = 500;
                $result['message'] = $e->getMessage();
            }
            \Yii::$app->response->statusCode = $result['status'];
            return $result;
        }
    }

    public function actionFormEsignRevoke($id) {
        $request = Yii::$app->request;
        if(empty($request->post())) {
            $title = "Revoke Sertifikat Tanda Tangan Digital";
            $error = "";
            try {
                $response = $this->_restMaster->get('pegawai/get-esign-revoke?id=' . DocoHelpers::decrypt($id));
                // tidak perlu diolah karena hanya butuh status 200 
            } catch (RequestException $e) {
                $body = json_decode($e->getResponse()->getBody()->getContents(), true);
                $error = isset($body['metadata']['message']) ? $body['metadata']['message'] : 'Internal Server Error';
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }

            if(!empty($error)) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->statusCode = 500;
                return [
                    'message' => $error,
                ];
            }
            return $this->renderAjax('form-revoke', [
                'title' => $title,
                'id' => $id,
                'reasonList' => [
                    DocoConstants::REASON_REVOKE_TILAKA_RESIGN => DocoConstants::REASON_REVOKE_TILAKA_RESIGN,
                    DocoConstants::REASON_REVOKE_TILAKA_PHK => DocoConstants::REASON_REVOKE_TILAKA_PHK,
                    DocoConstants::REASON_REVOKE_TILAKA_HABIS_KONTRAK => DocoConstants::REASON_REVOKE_TILAKA_HABIS_KONTRAK,
                    DocoConstants::REASON_REVOKE_TILAKA_MUTASI => DocoConstants::REASON_REVOKE_TILAKA_MUTASI,
                    DocoConstants::REASON_REVOKE_TILAKA_PEMINDAHAN_DEPARTEMEN => DocoConstants::REASON_REVOKE_TILAKA_PEMINDAHAN_DEPARTEMEN,
                    DocoConstants::REASON_REVOKE_TILAKA_PINDAH_DIVISI => DocoConstants::REASON_REVOKE_TILAKA_PINDAH_DIVISI,
                    DocoConstants::REASON_REVOKE_TILAKA_INTERNAL_FRAUD => DocoConstants::REASON_REVOKE_TILAKA_INTERNAL_FRAUD,
                    DocoConstants::REASON_REVOKE_TILAKA_PENUTUPAN_HAK_AKSES => DocoConstants::REASON_REVOKE_TILAKA_PENUTUPAN_HAK_AKSES,
                    DocoConstants::REASON_REVOKE_TILAKA_PELANGGARAN_HUKUM_DARI_USER => DocoConstants::REASON_REVOKE_TILAKA_PELANGGARAN_HUKUM_DARI_USER,
                    DocoConstants::REASON_REVOKE_TILAKA_PERANGKAT_HILANG_PERANGKAT_DICURI => DocoConstants::REASON_REVOKE_TILAKA_PERANGKAT_HILANG_PERANGKAT_DICURI,
                ],
            ]);
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $post = $request->post();
            $reason = $post['reason'];
            if (empty($reason)) {
                return DocoHelpers::response([
                    'response' => [
                        'data' => [
                            'Alasan Revoke tidak boleh kosong',
                        ]
                    ]
                ],422);
            }
            try {
                $response = $this->_restMaster->post('pegawai/esign-revoke?id=' . DocoHelpers::decrypt($id), [
                    'form_params'=> [
                        'data' => [
                            'reason' => $reason,
                        ],
                    ],
                ]);
                $result = [
                    'status' => 200,
                    'message' => 'Sukses Revoke',
                    'data' => [],
                ];
            } catch (RequestException $e) {
                $body = json_decode($e->getResponse()->getBody()->getContents(), true);
                $result['status'] = 500;
                $result['message'] = $body['metadata']['message'];
            } catch (\Exception $e) {
                $result['status'] = 500;
                $result['message'] = $e->getMessage();
            }
            \Yii::$app->response->statusCode = $result['status'];
            return $result;
        }


        // "email":alamatemail,
        // "name":nama_pegawai,
        // "nik":jenis_identitas==DocoConstants::JENIS_KTP ? noidentitas:'',
        // "photo_ktp":"data:image/jpeg;base64,".upload
    }
}