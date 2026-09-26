<?php

namespace Doco\antrian\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\base\Exception;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\modules\antrian\models\AntrianForm;
use app\modules\antrian\models\PasienForm;
use yii\helpers\Json;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\modules\antrian\models\AntrianCheckinMjkn;
use app\modules\antrian\models\PasienRoutingRujukanForm;
use app\modules\antrian\models\AntrianOnsiteForm;
use yii\helpers\ArrayHelper;
use app\components\Services\Contracts\BpjsInterface;
use yii\web\NotFoundHttpException;
use app\modules\antrian\models\ReservasiNonMJKN;

class DashboardController extends DocoController
{
    protected $_title = "Dashboard";
    protected $_module = '/antrian/dashboard';
    protected $_restAntrian;
    protected $_restMaster;
    protected $_restApotek;
    protected $bpjsService;
    protected $_restPendaftaran;

    public function __construct($id, $module, $config = [], BpjsInterface $bpjsService)
    {
        $this->bpjsService = $bpjsService;
        parent::__construct($id, $module, $config);
    }

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restAntrian = Yii::$app->docoRest->antrian;
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    public function actionIndex()
    {
        try{
            $title = $this->_title;
            $session = Yii::$app->session;

            #Display Options
            $displayMenu = [];
            $indexRequest = $this->_restAntrian->get('dashboard/index-dashboard');
            $bodyIndex = json_decode($indexRequest->getBody(),TRUE);
            $DisplayOptions = $bodyIndex['response']['data-layar'];
            $ls = $bodyIndex['response']['list-jenis'];
            $is_detail = $bodyIndex['response']['konfig-system']['is_jenisantriandetail'];

            $imgQueue = Url::to('@web/media/img/icon-antrian/queue.png');
            $imgDisplay = Url::to('@web/media/img/icon-antrian/display-icon.png');

            $list_jenis_antrian = [];
            $ls_display = [];
            foreach ($ls as $key => $value) {
                if($value['jenisantrian_id'] != 312){
                    $keyJenisAntrian = DocoHelpers::encrypt($value['jenisantrian_id']);

                    $class = '';
                    $url = Url::to([$this->_module .'/jenis-antrian','jenis_id' => $keyJenisAntrian ]);

                    if($is_detail && $value['jenisantrian_id'] == '177'){
                        $class = ' show-detail-pendaftaran';
                        $url = '';
                    }

                    $list_jenis_antrian[] = [
                        'class' => $class,
                        'id'    => $keyJenisAntrian,
                        'name'  => '<br><b>'.Yii::t('fe', 'Antrian').'</b><br>'.Yii::t('fe', $value['jenisantrian_nama']),
                        'icon'  => '<img src="'.$imgDisplay.'" >',
                        'url'   => $url,
                    ];
                    $ls_display[$value['jenisantrian_id']] = $value['jenisantrian_nama'];
                }

            }

            $list_jenis_antrian = $this->generateCarousel($list_jenis_antrian);

            foreach($DisplayOptions as $row)
            {
                // if(array_key_exists($row['jenisantrian_id'],$ls_display)){
                    $primaryKey = DocoHelpers::encrypt($row['layarantrian_id']);
                    $keyJenisAntrian = DocoHelpers::encrypt($row['jenisantrian_id']);
                    $keyLoket = DocoHelpers::encrypt($row['loket_id']);
                    $keyLantai = DocoHelpers::encrypt($row['jenisantriandetail_id']);

                    $menu_name = explode(" ", $row['layarantrian_nama']);

                    if ($is_detail) {
                        $nextMenu = $row['layarantrian_nama'].' '.$row['lantai'];
                    } else {
                        $nextMenu = $row['layarantrian_nama'];
                    }

                    $displayMenu[] = [
                        'name' => '<br><b>'.Yii::t('fe', 'Layar').'</b><br>'.Yii::t('fe', $nextMenu),
                        'icon' => '<img src="'.$imgDisplay.'" alt="icon-'.$nextMenu.'" >',
                        'url' => $row['jenisantrian_id'] != 312 ?
                            Url::to([
                                $this->_module .'/layar-antrian',
                                'layar' => $primaryKey,
                                'loket' => $keyLoket,
                                'detail'=> $keyLantai
                            ])
                            :
                            Url::to([
                                $this->_module .'/layar-antrian-poli',
                                'layar' => $primaryKey
                            ])
                    ];
                // }
            }

            $displayMenu = $this->generateCarousel($displayMenu);
            // dump($displayMenu);die;
            // exit();
            return $this->render('index', get_defined_vars());
        } catch(RequestException $e){
            return DocoHelpers::responseTemplate(500,$e->getMessage());
        }
    }

    public function actionLayarAntrian($layar = null, $loket = null, $detail = null)
    {
        $state = false;
        $request = Yii::$app->request;
        $judulLayarAntrian = Yii::t('fe', 'Antrian Pendaftaran');
        $jenisLayarAntrian = Yii::t('fe', 'Rawat Jalan');
        $loketList = [];
        $namaLayar = "layarantrian";
        $img = array();
        $videos = array();
        $id = null;

        $cache = Yii::$app->cache;
        $appCache = $cache->get('app');
        if (!array_key_exists('header', $appCache) || $appCache['header'] == '') {
            $cache->delete('app');
        }

        if (!array_key_exists('header_detail', $appCache) || $appCache['header_detail'] == '') {
            $cache->delete('app');
        }

        if (!array_key_exists('footer', $appCache) || $appCache['footer'] == '') {
            $cache->delete('app');
        }

        if (!array_key_exists('path_logoheader', $appCache) || $appCache['path_logoheader'] == '') {
            $cache->delete('app');
        }

        // $getSlider = $this->_restMaster->get('info-slider-rumah-sakit/show-slider');
        // $dataSlider = json_decode($getSlider->getBody(),TRUE);
        // $sliders = $dataSlider['response'];

        if($layar != null)
        {
            $id = DocoHelpers::decrypt($layar);

            $layarRequest = $this->_restAntrian->get('dashboard/layar-dashboard',['query'=>['id'=>$id]]);
            $bodyLayar = json_decode($layarRequest->getBody(), true);
            $infoLayarAntrian = $bodyLayar['response']['info-layar'];
            $loketList = $bodyLayar['response']['list-loket'];
            $dt_antrian = $bodyLayar['response']['list-antrian'];
            $konfig_layar = $bodyLayar['response']['konfig-layar'];
            $slides = $bodyLayar['response']['konfig-layar']['slides'];
            $konfig_jenisantriandetail = ArrayHelper::getValue($bodyLayar,'response.konfig_jenisantriandetail');
            $pembagi_dt_antrian = count($dt_antrian) / 2;
            $total_dt_antrian = 0;
            $ruangan_response = "'-'";

            if ($konfig_layar['is_slider'] == 1) {
                if (!empty($slides)) {
                    foreach ($slides as $value) {
                        $videos[] = $value['file'];
                    }

                    $videos = json_encode($videos);
                }
            } else {
                if (!empty($slides)) {
                    foreach ($slides as $value) {
                        $img[] = $value['file'];
                    }
                }
            }

            if (empty($videos)) {
                $videos = -1;
            }

            foreach ($dt_antrian as $key => $value) {
                $total_dt_antrian += empty($value['count']) ? 0 : $value['count'];
            }

            $judulLayarAntrian = $infoLayarAntrian['layarantrian_nama'];


            if ($infoLayarAntrian['jenisantrian_id'] == DocoConstants::JA_FAR) {
                $status_ambil = DocoConstants::VAR_SF_3;
                $namaLayar = "layarantrianfarmasi";

                $namaLoket = empty($loketList[0]['loket_nama']) ? "Farmasi" : $loketList[0]['loket_nama'];
                $ruanganId = empty($loketList[0]['ruangan_id']) ? "Farmasi" : $loketList[0]['ruangan_id'];
                $ruangan_response = empty($bodyLayar['response']['ruangan_id']) ? "'-'" : $bodyLayar['response']['ruangan_id'];
            }

            if(count($loketList) > 0){
                $state = true;
            }
        }
        return $this->render($namaLayar, get_defined_vars());
    }

    public function actionGetDataAntrianFarmasi($id = null)
    {
        $layarRequest = $this->_restAntrian->get('dashboard/get-data-antrian-farmasi',['query'=>['id'=>$id]]);
        $bodyLayar = json_decode($layarRequest->getBody(), true);
        return DocoHelpers::response($bodyLayar,false,true);
    }

    public function actionLayarAntrianPoli($layar = null)
    {
        $state = false;
        $request = Yii::$app->request;
        $judulLayarAntrian = Yii::t('fe', 'Antrian Poliklinik');
        $jenisLayarAntrian = Yii::t('fe', 'Rawat Jalan');

        $loketList = [];
        $img = $videos = [];

        if($layar != null)
        {
            $id = DocoHelpers::decrypt($layar);
            $getRequest = $this->guzzleExec($this->_restAntrian,
            [
                'url' => 'display-antrian/view-data-layar-poli',
                'payload' => [
                    'query' => [
                        'id' => $id
                    ]
                ]
            ]);
            $listData = ArrayHelper::getValue($getRequest, 'data-ruangan');
            $konfig_layar = ArrayHelper::getValue($getRequest, 'konfig-layar');
            $konfig_jenisantriandetail = ArrayHelper::getValue($getRequest,'konfig_jenisantriandetail');
            $slides = ArrayHelper::getValue($getRequest, 'konfig-layar.slides');
            $jadwalList =  json_encode(ArrayHelper::getValue($getRequest, 'data-jadwal'));
            $layarTampilFull = $getRequest['konfig-system']['is_banyakloket'];
            $jumlahLayarTampil = ($getRequest['konfig-system']['is_banyakloket']) ? 8 : 4;

            if(!empty($listData)) {
                $listData = $this->generateCarousel($listData, $jumlahLayarTampil);
                if(count(end($listData)) < $jumlahLayarTampil) {
                    $counter = $jumlahLayarTampil - count(end($listData));
                    $listData = $this->generateDummyData($listData, $counter);
                }
            } else {
                $listData = $this->generateDummyData($listData);
            }

            if ($konfig_layar['is_slider'] == 1) {
                if (!empty($slides)) {
                    foreach ($slides as $value) {
                        $videos[] = $value['file'];
                    }

                    $videos = json_encode($videos);
                }
            } else {
                if (!empty($slides)) {
                    foreach ($slides as $value) {
                        $img[] = $value['file'];
                    }
                }
            }

            if (empty($videos)) {
                $videos = -1;
            }

        }

        return $this->render('layarantrianpoli', compact('judulLayarAntrian', 'listData', 'konfig_layar', 'videos', 'img', 'jadwalList', 'slides','layarTampilFull','konfig_jenisantriandetail'));
    }

    public function actionCariPasien($rm = null)
    {
        $response = $this->_restMaster->request('POST', 'pasien/',[
                            'form_params' => [
                                'no_rekam_medik' => $rm
                            ]
                        ]);

        $body = json_decode($response->getBody(),TRUE);
        $data = $body['response']['data'][0];

        echo json_encode($data);
        return;
    }

    public function actionJenisAntrian()
    {
        try{
            $module = $this->_module;
            $request = Yii::$app->request;
            $jenis_id = $request->get('jenis_id');
            $detail_id = $request->get('detail_id');

            $decryptJenisId = DocoHelpers::decrypt($jenis_id);
            $decryptDetailId = DocoHelpers::decrypt($detail_id);

            if($request->post()){
                $post = $request->post();
                $post['jenisantrian_id'] = $decryptJenisId;
                $post['jenisantriandetail_id'] = $decryptDetailId;
                $response = $this->_restAntrian->request('POST','dashboard/create-antrian',['form_params'=>$post]);
                $body = json_decode($response->getBody(), true);
                $dataRes = $body['response']['data'];
                $dataCetak = $dataRes['cetak'];
                if(isset($body['response']['data']) && is_array($body['response']['data'])){
                    $dataRes = $body['response']['data'];
                    if(isset($dataRes['antrian_id'])){
                        if (!is_array($dataRes['antrian_id'])) {
                            $dataRes['antrian_id'] = DocoHelpers::encrypt($dataRes['antrian_id']);
                        }
                    }

                    // penambahan update data poli di display : ali
                    if(isset($dataRes['list_antrian_poli'])){
                        // set ke display antrian
                        $data_display["list_antrian_poli"] = $dataRes['list_antrian_poli'];
                        $mode = Yii::$app->params->mode;
                        Yii::$app->redis->executeCommand('PUBLISH', [
                            'channel' => 'display-antrian-'.$mode,
                            'message' => Json::encode(['data' => $data_display])
                        ]);
                        // end set display antrian
                    }
                }
                return DocoHelpers::response($body,false,true);
            }

            $ts_gambar = Url::to('@web/media/img/img_avatar.png');

            $requests = [
                'actionListPolyAntrian',
                'actionListKlasifikasiPasien',
                'actionListStatusPasien',
                'actionListGroupCaraBayar',
                'actionListInstalasiPenunjang',
                'actionKonfigUrlCetak',
                'actionKonfigKuotaAntrian',
                'actionKonfigSystem'
            ];

            if ($decryptJenisId == 177) {
                $getKonfig = $this->_restAntrian->request('get', 'dashboard/get-konfig-system');
                $bodyRequest = json_decode($getKonfig->getBody(), TRUE);
                if ($bodyRequest['response']['is_jenisantriandetail']) {
                    if (!$detail_id) {
                        return $this->redirect(array('/antrian'));
                    }
                } else {
                    if ($detail_id) {
                        return $this->redirect(array('/antrian'));
                    }
                }
            }

            $allowRequest = $this->_restAntrian->request('POST', 'allow/lists',[
                'form_params' =>
                    [ 'params' =>$requests, 'jenis_id' => $decryptJenisId, 'detail_id' => $decryptDetailId]
            ]);
            $bodyRequest = json_decode($allowRequest->getBody(), TRUE);
            $allowResponse = $bodyRequest['response'];

            $list_poly_antrian = $allowResponse['actionListPolyAntrian'];
            $list_klasifikasi_pasien = $allowResponse['actionListKlasifikasiPasien'];
            $list_status_pasien = $allowResponse['actionListStatusPasien'];
            $list_carabayar = $allowResponse['actionListGroupCaraBayar'];
            $konfig_url_cetak = $allowResponse['actionKonfigUrlCetak'];
            $konfigKuotaAntrian = $allowResponse['actionKonfigKuotaAntrian'];
            $konfigSystem = $allowResponse['actionKonfigSystem'];
            $isKeteranganPasien = $konfigSystem['is_keteranganpasien'];

            if ($isKeteranganPasien) {
                $isKeteranganPasien = 1;
            } else {
                $isKeteranganPasien = -1;
            }

            $kuatoAntrian = 1;

            if ($konfigSystem['kuota_antrian'] == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK) {
                $kuatoAntrian = -1;
            }

            $rekap_carabayar = [];
            foreach ($list_carabayar as $key => $value) {
                $rekap_carabayar[] = $value['groupcarabayar_id'];
            }

            $listInstalasiPenunjang = $allowResponse['actionListInstalasiPenunjang'];
            $list_poly_penunjang = [];
            foreach ($listInstalasiPenunjang as $key => $value) {
                $primaryKey = DocoHelpers::encrypt($value['instalasi_id']);
                $value['instalasi_id'] = $primaryKey;
                $list_poly_penunjang[] = $value;
            }

            switch ($decryptJenisId) {
                case 177:
                    $newListPoly = [];
                    $empty = [];
                    foreach ($list_poly_antrian as $v_listpoly) {
                        if($v_listpoly['instalasi_id'] == DocoConstants::INSTALASI_ID_RJ){
                            if ($v_listpoly['sisa_kuota'] > 0) {
                                $newListPoly[] = $v_listpoly;
                            } else {
                               $empty[] = $v_listpoly;
                            }
                        }
                    }

                    $list_poly_antrian = array_merge($newListPoly, $empty);
                    $judulLayarAntrian = 'Antrian Pendaftaran';
                    $jenisLayarAntrian = 'Rawat Jalan';
                    $view_antrian = 'antrian-pendaftaran';
                    break;
                case 179:
                    $judulLayarAntrian = 'Antrian Penunjang';
                    $jenisLayarAntrian = ' ';
                    $request = $this->_restAntrian->get('dashboard/get-layar-by-jenis-id', ['query' => ['id' => $decryptJenisId]]);
                    $body = json_decode($request->getBody(), true);
                    $list_layar = $body['response'];
                    // dump($list_layar);exit;
                    $view_antrian = 'antrian-penunjang';
                    break;
                case 178 :
                    $judulLayarAntrian = Yii::t('fe', 'Antrian Kasir');
                    $request = $this->_restAntrian->get('dashboard/get-layar-by-jenis-id', ['query' => ['id' => $decryptJenisId]]);
                    $body = json_decode($request->getBody(), TRUE);
                    $list_layar = $body['response'];

                    return $this->render('antrian-kasir', get_defined_vars());
                    break;
                case 176 :
                    $judulLayarAntrian = Yii::t('fe', 'Antrian Pengambilan Obat');
                    $judulInstalasi = Yii::t('fe', 'Instalasi');
                    $judulJenisResep = Yii::t('fe', 'Jenis Resep');

                    $request = $this->_restAntrian->get('dashboard/get-layar-by-jenis-id', ['query' => ['id' => $decryptJenisId]]);
                    $body = json_decode($request->getBody(), true);
                    $list_layar = $body['response'];

                    $group_antrian = [];

                    foreach ($list_layar as $value) {
                        if (array_key_exists($value['ruangan_id'],$group_antrian)) {
                            $group_antrian[$value['ruangan_id']]['data_antrian'][] = $value;
                        } else {
                            $group_antrian[$value['ruangan_id']] = [
                                'ruangan_nama' => $value['ruangan_nama'],
                                'ruangan_id' => $value['ruangan_id'],
                                'data_antrian' => [$value]
                            ];
                        }
                    }

                    // dump($group_antrian);exit;

                    $layar_aktif = "";
                    $instalasi_id = "";
                    $ruangan_id = "";
                    // if(count($list_layar) > 0){
                    //     foreach ($list_layar as $key => $value) {
                    //         $layar_aktif = $value['fungsi_antrian'];
                    //         $instalasi_id = $value['instalasi_id'];
                    //         $ruangan_id = $value['ruangan_id'];
                    //     }
                    // }
                    // $request = $this->_restAntrian->get('allow/list-ruangan', ['query' => ['id' => 6]]);
                    // $body = json_decode($request->getBody(), TRUE);
                    // $requestFarmasi = $this->_restAntrian->get('dashboard/get-layar-antrian-farmasi');


                    // $bodyFarmasi = json_decode($requestFarmasi->getBody(), TRUE);
                    // $list_ruangan = $body['response'];
                    // $list_farmasi_antrian = $bodyFarmasi['response'];
                    // $title_farmasi_notif = "";
                    // foreach ($list_farmasi_antrian as $value) {
                    //   switch ($value['fungsi_antrian']) {
                    //     case 'Default Apotek':
                    //           $title_farmasi_notif = $value['fungsi_antrian'];
                    //       break;
                    //     case 'Racikan':
                    //           $title_farmasi_notif = " Racikan / Non Racikan ";
                    //       break;
                    //     case 'Non Racikan':
                    //           $title_farmasi_notif = " Racikan / Non Racikan ";
                    //       break;
                    //     default:
                    //           $title_farmasi_notif = " Cara Bayar ";
                    //       break;
                    //   }
                    // }

                    return $this->render('antrian-farmasi', get_defined_vars());
                    break;
                case DocoConstants::ANTRIAN_BPJS:
                    $judulLayarAntrian = 'Antrian BPJS';

                    $konfig_auto_daftar = $this->_restAntrian->get('dashboard/get-konfig-auto-daftar');
                    $konfig_auto_daftar = json_decode($konfig_auto_daftar->getBody(), TRUE);
                    $konfig_auto_daftar = ArrayHelper::getValue($konfig_auto_daftar, 'response', false);
                    return $this->render('antrian-bpjs', get_defined_vars());
                    break;
                default:
                    $judulLayarAntrian = 'Antrian Default';
                    $jenisLayarAntrian = 'Default';
                    $view_antrian = 'antrian-bpjs'; //temp default antrian
                    break;
            }
            return $this->render($view_antrian,get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(500,$e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionOnsiteBpjs()
    {
        $title = 'Pasien Onsite BPJS';
        $modelForm = new AntrianOnsiteForm;
        $activeTab = [
            [
                'title' => Yii::t('fe', 'Identitas Pasien'),
            ],
            [
                'title' => Yii::t('fe', 'Konfirmasi Data Pasien'),
            ]
        ];
        $modelForm->jenis_identitas = 0;
        /*
        $poliAll = $this->guzzleExec($this->_restPendaftaran,
        [
            'url' => 'api/referensi-poli-jkn'
        ]);
        if(isset($poliAll['response']) && count($poliAll['response'])>0){
            $poliAll = ArrayHelper::map($poliAll['response'], 'kdsubspesialis', 'nmsubspesialis');
        }else{
            $poliAll=[];
        }
        */

        $cache_jeniskunjungan = Yii::$app->cache->get('lookup_jenis_kunjungan');
        if($cache_jeniskunjungan === false){
            $getRequest = $this->guzzleExec($this->_restAntrian,
            [
                'url' => 'allow/list-jenis-kunjungan',
            ]);
            $jenis_kunjungan = ArrayHelper::map($getRequest,'jeniskunjungan_id','jeniskunjungan_nama');
            Yii::$app->cache->set('lookup_jenis_kunjungan',$jenis_kunjungan,3600);
        }else{
            $jenis_kunjungan = Yii::$app->cache->get('lookup_jenis_kunjungan');
        }

        // $jenis_kunjungan = DocoConstants::JENIS_KUNJUNGAN;
        return $this->renderPartial('modal-antrian-bpjs/modal-antrian-onsite', get_defined_vars());
    }

    public function actionGetHistoryPoli()
    {
        $datapoli = $this->guzzleExec($this->_restAntrian,
        [
            'url' => 'allow/get-history-poli-pasien',
            'payload'=>[
                'query' => [
                    'pasien_id' => Yii::$app->request->get('pasien_id')
                ]
            ]
        ]);

        return DocoHelpers::response($datapoli);
    }

    public function actionGetDokterByPoli()
    {
        $request = Yii::$app->request;
        $kode_poli = $request->get('kode_poli', null);
        $data_dokter = [];
        $optionsDokter = [];

        $response = $this->_restAntrian->get('allow/get-jadwal-dokter', [
            'query' => [
                'kode_poli' => $kode_poli
            ]
        ]);
        $body = json_decode($response->getBody(), True);
        foreach ($body['response'] as $key => $value) {
            $data_dokter[] = [
                'id' => $value['pegawai_id'],
                'nama_dokter' => $value['nama_pegawai'],
                'kode_dokter_bpjs' => $value['kode_dokter_bpjs'],
                'kode_ruangan_bpjs' => $value['kode_ruangan_bpjs'],
                'jam_praktek' => $value['jam_praktek']
            ];
        }
        $result['result'] = $data_dokter;
        return DocoHelpers::response($result);
    }

    public function actionModalRujukanRencanaKontrol()
    {
        $title = 'Daftar Rujukan & Daftar Rencana Kontrol';
        return $this->renderPartial('modal-antrian-bpjs/modal-rujukan-rencana-kontrol', get_defined_vars());
    }

    private function getPesertaNik($nomor_identitas)
    {
        $date = date('Y-m-d');
        $pesertaData = [];
        try {
            $pesertaRes = $this->bpjsService->cariPesertaNik($nomor_identitas, $date);
            if ($pesertaRes && ArrayHelper::getValue($pesertaRes, 'metaData.code') == 200) {
                $pesertaData = ArrayHelper::getValue($pesertaRes, 'response.peserta');
            }
            $nomor_identitas = ArrayHelper::getValue($pesertaData, 'noKartu');
            if(empty($nomor_identitas)){
                return $pesertaRes;
            }
            return $nomor_identitas;
        } catch (RequestException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function actionGetRujukan()
    {
        $request = Yii::$app->request;
        $jenis_identitas = $request->get('jenis_identitas', null);
        $nomor_identitas = $request->get('nomor_identitas', null);
        $result = $bpjsData = [];
        $result['data'] = [];
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;

        try {
            $waktuCariPesertaNik = 0;
            if($jenis_identitas == DocoConstants::IDENTITAS_NIK){
                $stime0 = microtime(true);
                $nomor_identitas = $this->getPesertaNik($nomor_identitas);
                $waktuCariPesertaNik = microtime(true) - $stime0;
                if(is_array($nomor_identitas)){
                    return DocoHelpers::response($result);
                }
            }
            $dataFaskes = $bpjsDataFaskes = [];
            $data = $bpjsData = [];

            // $bpjsRes = $this->bpjsService->rujukanBerdasarkanNoKartu($nomor_identitas);
            // $bpjsResFaskes = $this->bpjsService->rujukanFaskesBerdasarkanNoKartu($nomor_identitas);
            $stime1 = microtime(true);
            $bpjsRes = $this->bpjsService->listRujukanNoKartu($nomor_identitas);
            $waktuRujukanNoka = microtime(true) - $stime1;

            $stime2 = microtime(true);
            $bpjsResFaskes = $this->bpjsService->listRujukanNoKartuRS($nomor_identitas);
            $waktuRujukanRs = microtime(true) - $stime2;

            if ($bpjsRes && ArrayHelper::getValue($bpjsRes, 'metaData.code') == 200) {
                $bpjsData = ArrayHelper::getValue($bpjsRes, 'response.rujukan');
            }

            if ($bpjsResFaskes && ArrayHelper::getValue($bpjsResFaskes, 'metaData.code') == 200) {
                $bpjsDataFaskes = ArrayHelper::getValue($bpjsResFaskes, 'response.rujukan');
            }

            $today = date('Y-m-d');
            $start = date('Y-m-d',strtotime($today . "-90 days"));
            foreach ($bpjsData as $key => $value) {
                $newValue['primary'] = DocoHelpers::encrypt(ArrayHelper::getValue($value, 'noKunjungan'));
                $newValue['kode_poli'] = ArrayHelper::getValue($value, 'poliRujukan.kode');
                $newValue['no_rujukan'] = ArrayHelper::getValue($value, 'noKunjungan');
                $newValue['tgl_rujukan'] = ArrayHelper::getValue($value, 'tglKunjungan');
                $newValue['no_kartu'] = ArrayHelper::getValue($value, 'peserta.noKartu');
                $newValue['nama'] = ArrayHelper::getValue($value, 'peserta.nama');
                $newValue['ppk_perujuk'] = ArrayHelper::getValue($value, 'provPerujuk.nama');
                $newValue['subspesialis'] = ArrayHelper::getValue($value, 'poliRujukan.nama');
                $newValue['asal_rujukan'] = ArrayHelper::getValue($bpjsRes, 'response.asalFaskes');
                $data[] = $newValue;
            }
            ArrayHelper::multisort($data,function($item){
                return $item['tgl_rujukan'];
            },SORT_DESC);

            $filterData = array_filter($data, function($var) use ($start, $today) {  
                $evtime = strtotime($var['tgl_rujukan']);  
                return $evtime <= strtotime($today) && $evtime >= strtotime($start);  
            });

            $data = $filterData;

            foreach ($bpjsDataFaskes as $key => $value) {
                $newValue['primary'] = DocoHelpers::encrypt(ArrayHelper::getValue($value, 'noKunjungan'));
                $newValue['kode_poli'] = ArrayHelper::getValue($value, 'poliRujukan.kode');
                $newValue['no_rujukan'] = ArrayHelper::getValue($value, 'noKunjungan');
                $newValue['tgl_rujukan'] = ArrayHelper::getValue($value, 'tglKunjungan');
                $newValue['no_kartu'] = ArrayHelper::getValue($value, 'peserta.noKartu');
                $newValue['nama'] = ArrayHelper::getValue($value, 'peserta.nama');
                $newValue['ppk_perujuk'] = ArrayHelper::getValue($value, 'provPerujuk.nama');
                $newValue['subspesialis'] = ArrayHelper::getValue($value, 'poliRujukan.nama');
                $newValue['asal_rujukan'] = ArrayHelper::getValue($bpjsResFaskes, 'response.asalFaskes');
                $dataFaskes[] = $newValue;
            }
            ArrayHelper::multisort($dataFaskes,function($item){
                return $item['tgl_rujukan'];
            },SORT_DESC);

            $filterDataFaskes = array_filter($dataFaskes, function($var) use ($start, $today) {  
                $evtime = strtotime($var['tgl_rujukan']);  
                return $evtime <= strtotime($today) && $evtime >= strtotime($start);  
            });

            $dataFaskes = $filterDataFaskes;

            if(count($bpjsDataFaskes) > 0){
                $data = array_merge($data,$dataFaskes);
            }elseif(count($bpjsData) == 0){
                $data = [];
            }
            /*
            if(count($bpjsDataFaskes) > 0){
                $data = $dataFaskes;
            }elseif(count($bpjsData) ==0){
                $data=[];
            }else{
                $data = $data;
            }
            */

            // $primary = DocoHelpers::encrypt(ArrayHelper::getValue($bpjsData, 'noKunjungan'));
            // $no_rujukan = ArrayHelper::getValue($bpjsData, 'noKunjungan');
            // $tgl_kunjungan = ArrayHelper::getValue($bpjsData, 'tglKunjungan');
            // $no_kartu = ArrayHelper::getValue($bpjsData, 'peserta.noKartu');
            // $nama = ArrayHelper::getValue($bpjsData, 'peserta.nama');
            // $ppk_perujuk = ArrayHelper::getValue($bpjsData, 'provPerujuk.nama');
            // $subspesialis = ArrayHelper::getValue($bpjsData, 'poliRujukan.nama');
            // $kodePoli = ArrayHelper::getValue($bpjsData, 'poliRujukan.kode');
            // $asalRujukan = ArrayHelper::getValue($bpjsRes, 'response.asalFaskes');
            
            // $data = [
            //     'primary' => $primary,
            //     'kode_poli' => $kodePoli,
            //     'no_rujukan' => $no_rujukan,
            //     'tgl_rujukan' => $tgl_kunjungan,
            //     'no_kartu' => $no_kartu,
            //     'nama' => $nama,
            //     'ppk_perujuk' => $ppk_perujuk,
            //     'subspesialis' => $subspesialis,
            //     'asal_rujukan' => $asalRujukan
            // ];
            // $dataFaskes = [
            //     'primary' => DocoHelpers::encrypt(ArrayHelper::getValue($bpjsDataFaskes, 'noKunjungan')),
            //     'kode_poli' => ArrayHelper::getValue($bpjsDataFaskes, 'poliRujukan.kode'),
            //     'no_rujukan' => ArrayHelper::getValue($bpjsDataFaskes, 'noKunjungan'),
            //     'tgl_rujukan' => ArrayHelper::getValue($bpjsDataFaskes, 'tglKunjungan'),
            //     'no_kartu' => ArrayHelper::getValue($bpjsDataFaskes, 'peserta.noKartu'),
            //     'nama' => ArrayHelper::getValue($bpjsDataFaskes, 'peserta.nama'),
            //     'ppk_perujuk' => ArrayHelper::getValue($bpjsDataFaskes, 'provPerujuk.nama'),
            //     'subspesialis' => ArrayHelper::getValue($bpjsDataFaskes, 'poliRujukan.nama'),
            //     'asal_rujukan' => ArrayHelper::getValue($bpjsResFaskes, 'response.asalFaskes')
            // ];
            // if(count($bpjsDataFaskes) > 0){
            //     $data = [
            //         $dataFaskes,
            //     ];
            // }elseif(count($bpjsData) ==0){
            //     $data=[];
            // }else{
            //     $data = [
            //         $data,
            //     ];
            // }
            
            $result = [
                'data' => $data,
                'draw' => $request->get('draw'),
                'recordsTotal' => count($data),
                'recordsFiltered' => count($data),
                'trace_log' => [
                    'waktuCariPesertaNik' => number_format($waktuCariPesertaNik,2),
                    'waktuRujukanNoka' => number_format($waktuRujukanNoka,2),
                    'waktuRujukanRs' => number_format($waktuRujukanRs,2)

                ]
            ];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCekPasienBaru(){
        $request = Yii::$app->request;
        $no_identitas = $request->get('no_identitas');
        $no_kartu = $request->get('no_kartu');
        try{
            $stime0 = microtime(true);
            $result = $this->guzzleExec($this->_restAntrian,
            [
                'url' => 'allow/cek-pasien-baru',
                'payload' => [
                    'query' => [
                        'no_identitas' => $no_identitas,
                        'no_kartu' => $no_kartu,
                    ]
                ]
            ]);
            $waktuCariPasien = microtime(true) - $stime0;
            $result['trace_log'] = [
                'waktu_be_dh' => [
                    'enpoint' => 'allow/cek-pasien-baru',
                    'time' => number_format($waktuCariPasien, 3)
                ]
            ];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionJumlahSep(){
        $request = Yii::$app->request;
        $jenis_rujukan = $request->get('jenis_rujukan');
        $no_rujukan = $request->get('no_rujukan');

        try{
            $result = $this->guzzleExec($this->_restPendaftaran,
            [
                'url' => 'allow-bpjs/jumlah-sep-rujukan',
                'payload' => [
                    'query' => [
                        'jenis_rujukan' => $jenis_rujukan,
                        'no_rujukan' => $no_rujukan,
                    ]
                ]
            ]);
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    // public function getPoliBpjsAll(){
    //     $poliAll = $this->bpjsService->referensiPoli(null);
    //     return $bpjsRes;
    // }

    public function actionGetRencanaKontrol()
    {
        $request = Yii::$app->request;
        $jenis_identitas = $request->get('jenis_identitas', null);
        $nomor_identitas = $request->get('nomor_identitas', null);
        $jenis_kontrol = $request->get('jenis_kontrol', null);
        $bulan = date('m');
        $tahun = date('Y');
        $filter = 2;
        $data = [];
        $result = $bpjsData = [];
        $result['data'] = [];
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;

        try {
            $waktuCariPesertaNik = 0;
            $stime0 = microtime(true);
            if($jenis_identitas == DocoConstants::IDENTITAS_NIK){
                $nomor_identitas = $this->getPesertaNik($nomor_identitas);
                $waktuCariPesertaNik = microtime(true) - $stime0;
                if(is_array($nomor_identitas)){
                    return DocoHelpers::response($result);
                }
            }

            $stime1 = microtime(true);
            $bpjsRes = $this->bpjsService->rencanaKontrolBerdasarkanNoKartu($bulan, $tahun, $nomor_identitas, $filter);
            $waktuRencanaKontrolBpjs = microtime(true) - $stime1;

            if ($bpjsRes && ArrayHelper::getValue($bpjsRes, 'metaData.code') == 200) {
                $bpjsData = ArrayHelper::getValue($bpjsRes, 'response.list');
            }

            $ppk_perujuk = $this->getProfilRs();
            $stime2 = microtime(true);
            foreach ($bpjsData as $key => $value) {
                if (empty($jenis_kontrol) || (!empty($jenis_kontrol) && $value['jnsKontrol'] == $jenis_kontrol)) {
                    $newValue['primary'] = DocoHelpers::encrypt(ArrayHelper::getValue($value, 'noSuratKontrol'));
                    $newValue['kode_dokter'] = ArrayHelper::getValue($value, 'kodeDokter');
                    $newValue['nama_dokter'] = ArrayHelper::getValue($value, 'namaDokter');
                    $newValue['no_rujukan'] = $this->getNoRujukanBerdasarkanSuratKontrol(ArrayHelper::getValue($value, 'noSuratKontrol'));
                    $newValue['no_surat_kontrol'] = ArrayHelper::getValue($value, 'noSuratKontrol');
                    $newValue['rujuk_rencana'] = $newValue['no_rujukan'] . ' / ' . $newValue['no_surat_kontrol'];
                    $newValue['tgl_rujukan'] = ArrayHelper::getValue($value, 'tglRencanaKontrol');
                    $newValue['no_kartu'] = ArrayHelper::getValue($value, 'noKartu');
                    $newValue['nama'] = ArrayHelper::getValue($value, 'nama');
                    $newValue['ppk_perujuk'] = $ppk_perujuk;
                    $newValue['kode_poli'] = ArrayHelper::getValue($value, 'poliTujuan');
                    $newValue['subspesialis'] = ArrayHelper::getValue($value, 'namaPoliTujuan');
                    $data[] = $newValue;
                }
            }
            $waktuMencariSuratKontrolBpjs = microtime(true) - $stime2;

            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);
            $result['trace_log'] = [
                'waktuCariPesertaNik' => number_format($waktuCariPesertaNik,2),
                'waktuRencanaKontrolBpjs' => number_format($waktuRencanaKontrolBpjs,2),
                'waktuMencariSuratKontrolBpjs' => number_format($waktuMencariSuratKontrolBpjs,2)
            ];

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    private function getNoRujukanBerdasarkanSuratKontrol($noSuratKontrol)
    {
        $no_rujukan = '-';
        $bpjsRes = $this->bpjsService->cariSuratKontrol($noSuratKontrol);
        if ($bpjsRes && ArrayHelper::getValue($bpjsRes, 'metaData.code') == 200) {
            $bpjsData = ArrayHelper::getValue($bpjsRes, 'response');
        }

        if(!empty($bpjsData)){
            $no_rujukan = ArrayHelper::getValue($bpjsData, 'sep.provPerujuk.noRujukan');
        }

        return $no_rujukan;
    }

    private function getProfilRs()
    {
        $response = $this->_restAntrian->get('dashboard/get-profil-rs');
        $response = json_decode($response->getBody(), true);
        $nama_rs = $response['response']['nama_rumahsakit'];
        return $nama_rs;
    }

    public function actionCreateJknOnsite()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $nomorKartu = ArrayHelper::getValue($post, 'nomorkartu');

        /** Check Fingerprint */
        $checkFingerPrint = $this->_restAntrian->post('dashboard/check-finger-print', [
            'form_params' => [
                'no_kartu' => $nomorKartu
            ]
        ]);
        $checkFingerPrint = json_decode($checkFingerPrint->getBody(), TRUE);
        $resultFinger = ArrayHelper::getValue($checkFingerPrint, 'response.data.resultFinger', '1');

        if($resultFinger == '0'){
            return DocoHelpers::response($checkFingerPrint);
        }

        $post['tgl_pendaftaranol'] = date('Y-m-d');
        $post['jkn'] = true;
        $post['skipErrorJkn'] = true;
        $post['jenis_cara_bayar'] = DocoConstants::PASIEN_JKN;
        $post['is_checkin'] = true;
        $post['tgl_checkin'] = date('Y-m-d H:i:s');
        $post['carabayar_id'] = DocoConstants::CARA_BAYAR_BPJS;
        $post['penjamin_id'] = $this->getDefaultPenjaminId(DocoConstants::CARA_BAYAR_BPJS);
        $post['is_from_antrian'] = 1;

        $endpointRes = $this->_restPendaftaran->post('pendaftaran-online/daftar-online',[
            'form_params' => $post
        ]);
        $response = json_decode($endpointRes->getBody(), true);

        if (ArrayHelper::getValue($response, 'metadata.status') == 200) {
            $dataPendaftaranOnline = $this->_restAntrian->get('dashboard/get-pendaftaran-online-by-id', [
                'query' => [
                    'pendaftaranol_id' => ArrayHelper::getValue($response, 'response.response.pendaftaranol_id', '')
                ]
            ]);
            $dataPendaftaranOnline = json_decode($dataPendaftaranOnline->getBody(), true);

            $response['response']['pendaftaranol'] = ArrayHelper::getValue($dataPendaftaranOnline, 'response', []);
            $response['response']['randString'] = DocoHelpers::generateRandomString();
            $response['response']['pendaftaranol']['pendaftaranol_id'] = DocoHelpers::encrypt(ArrayHelper::getValue($response, 'response.pendaftaranol.pendaftaranol_id', ''));
        }

        return DocoHelpers::response($response);
    }

    public function actionCreateAntrianV2()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $post['antrian_jenis_id'] = isset($post['antrian_jenis_id']) ? DocoHelpers::decrypt($post['antrian_jenis_id']) : null;
        $endpointRes = $this->_restAntrian->post('dashboard/create-antrian-v2',[
            'form_params' => $post
        ]);
        $response = json_decode($endpointRes->getBody(), true);
        return DocoHelpers::response($response);
    }

    public function actionGetJamPraktekBpjs()
    {
        $request = Yii::$app->request;
        $kode_poli = $request->get('kode_ruangan_bpjs', null);
        $kode_dokter = $request->get('kode_dokter_bpjs', null);
        $date = date('Y-m-d');
        $currentTime = date('H:i');
        $post = [
            'kdpoli' => $kode_poli,
            'tgl' => $date,
        ];
        $data = $suggest_jadwal = [];

        try {
            $response = $this->_restPendaftaran->post('api/referensi-jadwal-dokter-jkn',[
                'form_params' => $post
            ]);
            $bpjsRes = json_decode($response->getBody(), true);
            if ($bpjsRes && $bpjsRes['response']['metadata']['code'] == 200) {
                $bpjsData = $bpjsRes['response']['response'];
            } else {
                return DocoHelpers::response($bpjsRes, 422);
            }

            foreach ($bpjsData as $key => $value) {
                if($value['kodedokter'] == $kode_dokter){
                    $data[] = ArrayHelper::getValue($value, 'jadwal');
                }
            }

            usort($data, function($a, $b){
                return ((int)explode('-', $a)[1] < (int)explode('-', $b)[0]) ? -1 : 1;
            });

            foreach ($data as $key => $value) {
                $jadwal = explode('-', $value);
                $startTime = $jadwal[0];
                $endTime = $jadwal[1];
                if($currentTime >= $startTime && $currentTime <= $endTime){
                    $suggest_jadwal = $value;
                }
            }

            $result['result'] = $data;
            $result['suggest_jadwal'] = $suggest_jadwal;
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetPoliBpjs()
    {
        $request = Yii::$app->request;
        $dokter_id = $request->get('dokter_id', null);
        $result = [];

        try {
            $response = $this->_restAntrian->get('allow/get-poli-dokter',[
                'query' => [
                    'dokter_id' => $dokter_id
                ]
            ]);
            $body = json_decode($response->getBody(), True);
            if(!empty($body['response'])){
                foreach ($body['response'] as $key => $value) {
                    $key = $value['ruangan_id'];
                    $data_dokter[] = [
                        'id' => $key,
                        'ruangan_nama' => $value['ruangan_nama'],
                        'kode_ruangan_bpjs' => $value['kode_ruangan_bpjs']
                    ];
                }
            }
            $result['result'] = $data_dokter;

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataPasienBpjs()
    {
        $request = Yii::$app->request;
        $nomor_identitas = $request->get('nomor_identitas', null);
        $jenis_identitas = $request->get('jenis_identitas', null);
        $date = date('Y-m-d');

        try {
            if($jenis_identitas == DocoConstants::IDENTITAS_NIK){
                $bpjsRes = $this->bpjsService->cariPesertaNik($nomor_identitas, $date);
            }else{
                $bpjsRes = $this->bpjsService->cariPeserta($nomor_identitas, $date);
            }

            if ($bpjsRes && $bpjsRes['metaData']['code'] == 200) {
                $bpjsData = $bpjsRes['response']['peserta'];
            } else {
                return DocoHelpers::response($bpjsRes, 422);
            }

            $noKartu = ArrayHelper::getValue($bpjsData, 'noKartu');
            $nik = ArrayHelper::getValue($bpjsData, 'nik');
            $nama = ArrayHelper::getValue($bpjsData, 'nama');
            $jenis_kelamin = ArrayHelper::getValue($bpjsData, 'sex');
            $tgl_lahir = ArrayHelper::getValue($bpjsData, 'tglLahir');
            $noMr = ArrayHelper::getValue($bpjsData, 'mr.noMR');
            $noTelepon = ArrayHelper::getValue($bpjsData, 'mr.noTelepon');

            $response = $this->_restAntrian->get('allow/get-pasien',[
                'query' => [
                    'nopeserta_bpjs' => $noKartu
                ]
            ]);
            $body = json_decode($response->getBody(), True);
            $data_pasien = !empty($body['response']) ? $body['response'] : null;
            $data = [
                'noKartu' => $noKartu,
                'nik' => $nik,
                'nama' => $nama,
                'jenis_kelamin' => $jenis_kelamin,
                'tgl_lahir' => $tgl_lahir,
                'noMr' => $noMr,
                'noTelepon' => $noTelepon,
                'data_pasien' => $data_pasien
            ];
            $result['result'] = $data;
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCheckinMjkn()
    {
        $request = Yii::$app->request;
        $title = 'Check-In MJKN';
        $modelForm = new AntrianCheckinMjkn;
        if($request->post()){
            $response = $this->_restPendaftaran->post('api/check-in-jkn',[
                'form_params' => $request->post()
            ]);
            $response = json_decode($response->getBody(), true);
            $response['response']['randString'] = DocoHelpers::generateRandomString();
            $response['response']['pendaftaranol']['pendaftaranol_id'] = DocoHelpers::encrypt(ArrayHelper::getValue($response, 'response.pendaftaranol.pendaftaranol_id', null));
            return DocoHelpers::response($response);
        } else {
            $url_aplikasi_finger_bpjs = $this->_restAntrian->get('allow/get-url-finger', [
                'query' => ['type' => 'url_finger_bpjs']
            ]);
            $url_aplikasi_finger_bpjs = json_decode($url_aplikasi_finger_bpjs->getBody(), TRUE);
            $url_aplikasi_finger_bpjs = ArrayHelper::getValue($url_aplikasi_finger_bpjs, 'response.lookup_value', 'fingerbpjs://param1=x');

            return $this->renderPartial('modal-antrian-bpjs/modal-checkin-mjkn', get_defined_vars());
        }
    }

    public function actionPasienRouting()
    {
        $request = Yii::$app->request;
        $title = 'Informasi Pasien Routing';
        $modelForm = new PasienRoutingRujukanForm;
        if($request->post()){
            $response = $this->_restPendaftaran->post('api/check-in-pasien-routing',[
                'form_params' => $request->post()
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
        } else {
            $url_aplikasi_finger_bpjs = $this->_restAntrian->get('allow/get-url-finger', [
                'query' => ['type' => 'url_finger_bpjs']
            ]);
            $url_aplikasi_finger_bpjs = json_decode($url_aplikasi_finger_bpjs->getBody(), TRUE);
            $url_aplikasi_finger_bpjs = ArrayHelper::getValue($url_aplikasi_finger_bpjs, 'response.lookup_value', 'fingerbpjs://param1=x');

            return $this->renderPartial('pasien-routing/modal-pasien-routing', get_defined_vars());
        }
    }

    public function actionModalListKonsulPasienRouting()
    {
        $request = Yii::$app->request;
        $response = $this->_restAntrian->get('dashboard/check-pasien-has-konsul?nomor_identitas='.$request->get('nomor_identitas'));
        $response = json_decode($response->getBody(), true);
        $pendaftaran = ArrayHelper::getValue($response, 'response');
        $pendaftaranFiltered = array_values(array_filter($pendaftaran, function ($value) {
            return $value['konsulpoli_id'] != null && $value['konsulpoli_id'] != '';
        }));
        if (count($pendaftaranFiltered) > 0 ) {
            $title = "Daftar Konsultasi Poliklinik";
            return $this->renderPartial('pasien-routing/modal-pasien-konsul', get_defined_vars());
        } else if (count($pendaftaran) > 0) {
            return DocoHelpers::responseTemplate(422, 'Nomor Identitas ditemukan, tetapi tidak memiliki data konsul');
        } else {
            return DocoHelpers::responseTemplate(422, 'Nomor Identitas tidak ditemukan');
        }
    }

    public function actionDatatableListKonsul()
    {
        $request = Yii::$app->request;
        $payloadDatatable = DocoDatatableHelper::convertToRestfulParams(Yii::$app->request->get());
        $response = $this->_restAntrian->get('dashboard/get-datatable-konsul-poli', [
            'query' => [
                'nomor_identitas' => $request->get('nomor_identitas'),
                'paginationOption' => [
                    'page' => $payloadDatatable['page'],
                    'limit' => $payloadDatatable['per-page']
                ]
            ]
        ]);
        $response = json_decode($response->getBody(), true);

        return DocoHelpers::response([
            'data' => ArrayHelper::getValue($response, 'response.data', []),
            'draw' => Yii::$app->request->get('draw'),
            'recordsTotal' => ArrayHelper::getValue($response, 'response.recordsTotal', 0),
            'recordsFiltered' => ArrayHelper::getValue($response, 'response.recordsFiltered', 0),
        ]);
    }

    public function actionPilihStatusPasien()
    {
        $request = Yii::$app->request;

        $title = 'Status Pasien';

        $carabayar = $request->get('cara_bayar', null);
        $ruangan = DocoConstants::RUANGAN_PENDAFTARAN_RAJAL;
        $jenisantrian_id = $request->get('jenis_antrian_id', null);

        if ($jenisantrian_id == DocoConstants::JA_PDN) {
            $modelForm = new PasienForm;
            return $this->renderPartial('modal-antrian-umum/modal-pilih-status-pasien', get_defined_vars());
        } else {
            return $this->renderPartial('modal-antrian-bpjs/modal-pilih-status-pasien', get_defined_vars());
        }

    }

    public function actionCaraBayar()
    {
        $title = 'Cara Bayar';
        return $this->renderPartial('modal-carabayar', get_defined_vars());
    }

    public function actionGetListDokterByPolyId($poly_id, $jadwalbukapoli_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = $empty = [];
        $DokterRequest = $this->_restAntrian->get('dashboard/list-poly-dokter',['query'=>['id'=>$poly_id, 'jadwalbukapoli_id' => $jadwalbukapoli_id]]);
        $bodyDokterRequest = json_decode($DokterRequest->getBody(), TRUE);
        $list_poly_dokter = $bodyDokterRequest['response'];
        if ($list_poly_dokter) {
            foreach ($list_poly_dokter as $key => $value) {
                if ($value['kuota_tersedia'] > 0) {
                    $data[] = $value;
                } else {
                    $empty[] = $value;
                }
            }
        }
        $result['message'] = 'success';
        $result['data'] = array_merge($data, $empty);
        return $result;
    }

    public function actionGetPasienByNorm($no_rm)
    {
        try{
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = $this->_restAntrian->get('dashboard/cari-pasien',['query'=>['no_rm'=>$no_rm]]);
            $body = json_decode($response->getBody(), TRUE);
            $result['status'] = isset($body['response']['data'])?200:500;
            $result['data'] = $body['response']['data'];
            return $result;
        }catch (RequestException $e){
            var_dump($e);exit;
        }
    }

    public function actionKasir()
    {
        $judulLayarAntrian = Yii::t('fe', 'Antrian Kasir');
        $request = $this->_restAntrian->get('dashboard/get-layar-by-jenis-id', ['query' => ['id' => 178]]);
        $body = json_decode($request->getBody(), TRUE);
        $list_layar = $body['response'];

        return $this->render('antrian-kasir', get_defined_vars());
    }

    public function actionAntrianPenunjang($id)
    {
        $judulLayarAntrian = Yii::t('fe', 'Penunjang');
        $id = DocoHelpers::decrypt($id);
        $jenisantrian_id = 179;
        $fungsiantrian_id = 321;
        $InstPenunjangRequest = $this->_restAntrian->get('dashboard/list-ruangan-penunjang',['query'=>['id'=>$id]]);
        $bodyPenunjangRequest = json_decode($InstPenunjangRequest->getBody(), TRUE);
        $list_poly_penunjang = $bodyPenunjangRequest['response']['data'];

        return $this->render('antrian-penunjang-form',get_defined_vars());
    }

    public function actionSimpanAntrian()
    {
        try{
            if(!isset($_POST)){
                throw new Exception("Method Tidak Diijinkan", 500);
            }
            $post = $_POST;
            $simpanRequest = $this->_restAntrian->post('dashboard/create-antrian',['form_params'=>$post]);
            $body = json_decode($simpanRequest->getBody(), true);
            if(isset($body['response']['data']) && is_array($body['response']['data'])){
                $dataRes = $body['response']['data'];
                if(isset($dataRes['antrian_id'])){
                    $dataRes['antrian_id'] = DocoHelpers::encrypt($dataRes['antrian_id']);
                }
            }
            return DocoHelpers::response($body,false,true);
        } catch(RequestException $e){
            // $body = json_decode($e->getResponse()->getBody(), true);
            return DocoHelpers::responseTemplate(500,$e->getResponse()->getBody());
        } catch(Exception $e){
            return DocoHelpers::responseTemplate(500,$e->getMessage());
        }
    }

    public function actionCetakAntrianKasir($no,$jenisantrian_id)
    {
        $no_antrian = $no;

        if ($jenisantrian_id == 178) {
            $info = "Silahkan ke Loket Antrian Kasir";
            $warning = "Simpan antrian hingga transaksi selesai";
        } elseif ($jenisantrian_id == 176) {
            $info = "Silahkan ke Loket Antrian";
            $warning = "Simpan antrian hingga mendapatkan obat";
        }

        return $this->render('cetak-antrian-kasir', get_defined_vars());
    }

    public function actionCetakAntrianDashboard()
    {
        $request = Yii::$app->request;
        try {
            $post = $request->post();
            if(isset($post['no_antrian']) && isset($post['jenisantrian_id']) && isset($post['antrian_id'])){

                if ($post['jenisantrian_id'] != 176) {
                    $antrian_id = DocoHelpers::decrypt($post['antrian_id']);
                }
                $antrian_id = $post['antrian_id'];
                $antrian_jenis_id = $request->post('antrian_jenis_id', 'MjEyMQ');

                switch ($post['jenisantrian_id']) {
                  case '176'://farmasi
                      $bodyReq = $this->_restAntrian->get('dashboard/cetak-antrian-farmasi',['query'=>['antrian_id'=>$antrian_id]]);

                      $body = json_decode($bodyReq->getBody(), true);
                      $response = $body['response'];
                      $data_header = @$response['header'];
                      $data_body = @$response['body'];
                      $data_footer = @$response['footer'];

                      $no_antrian = $_POST['no_antrian'];
                      $jenisantrian_id = DocoHelpers::encrypt($_POST['jenisantrian_id']);
                      $url_kembali = '/antrian/dashboard/jenis-antrian?jenis_id='.$antrian_jenis_id;
                      return $this->render('cetak-antrian-farmasi',get_defined_vars());
                    break;
                  case '177'://pendaftaran
                      $no_antrian_poli = isset($_POST['no_antrian_poli']) ? $_POST['no_antrian_poli'] : null;
                      if(isset($post['tipe']) && $post['tipe'] == 'default'){
                        $bodyReq = $this->_restAntrian->get('dashboard/cetak-antrian-pendaftaran',['query'=>['antrian_id'=>$antrian_id, 'no_antrian_poli' => $no_antrian_poli]]);
                      }else{
                        $bodyReq = $this->_restAntrian->get('dashboard/cetak-antrian-pendaftaran-v2',['query'=>['antrian_id'=>$antrian_id, 'no_antrian_poli' => $no_antrian_poli]]);
                      }
                      $body = json_decode($bodyReq->getBody(), true);
                      $response = $body['response'];
                      $data_header = @$response['header'];
                      $data_body = @$response['body'];
                      $data_footer = @$response['footer'];
                      $jumlah_cetak = empty($no_antrian_poli) ? 1 : (isset($post['tipe']) && $post['tipe'] == 'default') ? 1 : 2;

                      $no_antrian = $_POST['no_antrian'];
                      $jenisantrian_id = DocoHelpers::encrypt($_POST['jenisantrian_id']);
                      $url_kembali = '/antrian/dashboard/jenis-antrian?jenis_id='.$antrian_jenis_id;
                      if (isset($post['detail_id']) && $post['detail_id']) {
                          $url_kembali .= '&detail_id='. $post['detail_id'];
                      }
                      return $this->render('cetak-antrian-pendaftaran',get_defined_vars());
                    break;
                  case '179'://penunjang
                      $bodyReq = $this->_restAntrian->get('dashboard/cetak-antrian-penunjang',['query'=>['antrian_id'=>$antrian_id]]);

                      $body = json_decode($bodyReq->getBody(), true);
                      $response = $body['response'];
                      $data_header = @$response['header'];
                      $data_body = @$response['body'];
                      $data_footer = @$response['footer'];

                      $no_antrian = $_POST['no_antrian'];
                      $jenisantrian_id = DocoHelpers::encrypt($_POST['jenisantrian_id']);
                      $url_kembali = '/antrian/dashboard/jenis-antrian?jenis_id='.$antrian_jenis_id;
                      return $this->render('cetak-antrian-penunjang',get_defined_vars());
                    break;
                  case '178'://kasir
                      $bodyReq = $this->_restAntrian->get('dashboard/cetak-antrian-kasir',['query'=>['antrian_id'=>$antrian_id]]);

                      $body = json_decode($bodyReq->getBody(), true);
                      $response = $body['response'];
                      $data_header = @$response['header'];
                      $data_body = @$response['body'];
                      $data_footer = @$response['footer'];

                      $no_antrian = $_POST['no_antrian'];
                      $jenisantrian_id = DocoHelpers::encrypt($_POST['jenisantrian_id']);
                      $url_kembali = '/antrian/dashboard/jenis-antrian?jenis_id='.$antrian_jenis_id;
                      return $this->render('cetak-antrian-kasir',get_defined_vars());
                    break;
                    case DocoConstants::ANTRIAN_BPJS:
                        $no_antrian = isset($_POST['no_antrian']) ? $_POST['no_antrian'] : null;
                        $bodyReq = $this->_restAntrian->get('dashboard/cetak-antrian-bpjs-onsite',[
                            'query' => [
                                'antrian_id' => $antrian_id,
                                'no_antrian' => $no_antrian,
                            ]
                        ]);
                        $body = json_decode($bodyReq->getBody(), true);
                        $response = $body['response'];
                        $data_header = @$response['header'];
                        $data_body = @$response['body'];
                        $data_footer = @$response['footer'];

                        $no_antrian = $post['no_antrian'];
                        $jenisantrian_id = DocoHelpers::encrypt($_POST['jenisantrian_id']);
                        $url_kembali = '/antrian/dashboard/jenis-antrian?jenis_id='.$antrian_jenis_id;
                        return $this->render('cetak-antrian-bpjs-onsite',get_defined_vars());
                    break;
                  default:
                      $bodyReq = $this->_restAntrian->get('dashboard/cetak-antrian',['query'=>['antrian_id'=>$antrian_id]]);

                      $body = json_decode($bodyReq->getBody(), true);
                      $response = $body['response'];
                      $data_header = @$response['header'];
                      $data_body = @$response['body'];
                      $data_footer = @$response['footer'];

                      $no_antrian = $_POST['no_antrian'];
                      $jenisantrian_id = DocoHelpers::encrypt($_POST['jenisantrian_id']);
                      $url_kembali = '/antrian/dashboard/jenis-antrian?jenis_id='.$antrian_jenis_id;
                      return $this->render('cetak-antrian-poly',get_defined_vars());
                    break;
                }

            } else{
                throw new Exception("Method Tidak Diijinkan", 1);
            }
        } catch(Exception $e){
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch(RequestException $e){
            $resBody = json_decode($e->getResponse()->getBody(),TRUE);
            return DocoHelpers::response(['message' => $resBody['message']]);
        }
    }

    public function actionCetakAntrianV2()
    {
        $request = Yii::$app->request;
        try {
            $post = $request->post();
            if(isset($post['no_antrian']) && isset($post['jenisantrian_id']) && isset($post['antrian_id'])){
                $antrian_id = $post['antrian_id'];
                $no_antrian = isset($_POST['no_antrian']) ? $_POST['no_antrian'] : null;
                $bodyReq = $this->_restAntrian->get('dashboard/cetak-antrian-bpjs-onsite-pasien-baru',[
                    'query' => [
                        'antrian_id' => $antrian_id,
                        'no_antrian' => $no_antrian,
                        'jenisantrian_id' => $request->post('jenis_antrian_id'), // jenis_antrian_id = jenisantrian_id yang digunakanan pada antrian_t pendaftaran/poli dkk
                        'nama_antrian' => isset($post['nama_antrian']) ? $post['nama_antrian'] : 'BPJS'
                    ]
                ]);
                $body = json_decode($bodyReq->getBody(), true);
                $response = $body['response'];
                $data_header = @$response['header'];
                $data_body = @$response['body'];
                $data_footer = @$response['footer'];

                $no_antrian = $post['no_antrian'];
                $jenisantrian_id = DocoHelpers::encrypt($_POST['jenisantrian_id']);
                $antrian_jenis_id = $request->post('antrian_jenis_id', '');
                $url_kembali = '/antrian/dashboard/jenis-antrian?jenis_id='.$antrian_jenis_id;
                return $this->render('cetak-antrian-bpjs-onsite-pasien-baru',get_defined_vars());
            }else{
                throw new Exception("Method Tidak Diijinkan", 1);
            }
        } catch(Exception $e){
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch(RequestException $e){
            $resBody = json_decode($e->getResponse()->getBody(),TRUE);
            return DocoHelpers::response(['message' => $resBody['message']]);
        }
    }

    public function actionCetakAntrian($no, $jenisantrian_id)
    {
        $no_antrian = $no;

        if ($jenisantrian_id == 176) {
          $info = "Silahkan ke Loket Antrian";
          $warning = "Simpan antrian hingga mendapatkan obat";
        } else {
          $info = "Silahkan ke Loket Antrian Kasir";
          $warning = "Simpan antrian hingga transaksi selesai";
        }

        return $this->render('cetak-antrian', get_defined_vars());
    }

    public function actionGetCallAntrian() {
        $request = Yii::$app->request;
        $post = $request->post();

        $response = $this->_restAntrian->get('allow/get-antrian-by-layar?advanced-filter[loket_id]='.$post['loket'].'&advanced-filter[status_antrian]=1',
                            [
                                'form_params' => []
                            ]);
        $body = json_decode($response->getBody(), True);
        $infoAntrian = $body['response']['data'];

        echo json_encode($infoAntrian);
    }

    public function actionGetQueueRemains() {
        $request = Yii::$app->request;
        $post = $request->post();

        $response = $this->_restAntrian->get('allow/get-antrian-by-layar?advanced-filter[loket_id]='.$post['loket'].'&advanced-filter[status_antrian]=[2,4]',
                            [
                                'form_params' => []
                            ]);
        $body = json_decode($response->getBody(), True);
        $infoAntrian = $body['response']['_meta'];

        echo json_encode($infoAntrian);
    }

    public function actionGetPasienApotekByNorm($no_rm)
    {
        try{
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = $this->_restApotek->get('allow/get-reseptur-antrian',['query'=>['no_rekam_medik'=>$no_rm]]);
            $body = json_decode($response->getBody(), TRUE);
            $result['status'] = empty($body['response']['data'])?500:200;
            $result['data'] = $body['response']['data'];
            return $result;
        }catch (RequestException $e){
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionFetchInfoNotif()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $day_now = date('N');
            $response = $this->_restMaster->get('notifikasi/list-notif-day', ['query' => ['hari' => $day_now]]);
            $res = json_decode($response->getBody(), TRUE);

            return $res;
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionListAntrian()
    {
        $title = $this->_title;
        $indexRequest = $this->_restAntrian->get('dashboard/index-dashboard');
        $body = json_decode($indexRequest->getBody(),TRUE);

        return $this->render('list', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restAntrian->request('get', 'dashboard/jenis-antrian-poli?');

            $row = $cache = [];
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->get('start',1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['lookup_id']);
                $value['primary'] = $primaryKey;
                $cache[] = [
                    'lookup_id' => $value['lookup_id'],
                    'lookup_value' => $value['lookup_value'],
                ];
                unset($value['lookup_id']);

                $value['is_active'] = ($value['is_active']) ? 'Aktif' : 'Tidak aktif';
                $value['rowNum'] = $no;
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

    public function actionGenerateLink($id)
    {
        $id_decrypt = DocoHelpers::decrypt($id);

        $name = "antrian";

        $url_login = "http://".$_SERVER['HTTP_HOST']."/allow/antrian";
        $url = "http://".$_SERVER['HTTP_HOST']."/antrian/dashboard/jenis-antrian?jenis_id=".$id;


        $val = [
            'taskkill /F /IM Chrome.exe /T',
            'start chrome --kiosk --profile-directory=Default --app="'.$url_login.'"',
            'timeout /T 8',
            'start chrome --kiosk --profile-directory=Default --app="'.$url.'"'
        ];

        return DocoHelpers::generateBatFile($val,$name);
    }

    public function actionJenisAntrianDetail()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $data = [];
            $jenisantrian_id = DocoHelpers::decrypt($get['id']);
            $response = $this->_restAntrian->get('dashboard/get-jenis-antrian-detail', ['query' => ['jenisantrian_id' => $jenisantrian_id]]);
            $body = json_decode($response->getBody(), TRUE);
            if ($body['response']['data']) {
                foreach ($body['response']['data'] as $key => $value) {
                   $value['jenisantrian_id'] = DocoHelpers::encrypt($value['jenisantrian_id']);
                   $value['jenisantriandetail_id'] = DocoHelpers::encrypt($value['jenisantriandetail_id']);
                   $data[] = $value;
                }
            }
            return DocoHelpers::response($data);

        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    private function generateCarousel($data, $limit = 3)
    {
        $count = 1;
        $idx = 0;
        $newList = [];
        foreach ($data as $key => $value) {
            $newList[$idx][] = $value;
            if ($count == $limit) {
                $count = 1;
                $idx++;
            } else {
                $count++;
            }
        }

        return $newList;
    }

    private function generateDummyData($loketList, $counter = 4)
    {
        $idx = empty($loketList) ? 0 : key($loketList);
        for ($i=0; $i < $counter; $i++) {
            $dataDummy = [
                'ruangan_id' => null,
                'ruangan_nama' => '-',
                'pegawai_id' => null,
                'pegawai_nama' => '-',
                'jadwal' => '-',
                'jadwaldokter_id' => null,
            ];
            $loketList[$idx][] = $dataDummy;
        }
        return $loketList;
    }

    public function actionLayarJadwalOperasi($layar = null)
    {
        $state = false;
        $request = Yii::$app->request;
        $judulLayarAntrian = Yii::t('fe', 'Dashboard Kamar Operasi');
        $jenisLayarAntrian = Yii::t('fe', 'Bedah');

        $getRequest = $this->guzzleExec($this->_restAntrian,
        [
            'url' => 'display-antrian/data-layar-jadwal-operasi',
        ]);


        $listData = ArrayHelper::getValue($getRequest, 'dataJadwal');
        $konfig_layar = ArrayHelper::getValue($getRequest, 'konfig-layar');
        return $this->render('layarjadwaloperasi', compact('listData', 'judulLayarAntrian', 'jenisLayarAntrian', 'konfig_layar'));
    }

    public function actionCekPasien()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $endpointRes = $this->_restAntrian->get('dashboard/cek-pasien?no_identitas_pasien='.$get['no_identitas_pasien']);
            $response = json_decode($endpointRes->getBody(), true);
            if (count(ArrayHelper::getValue($response, 'response', [])) <= 0) {
                return DocoHelpers::responseTemplate(422, 'NIK yang di inputkan tidak ditemukan, harap mendaftar menggunakan opsi pasien baru');
            } else if (count(ArrayHelper::getValue($response, 'response', [])) > 1) {
                return DocoHelpers::responseTemplate(422, 'NIK yang di inputkan double, harap menghubungi petugas');
            } else if (ArrayHelper::getValue($response, 'response.0.pendaftaran_hari_ini', null)) {
                return DocoHelpers::responseTemplate(422, 'Tidak dapat mengambil antrian lebih dari satu kali untuk nik yang sama pada hari ini');
            } else {
                $title = 'Konfirmasi Data Pasien';
                $data = ArrayHelper::getValue($response, 'response.0', []);
                return $this->renderPartial('pasien-lama/modal-konfirmasi-pasien', compact('title', 'data'));
            }
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionAntrianV2()
    {
        $request = Yii::$app->request;
        $decryptJenisId = DocoHelpers::decrypt(Yii::$app->request->get('jenis_id'));

        if($request->post()){
            $post = $request->post();
            $post['jenisantrian_id'] = DocoConstants::JA_PDN;
            $post['jenisantriandetail_id'] = null;
            $post['carabayar_pilih'] = DocoConstants::GROUP_UMUM;
            $post['statuspasien'] = $request->post('statuspasien',310);

            $response = $this->_restAntrian->request('POST','dashboard/create-antrian',['form_params'=>$post]);
            $body = json_decode($response->getBody(), true);
            $dataRes = $body['response']['data'];
            $dataCetak = $dataRes['cetak'];
            if(isset($body['response']['data']) && is_array($body['response']['data'])){
                $dataRes = $body['response']['data'];
                if(isset($dataRes['antrian_id'])){
                    if (!is_array($dataRes['antrian_id'])) {
                        $dataRes['antrian_id'] = DocoHelpers::encrypt($dataRes['antrian_id']);
                    }
                }

                // penambahan update data poli di display : ali
                if(isset($dataRes['list_antrian_poli'])){
                    // set ke display antrian
                    $data_display["list_antrian_poli"] = $dataRes['list_antrian_poli'];
                    $mode = Yii::$app->params->mode;
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'display-antrian-'.$mode,
                        'message' => Json::encode(['data' => $data_display])
                    ]);
                    // end set display antrian
                }


                // create reservasi
                $payloadReservasi = [
                  "antrian_id" => ArrayHelper::getValue($dataRes, 'antrian_poli_id'),
                  "pendaftaranol_id" => null,
                  "pendaftaran_id" => null,
                  "no_pendaftaranol" => null,
                  "ruangan_id" => $post['ruangan_id'],
                  "tgl_pendaftaranol" => date("Y-m-d"),
                  "pegawai_id" => $post['dokter_pilih'],
                  "jam_kunjungan" => $post['jam_mulai']." - ".$post['jam_tutup'],
                  "carabayar_id" => DocoConstants::CARA_BAYAR_UMUM,
                  "penjamin_id" => DocoConstants::VAR_P_Perseorangan,
                  "jam_mulai" => $post['jam_mulai'],
                  "jam_tutup" => $post['jam_tutup'],
                  "jadwaldokter_id" => $post['jadwaldokter_pilih'],
                  "pasien_id" => DocoHelpers::decrypt($post['pasienEnc']),
                  "klasifikasipasien_id" => $post['klasifikasi_pilih'],
                  "jenis_reservasi" => null,
                  "keterangan" => "",
                  "jadwalbukapoli_id" => null,
                  "shift_id" => null,
                  "no_asuransi" => null,
                  "no_rujukan" => "",
                  "status_pasien" => null,
                  "status_daftar_ol" => null,
                  "user_id" => 1, //administrator
                  "nomor_urut" => null,
                  "is_nomor_urut" => null,
                  "no_bpjs" => "",
                  "jeniskunjungan" => "",
                  "jenisidentitas" => null,
                  "no_identitas_pasien" => null,
                  "namadepan" => null,
                  "nama_pasien" => null,
                  "tempat_lahir" => null,
                  "tanggal_lahir" => null,
                  "jeniskelamin" => null,
                  "no_telepon_pasien" => null,
                  "alamat_pasien" => null,
                  "tipe_pasien" => 0,
                  "is_checkin" => true,
                  "tgl_checkin" => date('Y-m-d H:i:s'),
                  "is_from_antrian" => 1
                ];


                $restPendaftaran = $this->_restPendaftaran->post('pendaftaran-online/daftar-online', [
                    'form_params' => $payloadReservasi
                ]);
                $response = json_decode($restPendaftaran->getBody(), true);
                $status = ArrayHelper::getValue($response,'metadata.status',200);
                if($status != 200){
                    $getRequest = $this->guzzleExec(
                        $this->_restAntrian,
                        [
                            'url' => 'dashboard/delete-antrian-poli',
                            'method' => 'delete'
                        ],
                        ['query' => ['id'=>ArrayHelper::getValue($dataRes, 'antrian_poli_id')]]
                    );
                    return DocoHelpers::response($response,$status,false);
                }
            }

            return DocoHelpers::response($body,false,true);
        }



        $requests = [
            'actionListPolyAntrian',
            'actionKonfigSystem',
            'actionListKlasifikasiPasien',
            'actionListGroupCaraBayar',
            'actionKonfigKuotaAntrian',
            'actionKonfigUrlCetak'
        ];

        $allowRequest = $this->_restAntrian->request('POST', 'allow/lists',[
            'form_params' =>
                [ 'params' =>$requests, 'jenis_id' => $decryptJenisId]
        ]);
        $bodyRequest = json_decode($allowRequest->getBody(), TRUE);
        $allowResponse = $bodyRequest['response'];



        $list_poly_antrian = $allowResponse['actionListPolyAntrian'];
        $konfigSystem = $allowResponse['actionKonfigSystem'];
        $isKeteranganPasien = $konfigSystem['is_keteranganpasien'];
        $klasifikasiPasien = $konfigSystem['klasifikasi_pasien_umum'];
        $list_klasifikasi_pasien = $allowResponse['actionListKlasifikasiPasien'];
        $list_carabayar = $allowResponse['actionListGroupCaraBayar'];
        $konfigKuotaAntrian = $allowResponse['actionKonfigKuotaAntrian'];
        $konfig_url_cetak = $allowResponse['actionKonfigUrlCetak'];

        $isKeteranganPasien = -1;

        $kuatoAntrian = 1;
        if ($konfigSystem['kuota_antrian'] == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK) {
            $kuatoAntrian = -1;
        }


        $newListPoly = [];
        $empty = [];
        foreach ($list_poly_antrian as $v_listpoly) {
            if($v_listpoly['instalasi_id'] == DocoConstants::INSTALASI_ID_RJ){
                if ($v_listpoly['sisa_kuota'] > 0) {
                    $newListPoly[] = $v_listpoly;
                } else {
                   $empty[] = $v_listpoly;
                }
            }
        }

        $list_poly_antrian = array_merge($newListPoly, $empty);
        $judulLayarAntrian = 'Antrian Pendaftaran';
        $jenisLayarAntrian = 'Rawat Jalan';
        $idPasien = $request->get('id');

        return $this->render('antrian-pendaftaran-umum',get_defined_vars());
    }

    public function actionReservasiNonMjkn()
    {
        $request = Yii::$app->request;
        $title = 'Pendaftaran Mandiri';
        $modelForm = new ReservasiNonMJKN;
        if($request->post()){
            $modelForm->kode_booking = $request->post('kode_booking');
            $modelForm->penjamin_id = $request->post('penjamin_id');
            $requestPayload = json_decode($request->post('payload'), true);
            $pendaftaranOlId = ArrayHelper::getValue($requestPayload, 'pendaftaranol_id');
            $requestPayload['pendaftaranol_id'] = DocoHelpers::encrypt($pendaftaranOlId);
            $requestPayload['dokter_id'] = ArrayHelper::getValue($requestPayload, 'pegawai_id');
            $requestPayload['asalrujukan_id'] = DocoConstants::ASAL_RUJUKAN_DS;
            $requestPayload['pj_pengantar'] = DocoConstants::PJ_DIRI_SENDIRI;
            $payload = [];
            $payload[] = $requestPayload;
            if($modelForm->validate()) {
                return $this->guzzleExec($this->_restPendaftaran, [
                    'url' => 'inf-reservasi-poliklinik/auto-register',
                    'method' => 'POST',
                    'payload' => [
                        'form_params' => [
                            'processKey' => $request->post('randString'),
                            'reservationItems' => $payload,
                        ],
                    ],
                    'returnResponse' => true,
                ]);
            }
            else {
                $errors = DocoHelpers::parseError($modelForm->errors, 'ReservasiNonMJKN');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ],422);
            }
        }
        
        return $this->renderPartial('pasien-non-mjkn/modal-non-mjkn', compact('title', 'modelForm', 'urlAplikasiFingerBpjs'));
    }

    public function actionCekKodeBooking()
    {
        $title = 'Pendaftaran Mandiri';
        $request = Yii::$app->request;
        $kodeBooking = $request->get('kode_booking');
        $response = $this->_restPendaftaran->get('allow/cek-kode-booking-non-mjkn?kode_booking='.$kodeBooking);
        $response = json_decode($response->getBody(), true);

        if(isset($response['metadata']['status']) && $response['metadata']['status'] !== 200) {
            return DocoHelpers::response($response);
        }

        $response = ArrayHelper::getValue($response, 'response');
        $pendaftaran = ArrayHelper::getValue($response, 'data');
        $listPenjamin = ArrayHelper::getValue($response, 'list_penjamin.data');
        if(!empty($listPenjamin)) {
            $listPenjamin = ArrayHelper::map($listPenjamin, 'penjamin_id', 'penjamin_nama');
        }
        $defaultPenjamin = ArrayHelper::getValue($response, 'default_penjamin_mqare');
        $randString = DocoHelpers::generateRandomString();
        $modelForm = new ReservasiNonMJKN;
        $modelForm->penjamin_id = $defaultPenjamin;
        $tempatLahir = ArrayHelper::getValue($pendaftaran, 'tempat_lahir_ol');
        $tanggalLahir = ArrayHelper::getValue($pendaftaran, 'tanggal_lahir_ol');
        $tanggalReservasi = ArrayHelper::getValue($pendaftaran, 'tgl_kunjungan');
        $tanggalReservasi = date('d M Y', strtotime($tanggalReservasi));
        $umur = '';
        if($tanggalLahir) {
            $umur = DocoHelpers::getUmur($tanggalLahir);
            $umur =  substr($umur,5);

            $tanggalLahir = date('d M Y', strtotime($tanggalLahir));
        }

        $keterangan = $tempatLahir.', '.$tanggalLahir.' - '.$umur;
        return $this->renderPartial('pasien-non-mjkn/modal-pilih-penjamin', compact(
            'title', 'modelForm', 'pendaftaran', 'keterangan', 'kodeBooking', 'tanggalReservasi',
            'listPenjamin', 'randString'
        ));
    }

    public function actionPrintAntrianPoli()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-antrian-poli.pdf";
        try {
            $pendaftaran_id = $request->get('pendaftaran_id', null); // jika antrian_id kosong bisa pake pendaftaran_id
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $antrian_id = $request->get('antrian_id', 0); // jika pendaftaran id kosong, biasanya pake antrian_id

            $post = [
                'pendaftaran_id' => $decryptPendaftaranId,
                'antrian_id' => empty($antrian_id) || $antrian_id == 0 ? 0 : $antrian_id,
                'jumlah' => $request->get('jumlah', 1),
                // 'waktuestimasi_mulai' => ArrayHelper::getValue($waktuestimasi, 'response.waktuestimasi_mulai'),
                // 'waktuestimasi_berakhir' => ArrayHelper::getValue($waktuestimasi, 'response.waktuestimasi_berakhir'),
            ];
            
            if (Yii::$app->report->enabled) {
                $urlReport = 'print-antrian-poliklinik';
                
                return Yii::$app->report->exec($urlReport . '?' . http_build_query($post), [
                    'queryParameter' => $post,
                    'manualRender' => function () use ($post, $path) {
                        $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-antrian-poli', [
                            'form_params' => $post,
                            'save_to' => $path
                        ]);
                        
                        return DocoHelpers::previewPdf($path);
                    }
                ]);
            }

            $response = $this->_restPendaftaran->post(
                'inf-daftar-sepuluh-terakhir/print-antrian-poli',
                [
                    'form_params' => $post,
                    'save_to' => $path
                ]
            );

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakAntrianNonMjkn()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id', null);
            $jenisAntrianId = $request->get('jenisantrian_id', null);
            $url_kembali = '/antrian/dashboard/jenis-antrian?jenis_id='.$jenisAntrianId;

            return $this->render('cetak-antrian-non-mjkn', compact('url_kembali', 'pendaftaran_id'));
        } catch(Exception $e){
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch(RequestException $e){
            $resBody = json_decode($e->getResponse()->getBody(),TRUE);
            return DocoHelpers::response(['message' => $resBody['message']]);
        }
    }

    public function actionAutoRegisterProcess()
    {
        $request = Yii::$app->request;
        $randString = $request->post('randString', null);
        $payload = $request->post('reservationItems', []);

        if ($payload) {
            $payload = json_decode($payload, true);
        }

        $this->guzzleExec($this->_restPendaftaran, [
            'url' => 'inf-reservasi-poliklinik/auto-register',
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'processKey' => $randString,
                    'reservationItems' => $payload,
                ],
            ],
            'returnResponse' => true,
        ]);

        return DocoHelpers::response([
            'msg' => 'Success set Reservation Queue',
            'data' => compact('randString')
        ], 200, true);
    }

    private function getDefaultPenjaminId($carabayar_id)
    {

        $default_penjamin_ids = $this->_restAntrian->get('dashboard/get-lookup-by-type',
                ['query' => ['type' => 'default_penjamin_id_by_carabayar']
            ]);
        $default_penjamin_ids = json_decode($default_penjamin_ids->getBody(), True);
        $default_penjamin_ids = ArrayHelper::getValue($default_penjamin_ids, 'response', []);
        $default_penjamin_id_bpjs = array_filter(
                $default_penjamin_ids,
                function($val) use ($carabayar_id){
                    $additional_data = json_decode($val['additional_data'], true);
                    if (isset($additional_data['carabayar_id']) && $additional_data['carabayar_id'] == $carabayar_id) {
                        return true;
                    }
                }
            );

        return ArrayHelper::getValue($default_penjamin_id_bpjs, '0.lookup_value', null);
    }
}
