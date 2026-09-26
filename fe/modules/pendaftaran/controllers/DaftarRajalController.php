<?php

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\UploadedFile;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

use app\modules\pendaftaran\components\Lookup;

use app\modules\pendaftaran\models\BpjsNewForm;
use app\modules\pendaftaran\models\AsuransiForm;
use app\modules\pendaftaran\models\KunjunganForm;
use app\modules\pendaftaran\models\PasienForm;
use app\modules\pendaftaran\models\PendaftaranForm;
use app\modules\pendaftaran\models\RujukanForm;
use app\modules\pendaftaran\models\TipePasienForm;
use app\modules\pendaftaran\models\MultiCarabayarForm;
use app\modules\pendaftaran\models\PjpasienForm;

use GuzzleHttp\Exception\RequestException;

use app\modules\pendaftaran\components\traits\PendaftaranTrait;

class DaftarRajalController extends DocoController
{
    use PendaftaranTrait;

    protected $_title = 'Pendaftaran Rawat Jalan';
    protected $_module = '/pendaftaran/daftar-rajal';
    protected $_moduleRedirect = '/pendaftaran/daftar-rajal';
    protected $allowAction = ['*'];
    protected $_restPendaftaran;
    protected $_instalasi_id_rj = DocoConstants::INSTALASI_ID_RJ;

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    public function actionIndex($id_booking = null, $pendaftaranol_id = null, $loket_id = null, $params = 'rajal', $janji_id = null)
    {
        if ($janji_id) {
            $janji_id = DocoHelpers::decrypt($janji_id);
        }
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $loket_nama = '';
        $pasien_id = $carabayar_id = $penjamin_id = $ruangan_id = '';
        $penOl = $ruangan_bpjs = [];
        /** Menampilkan Form Pendaftaran */
        $instalasi_id = $this->_instalasi_id_rj;
        if ($params != 'rajal') {
            $instalasi_id = DocoConstants::INSTALASI_MCU;
            $params = 'mcu';
            $this->_title = 'Pendaftaran MCU';
            $this->_module = '/pendaftaran/daftar-mcu';
        }
        $module = $this->_module;
        $title = $this->_title;

        $modelPasien = $this->SetPasienFormModel();
        if($params != 'rajal'){
            $modelPasien->scenario = "default";
        } else{
            $modelPasien->scenario = "pendaftaran-rajal";
        }
        $modelKunjungan = new KunjunganForm;
        $tipePasien = new TipePasienForm;
        $multiPayer = new MultiCarabayarForm;
        $modelRujukan = new RujukanForm;
        $modelPj = new PjpasienForm;
        $modelAsuransi = new AsuransiForm;
        $modelBpjs = new BpjsNewForm;
        $modelBpjs->scenario = 'rajalskdp';
        $modelKunjungan->scenario = 'with_mandatory_pjawab_nourut';
        $modelKunjungan->konfig_referral_required = $modelKunjungan->konfig_referral_required = ArrayHelper::getValue((new Lookup)->getValueFromLookupT(NULL, 'required_referral'), 'additional_value', FALSE) == TRUE 
                                                    && strtoupper(ArrayHelper::getValue((new Lookup)->getValueFromLookupT(NULL, 'required_referral'), 'additional_value', FALSE)) == 'TRUE';

        try {
            if (Yii::$app->request->post()) {
                return $this->actionSimpanKunjungan($params);
            }

            $ruanganId = $active_workspace['ruangan_id'];
            $instalasi_workspace = $active_workspace['instalasi_id'];
            $dataForm = $this->getDataApi($instalasi_id, 2, $pendaftaranol_id, null, $janji_id);

            $modelPasien->propinsi_id = isset($dataForm['defaultPropinsi']) ? $dataForm['defaultPropinsi'] : null;
            $modelPasien->kabupaten_id = isset($dataForm['defaultKota']) ? $dataForm['defaultKota'] : null;
            if($pendaftaranol_id) {
                $dataOl = isset($dataForm['pendaftaranol']) ? $dataForm['pendaftaranol'] : [];
                $penOl = [
                    'pendaftaranol_id' => isset($dataOl['pendaftaranol_id']) ? $dataOl['pendaftaranol_id'] : null,
                    'status_pasien' => isset($dataOl['status_pasien']) ? $dataOl['status_pasien'] : null,
                    'pasien_id' => isset($dataOl['pasien_id']) ? $dataOl['pasien_id'] : null,
                    'dokter_id' => isset($dataOl['pegawai_id']) ? $dataOl['pegawai_id'] : null,
                    'carabayar_id' => isset($dataOl['carabayar_id']) ? $dataOl['carabayar_id'] : null,
                    'penjamin_id' => isset($dataOl['penjamin_id']) ? $dataOl['penjamin_id'] : null,
                    'ruangan_id' => isset($dataOl['ruangan_id']) ? $dataOl['ruangan_id'] : null,
                    'no_rekam_medik' => isset($dataOl['no_rekam_medik']) ? $dataOl['no_rekam_medik'] : null,
                    'nama_pasien' => isset($dataOl['nama_pasien']) ? $dataOl['nama_pasien'] : null,
                    'no_asuransi' => isset($dataOl['no_asuransi']) ? $dataOl['no_asuransi'] : null,
                    'tanggal_lahir' => isset($dataOl['tanggal_lahir']) ? date('d-m-Y',strtotime($dataOl['tanggal_lahir'])) : null,
                    'tgl_pendaftaranol' => isset($dataOl['tgl_pendaftaranol']) ? date('d-m-Y H:i',strtotime($dataOl['tgl_pendaftaranol'])) : null,
                    'all' => $dataOl 
                ];
            }
            if($janji_id){
                $dataJanji = isset($dataForm['janjiPoli']) ? $dataForm['janjiPoli'] : [];
                /* permintan konsul override ke var penOl agar form pendaftaran tidak berubah lgi */
                $penOl = [
                    'buatjanjipoli_id' => isset($dataJanji['buatjanjipoli_id']) ? $dataJanji['buatjanjipoli_id'] : null,
                    'pasien_id' => isset($dataJanji['pasien_id']) ? $dataJanji['pasien_id'] : null,
                    'dokter_id' => isset($dataOl['pegawai_id']) ? $dataOl['pegawai_id'] : null,
                    'carabayar_id' => isset($dataJanji['carabayar_id']) ? $dataJanji['carabayar_id'] : null,
                    'penjamin_id' => isset($dataJanji['penjamin_id']) ? $dataJanji['penjamin_id'] : null,
                    'ruangan_id' => isset($dataJanji['ruangan_id']) ? $dataJanji['ruangan_id'] : null,
                    'no_rekam_medik' => isset($dataJanji['no_rekam_medik']) ? $dataJanji['no_rekam_medik'] : null,
                    'nama_pasien' => isset($dataJanji['nama_pasien']) ? $dataJanji['nama_pasien'] : null,
                    'tanggal_lahir' => isset($dataJanji['tanggal_lahir']) ? date('d-m-Y',strtotime($dataJanji['tanggal_lahir'])) : null,
                    'tgl_pendaftaran' => isset($dataJanji['tgl_pendaftaran']) ? date('d-m-Y H:i',strtotime($dataJanji['tgl_pendaftaran'])) : null,
                    'tgl_buatjanji' => isset($dataJanji['tgl_buatjanji']) ? date('d-m-Y H:i',strtotime($dataJanji['tgl_buatjanji'])) : null,
                    'status_janji' =>isset($dataJanji['status_janji']) ? $dataJanji['status_janji'] : null
                ];
            }

            if ($dataForm['konfigSystem']['is_nourut'] && !$pendaftaranol_id && !$janji_id) {
                $modelKunjungan->scenario = 'with_mandatory_pjawab_nourut';
            }
            
            $rujukan_dari = isset($dataForm['rujukan_dari']) ? $dataForm['rujukan_dari'] : null;
            $render_pasien_data = [
                "tipePasien" => $tipePasien,
                "modelKunjungan" => $modelKunjungan,
                "modelRujukan" => $modelRujukan,
                'modelPasien' => $modelPasien,
                'modelBpjs' => $modelBpjs,
                'data_lookup' => isset($dataForm["lookup"]) ? $dataForm["lookup"] : [],
                'data_master' => $dataForm["master"],
                'optionsProv' => $dataForm['optionsProv'],
                "instalasi_id" => $instalasi_id,
                "instalasi" => $dataForm['instalasi'],
                "ruangan" => $dataForm['ruangan'],
                "asal_rujukan" => $dataForm['asal_rujukan'],
                "carabayar" => $dataForm['cara_bayar'],
                "penjamin_id" => $penjamin_id,
                'carabayarOptions' => $dataForm['carabayarOptions'],
                'penOl' => $penOl,
                'default_asal_rujukan' => $dataForm['default_asal_rujukan'],
                'instalasi_workspace' => $instalasi_workspace,
                'is_hide_alias' => isset($dataForm['konfigSystem']['is_hide_alias']) ? $dataForm['konfigSystem']['is_hide_alias'] : null,
                'support_multipayer' => ArrayHelper::getValue($dataForm,'konfigSystem.support_multipayer',false),
                'multiPayer' => $multiPayer,
                'is_validasipendaftaran' => isset($dataForm['konfigSystem']['is_validasipendaftaranrj']) ? $dataForm['konfigSystem']['is_validasipendaftaranrj'] : false,
            ];

            if(!empty($dataForm['ruangan_bpjs'])) {
                $session = Yii::$app->session;

                if( empty($session->get('ruangan-bpjs')) ) {
                    $session->set('ruangan-bpjs', $dataForm['ruangan_bpjs']);
                }
            }

            $render_kunjungan_data = [
                "modelKunjungan" => $modelKunjungan,
                "modelPj" => $modelPj,
                'data_lookup' => $dataForm["lookup"],
                'data_master' => $dataForm["master"],
                'kelaspelayanan' => $dataForm["kelas_pelayanan"],
                "instalasi_id" => $instalasi_id,
                "instalasi" => $dataForm['instalasi'],
                "ruangan" => $dataForm['ruangan'],
                "asal_rujukan" => $dataForm['asal_rujukan'],
                "carabayar" => $dataForm['cara_bayar'],
                "penjamin_id" => $penjamin_id,
                'carabayarOptions' => $dataForm['carabayarOptions'],
                'modelAsuransi' => $modelAsuransi,
                'modelBpjs' => $modelBpjs,
                'penOl' => $penOl,
                'ruanganId' => $ruanganId,
                'default_jenis_penyakit' => $dataForm['default_jenis_penyakit'],
                'instalasi_workspace' => $instalasi_workspace,
                'is_nourut' => $dataForm['konfigSystem']['is_nourut'],
                'is_limit_tagihan' => $dataForm['konfigSystem']['is_limit_tagihan'],
                'support_multipayer' => ArrayHelper::getValue($dataForm,'konfigSystem.support_multipayer',false),
                'multiPayer' => $multiPayer,
                'optionsRuangan' => $dataForm['optionsRuangan'],
                'show_referal' => $dataForm['konfigSystem']['show_referal_pendaftaran'],
            ];

            $penjaminIntegrasi = $dataForm['penjaminIntegrasi'];
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],500);
        } catch (Exception $e) {
            $render_pasien_data =
            $render_kunjungan_data = [];
        }
        $jenisantrian_id = DocoConstants::JA_PDN;
        return $this->render('index', get_defined_vars());
    }


    public function actionGetAntrian($antrian_id)
    {
        if (!$antrian_id) return DocoHelpers::response([], 200);
        try {
            $request = $this->_restPendaftaran->get('allow-antrian/get-antrian?antrian_id=' . $antrian_id);
            $body = json_decode($request->getBody(),TRUE);
            $response = $body['response'];
            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUbahPasien($id, $param)
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $model = new PasienForm;
        if ($request->post()) {
            $model->load($post['PasienForm']);
            try {
                $image = UploadedFile::getInstance($model, "photopasien");
                if(isset($image->name)){
                    $path = \Yii::getAlias('@webroot').'/media/img/pasien/';
                    $oldFile = $path . $post['PasienForm']['photopasien'];
                    if ($post['PasienForm']['photopasien'] && file_exists($oldFile)) {
                        unlink($oldFile); // hapus file lama
                    }
                    $ext = end(explode(".", $image->name));
                    $rand = substr(Yii::$app->security->generateRandomString(), 0, 7);
                    $post['PasienForm']['photopasien'] = $rand . ".{$ext}";
                    if(!$image->saveAs($path . $post['PasienForm']['photopasien'])){
                        return DocoHelpers::responseTemplate(500,'Terjadi Kesalahan simpan gambar');
                    }
                }

                $requests = $this->_restPendaftaran->post('pasien/update?id='.$id,
                    ['form_params' => $post]
                );
                $response = json_decode($requests->getBody(), true);
                return DocoHelpers::response($response);
            } catch (RequestException $e) {
                echo DocoHelpers::dataTabelsException($e->getMessage());
            } catch (\Exception $e) {
                echo DocoHelpers::dataTabelsException($e->getMessage());
            }
        }
        return $this->renderAjax('partial/_pasien', get_defined_vars());
    }

    /**
     * @todo Fungsi untuk mendapatkan data kunjungan pasien
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataKunjunganPasien()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $pasien_id = $request->get('pasien_id', null);
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            if ($pasien_id) {
                $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-rajal/get-data-kunjungan-pasien?pasien_id='.$pasien_id.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);
                $body = json_decode($restPendaftaran->getBody(), true);
                $data = $body['response']['data'];

                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $no++;
                        $value['tgl_pendaftaran'] = $value['tgl_pendaftaran'] != '' ? DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_pendaftaran'])), false, false) : '';
                        unset($value['pasien_id']);
                        $data[$key] = $value;
                    }

                    $result['data'] = $data;
                    $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                    $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                }
                else {
                    $result['data'] = $data;
                    $result['recordsTotal'] = 0;
                    $result['recordsFiltered'] = 0;
                }
            }

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionTambahPasien()
    {
        try {
            $post = Yii::$app->request->post();
            $model = new PasienForm();
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $model->load($post);
            if ($model->validate()) {
                $image = UploadedFile::getInstance($model, "photopasien");
                if (isset($image->name)) {
                    $ext = end(explode(".", $image->name));
                    $rand = substr(Yii::$app->security->generateRandomString(), 0, 7);
                    $model->photopasien = $model->no_rekam_medik . '-' . $rand . ".{$ext}";
                    $path = \Yii::getAlias('@webroot') . '/media/img/pasien/';
                    if (!$image->saveAs($path . $model->photopasien)) {
                        return DocoHelpers::responseTemplate(500, 'Terjadi Kesalahan simpan gambar');
                    }
                }
                $requests = $this->_restPendaftaran->post(
                    'pasien/create',
                    ['form_params' => $model->attributes]
                );
                $response = json_decode($requests->getBody(), true);
                return DocoHelpers::response($response);
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, 'PasienForm');

            }


        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (Exception $e) {
            return DocoHelpers::responseJsonString($e->getMessage(),$formName);
        }
    }

    public function actionGetPasienAutofill($nopesertabpjs)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response_field = [
            'nik' => '',
            'namapasien' => '',
            'tanggallahir' => '',
            'jeniskelamin' => '',
            'alamatpasien' => '',
            'provinsi' => '',
            'kabupaten' => '',
            'kecamatan' => '',
            'kelurahan' => ''
        ];
        try{
            $getDataRequest = $this->_restPendaftaran->get('allow/get-pasien-autofill',[
                'query' => [
                    'nopesertabpjs' => $nopesertabpjs
                ]
            ]);
            $parsingRequest = json_decode($getDataRequest->getBody(),TRUE);
            $response = $parsingRequest['response'];
            return $response;
        }catch (RequestException $e) {
            return $response_field;
        } catch (\Exception $e) {
            return $response_field;
        }
    }

    public function actionCekEligiblePeserta()
    {
        try {
            $request = Yii::$app->request;
            $getDataRequest = $this->_restPendaftaran->get('allow/cek-eligible-peserta',[
                'query' => [
                    'no_kartu' => $request->get('no_asuransi'),
                    'penjamin_id' => $request->get('penjamin_id')
                ]
            ]);
            $parsingRequest = json_decode($getDataRequest->getBody(),TRUE);
            $response = $parsingRequest['response'];

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}