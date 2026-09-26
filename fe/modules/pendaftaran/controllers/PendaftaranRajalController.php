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

use app\modules\pendaftaran\models\BpjsNewForm;
use app\modules\pendaftaran\models\AsuransiForm;
use app\modules\pendaftaran\models\KunjunganForm;
use app\modules\pendaftaran\models\PasienForm;
use app\modules\pendaftaran\models\PendaftaranForm;
use app\modules\pendaftaran\models\RujukanForm;
use app\modules\pendaftaran\models\TipePasienForm;
use app\modules\pendaftaran\models\PjpasienForm;
use app\modules\pendaftaran\models\KeluargaPasienForm;
use app\modules\pendaftaran\models\PasienBpjsForm;
use app\modules\pendaftaran\models\PenanggungBiayaForm;
use app\modules\pendaftaran\models\EditPendaftaranForm;
use app\modules\api\models\BpjsForm;
use app\modules\pendaftaran\models\PasienAdmisiForm;
use app\modules\pendaftaran\models\PasienBatalPeriksaForm;

use GuzzleHttp\Exception\RequestException;

use app\modules\pendaftaran\components\traits\PendaftaranTrait;

class PendaftaranRajalController extends DocoController
{
    use PendaftaranTrait;

    protected $_title = 'Pendaftaran Rawat Jalan';
    protected $_module = '/pendaftaran/pendaftaran-rajal';
    protected $_moduleRedirect = '/pendaftaran/pendaftaran-rajal';
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
        $ruanganId = DocoConstants::WS_RAJAL;
        $instalasi_workspace = DocoConstants::PENJADWALAN_DAN_PENDAFTARAN;
        $loket_nama = '';
        $pasien_id = $carabayar_id = $penjamin_id = $ruangan_id = '';
        $penOl = [];
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

        $modelPasien = new PasienForm();
        if($params != 'rajal'){
            $modelPasien->scenario = "default";
        } else{
            $modelPasien->scenario = "pendaftaran-rajal";
        }
        $modelKunjungan = new KunjunganForm;
        $tipePasien = new TipePasienForm;
        $modelRujukan = new RujukanForm;
        $modelPj = new PjpasienForm;
        $modelAsuransi = new AsuransiForm;
        $modelBpjs = new BpjsNewForm;
        $modelPenanggungBiaya = new PenanggungBiayaForm;
        $modelKp = new KeluargaPasienForm;
        $modelBpjs->scenario = 'rajalskdp';
        $modelKunjungan->scenario = 'with_mandatory_pjawab_nourut';

        try {
            if (Yii::$app->request->post()) {
                return $this->actionSimpanKunjunganV2($params);
            }
            $dataForm = $this->getDataApi($instalasi_id, 2, $pendaftaranol_id, null, $janji_id);
            $modelPasien->propinsi_id = $dataForm['defaultPropinsi'];
            $modelPasien->kabupaten_id = $dataForm['defaultKota'];
            /*$modelKunjungan->propinsi_id = 12;
            $modelKunjungan->kabupaten_id = 179;*/
            $modelPj->pj_propinsi_id = $dataForm['defaultPropinsi'];
            $modelPj->pj_kabupaten_id = $dataForm['defaultKota'];
            $modelKp->keluarga_propinsi_id = $dataForm['defaultPropinsi'];
            $modelKp->keluarga_kabupaten_id = $dataForm['defaultKota'];
            $additionalDataSty =  $this->_restPendaftaran->get('allow/pack-data-sty?', [
                'query'=>[
                    'propinsi'=> $modelPj->pj_propinsi_id,
                    'kabupaten'=> $modelPj->pj_kabupaten_id,
                ]
            ]);
            $addData = json_decode($additionalDataSty->getBody(), true);
            $listBagian = (count($addData['response']['bagian']) > 0) ? ArrayHelper::map($addData['response']['bagian'],'additional_data','ruangan_nama') : [];
            $prosedurMasuk = (count($addData['response']['prosedurMasuk']) > 0) ? ArrayHelper::map($addData['response']['prosedurMasuk'],'additional_data','ruangan_nama') : [];
            $ddlkabupaten = (count($addData['response']['kabupaten']) > 0) ? ArrayHelper::map($addData['response']['kabupaten'],'kabupaten_id','kabupaten_nama') : [];
            $ddlkecamatan = (count($addData['response']['kecamatan']) > 0) ? ArrayHelper::map($addData['response']['kecamatan'],'kecamatan_id','kecamatan_nama') : [];
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
                "modelPj" => $modelPj,
                'modelAsuransi' => $modelAsuransi,
                'modelKp' => $modelKp,
                'modelPenanggungBiaya' => $modelPenanggungBiaya,
                'data_lookup' => isset($dataForm["lookup"]) ? $dataForm["lookup"] : [],
                'data_master' => $dataForm["master"],
                'optionsProv' => $dataForm['optionsProv'],
                "instalasi_id" => $instalasi_id,
                "instalasi" => $dataForm['instalasi'],
                "ruangan" => $dataForm['ruangan'],
                "asal_rujukan" => $dataForm['asal_rujukan'],
                "carabayar" => $dataForm['cara_bayar'],
                "penjamin_id" => $penjamin_id,
                'kelaspelayanan' => $dataForm["kelas_pelayanan"],
                'carabayarOptions' => $dataForm['carabayarOptions'],
                'penOl' => $penOl,
                'default_asal_rujukan' => $dataForm['default_asal_rujukan'],
                'instalasi_workspace' => $instalasi_workspace,
                'ddlkabupaten' => $ddlkabupaten,
                'ddlkecamatan' => $ddlkecamatan,
                'bagianOptions' => $listBagian,
                'ruanganId' => $ruanganId
            ];

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
                'optionsProv' => $dataForm['optionsProv'],
                'ddlkabupaten' => $ddlkabupaten,
                'ddlkecamatan' => $ddlkecamatan,
                'ruanganId' => $ruanganId,
                'default_jenis_penyakit' => $dataForm['default_jenis_penyakit'],
                'instalasi_workspace' => $instalasi_workspace,
                'is_nourut' => $dataForm['konfigSystem']['is_nourut'],
                'is_limit_tagihan' => $dataForm['konfigSystem']['is_limit_tagihan']
            ];

        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],500);
        } catch (\Exception $e) {
            $render_pasien_data =
            $render_kunjungan_data = [];
        }
        $jenisantrian_id = DocoConstants::JA_PDN;
        $countSyncData = $this->actionCountSyncData();
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

    public function actionCekRetensi()
    {
        $request = Yii::$app->request;
        $no_rekam_medik = $request->get('no_rekam_medik');

        try {
            $response = $this->_restPendaftaran->get('pasien/cek-retensi',[
                'query' => [
                    'no_rekam_medik' => $no_rekam_medik,
                ]
            ]);
            $response = json_decode($response->getBody(), true);
            $results = isset($response['response']['result'][0]) ? $response['response']['result'][0] : [];

            return DocoHelpers::response(['results' => $results]);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        }
    }

    /*public function actionSimpanKunjunganRajal($params)
    {
        $request = Yii::$app->request;
        if($request->post('instalasi_id')) {
            if($request->post('instalasi_id') == DocoConstants::INSTALASI_MCU) {
                $params = DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU];
            }
        }

        $payLoadRequest = [
            'tipe_pasien' => [],
            'kunjungan' => [],
            'pasien' => [],
            'rujukan' => [],
            'penanggung_jawab' => [],
            'pj_pasien' => [],
            'asuransi' => [],
            'bpjs' => [],
            'penanggungbiaya' => []
        ];

        switch ($params) {
            case DocoConstants::PARAM_DFTR[DocoConstants::WS_RAJAL]:
                $instalasi_id = DocoConstants::INSTALASI_ID_RJ;
                $default_scenario = "pendaftaran-rajal";
                break;
            case DocoConstants::PARAM_DFTR[DocoConstants::WS_IGD]:
                $instalasi_id = DocoConstants::INSTALASI_ID_RD;
                $default_scenario = "pendaftaran-igd";
                break;
            case DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU]:
                $instalasi_id = DocoConstants::INSTALASI_MCU;
                $default_scenario = "default";
                break;
            default:
                $instalasi_id = DocoConstants::INSTALASI_ID_RI;
                $default_scenario = "default";
                break;
        }

        $modelPasien = new PasienForm;
        $modelRujukan = new RujukanForm;
        $modelKunjungan = new KunjunganForm;
        $modelTipePasien = new TipePasienForm;
        $modelPjPasien= new PjpasienForm;
        $modelAsuransi = new AsuransiForm;
        $modelBpjs = new BpjsNewForm;
        $modelPenanggungBiaya = new PenanggungBiayaForm;
        
        if (!empty($request->post('TipePasienForm'))) {
            $modelKunjungan->scenario = 'kunjungan_penunjang';
        } else {
            $modelKunjungan->scenario = 'with_mandatory_pjawab';
        }

        if (!empty($request->post('PasienForm'))) {
            $modelPasien->attributes = $request->post('PasienForm');

            // Improvement multiple jenis identitas
            if (!empty($modelPasien->no_identitas_pasien)) {
                $data = array();
                $jenisIdentitas = $modelPasien->jenisidentitas ? $modelPasien->jenisidentitas : [];
                $noIdentitas = $modelPasien->no_identitas_pasien;

                foreach ($noIdentitas as $key => $value) {
                    if ($value) {
                        $data[] = [
                            'jenisidentitas' => array_key_exists($key, $jenisIdentitas) ? $jenisIdentitas[$key] : null,
                            'no_identitas_pasien' => $value
                        ];
                    }
                }

                $modelPasien->jenisidentitas = null;
                $modelPasien->no_identitas_pasien = null;
                $modelPasien->additional_identitas = !empty($data) ? json_encode($data) : null;
            }

            $modelPasien->scenario = $default_scenario;
            $payLoadRequest['pasien'] = $modelPasien->attributes;
        }

        if (!empty($request->post('TipePasienForm'))) {
            $modelTipePasien->attributes = $request->post('TipePasienForm');
            $modelTipePasien->antrian_id = $request->post('antrian_id');
            $modelTipePasien->pendaftaranol_id = $request->post('pendaftaranol_id');
            /** Kondisi ketika Bpjs Error 
            if (!empty($modelPasien->no_rekam_medik)){
                $modelTipePasien->no_rekam_medik = $modelPasien->no_rekam_medik;
                $modelTipePasien->asalrujukan_id = 2; // WIP
            };

            /** Kondisi asal rujukan di MCU 
            if($params == DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU]) {
                $modelTipePasien->asalrujukan_id = $request->post('asalrujukan_id_hidden');
            }
            $payLoadRequest['tipe_pasien'] = $modelTipePasien->attributes;
        }

        if (!empty($request->post('PjpasienForm'))) {
            $modelPjPasien->attributes = $request->post('PjpasienForm');
            $payLoadRequest['pj_pasien'] = $modelPjPasien->attributes;
        }

        if (!empty($request->post('BpjsNewForm'))
                && $request->post('is_bpjs')
                && empty($modelPasien->no_rekam_medik) && !$request->post('allow_notif_bpjs')) {
                $modelBpjs->attributes = $request->post('BpjsNewForm');
                $payLoadRequest['bpjs'] = $modelBpjs->attributes;

        }

        if (!empty($request->post('RujukanForm'))) {
            $modelRujukan->attributes = $request->post('RujukanForm');
            $payLoadRequest['rujukan'] = $modelRujukan->attributes;
        }

        if (!empty($request->post('KunjunganForm'))) {
            $kunjungan = $request->post('KunjunganForm');
            $modelKunjungan->attributes = $kunjungan;
            $modelKunjungan->tindakan_karcis = $request->post('list_tindakan');
            if($params == 'mcu') {
                $list_paket = json_decode($request->post('list_penunjang','{}'), true);
                if(empty($list_paket)) {
                    return DocoHelpers::response([
                        'response' => [
                            'title' => 'Proses Gagal!',
                            'text' => 'Paket MCU tidak boleh kosong.'
                        ]
                    ],422);
                }
                else {
                    $list_paket = $list_paket['paket'];
                    $tipepaket_id = $arrPaket = [];
                    if(!empty($list_paket)) {
                        foreach ($list_paket as $key => $value) {
                            $tipepaket_id[] = $value['id'];
                        }
                    }

                    $arrPaket = "[" . implode(",", $tipepaket_id) . "]";
                    $modelKunjungan->list_paket = $arrPaket;
                }

                if($modelTipePasien->is_kolektif == true){
                    $listPasienMcu = $request->post('listPasienMcu');
                    if(empty($listPasienMcu)) {
                        return DocoHelpers::response([
                            'response' => [
                                'title' => 'Proses Gagal!',
                                'text' => 'List Pasien MCU tidak boleh kosong.'
                            ]
                        ],422);
                    }
                    $modelKunjungan->list_pasien_mcu = $listPasienMcu;
                }
            }
            else {
                $modelKunjungan->list_penunjang = $request->post('list_penunjang','{}');
            }

            $payLoadRequest['kunjungan'] = $modelKunjungan->attributes;
            if ($params == 'penunjang') {
                $listPenunjang = json_decode($modelKunjungan->list_penunjang,true);
                if (empty($listPenunjang ) && $modelKunjungan->instalasi_id != DocoConstants::INSTALASI_ID_BEDAH) {
                    return DocoHelpers::response([
                        'response' => [
                            'title' => 'Proses Gagal!',
                            'text' => 'Tindakan tidak boleh kosong.'
                        ]
                    ], 422);
                }
            }
        }

        if (!empty($request->post('no_asuransi'))) {
            $asuransi = $request->post('AsuransiForm');
            $modelAsuransi->attributes = $asuransi;
            $modelAsuransi->nokartuasuransi = $request->post('no_asuransi');
            $payLoadRequest['asuransi'] = $modelAsuransi->attributes;
        }

        if (!empty($request->post('PenanggungBiayaForm'))) {
            $penanggungbiaya = $request->post('PenanggungBiayaForm');
            if(!empty($penanggungbiaya['penanggungbiaya_nama'])){
                $modelPenanggungBiaya->carabayar_id = $modelTipePasien->carabayar_id;
                $modelPenanggungBiaya->attributes = $penanggungbiaya;
                $payLoadRequest['penanggungbiaya'] = $modelPenanggungBiaya->attributes;
            }
        }

        if(!empty($request->post('buatjanjipoli_id'))){
            $payLoadRequest['buatjanjipoli_id'] = $request->post('buatjanjipoli_id');
            $payLoadRequest['status_janji'] = !empty($request->post('status_janji')) ? $request->post('status_janji') : '';
        }

        if ($request->post('is_bbl')) {
            $payLoadRequest['is_bbl'] = $request->post('is_bbl');
            if (!$request->post('kelahiran_id')) {
                return (new DocoHelpers)->macroResponseJson(400, 'Mohon pilih data bayi', []);
            }
            $payLoadRequest['kelahiran_id'] = $request->post('kelahiran_id');
            $payLoadRequest['pendaftaran_ibu_id'] = $request->post('pendaftaran_ibu_id');
        }
        if ($request->post('is_ranap')) {
            $payLoadRequest['tipe_pasien']['no_rekam_medik'] = $request->post('TipePasienForm')['no_rekam_medik'];
            $payLoadRequest['is_ranap'] = $request->post('is_ranap');
            $payLoadRequest['kelaspelayanan_selected'] = $request->post('kelaspelayanan_selected');
            $payLoadRequest['is_pasientitipan'] = $request->post('pasientitipan');
            $payLoadRequest['is_aps'] = $request->post('pasienaps');
            $payLoadRequest['pasien_admisi'] = $request->post('PasienAdmisiForm');
            $payLoadRequest['pendaftaranasal_id'] = $request->post('pendaftaranasal_id');
            // $payLoadRequest['pasien_admisi']['is_pasientitipan'] = ((int) $payLoadRequest['is_pasientitipan'] == 1) ? true : false;
            $payLoadRequest['pasien_admisi']['is_aps'] = ((int) $payLoadRequest['is_aps'] == 1) ? true : false;

        }
        $payLoadRequest['allow_bpjs'] = $request->post('allow_bpjs');

        try {
            $sent = $this->_restPendaftaran->post('pendaftaran-'.$params.'/save-pendaftaran-rajal', [
                    'form_params'=> $payLoadRequest
                ]
            );

            $response = json_decode($sent->getBody(), true);
            
            if (isset($response['metadata']['status'])) {
                $status = $response['metadata']['status'];
                if ($status == 422) {
                    $error = [];
                    if (isset($response['response']['data'])) {
                        $data = $response['response']['data'];
                        if (isset($data['kunjungan'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['kunjungan'],'KunjunganForm'));
                        }

                        if (isset($data['KunjunganForm[data]'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['KunjunganForm[data]'],'KunjunganForm'));
                        }

                        if (isset($data['tipe_pasien'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['tipe_pasien'],'TipePasienForm'));
                        }

                        if (isset($data['pasien_admisi'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['pasien_admisi'],'PasienAdmisiForm'));
                        }

                        if (isset($data['pj_pasien'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['pj_pasien'],'PjpasienForm'));
                        }

                        if (isset($data['asuransi'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['asuransi'],'AsuransiForm'));
                        }

                        if (isset($data['pasien'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['pasien'],'PasienForm'));
                        }

                        if (isset($data['bpjs'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['bpjs'],'BpjsNewForm'));
                        }

                        $response = [
                            'metadata' => [
                                'status' => 422
                            ],
                            'response' => [
                                'data' => $error
                            ]
                        ];
                    }
                }
            }
            return DocoHelpers::response($response);
        } catch(RequestException $e) {
            $contentGuzzle = json_decode($e->getResponse()->getBody(true));
            if (isset($contentGuzzle->metadata) && $contentGuzzle->metadata->status < 500) {
                return (new DocoHelpers)->macroResponseJson($contentGuzzle->metadata->status, $contentGuzzle->response->message, []);
            } else {
                return (new DocoHelpers)->macroResponseJson(500, 'Terjadi kesalahan pada server', []);
            }
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }*/
}