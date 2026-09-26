<?php

/**
 * @Author: afil
 * @Date:   2018-01-12 15:25:02
 * @Last Modified by:   Iqbal
 * @Last Modified time: 2018-08-13 14:13:20
 * @Description:
 */

namespace Doco\rajal\controllers;

use app\components\DocoConstants;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\Pelayanan\PelayananHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\rajal\models\InformasiForm;
use app\modules\rajal\models\AnamnesaForm;
use app\modules\rajal\models\BuatJanjiPoliForm;
use app\modules\rajal\models\PendaftaranForm;
use app\modules\rajal\models\KonsulpoliForm;
use app\modules\rajal\models\PasienBatalPeriksaForm;
use app\modules\rajal\models\SkriningCovidForm;
use app\modules\rajal\models\SkriningAssesmentPasienForm;
use app\modules\rajal\models\SkriningRajalForm;

class InformasiController extends DocoController
{
    protected $_title = "Rajal :: Informasi pasien rawat jalanxx";
    protected $_module = '/rajal';
    protected $_controller = '/rajal/informasi';
    protected $_page;
    protected $_restRajal;
    protected $_id_ruangan;
    protected $allowAction = [
        '*'
    ];
    public function init()
    {
        parent::init();
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_id_ruangan = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
        $this->_page = Yii::t('fe', 'Informasi pasien rawat jalan');
    }

    public function actionIndex()
    {
        $id_ruangan = DocoHelpers::encrypt($this->_id_ruangan);
        $status = $this->_status;

        // data select
        $list_data = $this->getListData();
        $data_pegawai = $list_data["data_pegawai"];
        $data_penjamin = $list_data["data_penjamin"];
        $data_statusperiksa = $list_data["data_statusperiksa"];

        return $this->render('index', get_defined_vars());
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
        $result['recordsFiltered'] = 0;

        try {
            $response = $this->_restRajal->get('inf-kunjungan-pasien/index?ruangan_id=' . $this->_id_ruangan . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start', 1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;

                $value['aksi'] = $this->getAksi($value);

                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function getListData($pendaftaran_id)
    {
        

        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($pendaftaran_id);
        $title = Yii::t('fe', 'Mulai pemeriksaan');
        $sub_title = $this->_page;
        $list_data = $this->getListDataApi();
        $data_pegawai = $list_data["data_pegawai"];
        // dump($data_pegawai);die;
        $modelPendaftaran = new PendaftaranForm;
        $form_name = substr(strrchr(get_class($modelPendaftaran), "\\"), 1);
        $is_disabled = 'true';

        $response = $this->_restRajal->get('inf-kunjungan-pasien/get-pasien?id=' . $id);
        $response = json_decode($response->getBody(), true);
        $data_pasien = $response["response"]["data"];
        // Cek data
        if ($modelPendaftaran->load($data_pasien, '')) {
            // Assign manually
            $modelPendaftaran->pegawai_id = $data_pasien['pegawai_id'];
            // Cek post
            if ($request->post()) {
                $data = $request->post('PendaftaranForm');
                $modelPendaftaran->pegawai_id = $data["pegawai_id"];
                $modelPendaftaran->tgl_masukperiksa = $data["tgl_masukperiksa"];
                $modelPendaftaran->pendaftaran_id = $id;
                $response = $this->_restRajal->post('inf-kunjungan-pasien/ubah-dokter', [
                    'form_params' => $modelPendaftaran->attributes
                ]);
                $response = json_decode($response->getBody(), true);
                $redirect = Url::to(['/rajal/pemeriksaan/periksa', 'id' => $pendaftaran_id]);

                return json_encode(['url' => $redirect]);
                $this->redirect(['pemeriksaan/periksa?id=' . $pendaftaran_id]);
                /*return DocoHelpers::responseTemplate(
                    200,
                    Yii::t('fe', 'Proses Berhasil Pasien Diperiksa'),
                    [],
                    ['title' => Yii::t('fe', 'Proses Berhasil Pasien Diperiksa'), 'text' => Yii::t('fe', 'Data Berhasil Disimpan')]
                );*/
            } else {
                return $this->renderAjax('_periksa', get_defined_vars());
            }
        } else {
            return DocoHelpers::responseTemplate(
                500,
                Yii::t('fe', 'Kegagalan Pada Sistem'),
                [],
                ['title' => Yii::t('fe', 'Gagal Mengubah Dokter'), 'text' => Yii::t('fe', 'Data tidak ditemukan.')]
            );
        }
    }

    public function actionBatalPeriksa($pendaftaran_id)
    {
        $model = new PasienBatalPeriksaForm;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($pendaftaran_id);

        $title = Yii::t('fe', 'Batal pemeriksaan');
        $sub_title = $this->_page;
        $username = Yii::$app->docoVars->user('nama');
        $model->load($request->post());
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $dataTagihan = $this->getTagihanPasien($id);
        $status_bayar = $dataTagihan['status_bayar'];
        if ($request->post()) {
            $model = new PasienBatalPeriksaForm;
            $model->load($request->post());
            if ($model->validate()) {
                $post = $request->post();
                $post['PasienBatalPeriksaForm']['tgl_batal'] = date('Y-m-d H:i:s', strtotime('NOW'));
                $response = $this->_restRajal->post('inf-kunjungan-pasien/batal-periksa', [
                    'form_params' => $post
                ]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body, false, $formName);
            } else {
                $errors = $model->errors;
                return DocoHelpers::response($errors, 422, $formName);
            }
        } else {
            return $this->renderAjax('_batalperiksa', get_defined_vars());
        }
    }

    public function actionRencanaKontrol($pendaftaran_id)
    {
        $request = Yii::$app->request;
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $title = Yii::t('fe', 'Rencana kontrol');
        $modelJanjiPoli = new BuatJanjiPoliForm;
        $modelInformasi = new InformasiForm;

        $form_name = substr(strrchr(get_class($modelJanjiPoli), "\\"), 1);

        try {
            $response = $this->_restRajal->get('inf-kunjungan-pasien/get-pasien?id=' . $pendaftaran_id);
            $response = json_decode($response->getBody(), true);
            $data_pasien = $response["response"]["data"];

            $modelInformasi->attributes = $data_pasien;

            if ($request->post()) {
                $data = $request->post('BuatJanjiPoliForm');

                $modelJanjiPoli->load($request->post());
                $modelJanjiPoli->ruangan_id = $this->_id_ruangan;
                $modelJanjiPoli->hari_jadwal = date("l", strtotime($modelJanjiPoli->tgl_jadwal));
                $modelJanjiPoli->tgl_buatjanji = date("Y-m-d H:i:s");
                $modelJanjiPoli->by_phone = 0;

                if ($modelJanjiPoli->validate()) {
                    $response = $this->_restRajal->post('inf-kunjungan-pasien/buat-janji-poli', [
                        'form_params' => $modelJanjiPoli->attributes
                    ]);
                    $response = json_decode($response->getBody(), true);

                    return DocoHelpers::responseTemplate(
                        200,
                        Yii::t('fe', 'message_berhasil'),
                        [],
                        ['title' => Yii::t('fe', 'message_berhasil'), 'text' => Yii::t('fe', 'message_tambah')]
                    );
                } else {
                    $errors = DocoHelpers::parseError($modelJanjiPoli->errors, $form_name);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            } else {
                return $this->renderAjax('_rencanakontrol', get_defined_vars());
            }
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     *
     * private function
     *
     */

    private function getListDataApi($ruangan_id = null)
    {
        $ruangan_id = is_null($ruangan_id) ? $this->_id_ruangan : $ruangan_id;
        try {
            $request = Yii::$app->request;
            $response = $this->_restRajal->get('inf-kunjungan-pasien/get-list-data?id_ruangan=' . $ruangan_id);
            $body = json_decode($response->getBody(), TRUE);

            $data_statusperiksa = empty($body['response']['data-statusperiksa']) ? [] : $body['response']['data-statusperiksa'];
            $data_pegawai = empty($body['response']['data-pegawai']) ? [] : $body['response']['data-pegawai'];
            $data_penjamin = empty($body['response']['data-penjamin']) ? [] : $body['response']['data-penjamin'];
            $result = [
                'data_statusperiksa' => $data_statusperiksa,
                'data_pegawai' => $data_pegawai,
                'data_penjamin' => $data_penjamin,
            ];

            return $result;
        } catch (RequestException $e) {
            return [
                'data_statusperiksa' => [],
                'data_pegawai' => [],
                'data_penjamin' => [],
            ];
        } catch (\Exception $e) {
            return [
                'data_statusperiksa' => [],
                'data_pegawai' => [],
                'data_penjamin' => [],
            ];
        }
    }

    private function getAksi($data)
    {
        $pendaftaran_id = DocoHelpers::encrypt($data['pendaftaran_id']);

        $return_data = '';
        if ($data['status_periksa'] == 1) { // antrian poli
            $return_data .= Html::button(
                '<i class="fa fa-volume-up"></i>',
                [
                    'class' => 'btn btn-turquoise btn-xs antrian',
                    'data-popup' => "tooltip",
                    'data-placement' => 'bottom',
                    'data-original-title' => Yii::t('fe', 'Panggil antrian'),
                ]
            );
        }

        $return_data .= '&nbsp;&nbsp;';

        // tombol periksa
        if ($data['status_periksa'] == 339 || $data['status_periksa'] == 4) { // antrian pendaftaran & pulang
            $return_data .= Html::button(
                '<i class="fa fa-stethoscope"></i>',
                [
                    'class' => 'btn btn-turquoise btn-xs',
                    'disabled' => 'disabled',
                ]
            );
        } elseif ($data['status_periksa'] == 1) { // antrian poli
            $return_data .= Html::button(
                '<i class="fa fa-stethoscope"></i>',
                [
                    'class' => 'btn btn-turquoise btn-xs periksa',
                    'action' => Url::to([$this->_controller . '/confirm-periksa', 'pendaftaran_id' => $pendaftaran_id]),
                    'data-popup' => "tooltip",
                    'data-placement' => 'bottom',
                    'data-original-title' => Yii::t('fe', 'Periksa'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop'
                ]
            );
        } else { // diperiksa
            $return_data .= Html::a(
                '<i class="fa fa-stethoscope"></i>',
                Url::to([$this->_module . '/pemeriksaan/periksa', 'id' => $pendaftaran_id]),
                [
                    'class' => 'btn btn-turquoise btn-xs',
                    'data-popup' => "tooltip",
                    'data-placement' => 'bottom',
                    'data-original-title' => Yii::t('fe', 'Periksa'),
                ]
            );
        }

        $return_data .= '&nbsp;&nbsp;';

        // tombol rencana kontrol
        if ($data['konsulpoli_id'] != null || $data['status_periksa'] == 2) { // sudah buat janji poli
            $return_data .= Html::button(
                '<i class="fa fa-plus-square"></i>',
                [
                    'class' => 'btn btn-spring-green btn-xs kontrol',
                    'disabled' => 'disabled',
                    'data-popup' => "tooltip",
                    'data-placement' => 'bottom',
                    'data-original-title' => Yii::t('fe', 'Sudah buat janji'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop'
                ]
            );
        } else { // belum buat janji poli
            $return_data .= Html::button(
                '<i class="fa fa-plus-square"></i>',
                [
                    'class' => 'btn btn-spring-green btn-xs kontrol',
                    'action' => Url::to([$this->_controller . '/rencana-kontrol', 'pendaftaran_id' => $pendaftaran_id]),
                    'data-popup' => "tooltip",
                    'data-placement' => 'bottom',
                    'data-original-title' => Yii::t('fe', 'Buat rencana kontrol'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop'
                ]
            );
        }

        $return_data .= '&nbsp;&nbsp;';

        // tombol rincian tagihan
        $return_data .= Html::a(
            '<i class="fa fa-eye"></i>',
            '#',
            [
                'class' => 'btn btn-info btn-xs tagihan',
                'action' => Url::to([$this->_controller . '/tagihan', 'pendaftaran_id' => $pendaftaran_id]),
                'data-popup' => "tooltip",
                'data-placement' => 'bottom',
                'data-original-title' => Yii::t('fe', 'Tagihan'),
            ]
        );

        $return_data .= '&nbsp;&nbsp;';

        // tombol batal poli
        if ($data['status_periksa'] == 1) { // antrian poli
            $return_data .= Html::a(
                '<i class="fa fa-times-circle-o"></i>',
                '#',
                [
                    'class' => 'btn btn-danger btn-xs batal',
                    'action' => Url::to([$this->_controller . '/batal', 'pendaftaran_id' => $pendaftaran_id]),
                    'data-popup' => "tooltip",
                    'data-placement' => 'bottom',
                    'data-original-title' => Yii::t('fe', 'Batal'),
                ]
            );
        }

        return $return_data;
    }

    public function actionConfirmPeriksa($pendaftaran_id, $ruangan_id = null, $konsulpoli_id = null, $is_jenis = 'rj')
    {
        
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($pendaftaran_id);
        $title = Yii::t('fe', 'Mulai pemeriksaan');
        $sub_title = $this->_page;
        $modelPendaftaran = new PendaftaranForm;
        $modelKonsul = new KonsulpoliForm;
        $model = 'PendaftaranForm';
        $form_name = substr(strrchr(get_class($modelPendaftaran), "\\"), 1);
        $is_disabled = 'true';

        try {
            $bundleData = $this->guzzleExec($this->_restRajal, [
                'url' => 'inf-kunjungan-pasien/bundle-confirm-periksa',
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'getPasien' => [
                            'id' => $id,
                            'konsulpoli_id' => $konsulpoli_id
                        ],
                        'dataPegawai' => [
                            'ruangan_id' => is_null($ruangan_id) ? $this->_id_ruangan : $ruangan_id
                        ],
                    ]
                ]
            ]);
            $data_pegawai = isset($bundleData['dataPegawai']) ? $bundleData['dataPegawai'] : [];
            $response = isset($bundleData['getPasien']) ? $bundleData['getPasien'] : [];
            $data_pasien = $response["response"]["data"];
            $dokterKonsulId = null;

            // Cek data
            if ($modelPendaftaran->load($data_pasien, '')) {
                // Assign manually
                $modelPendaftaran->pegawai_id = $data_pasien['pegawai_id'];
                $statusPeriksa = $data_pasien['status_periksa'];
                $instalasiIdPeriksa = $data_pasien['instalasi_id'];

                if(!empty($konsulpoli_id)) {
                    $dokterKonsulId = ArrayHelper::getValue($data_pasien, 'dokter_konsul_id');
                    $modelPendaftaran->pegawai_id = $dokterKonsulId;
                    if($is_jenis != 'MCU') {
                        $disableDokter = true;
                    }
                }

                // Cek post
                if ($request->post()) {
                    $data = $request->post('PendaftaranForm');
                    $redirectUrl = 'rajal/pemeriksaan/periksa?id=' . $pendaftaran_id;
                    if($data_pasien['status_periksa']=='4' || $data_pasien['status_periksa']=='433'){
                        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
                        $redirect = $this->helper->crossUrl('jumpto', [
                            'ruangan_id' => $ruangan_id,
                            'instalasi_id' => DocoConstants::INSTALASI_ID_RJ,
                            'modul' => 'rajal',
                            'url' => $redirectUrl
                        ]);
                        return json_encode(['url' => $redirect]);
                    }
                    if (empty($konsulpoli_id)) {
                        $modelPendaftaran->pegawai_id = $data["pegawai_id"];
                        $modelPendaftaran->tgl_masukperiksa = ArrayHelper::getValue($data, 'tgl_masukperiksa');
                        $modelPendaftaran->pendaftaran_id = $id;
                        $model = 'KonsulpoliForm';
                        $rest = $this->guzzleExec($this->_restRajal, [
                            'url' => 'inf-kunjungan-pasien/ubah-dokter',
                            'payload' => [
                                'form_params' =>  $modelPendaftaran->attributes,
                            ]
                        ]);
                    } else {
                        $modelKonsul->pendaftaran_id = $id;
                        $modelKonsul->konsulpoli_id = PelayananHelpers::decryptId($konsulpoli_id);
                        if($is_jenis != 'MCU') {
                            $modelKonsul->pegawai_id = $dokterKonsulId;
                        }
                        else {
                            if(isset($data["pegawai_id"])) {
                                $modelKonsul->pegawai_id = $data['pegawai_id'];
                            }
                        }
                        $modelKonsul->tgl_masukperiksa = ArrayHelper::getValue($data, 'tgl_masukperiksa');
                        $rest = $this->guzzleExec($this->_restRajal, [
                            'url' => 'inf-kunjungan-pasien/ubah-dokter',
                            'payload' => [
                                'form_params' =>  $modelKonsul->attributes,
                                'konsulpoli_id' => $konsulpoli_id
                            ]
                        ]);
                       
                    }
                    if(isset($rest['title']) && $rest['title'] == 'Gagal Assign Dokter'){
                        return DocoHelpers::responseTemplate(
                            422,
                            Yii::t('fe', 'Kegagalan Pada Sistem'),
                            [],
                            [ 'title' => 'Gagal Assign Dokter','text' => $rest['text']]
                        );
                    }
                    if(isset($rest['title']) && $rest['title'] == 'Pasien Sudah Dipulangkan'){
                        return DocoHelpers::responseTemplate(
                            422,
                            Yii::t('fe', 'Kegagalan Pada Sistem'),
                            [],
                            [ 'title' => 'Pasien Sudah Dipulangkan','text' => $rest['text']]
                        );
                    }

                    if(!empty($konsulpoli_id)){
                        $redirectUrl .= '&ruanganId=' . DocoHelpers::encrypt($rest['data']['ruangan_id']) . '&konsulpoliId=' . PelayananHelpers::encryptId($konsulpoli_id). '&is_jenis=' . $is_jenis;
                    }
                    $redirect = $this->helper->crossUrl('jumpto', [
                        'ruangan_id' => !is_null($ruangan_id) ? $ruangan_id : $rest['data']['ruangan_id'],
                        'instalasi_id' => DocoConstants::INSTALASI_ID_RJ,
                        'modul' => 'rajal',
                        'url' => $redirectUrl
                    ]);
                    return json_encode(['url' => $redirect]);
                }else if($data_pasien['status_periksa']=='4' || $data_pasien['status_periksa']=='433'){
                    $redirectUrl = 'rajal/pemeriksaan/periksa?id=' . $pendaftaran_id .'&ruanganId=' . $ruangan_id . '&konsulpoliId=' . PelayananHelpers::encryptId($konsulpoli_id). '&is_jenis=' . $is_jenis;
                    $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
                    $redirect = $this->helper->crossUrl('jumpto', [
                        'ruangan_id' => $ruangan_id,
                        'instalasi_id' => DocoConstants::INSTALASI_ID_RJ,
                        'modul' => 'rajal',
                        'url' => $redirectUrl
                    ]);
                    return "<script>window.location.href='" . $redirect . "';</script>";
                }else{
                    // if($statusPeriksa != DocoConstants::STATUS_ANTRIAN_POLI) {
                    //     return $this->renderAjax('_gagalperiksa', get_defined_vars());
                    // }
                    return $this->renderAjax('_periksa', get_defined_vars());


                }
            } else {
                return DocoHelpers::responseTemplate(
                    500,
                    Yii::t('fe', 'Kegagalan Pada Sistem'),
                    [],
                    ['title' => Yii::t('fe', 'Gagal Mengubah Dokter'), 'text' => Yii::t('fe', 'Data tidak ditemukan.')]
                );
            }
        } catch (\Exception $e) {
            
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (RequestException $e) {

            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function getTagihanPasien($id)
    {
        $response = $this->_restRajal->get('inf-kunjungan-pasien/get-tagihan-pasien?id=' . $id);
        $body = json_decode($response->getBody(), TRUE);

        $tagihan = $body['response']['tagihan'];
        $status_bayar = $body['response']['status_bayar'];

        $result = [
            'tagihan' => $tagihan,
            'status_bayar' => $status_bayar,
        ];
        return $result;
    }
    
    public function actionSkriningPasien($pendaftaran_id, $is_riwayat) {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($pendaftaran_id);
        $title = Yii::t('fe', 'Skrining Pasien Rawat Jalan');
        
        try {
            $response = $this->_restRajal->get('skrining/get-data-pasien?pendaftaran_id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $datapasien = ArrayHelper::getValue($body['response'], 'datapasien');
            $namaPasien = ArrayHelper::getValue($datapasien, 'nama_pasien');
            $nomerRm = ArrayHelper::getValue($datapasien, 'no_rekam_medik');
            $tempatLahir = ArrayHelper::getValue($datapasien, 'tempat_lahir');
            $tanggalLahir = ArrayHelper::getValue($datapasien, 'tanggal_lahir');
            $jenisKelamin = ArrayHelper::getValue($datapasien, 'jenis_kelamin');
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }

        return $this->renderAjax('_skriningpasien', compact('id', 'title', 'namaPasien', 'nomerRm', 'tempatLahir', 'tanggalLahir', 'jenisKelamin', 'is_riwayat'));
    }

    public function actionFormSkriningRajal($is_riwayat)
    {
        $model = new SkriningRajalForm;
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $userlogin =  $session->get('user_identity');

        try {
            $response = $this->_restRajal->get('skrining/get-data-skrining?pendaftaran_id='.$pendaftaran_id);
            $body = json_decode($response->getBody(), TRUE);
    
            $pertanyaanSkrining = ArrayHelper::getValue($body['response'], 'pertanyaan_skrining', []);
            $kesadaran = ArrayHelper::map(ArrayHelper::getValue($pertanyaanSkrining, 'kesadaran', []), 'lookup_id', 'lookup_name');
            $batuk = ArrayHelper::map(ArrayHelper::getValue($pertanyaanSkrining, 'batuk', []), 'lookup_id', 'lookup_name');
            $pernapasan = ArrayHelper::map(ArrayHelper::getValue($pertanyaanSkrining, 'pernapasan', []), 'lookup_id', 'lookup_name');
            $nyeri_dada = ArrayHelper::map(ArrayHelper::getValue($pertanyaanSkrining, 'nyeri_dada', []), 'lookup_id', 'lookup_name');
            $resiko_jatuh = ArrayHelper::map(ArrayHelper::getValue($pertanyaanSkrining, 'resiko_jatuh', []), 'lookup_id', 'lookup_name');
            $keputusan = ArrayHelper::map(ArrayHelper::getValue($pertanyaanSkrining, 'keputusan', []), 'lookup_id', 'lookup_name');
            $bahasa = ArrayHelper::map(ArrayHelper::getValue($pertanyaanSkrining, 'bahasa', []), 'lookup_id', 'lookup_name');
            $skala_nyeri = DocoConstants::SKALA_NYERI;
            $datapasien = ArrayHelper::getValue($body['response'], 'laporankunjungan');
            $dataskrining = ArrayHelper::getValue($body['response'], 'data_skrining');
            $petugas_loket = $userlogin['nama_pegawai'];

            if(! empty($dataskrining)) {
                $model->kesadaran = ArrayHelper::getValue($dataskrining, 'kesadaran');
                $model->pernapasan = ArrayHelper::getValue($dataskrining, 'pernapasan');
                $model->batuk = ArrayHelper::getValue($dataskrining, 'batuk');
                $model->pernapasan = ArrayHelper::getValue($dataskrining, 'pernapasan');
                $model->nyeri_dada = ArrayHelper::getValue($dataskrining, 'nyeri_dada');
                $model->keputusan = ArrayHelper::getValue($dataskrining, 'keputusan');
                $model->resiko_jatuh = ArrayHelper::getValue($dataskrining, 'resiko_jatuh');
                $model->bahasa = ArrayHelper::getValue($dataskrining, 'bahasa');
                $bahasaDaerah = ArrayHelper::getValue($dataskrining, 'bahasa_daerah');
                $bahasaAsing = ArrayHelper::getValue($dataskrining, 'bahasa_asing');
                $model->bahasa_daerah = ! empty($bahasaDaerah) ? true : false;
                $model->bahasa_daerah_text = $bahasaDaerah;
                $model->bahasa_asing = ! empty($bahasaAsing) ? true : false;
                $model->bahasa_asing_text = $bahasaAsing;
                $model->nyeri = ArrayHelper::getValue($dataskrining, 'nyeri');
                if($is_riwayat == 'true'){
                    $petugas_loket = $model->petugas_loket_pendaftaran = ArrayHelper::getValue($dataskrining, 'petugas_loket_pendaftaran');
                }
            }
        
            return $this->renderAjax('formskrining/form_skriningrajal', compact('model', 'kesadaran', 'resiko_jatuh', 'pernapasan', 'nyeri_dada', 'batuk', 'keputusan', 'bahasa', 'pendaftaran_id', 'datapasien', 'skala_nyeri', 'userlogin', 'is_riwayat', 'petugas_loket'));
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionSaveSkriningRajal()
    {
        $model = new SkriningRajalForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $request = Yii::$app->request;
        try {
            $post = $request->post('SkriningRajalForm');
            $model->load($request->post());
            $model->bahasa = ArrayHelper::getValue($post, 'bahasa');
            $model->bahasa_asing = $post['bahasa_asing'] == 1 ? ArrayHelper::getValue($post, 'bahasa_asing_text') : null;
            $model->bahasa_daerah = $post['bahasa_daerah'] == 1 ? ArrayHelper::getValue($post, 'bahasa_daerah_text') : null;
            $model->nyeri = [
                'nyeri_1' => $post['nyeri_1'],
                'nyeri_2' => $post['nyeri_2'],
                'nyeri_3' => $post['nyeri_3']
            ];
            
            $model->nyeri = json_encode($model->nyeri);
            if ($model->validate()) {
                $response = $this->_restRajal->post('skrining/save-skrining-rajal',['form_params' => $model->attributes]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body, false, $formName);
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }

        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionCetakSkriningRajal()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $urlReport = 'skrining-pasien';
        if(Yii::$app->report->isAvailable($urlReport)){
            return Yii::$app->report->exec($urlReport, [
                   'queryParameter' => [
                        'pendaftaran_id' => (int) $pendaftaran_id,
                        'halaman' => "1"
                   ],
            ]);
        }
    }

    public function actionFormSkriningCovid($is_riwayat)
    {
        $model = new SkriningCovidForm;
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $response = $this->_restRajal->get('skrining/get-data-skrining-covid?pendaftaran_id='.$pendaftaran_id);
        $body = json_decode($response->getBody(), TRUE)['response'];
        $dataSkrining = isset($body['data']['skrining']) ? $body['data']['skrining'] : null;
        $pasienId = isset($body['data']['pasien_id']) ? DocoHelpers::encrypt($body['data']['pasien_id']) : null;
        $model->setAttributes($dataSkrining);
        $model->demam = isset($dataSkrining['demam']) ? ($dataSkrining['demam'] == true) ? 1:0 : null ;
        $model->batuk_pilek_nyeri_tenggorokan = isset($dataSkrining['batuk_pilek_nyeri_tenggorokan']) ? ($dataSkrining['batuk_pilek_nyeri_tenggorokan'] == true) ? 1:0 : null ;
        $model->sesak_napas = isset($dataSkrining['sesak_napas']) ? ($dataSkrining['sesak_napas'] == true) ? 1:0 : null ;
        $model->riwayat_luar_negeri = isset($dataSkrining['riwayat_luar_negeri']) ? ($dataSkrining['riwayat_luar_negeri'] == true) ? 1:0 : null ;
        $model->riwayat_dalam_negeri = isset($dataSkrining['riwayat_dalam_negeri']) ? ($dataSkrining['riwayat_dalam_negeri'] == true) ? 1:0 : null ;
        $model->resiko_kontak_pasien_covid = isset($dataSkrining['resiko_kontak_pasien_covid']) ? ($dataSkrining['resiko_kontak_pasien_covid'] == true) ? 1:0 : null ;
        $model->kontak_tatap_muka = isset($dataSkrining['kontak_tatap_muka']) ? ($dataSkrining['kontak_tatap_muka'] == true) ? 1:0 : null ;
        $model->kontak_perawatan_tanpa_apd = isset($dataSkrining['kontak_perawatan_tanpa_apd']) ? ($dataSkrining['kontak_perawatan_tanpa_apd'] == true) ? 1:0 : null ;
        $model->swab_positif = isset($dataSkrining['swab_positif']) ? ($dataSkrining['swab_positif'] == true) ? 1:0 : null ;
        $model->swab_negatif = isset($dataSkrining['swab_negatif']) ? ($dataSkrining['swab_negatif'] == true) ? 1:0 : null ;
        $model->suspek = isset($dataSkrining['suspek']) ? ($dataSkrining['suspek'] == true) ? 1:0 : null ;
        $model->terkonfirmasi = isset($dataSkrining['terkonfirmasi']) ? ($dataSkrining['terkonfirmasi'] == true) ? 1:0 : null ;
        $model->kontak_fisik = isset($dataSkrining['kontak_fisik']) ? ($dataSkrining['kontak_fisik'] == true) ? 1:0 : null ;
        $model->tgl_swab_positif = isset($dataSkrining['tgl_swab_positif']) ? date('d-M-Y',strtotime($dataSkrining['tgl_swab_positif'])) : null ;
        $model->tgl_swab_negatif = isset($dataSkrining['tgl_swab_negatif']) ? date('d-M-Y',strtotime($dataSkrining['tgl_swab_negatif'])) : null ;
        $pendaftaranId = isset($dataSkrining['pendaftaran_id']) ? DocoHelpers::encrypt($dataSkrining['pendaftaran_id']) : null; 

        return $this->renderAjax('formskrining/form_skriningcovid', get_defined_vars());
    }

    public function actionSaveSkriningCovid()
    {
        $model = new SkriningCovidForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $request = Yii::$app->request;
        try {
            $model->load($request->post());
            if ($model->swab_positif == 1) {
                $model->tgl_swab_positif = isset($model->tgl_swab_positif) ?  date('Y-m-d',strtotime($model->tgl_swab_positif)) : null;
            }else{
                $model->tgl_swab_positif = null;
            }
            
            if ($model->swab_negatif == 1) {
                $model->tgl_swab_negatif = isset($model->tgl_swab_negatif) ?  date('Y-m-d',strtotime($model->tgl_swab_negatif)) : null;
            }else{
                $model->tgl_swab_negatif = null;
            }
            $model->petugas_pemeriksa = Yii::$app->session->get('user_identity')['nama_pegawai'];
            $model->additional_data = json_encode($model->attributes);
            if ($model->validate()) {
                $response = $this->_restRajal->post('skrining/save-skrining-covid',['form_params' => $model->attributes]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body, false, $formName);
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }

        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionPrintSkriningCovid() {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        $pasien_id = $request->get('pasien_id', null);
        $urlReport = 'skrining-pasien';
        
        try {
            if(Yii::$app->report->isAvailable($urlReport)){
                return Yii::$app->report->exec($urlReport, [
                    'queryParameter' => [
                        'pendaftaran_id' => DocoHelpers::decrypt($pendaftaran_id),
                        'pasien_id' => DocoHelpers::decrypt($pasien_id),
                        'halaman' => "2"
                    ],
             ]);
            }
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionFormAssesmentPasien($id, $is_riwayat)
    {
        $model = new SkriningAssesmentPasienForm;
        $request = Yii::$app->request;
        $pendaftaran_id = $id;
        $session = Yii::$app->session;
        $userlogin =  $session->get('user_identity');

        try {
            $response = $this->_restRajal->get('skrining/get-data-skrining-assesment-pasien?pendaftaran_id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $skriningAssesment = ArrayHelper::getValue($body['response'], 'skrining_pasien', []);
            $pendaftaran = ArrayHelper::getValue($body['response'], 'pendaftaran', []);

            $alamat = isset($skriningAssesment) ? $skriningAssesment['alamat_pasien'] : $pendaftaran['alamat_pasien'];
            $kecamatan = isset($skriningAssesment) ? $skriningAssesment['kecamatan'] : $pendaftaran['kecamatan_nama'];
            $kelurahan = isset($skriningAssesment) ? $skriningAssesment['kelurahan_desa'] : $pendaftaran['kelurahan_nama'];
            $pekerjaan = isset($skriningAssesment) ? $skriningAssesment['pekerjaan'] : $pendaftaran['pekerjaan_nama'];
            $carabayar = isset($skriningAssesment) ? $skriningAssesment['pekerjaan'] : $pendaftaran['carabayar_nama'];
            $caramasuk = isset($skriningAssesment) ? $skriningAssesment['cara_masuk'] : $pendaftaran['caramasuk_nama'];
            $kabupaten = isset($skriningAssesment) ? $skriningAssesment['kabupaten'] : $pendaftaran['kabupaten_nama'];
            $nokartuasuransi = isset($skriningAssesment) ? $skriningAssesment['no_peserta_bpjs_kis'] : $pendaftaran['nokartuasuransi'];
            $tempat_lahir = isset($skriningAssesment) ? $skriningAssesment['tempat_lahir'] : $pendaftaran['tempat_lahir'];
            $tanggal_lahir = isset($skriningAssesment) ? $skriningAssesment['tgl_lahir'] : $pendaftaran['tanggal_lahir'];
            $model->jenis_kelamin = isset($skriningAssesment) ? $skriningAssesment['jenis_kelamin'] : $pendaftaran['lookup_kode'];
            $model->pasien_keluarga_pasien = isset($skriningAssesment) ? $skriningAssesment['pasien_keluarga_pasien'] : $pendaftaran['nama_pasien'];
            $model->petugas_loket_pendaftaran = isset($skriningAssesment) ? $skriningAssesment['petugas_loket_pendaftaran'] : $userlogin['nama_pegawai'];
            $nama_ayah = isset($skriningAssesment) ? $skriningAssesment['nama_ayah_atau_ibu'] : $pendaftaran['nama_ayah'];
            $no_tlp = isset($skriningAssesment) ? $skriningAssesment['no_hp'] : $pendaftaran['no_mobile_pasien'];

            if(!empty($skriningAssesment)){
                $model->status_kepesertaan = ArrayHelper::getValue($skriningAssesment, 'status_kepesertaan');
                $model->status_pernikahan = ArrayHelper::getValue($skriningAssesment, 'status_pernikahan');
                $model->agama = ArrayHelper::getValue($skriningAssesment, 'agama');
                $model->pendidikan = ArrayHelper::getValue($skriningAssesment, 'pendidikan');
                $model->nama_suami_atau_istri = ArrayHelper::getValue($skriningAssesment, 'nama_suami_atau_istri');
                $model->pekerjaan_suami_atau_istri = ArrayHelper::getValue($skriningAssesment, 'pekerjaan_suami_atau_istri');
                $model->pekerjaan_ayah_atau_ibu = ArrayHelper::getValue($skriningAssesment, 'pekerjaan_ayah_atau_ibu');
                $model->cara_berkunjung = ArrayHelper::getValue($skriningAssesment, 'cara_berkunjung');
                $model->tgl_kunjungan = ArrayHelper::getValue($skriningAssesment, 'tgl_kunjungan');
            }

            return $this->renderAjax('formskrining/form_assesmentpasien', get_defined_vars());
            
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionSaveAssesmentPasien()
    {
        $model = new SkriningAssesmentPasienForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $request = Yii::$app->request;
        try {
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->_restRajal->post('skrining/save-assesment-pasien',['form_params' => $model->attributes]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body, false, $formName);
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }

        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionPrintSkriningAssesment($pendaftaran_id) {
        $request = Yii::$app->request;
        $pendaftaran_id = $pendaftaran_id;
        $urlReport = 'skrining-pasien';
        
        try {
            if(Yii::$app->report->isAvailable($urlReport)){
                return Yii::$app->report->exec($urlReport, [
                    'queryParameter' => [
                        'pendaftaran_id' => $pendaftaran_id,
                        'halaman' => "3"
                    ],
             ]);
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}
