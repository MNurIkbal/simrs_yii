<?php
/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use app\modules\pendaftaran\models\PendaftaranForm;
use app\modules\api\models\BpjsForm;
use app\modules\pendaftaran\models\PasienAdmisiForm;
use app\modules\pendaftaran\models\AsuransiForm;
use app\modules\pendaftaran\models\RujukanForm;
use app\modules\pendaftaran\models\PasienBatalPeriksaForm;
use app\modules\pendaftaran\models\EditPendaftaranForm;
use app\modules\pendaftaran\models\EditStatusPasienForm;
use app\modules\pendaftaran\models\BpjsNewForm;
use app\modules\pendaftaran\models\PengajuanSepForm;
use app\modules\pendaftaran\components\traits\EditPasienTrait;
use app\modules\pendaftaran\components\Lookup;
use app\modules\pendaftaran\components\traits\UploadDokumenTrait;

class InformasiPasienController extends DocoController
{
    use EditPasienTrait;
    use UploadDokumenTrait;

    protected $_titleInfo = "Informasi Pasien";
    protected $_titleUpdate = "Pendaftaran Rawat Jalan / Darurat";
    protected $_module = 'pendaftaran/informasi-pasien/';
    protected $_moduleDaftarRajal = 'pendaftaran/daftar-rajal/';
    protected $_moduleDaftarRanap = 'pendaftaran/daftar-ranap/';
    protected $_moduleDaftarIgd = 'pendaftaran/daftar-igd/';
    protected $_restMaster;
    protected $_restPendaftaran;
    protected $allowAction = ['*'];

    protected $_id_carabayar_bpjs;
    protected $_is_hide_alias;
    public $_is_SEP_mandatory;

    const FLAG_MODEL = 'model_error';

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $this->_id_carabayar_bpjs = null;

        $cache = Yii::$app->cache;
        if (!empty($cache->get(DocoConstants::VAR_K_S))) {
            $konfigSystem = $cache->get(DocoConstants::VAR_K_S);
        } else {
            $requests = $this->_restPendaftaran->post('allow/get-konfig-system');
            $response = json_decode($requests->getBody(), true);
            $konfigSystem = $response['response'];
            $cache->set(DocoConstants::VAR_K_S, $konfigSystem);
        }
        $this->_is_hide_alias = ArrayHelper::getValue($konfigSystem, 'is_hide_alias');
        $this->_is_SEP_mandatory = ArrayHelper::getValue($konfigSystem, 'is_sep_mandatory_on_edit');
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
        return Yii::$app->docoPlugin->execute($this,'informasi_pasien_index');
    }

    public function actionGetData($jenis='rajal')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $is_executive = $request->get('is_executive', false);

        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
        } else {
            $tgl_awal = $tgl_akhir = date('Y-m-d');
        }

        if(isset($yiiRestfulParams['advanced-filter']['nama_pegawai'])) {
            $yiiRestfulParams['advanced-filter']['pegawai_id'] = $yiiRestfulParams['advanced-filter']['nama_pegawai'];
            unset($yiiRestfulParams['advanced-filter']['nama_pegawai']);
        }

        $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
        $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
        $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
        $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
        unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);

        $type = $request->get('type', null);
        if (isset($type))
        {
            $yiiRestfulParams['advanced-filter']['carabayar_id'] = $type;
        }
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        $response = $this->_restPendaftaran->get('inf-pasien/' . $jenis . '?is_executive=' . $is_executive . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
        $body = json_decode($response->getBody(), True);
        $no = $request->get('start',1);
        if(!empty($body['response']['data'])) {
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['pasien_id'] = DocoHelpers::encrypt($value['pasien_id']);
                $value['prev_no_pendaftaran'] = !empty($value['prev_no_pendaftaran']) ? DocoHelpers::encrypt($value['prev_no_pendaftaran']) : 0;
                $status_kamar = '-';
                $titipan = '-';
                if($jenis == DocoConstants::PARAM_DFTR[DocoConstants::WS_RANAP]){
                    if($value['carabayar_id'] == 6) {
                        $status_kamar = 'Sesuai Kelas';
                        $titipan = $value['kelas_ditagihkan_nama'];
                        if($value['klsrawat'] != null) {
                            if($value['is_aps'] == true) {
                                if($value['bpjs_kelas'] != null) {
                                    if($value['kelaspelayanan_id'] < $value['klsrawat']) {
                                        $status_kamar = 'APS / Naik kelas';
                                    }
                                    elseif($value['kelaspelayanan_id'] > $value['klsrawat']) {
                                        $status_kamar = 'APS / Turun kelas';
                                    }
                                    $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$titipan;
                                }
                                else {
                                    $status_kamar = 'APS / Naik kelas';
                                    $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$titipan;
                                }
                            }
                            elseif($value['is_pasientitipan'] == true) {
                                if($value['bpjs_kelas'] != null) {
                                    if($value['kelaspelayanan_id'] < $value['klsrawat']) {
                                        $status_kamar = 'Titipan / Naik kelas';
                                    }
                                    elseif($value['kelaspelayanan_id'] > $value['klsrawat']) {
                                        $status_kamar = 'Titipan / Turun kelas';
                                    }
                                    $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$titipan;
                                }
                                else {
                                    $status_kamar = 'Titipan / Naik kelas';
                                    $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$titipan;
                                }
                            }
                        }else{
                            $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$titipan;
                        }
                    } else if (!empty($value['is_pasientitipan_pk'])) {
                        if($value['is_pasientitipan_pk'] == true && $value['is_stoppasientitipan'] == false){
                            $titipan = $value['kelas_ditagihkan_nama'];
                        }
                        $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$titipan;
                    } else if (empty($value['is_pasientitipan_pk'])) {
                        if($value['is_pasientitipan'] == true && $value['is_stoppasientitipan'] == false){
                            $titipan = $value['kelas_ditagihkan_nama'];
                        }
                        $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$titipan;
                    }

                    $button = Html::a('<i class="fa fa-print"></i>', ['informasi-pasien/print-tracer-ranap', 'id' => $primaryKey],['target'=>'_blank']);
                    $value['norm'] = $value['no_rekam_medik'];
                    $value['no_rekam_medik'] = $button." ".$value['no_rekam_medik'];
                    $value['tgl_pendaftaran'] = date('d-m-Y H:i:s', strtotime($value['tgl_admisi']));
                    $value['status_bayar'] = Arrayhelper::getValue($value, 'status_bayar', null);
                    $status_kamar = $value['status_kelas'];
                }

                if($jenis == DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU]) {
                    $value['alamat'] = $value['alamat_pasien'];
                    $value['jenis_kelamin'] = $value['j_kelamin'];
                    $value['nama_pegawai'] = $value['dokter_penunjang'];
                    $value['status_periksa_mcu'] = $value['status_periksa'];
                    $value['status_periksa'] = $value['status_periksa_nama'];
                    
                    $value['jeniskasuspenyakit_nama'] = '';
                    $value['nosep'] = '';

                    if (isset($this->_is_hide_alias) && $this->_is_hide_alias != false) {
                        $value['info_pasien'] = $value['no_rekam_medik'] . '<br>' . $value['nama_pasien'] . '<br>' . (isset($value['tanggal_lahir']) ? date('d-m-Y', strtotime($value['tanggal_lahir'])) : '');
                    } else {
                        $value['info_pasien'] = $value['no_rekam_medik'] . '<br>' . (isset($value['nama_depan']) ? $value['nama_depan'] : '') . ' ' . $value['nama_pasien'] . '<br>' . (isset($value['tanggal_lahir']) ? date('d-m-Y', strtotime($value['tanggal_lahir'])) : '');
                    }
                }

                if ($jenis == DocoConstants::PARAM_DFTR[DocoConstants::WS_PENUNJANG]) {
                    $value['nosep'] = array_key_exists('nosep', $value) ? $value['nosep'] : '';
                    $value['status_periksa'] = array_key_exists('nama_status_periksa', $value) ? $value['nama_status_periksa'] : '';
                }

                $value['tgl_pendaftaran'] = date('d-m-Y H:i:s', strtotime($value['tgl_pendaftaran']));

                if (isset($this->_is_hide_alias) && $this->_is_hide_alias != false) {
                    $value['info_pasien'] = $value['no_rekam_medik'] . '<br>' . $value['nama_pasien'] . '<br>' . (isset($value['tanggal_lahir']) ? date('d-m-Y', strtotime($value['tanggal_lahir'])) : '');
                } else {
                    $value['info_pasien'] = $value['no_rekam_medik'] . '<br>' . (isset($value['nama_depan']) ? $value['nama_depan'] : '') . ' ' . $value['nama_pasien'] . '<br>' . (isset($value['tanggal_lahir']) ? date('d-m-Y', strtotime($value['tanggal_lahir'])) : '');
                }

                $value['rowNum'] = $no;
                $nomor_kartu = (!empty($value['nokartuasuransi']) ? $value['nokartuasuransi'] : ' ');
                $value['carabayar_penjamin'] = ($value['groupcarabayar_id'] != DocoConstants::GROUP_UMUM) ? $value['penjamin_nama'] .' - '. $nomor_kartu : $value['penjamin_nama'];
                $value['ruangan'] = ($jenis == 'ranap') ? $value['nama_pegawai'] . '</br>'. $value['ruangan_nama'] . ' - ' . $value['kamarruangan_nokamar'] : $value['nama_pegawai'] . '</br>'.  $value['jeniskasuspenyakit_nama'] . '<br>' . $value['ruangan_nama'] . '<br>' . $value['kelaspelayanan_nama'] ;
                $value['can_cancel'] = false;
                $value['status_kamar'] = $status_kamar;
                if (isset($value['status_periksa_id'])) {
                    if (($value['status_periksa_id'] == DocoConstants::STATUS_PERIKSA_ANTR_KASIR)
                    || ($value['status_periksa_id'] == DocoConstants::STATUS_PERIKSA_ANTR_POLI)
                    || ($value['status_periksa_id'] == DocoConstants::STATUS_RANAP_BELUM_PERIKSA)) {
                        $value['can_cancel'] = true;
                    }
                }

                if($jenis == DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU]) {
                    if ($value['status_periksa_mcu'] == DocoConstants::STATUS_PERIKSA_MCU_SELESAI) {
                        $value['can_cancel'] = false;
                    }
                }
                if($jenis == DocoConstants::PARAM_DFTR[DocoConstants::WS_PENUNJANG] && $value['instalasiasal_id'] == DocoConstants::INSTALASI_MCU){
                    $value['can_cancel'] = false;
                }
                if(isset($value['petugas_id']) && !empty($value['petugas_id'])){
                    $value['petugas'] = (isset($value['pegpetugas_nama']) ? $value['pegpetugas_nama'] : '') . '<br>' . (isset($value['petugas_tgl_pembuat']) ? explode(" ", $value['petugas_tgl_pembuat'])[0] . '<br>' . explode(" ", $value['petugas_tgl_pembuat'])[1] : null);
                } else {
                    $nama_petugas = !empty($value['pembuat_nama']) ? $value['pembuat_nama'] : $value['petugas_nama'];
                    $tgl_petugas = !empty($value['tgl_pembuatan']) ? $value['tgl_pembuatan'] : $value['tgl_update_terakhir'];
                    if(!is_null($tgl_petugas)){
                        $value['petugas'] = $nama_petugas . '<br>' . explode(" ", $tgl_petugas)[0] . '<br>' . explode(" ", $tgl_petugas)[1];
                    }else{
                        $value['petugas'] = $nama_petugas;
                    }
                }
                $value['limit_tagihan'] = array_key_exists('limit_tagihan', $value) ? number_format($value['limit_tagihan'],0,',','.') : '';

                if(isset($value['is_multipayer']) && $value['is_multipayer'] == true) {
                    $value['carabayar_penjamin'] = $value['carabayar_penjamin'] . ' <br> ' . $this->searchAdditionalPayer($value['pendaftaran_id']);
                }

                $value['primary'] = $primaryKey;

                //cek ri atau rd
                $tipe = substr($value["no_pendaftaran"], 0, 2);
                $value["type"] = $tipe == "RI" ? "ri" : "rd";

                $data[$key] = $value;
            }
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
        $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
        return $result;
    }


    /**
    * @author Rizal
    * @since 2018-01-26 15:39:31
    * @param string id encrypted
    * @return
    * @desc update for RJ / IGD / RI update disini
    */
    public function actionUpdate($id = null)
    {
        return Yii::$app->docoPlugin->execute($this,'informasi_pasien_update');
    }

     /**
    * @author Rizal
    * @since 2018-01-26 15:39:31
    * @param string id encrypted
    * @return
    * @desc
    */
    public function actionUpdateRanap($id)
    {
        // Init
        $id = DocoHelpers::decrypt($id);
        $status = $this->_status; $options = $this->_options;
        $request = Yii::$app->request;
        $model = new PendaftaranForm;
        $model->scenario = 'update';
        $modelPasienAdmisi = new PasienAdmisiForm;
        $modelBpjs = new BpjsForm;
        $modelAsuransi = new AsuransiForm();
        $modelRujukan = new RujukanForm();
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', 'Pasien');

        $instalasi_id = DocoConstants::INSTALASI_ID_RD;
        $response = $this->_restPendaftaran->get('allow/get-api?instalasi_id=' . $instalasi_id . '&default=2');
        $body = json_decode($response->getBody(), true);

        $data = [
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
        ];

        // $formName = substr(strrchr(get_class($model), "\\"), 1);
        if (!empty($data['ruangan'])) {
            $ruangan = ArrayHelper::map($data['ruangan'], 'ruangan_id','ruangan_nama');
        }

        if (!empty($data['cara_bayar'])) {
            $carabayar = $data['cara_bayar'];
            $carabayarOptions = [];
            foreach ($carabayar as $key => $value) {
                $carabayarOptions[$value['carabayar_id']] = ['data-id' => $value['groupcarabayar_id']];
            }
            $carabayar = ArrayHelper::map($carabayar, 'carabayar_id', 'carabayar_nama');
        }

        if (!empty($data['asal_rujukan'])) {
            $asalrujukan = $data['asal_rujukan'];
        }

        if (!empty($data['kelas_pelayanan'])) {
            $kelaspelayanan = ArrayHelper::map($data['kelas_pelayanan'], 'kelaspelayanan_id','kelaspelayanan_nama');
        }

        if (!empty($data['jeniskasus'])) {
            $jeniskasus = ArrayHelper::map($data['jeniskasus'], 'jeniskasuspenyakit_id','jeniskasuspenyakit_nama');
        }
        $packFormKunjunganRanap = [
            'modelAsuransi' => $modelAsuransi,
            // 'modelKunjungan' => $modelKunjungan,
            // 'modelAdmisi' => $modelAdmisi,
            'jeniskasus' => $jeniskasus,
            'kelaspelayanan' => $kelaspelayanan,
            'carabayar' => $carabayar,
            'listResponses' => $data['lookup'],
            'listRujukan' => $asalrujukan,
            'carabayarOptions' => $carabayarOptions,
            'instalasi_id' => $instalasi_id,
            // 'lib'=>$requestsKunjungan['response']
        ];
        $packFormRujukan = ['modelRujukan' => $modelRujukan];
        if ($request->post()) {
            $post = $request->post();
            $model->attributes = $post['PendaftaranForm'];
            $modelPasienAdmisi->attributes = $post['PasienAdmisiForm'];

            if ($model->validate()) {
                if ($modelPasienAdmisi->carabayar_id == $this->_id_carabayar_bpjs) {
                    $modelBpjs->attributes = $post['BpjsForm'];
                    $modelBpjs->no_rekam_medik = $model->no_rekam_medik;


                    // backup
                    $modelBpjs->additional_data = json_encode($post['BpjsForm']);
                    if ($modelBpjs->validate()) {
                        // return json_encode($modelBpjs->attributes);
                        $response = $this->_restPendaftaran->post('allow/bpjs-create', [
                            'form_params' => $modelBpjs->attributes
                        ]);
                        $responseBpjs = json_decode($response->getBody(),true);
                        $model->bpjs_id = $responseBpjs['response']['bpjs_id'];
                    } else {
                        $errors = DocoHelpers::parseError($modelBpjs->errors, 'BpjsForm');
                        return DocoHelpers::responseTemplate(422, 'Error', $errors);
                    }
                }
                $response = $this->_restPendaftaran->put('tra-pasien-rawat-inap/update?id='.$id, [
                    'form_params' => $post//$model->attributes
                ]);
                $body = json_decode($response->getBody(), true);

                $session = Yii::$app->session;
                $session->set('trans_update_success', Yii::t('fe', 'Data rawat inap berhasil diubah.'));
                return DocoHelpers::response($body['response']);
                // return $this->redirect('/pendaftaran/informasi-pasien/ranap');
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'PendaftaranForm');
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }

        } else {
            $response = $this->_restPendaftaran->get('tra-pasien-rawat-inap/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            $model->pendaftaran_id = $id;
            $model->pasien_id = $attributes['pasien_id'];
            if (!empty($attributes['pasienadmisi_id'])) {
                $response = $this->_restPendaftaran->get('tra-pasien-admisi/view?id='.$attributes['pasienadmisi_id']);
                $body = json_decode($response->getBody(), TRUE);
                $attributes = $body['response'];
                $modelPasienAdmisi->attributes = $attributes;
            }

            $bundleReq = $this->_restPendaftaran->get('tra-pasien-rawat-inap/get-bundle-data',['query'=> ['id'=>$id]]);
            $bundleBody = json_decode($bundleReq->getBody(),TRUE);
            $dokterList = !empty($bundleBody['response']['list-dokter-rajal'])
                ? $bundleBody['response']['list-dokter-rajal']
                : [];
            $penyakitList = !empty($bundleBody['response']['list-penyakit'])
                ? $bundleBody['response']['list-penyakit']
                : [];
            $carabayarList = !empty($bundleBody['response']['list-cara-bayar'])
                ? $bundleBody['response']['list-cara-bayar']
                : [];
            $penjaminList = !empty($bundleBody['response']['list-penjamin'])
                ? $bundleBody['response']['list-penjamin']
                : [];
            $asalRujukanList = !empty($bundleBody['response']['list-asalrujukan'])
                ? $bundleBody['response']['list-asalrujukan']
                : [];
            $attributesAsuransi = !empty($bundleBody['response']['view-asuransi'])
                ? $bundleBody['response']['view-asuransi']
                : [];
            $attributesRujukan = !empty($bundleBody['response']['view-rujukan'])
                ? $bundleBody['response']['view-rujukan']
                : [];
            $modelAsuransi->attributes = array_merge($attributesAsuransi, $attributesRujukan);
            $modelRujukan->attributes = $attributesRujukan;

            return $this->render('form_update_ranap', get_defined_vars());
        }
    }


    public function actionSaveAsuransi()
    {
        $request = Yii::$app->request;
        if($request->post()){
            $model = new AsuransiForm();
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            try {
                $post = $request->post();
                $model->load($post);
                if($model->validate()){
                    $response = $this->_restPendaftaran->post('tra-pasien-rawat-inap/save-asuransi', [
                        'form_params'=>$post
                    ]);
                    $body = json_decode($response->getBody(), true);
                    if(isset($body['response']['errors'])){
                        return DocoHelpers::response($body['response']['errors'], 422, $formName);
                    }
                    return DocoHelpers::response($body['response']);
                }else{
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            } catch (Exception $e) {
                $result['error'] = $e->getMessage();
                return DocoHelpers::response($result);
            } catch(\RequestException $e){
                $result['error'] = $e->getMessage();
                return DocoHelpers::response($result);
            }
        }
    }

    public function actionSaveRujukan($params){
        $request = Yii::$app->request;
        if($request->post()){
            $model = new RujukanForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            try {
                $post = $request->post();
                $model->load($post);
                if($model->validate()){
                    $response = $this->_restPendaftaran->post('tra-pasien-rawat-inap/save-rujukan', [
                        'query'=>[
                            'params'=>$params
                        ],
                        'form_params'=>$post
                    ]);
                    $body = json_decode($response->getBody(), true);
                    // if(isset($body['response']['errors'])){
                    //     return DocoHelpers::response($body['response']['errors'], 422, $formName);
                    // }
                    return DocoHelpers::response($body['response']);
                }else{
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            } catch (Exception $e) {
                $result['error'] = $e->getMessage();
                return DocoHelpers::response($result);
            } catch(\RequestException $e){
                $result['error'] = $e->getMessage();
                return DocoHelpers::response($result);
            }
        }
    }


    public function actionGetRujukanDari(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        $depdrop_params = $request->post('depdrop_params');
        $param_label = $depdrop_params[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restPendaftaran->get('allow/get-rujukan-dari?id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $key => $value){
                $result['output'][] = [
                    'id' => $key,
                    'name' => $value
                ];
            }

            $response = $this->_restPendaftaran->get('tra-pasien-rawat-inap/get-select-rujukan-dari?id='.$param_label);
            $body = json_decode($response->getBody(), True);
            if($body['response']){
                $result['selected'] = $body['response'];
            }
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }

    public function actionRiwayatPerubahanDataPendaftaran()
    {
        $title = 'Riwayat Perubahan Data Pendaftaran';
        $id = Yii::$app->request->get('id');
        return $this->renderAjax('_modal_riwayat_perubahan_data_pendaftaran', get_defined_vars());
    }

    public function actionGetDataRiwayatPerubahanDataPendaftaran($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPendaftaran->get('inf-pasien/get-data-riwayat-perubahan-data-pendaftaran', [
                'query' => [
                    'id' => DocoHelpers::decrypt($id)
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $detail = json_decode($value['additional_detail'],true);
                $newValue = [
                    'rowNum' => $no,
                    'tanggal' => date('Y-m-d H:i:s',strtotime($value['tgl'])),
                    'data_sebelum' => implode($this->parseDetailRiwayat(ArrayHelper::getValue($detail,'before')),'<br/>'),
                    'data_sesudah' => implode($this->parseDetailRiwayat(ArrayHelper::getValue($detail,'after')),'<br/>'),
                    'user' => ArrayHelper::getValue($value,'nama_pegawai'),
                ];
                $data[$key] = $newValue;
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
            $result['line'] = $e->getLine();
            return $result;
        }
    }

    private function parseDetailRiwayat($riwayat)
    {
        $result = [];
        $model = new EditPendaftaranForm();
        $labels = array_keys($model->attributeLabels());
        foreach($riwayat as $key => $value) {
            if($key == 'referal_pegawai_id') {
                continue;
            }else if(in_array($key,$labels)){
                $key_value = $model->attributeLabels()[$key];
            }else{
                $key_value = $this->snakeToCamelCase($key);
            }
            $result[] = $key_value.' : <b>'.$value.'</b>';
        }

        return $result;
    }

    function snakeToCamelCase($input)
    {
        return \str_replace('_', ' ', \ucwords($input, '_'));
    }

    /**
    * @author Naufal Ziyad L edited by Rizal
    * @since 2018-05-02 11:16:59
    * @param
    * @return
    * @desc duplicate from master and modified
    */
    public function actionListPenjamin() {
        $request = Yii::$app->request;
        $post = $request->post();
        $get = $request->get();

        $carabayar_id = isset($post['depdrop_parents'][0]) ? $post['depdrop_parents'][0] : '';
        if (isset($get['type'])){
            $carabayar_id = $get['type'];
        }

        $penjaminRequest = $this->_restPendaftaran->get('allow/list-penjamin?carabayar_id='.$carabayar_id);
        $body = json_decode($penjaminRequest->getBody(),TRUE);
        $responses = $body['response'];

        $out = [];
        foreach($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response
            ];
        }

        return json_encode(['output'=>$out, 'selected'=>'']);
    }

    public function actionExportExcel($jenis)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $type = $request->get('type');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }

        if ($type) {
            $jenis = $jenis.'_'.$type;

            if (array_key_exists('nama_pasien', $yiiRestfulParams['advanced-filter'])) {
                $yiiRestfulParams['advanced-filter']['Nama Lengkap'] = $yiiRestfulParams['advanced-filter']['nama_pasien'];
                unset($yiiRestfulParams['advanced-filter']['nama_pasien']);
            }

            if (array_key_exists('order', $yiiRestfulParams)) {
                if (strpos($yiiRestfulParams['order'], 'tgl_pendaftaran') !== false) {
                    $yiiRestfulParams['order'] = str_replace('tgl_pendaftaran', 'Tanggal Pemeriksaan', $yiiRestfulParams['order']);
                }
            }
        }

        try {
            $path = Yii::getAlias("@download") . "/informasi-pasien.xlsx";
            $query = [
                'jenis' => $jenis
            ];
            $query = array_merge($query,$yiiRestfulParams);
            $response = $this->_restPendaftaran->get('inf-pasien/export-excel',[
                'query' => $query,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
    public function actionExportPdf($jenis)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }
        $path = Yii::getAlias("@download") . "/informasi-pasien-".$jenis.".pdf";
        try {
            $response = $this->_restPendaftaran->get('inf-pasien/export-pdf?jenis='.$jenis.'&ruangan='.Yii::$app->docoVars->workspace("ruangan_id").'&'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPrintTracerRanap()
    {

        // Get request
        $request = Yii::$app->request;

        // Get params
        $id = $request->get('id');
        $id = DocoHelpers::decrypt($id);

        // Try catch
        try {
            $bodyReq = $this->_restPendaftaran->get('tra-pasien-rawat-inap/cetak-tracer-ranap',['query'=>['id'=>$id]]);

              $bodyReq = json_decode($bodyReq->getBody(), true);
              $response = $bodyReq['response'];
              $data_header = @$response['header'];
              $data_body = @$response['body'];
              $data_footer = @$response['footer'];

            // Render
            return $this->render('print_tracer_ranap', get_defined_vars());
        } catch (RequestException $e) {

            return DocoHelpers::responseTemplate(500,$e->getMessage());
            // Error
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500,$e->getMessage());
            // Error
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
        }
    }

    /**
     * summary
     *
     * @return void
     * @author
     */
    public function actionConfirmBatal()
    {
        $request        = Yii::$app->request;
        $pendaftaran_id = $request->get('id');
        $no_pendaftaran = $request->get('no_pendaftaran');
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);

        $username = Yii::$app->docoVars->user('nama');
        $title     = Yii::t('fe', 'Batal Kunjungan');
        $batalForm = new PasienBatalPeriksaForm;
        $jenis = $request->get('jenis');

        if ($request->post()) {
            $batalForm->load($request->post());
            $batalForm->tgl_batal = date('Y-m-d');
            $batalForm->jenis = isset($jenis) ? $jenis : '';
            if ($batalForm->validate()) {
                $response = $this->_restPendaftaran->post('tra-pasien-rawat-jalan/batal', [
                    'form_params' => $batalForm->attributes
                ]);
                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response);
            }

            return DocoHelpers::response($batalForm->errors,422,'PasienBatalPeriksaForm');

        } else {
            $getResponse = $this->getTotalTagihan($no_pendaftaran);
            $batalForm->total_tagihan = !empty($getResponse['total_tagihan']) ? $getResponse['total_tagihan'] : 0;

            return $this->renderAjax('_modal_batal', get_defined_vars());
        }
    }

    public function actionConfirmBatalRanap() {
        $request        = Yii::$app->request;
        $pendaftaran_id = $request->get('id');
        $no_pendaftaran = $request->get('no_pendaftaran');
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);

        $username = Yii::$app->docoVars->user('nama');
        try{
            $title     = Yii::t('fe', 'Batal Kunjungan');
            $batalForm = new PasienBatalPeriksaForm;
            $jenis = $request->get('jenis');

            if ($request->post()) {
                $batalForm->load($request->post());
                $batalForm->tgl_batal = date('Y-m-d');
                if ($batalForm->validate()) {
                    $response = $this->_restPendaftaran->post('tra-pasien-rawat-inap/batal', [
                        'form_params' => $batalForm->attributes
                    ]);
                    $response = json_decode($response->getBody(), true);
                    return DocoHelpers::response($response);
                }

                /*if (isset($response['response']['data']))
                {
                    return DocoHelpers::responseTemplate(
                        422,
                        Yii::t('fe', 'Proses Gagal'),
                        [],
                        ['title' => Yii::t('fe', 'Proses Gagal'), 'text' => Yii::t('fe', $response['response']['data'])]
                    );
                }*/
                return DocoHelpers::response($batalForm->errors,422,'PasienBatalPeriksaForm');

            }else{
                $getResponse = $this->getTotalTagihan($no_pendaftaran);
                $batalForm->total_tagihan = !empty($getResponse['total_tagihan']) ? $getResponse['total_tagihan'] : 0;

                return $this->renderAjax('_modal_batal', get_defined_vars());
            }
        } catch (\Exception $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (RequestException $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * summary
     *
     * @return void
     * @author
     */
    public function actionLihatBatal() {
        $request        = Yii::$app->request;
        $pendaftaran_id = $request->get('id');
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $jenis          = $request->get('jenis'); // Menambahkan jenis untuk memisahkan query data
        $username = Yii::$app->docoVars->user('nama');
        try{
            $title     = Yii::t('fe', 'Batal Kunjungan');
            $batalForm = new PasienBatalPeriksaForm;

            $alasanRequest = $this->_restPendaftaran->get('inf-pasien/list-alasan?pendaftaran_id='.$pendaftaran_id.'&jenis='.$jenis); // Param jenis dipassing ke backend
            $body          = json_decode($alasanRequest->getBody(),TRUE);
            $responses     = $body['response'];

            $alasan       = $responses['data']['alasan_batal'];
            $nama_pegawai = $responses['data']['nama_pegawai'];
            $tanggal_jam  = $responses['data']['created_date'];

                return $this->renderAjax('_modal_lihat_batal', get_defined_vars());
        } catch (\Exception $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (RequestException $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionPilihJumlahCetakan()
    {
        return Yii::$app->docoPlugin->execute($this,'modal_label_multiple');
    }

    public function actionPilihJumlahCetakanMultiple()
    {
        $title = 'Pilih jumlah label';
        $request = Yii::$app->request;
        $jenis = $request->get('jenis');
        $pendaftaran_id = $request->get('pendaftaran_id');
        $pasien_id = $request->get('pasien_id');
        $no_pendaftaran = DocoHelpers::encrypt($request->get('no_pendaftaran'));

        $jenisTemplate = 'kn';
        $jumlahCetakan = 11;

        return $this->renderPartial('_modal_jumlah_cetakan_multiple', get_defined_vars());
    }

    private function getTotalTagihan($noPendaftaraan)
    {
        $response = $this->_restPendaftaran->get('tra-pasien-rawat-jalan/get-total-tagihan-pasien', [
            'query' => [
                'no_pendaftaran' => $noPendaftaraan
            ]
        ]);
        $response = json_decode($response->getBody(), true);
        $getResponse = isset($response['response']) ? $response['response'] : [];

        return $getResponse;
    }

    /**
     * @todo Fungsi untuk stop pasien titipan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionStopPasienTitipan($pendaftaran_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = $this->_restPendaftaran->get(
            'inf-pasien/stop-pasien-titipan?pendaftaran_id='.$pendaftaran_id
        );
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::response($body);
    }

    public function actionPrintStatusPasien()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-status-pasien.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::setDecryptIdFromString($pasien_id); // ex: oiqjasd,dqwdoiqj,sdoiqwjd = array string dari get
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $pendaftaranol_id = $request->get('id',null);
            $decryptPendaftaranId = DocoHelpers::setDecryptIdFromString($pendaftaran_id); // ex: oiqjasd,dqwdoiqj,sdoiqwjd = array string dari get
            $pasienmasukpenunjang_id = $request->get('pasienmasukpenunjang_id', null);
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $pegawai_id = Yii::$app->docoVars->user('uid');
            $source = $request->get('source', 'pendaftaran');
            $param = ArrayHelper::getValue((new Lookup)->getValueFromLookupT($ruangan_id, 'workspace_pendaftaran'), 'additional_value');
            $konsulpoli_id = $request->get('konsulpoli_id',null);
            if($konsulpoli_id == "null"){
                $konsulpoli_id = 0;
            }
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId,'param'=> $param, 'source'=>$source, 'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id, 'pegawai_id' => $pegawai_id, 'konsulpoli_id' => $konsulpoli_id];
            if ( $source == 'reservasi') {
                $post = [
                    'pendaftaranol_id' => DocoHelpers::setDecryptIdFromString($pendaftaranol_id),
                    'pegawai_id' => $pegawai_id,
                    'param' => $source
                ];
                $param = $source;
            }
            if(Yii::$app->report->enabled){
                if ($param != 'ranap' && $param != 'penunjang' && $param != 'reservasi' && $param != 'igd' ) {
                    $urlReport = 'pendaftaran/inf-daftar-sepuluh-terakhir/print-status-pasien';
                } else if ($param == 'penunjang') {
                    $urlReport = 'print-tracer-penunjang';
                } else if ($param == 'ranap'){
                    $urlReport = 'print-tracer-ranap';
                } else if ($param == 'igd'){
                    $urlReport = 'print-tracer-igd';
                } else if ($param == 'reservasi') {
                    $urlReport = 'print-tracer-reservasi';
                }

                Yii::$app->report->exec($urlReport,[
                    'queryParameter' => $post,
                    'manualRender'=>function() use($post,$path){
                        $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-status-pasien',[                          
                            'form_params' => $post,
                            'save_to' => $path
                        ]);
            
                        return DocoHelpers::previewPdf($path);
                    }
                ]);
            }
            $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-status-pasien',
                [
                    'form_params' => $post,
                    'save_to' => $path
                ]
            );
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetTagihanSudahBayar($pendaftaran_id)
    {
        $response = $this->_restPendaftaran->get('tra-pasien-rawat-jalan/get-tagihan-sudah-bayar', [
            'query' => [
                'pendaftaran_id' => $pendaftaran_id
            ]
        ]);
        $response = json_decode($response->getBody(), true);
        $getResponse = isset($response['response']) ? $response['response'] : [];

        return DocoHelpers::response($getResponse);
    }

    public function actionReSync()
    {
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $status = $cache->get('sync-pendaftaran');
        $response = $this->_restPendaftaran->post('inf-pasien/sync-pendaftaran', [
            'form_params' => []
        ]);

        if(!empty($status)) {
            return DocoHelpers::response(false, 422);
        } else {
            return DocoHelpers::response(true);
        }
    }

    public function actionConfirmUpdateStatus()
    {
        $title = 'Edit Status Kunjungan';
        $request = Yii::$app->request;
        $pendaftaran_id = DocoHelpers::encrypt($request->get('pendaftaran_id'));
        $pasien_id = DocoHelpers::encrypt($request->get('pasien_id'));
        $no_pendaftaran = $request->get('no_pendaftaran');

        $username = Yii::$app->docoVars->user('nama');
        $editForm = new EditStatusPasienForm;
        $jenis = $request->get('jenis');

        if ($request->post()) {
            $editForm->load($request->post());
            $editForm->jenis = isset($jenis) ? $jenis : '';
            if ($editForm->validate()) {
                if ($jenis == 'ranap') {

                    $response = $this->_restPendaftaran->post('tra-pasien-rawat-inap/update-status', [
                        'form_params' => $editForm->attributes
                    ]);
                } else {
                    $response = $this->_restPendaftaran->post('tra-pasien-rawat-jalan/update-status', [
                        'form_params' => $editForm->attributes
                    ]);
                }

                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response);
            }

            return DocoHelpers::response($editForm->errors,422,'EditStatusPasienForm');
        } else {
            if ($jenis == 'ranap') {
                $response = $this->helper->guzzleExec($this->_restPendaftaran, [
                    'url' => 'allow/list-status-periksa-ranap',
                    'method' => 'post',
                    'payload' => [
                        'form_params' => [
                            'no_pendaftaran' => $no_pendaftaran
                        ]
                    ]
                ]);
            } else {
                $response = $this->helper->guzzleExec($this->_restPendaftaran, [
                    'url' => 'allow/list-status-periksa',
                    'method' => 'post',
                    'payload' => [
                        'form_params' => [
                            'no_pendaftaran' => $no_pendaftaran
                        ]
                    ]
                ]);
            }
            $result = $response;

            $listStatusPeriksa = $result['status_periksa'];
            $editForm->status_periksa = $result['pendaftaran']['status_periksa'];

            return $this->renderPartial('_modal_edit_status', get_defined_vars());
        }
    }

    private function searchAdditionalPayer($id)
    {
        $info = '';
        $data = $this->guzzleExec($this->_restPendaftaran, [
            'url' => 'allow/list-additional-payer',
            'payload' => [
                'query' => [
                    'id' => $id
                ]
            ],
            'returnResponse' => true,
        ]);

        if(!empty($data['data'])) {
            foreach($data['data'] as $k => $v) {
                $info .= $v['carabayar_nama'] . ' / ' . $v['penjamin_nama'] . ' <br> ';
            }
        }

        return $info;
    }

    public function actionShowPopup()
    {
        $request = Yii::$app->request;
        $tipe = $request->get('tipe', 'pdf');
        $jenisFile = ($tipe == 'pdf') ? 'PDF' : 'Excel';
        $jenis = $request->get('jenis', 'rajal');
        $is_executive = $request->get('is_executive', false);
        if($jenis == 'rajal') {
            $titleJenis = DocoConstants::TITLE_RJ;
        }
        elseif($jenis == 'ranap') {
            $titleJenis = DocoConstants::TITLE_RI;
        }
        elseif($jenis == 'igd') {
            $titleJenis = DocoConstants::TITLE_RD;
        }
        elseif($jenis == 'mcu') {
            $titleJenis = DocoConstants::SINGKATAN_MCU;
        }
        else {
            $titleJenis = 'Penunjang';
        }

        $title = 'Unduh '.$jenisFile.' Informasi Pasien '.$titleJenis;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['jenis'] = $jenis;
        $yiiRestfulParams['ruangan'] = Yii::$app->docoVars->workspace("ruangan_id");
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }

        if(isset($yiiRestfulParams['advanced-filter']['nama_pegawai'])) {
            $yiiRestfulParams['advanced-filter']['pegawai_id'] = $yiiRestfulParams['advanced-filter']['nama_pegawai'];
            unset($yiiRestfulParams['advanced-filter']['nama_pegawai']);
        }

        $randString = DocoHelpers::generateRandomString();
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString, $tipe, $is_executive = false)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        return $this->guzzleExec($this->_restPendaftaran, [
            'url' => "inf-pasien/sync-file",
            'payload' => [
                'query' => [
                    'params' => $session,
                    'randString' => $randString,
                    'tipe' => $tipe,
                    'is_executive' => $is_executive
                ]
            ],
        ]);
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $tipe = $request->get('tipe', null);
        $fileName = $request->get('fileName', null);
        $fileDownloads = ($tipe == 'excel') ? 'Informasi Pasien.xlsx' : $fileName;
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restPendaftaran->get('inf-pasien/download-file',
        [
            'query' => [
                'fileName' => $fileName,
                'tipe' => $tipe,
            ],
            'save_to' => $path,
        ]);
        $response = json_decode($response->getBody(), true);
        if($tipe == 'excel') {
            return DocoHelpers::downloadFile($path,true);
        }
        else {
            return DocoHelpers::previewPdf($path);
        }
    }

    public function actionPengajuanSep()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $pendaftaran_id = DocoHelpers::decrypt($id);

        $username = Yii::$app->docoVars->user('nama');
        $title     = Yii::t('fe', 'Pengajuan SEP');
        $model = new PengajuanSepForm;

        if ($request->post()) {
            $model->load($request->post());
            $model->username = $username;
            $model->pendaftaran_id = $pendaftaran_id;
            if ($model->validate()) {
                $response = $this->helper->guzzleExec($this->_restPendaftaran, [
                    'url' => 'allow-bpjs/create-pengajuan-sep',
                    'method' => 'post',
                    'payload' => [
                        'form_params' => $model->attributes
                    ]
                ]);
                if ($response['metaData']['code'] == 201 || $response['metaData']['code'] == '201')
                {
                    return DocoHelpers::responseTemplate(
                        500,
                        'Error',
                        [],
                        [
                            'title' => Yii::t('fe', 'Peringatan!'),
                            'text' => $response['metaData']['message'],
                            'message' => $response['metaData']['message'],
                        ]
                    );
                }
                return DocoHelpers::response($response);
            }

            return DocoHelpers::response($model->errors,422,'PengajuanSepForm');

        } else {
            $result = $this->helper->guzzleExec($this->_restPendaftaran, [
                'url' => 'allow-bpjs/get-pengajuan-sep',
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => $pendaftaran_id
                    ]
                ]
            ]);
            $model->no_kartu = $result['pasien']['nopeserta_bpjs'];
            $model->pendaftaran_id = $id;
            $model->tgl_sep = date('Y-m-d', strtotime($result['pendaftaran']['tgl_pendaftaran']));
            $model->nama_pasien = $result['pasien']['nama_pasien'];
            return $this->renderAjax('_modal_pengajuan_sep', get_defined_vars());
        }
    }

    public function actionCreateSep()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $id = DocoHelpers::decrypt($request->get('id'));
        $jenis_pendaftaran = $request->get('jenis');

        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $this->_titleUpdate = isset($active_workspace['ruangan_name']) ? $active_workspace['ruangan_name'] : null;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_titleUpdate);
        $subtitle = \Yii::t('fe', 'Buat SEP');

        $model = new BpjsNewForm();
        if ($jenis_pendaftaran == 'ranap') {
            $model->scenario = "skdpranap";
        } else if ($jenis_pendaftaran == 'rajal') {
            $model->scenario = 'rajalsepbackdate';
        }

        if ($post) {
            $model->load($post);
            $model->info_response = Yii::$app->cache->get('peserta_'.$id) == false? null : Yii::$app->cache->get('peserta_'.$id);
           
            if ($model->validate()) {
                $response = $this->helper->guzzleExec($this->_restPendaftaran, [
                    'url' => "inf-pasien/update-create-sep",
                    'method' => 'put',
                    'payload' => [
                        'form_params' => $model->attributes,
                        'query' => [
                            'pendaftaran_id' => $id
                        ]
                    ]
                ]);

                if (isset($response['flag'])) {
                    if ($response['flag'] == self::FLAG_MODEL) {
                        $errors = $response['data'];
                        return DocoHelpers::response($errors, 422, 'BpjsNewForm');
                    } else {
                        return DocoHelpers::response([
                            'response' => $response
                        ],422);
                    }
                }

                return DocoHelpers::response($response);
            } else {
                return DocoHelpers::response($model->errors, 422, 'BpjsNewForm');
            }
        } else {
            Yii::$app->cache->delete('peserta_'.$id);

            $bundle = $this->helper->guzzleExec($this->_restPendaftaran, [
                'url' => 'inf-pasien/get-bundle-bpjs',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => $id,
                        'jenis' => $jenis_pendaftaran
                    ]
                ]
            ]);

            $data_bpjs = $bundle['data_bpjs'];
            $data_pasien = $bundle['data_pasien'];
            $tgl_sep = !empty($data_bpjs['tglsep']) ? $data_bpjs['tglsep'] : date('Y-m-d');
            $tgl_rujukan = !empty($data_bpjs['tglrujukan']) ? $data_bpjs['tglrujukan'] : date('Y-m-d');

            $model->attributes = $data_bpjs;
            $model->no_rujukan = $data_bpjs['norujukan'];
            $model->no_rujukan_f = $data_bpjs['norujukan'];
            $model->tanggal_sep = date('d-m-Y', strtotime($tgl_sep));
            $model->no_kartu = $data_bpjs['nokartuasuransi'];
            $model->tanggal_rujukan = date('d-m-Y', strtotime($tgl_rujukan));
            $model->kelas_rawat = $data_bpjs['klsrawat'];
            $model->catatan_sep = $data_bpjs['catatansep'];
            $model->tujuanKunj = $data_bpjs['tujuan_kunj'];
            $model->flagProcedure = $data_bpjs['flag_procedure'];
            $model->kdPenunjang = $data_bpjs['kd_penunjang'];
            $model->assesmentPel = $data_bpjs['assesment_pel'];
            $model->no_rekam_medik = $data_pasien['no_rekam_medik'];
            $model->no_telp = $data_pasien['no_telepon_pasien'];
            $model->jenis_rujukan = 1;
            return $this->render('form_create_sep', get_defined_vars());
        }
    }

    public function actionGetInfoBpjs()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $jenis_rujukan = $request->get('jenis_rujukan');
        $asal_rujukan = $request->get('asal_rujukan');
        $pendaftaran_id = $request->get('pendaftaran_id');
        $id = DocoHelpers::decrypt($pendaftaran_id);

        $no_rujukan = $request->get('no_rujukan');
        $no_kartu = $request->get('no_kartu');
        $tanggal_sep = $request->get('tanggal_sep');
        $jenis_kartu = $request->get('jenis_kartu');
        $result = [];

        $url = 'rujukan';
        $postAttr = [
            'nomor' => $no_rujukan,
            'asal_rujukan' => $asal_rujukan,
            'tglSEP' => date('Y-m-d', strtotime($tanggal_sep)),
        ];

        if ($jenis_rujukan == 2) {
            $url = 'peserta';
            $postAttr = [
                'nokartu' => $no_kartu,
                'tglSEP' => date('Y-m-d', strtotime($tanggal_sep)),
                'isktp' => $jenis_kartu == 1 ? 0 : 1,
            ];
        }

        $response = $this->helper->guzzleExec($this->_restPendaftaran, [
            'url' => "allow-bpjs/{$url}",
            'method' => 'post',
            'payload' => [
                'form_params' => $postAttr,
                'timeout' => 25,
            ]
        ]);
        if (isset($response['metaData']['code'])) {
            if ($response['metaData']['code'] == 201) {
                return DocoHelpers::response([
                    'response' => [
                        'title' => 'Proses Gagal!',
                        'text' => $response['metaData']['message']
                    ]
                ],422);
            } else if ($response['metaData']['code'] > 400) {
                $result = [
                    'messages' => $response['metaData']['message']
                ];
            } else {
                Yii::$app->cache->set('peserta_'.$id,isset($response['response']) ? $response['response'] : null);
                $result = isset($response['response']) ? $response['response'] : null;
            }
        } else {
            $result = [
                'messages' => 'Terjadi Kesalahan pada server bpjs'
            ];
        }

        return $result;
    }

    /**
     * @method : get data informasi pendaftaran STYP
     */
    public function actionGetDataInformasi($jenis='rajal')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];

        } else {
            $tgl_awal = $tgl_akhir = date('Y-m-d');
        }
        $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
        $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
        $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
        $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
        unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $response = $this->_restPendaftaran->get('inf-pasien/' . $jenis . '?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
        $body = json_decode($response->getBody(), True);
        // dump($body['response']['data']);die;
        $no = $request->get('start',1);
        if(!empty($body['response']['data'])) {
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['pasien_id'] = DocoHelpers::encrypt($value['pasien_id']);
                $status_kamar = '-';
                $titipan = '-';

                if($jenis == DocoConstants::PARAM_DFTR[DocoConstants::WS_RANAP]){
                    $kelasHak = empty($value['kelas_hak']) ? '-' : $value['kelas_hak'];
                    $kelasPermintaan = empty($value['kelas_permintaan']) ? '-' : $value['kelas_permintaan'];

                    if($value['carabayar_id'] == 6) {
                        $status_kamar = 'Sesuai Kelas';
                        if($value['klsrawat'] != null) {
                            if($value['is_aps'] == true) {
                                if($value['bpjs_kelas'] != null) {
                                    if($value['kelaspelayanan_id'] < $value['klsrawat']) {
                                        $status_kamar = 'APS / Naik kelas';
                                    }
                                    elseif($value['kelaspelayanan_id'] > $value['klsrawat']) {
                                        $status_kamar = 'APS / Turun kelas';
                                    }
                                    $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$titipan;
                                }
                                else {
                                    $status_kamar = 'APS / Naik kelas';
                                    $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$titipan;
                                }
                            }
                            elseif($value['is_pasientitipan'] == true) {
                                if($value['bpjs_kelas'] != null) {
                                    if($value['kelaspelayanan_id'] < $value['klsrawat']) {
                                        $status_kamar = 'Titipan / Naik kelas';
                                    }
                                    elseif($value['kelaspelayanan_id'] > $value['klsrawat']) {
                                        $status_kamar = 'Titipan / Turun kelas';
                                    }
                                    $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$titipan;
                                }
                                else {
                                    $status_kamar = 'Titipan / Naik kelas';
                                    $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$titipan;
                                }
                            }
                        }
                    } else if (!empty($value['is_pasientitipan_pk'])) {
                        if($value['is_pasientitipan_pk'] == true && $value['is_stoppasientitipan'] == false){
                            $titipan = $value['kelas_ditagihkan_nama'];
                        }
                        $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$titipan;
                    } else if (empty($value['is_pasientitipan_pk'])) {
                        if($value['is_pasientitipan'] == true && $value['is_stoppasientitipan'] == false){
                            $titipan = $value['kelas_ditagihkan_nama'];
                        }
                        $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$titipan;
                    }

                    $button = Html::a('<i class="fa fa-print"></i>', ['informasi-pasien/print-tracer-ranap', 'id' => $primaryKey],['target'=>'_blank']);
                    $value['norm'] = $value['no_rekam_medik'];
                    $value['no_rekam_medik'] = $button." ".$value['no_rekam_medik'];
                    $value['tgl_pendaftaran'] = date('d-m-Y H:i:s', strtotime($value['tgl_admisi']));
                    $value['kelaspelayanan_nama'] = $kelasHak.' / '.$kelasPermintaan.' / '.$value['kelaspelayanan_nama'];
                }

                if($jenis == DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU]) {
                    $value['alamat'] = $value['alamat_pasien'];
                    $value['jenis_kelamin'] = $value['j_kelamin'];
                    $value['nama_pegawai'] = $value['dokter_penunjang'];
                    $value['status_periksa'] = $value['status_periksa_nama'];
                    $value['jeniskasuspenyakit_nama'] = '';
                    $value['nosep'] = '';

                    if (isset($this->_is_hide_alias) && $this->_is_hide_alias != false) {
                        $value['info_pasien'] = $value['no_rekam_medik'] . '<br>' . $value['nama_pasien'] . '<br>' . (isset($value['tanggal_lahir']) ? date('d-m-Y', strtotime($value['tanggal_lahir'])) : '');
                    } else {
                        $value['info_pasien'] = $value['no_rekam_medik'] . '<br>' . (isset($value['nama_depan']) ? $value['nama_depan'] : '') . ' ' . $value['nama_pasien'] . '<br>' . (isset($value['tanggal_lahir']) ? date('d-m-Y', strtotime($value['tanggal_lahir'])) : '');
                    }
                }

                if ($jenis == DocoConstants::PARAM_DFTR[DocoConstants::WS_PENUNJANG]) {
                    $dokterPengganti = !empty($value['dokter_pengganti']) ? $value['dokter_pengganti'] : '-';
                    $value['nosep'] = array_key_exists('nosep', $value) ? $value['nosep'] : '';
                    $value['status_periksa'] = array_key_exists('nama_status_periksa', $value) ? $value['nama_status_periksa'] : '';
                    $value['nama_pegawai'] = $value['nama_pegawai'].' / '. $dokterPengganti;
                }

                $value['tgl_pendaftaran'] = date('d-m-Y H:i:s', strtotime($value['tgl_pendaftaran']));

                if (isset($this->_is_hide_alias) && $this->_is_hide_alias != false) {
                    $value['info_pasien'] = $value['no_rekam_medik'] . '<br>' . $value['nama_pasien'] . '<br>' . (isset($value['tanggal_lahir']) ? date('d-m-Y', strtotime($value['tanggal_lahir'])) : '');
                } else {
                    $value['info_pasien'] = $value['no_rekam_medik'] . '<br>' . (isset($value['nama_depan']) ? $value['nama_depan'] : '') . ' ' . $value['nama_pasien'] . '<br>' . (isset($value['tanggal_lahir']) ? date('d-m-Y', strtotime($value['tanggal_lahir'])) : '');
                }

                $value['rowNum'] = $no;
                $value['carabayar_penjamin'] = $value['carabayar_nama'] . ' / ' . $value['penjamin_nama'];
                $value['ruangan'] = ($jenis == 'ranap') ? $value['ruangan_nama'] . ' - ' . $value['kamarruangan_nokamar'] : $value['ruangan_nama'];
                $value['can_cancel'] = false;
                $value['status_kamar'] = $status_kamar;
                if (isset($value['status_periksa_id'])) {
                    if (($value['status_periksa_id'] == DocoConstants::STATUS_PERIKSA_ANTR_KASIR)
                    || ($value['status_periksa_id'] == DocoConstants::STATUS_PERIKSA_ANTR_POLI)
                    || ($value['status_periksa_id'] == DocoConstants::STATUS_RANAP_BELUM_PERIKSA)) {
                        $value['can_cancel'] = true;
                    }
                }
                if($jenis == DocoConstants::PARAM_DFTR[DocoConstants::WS_PENUNJANG] && $value['instalasiasal_id'] == DocoConstants::INSTALASI_MCU){
                    $value['can_cancel'] = false;
                }
                if(isset($value['petugas_id']) && !empty($value['petugas_id'])){
                    $value['petugas'] = (isset($value['pegpetugas_nama']) ? $value['pegpetugas_nama'] : '') . '<br>' . (isset($value['petugas_tgl_pembuat']) ? explode(" ", $value['petugas_tgl_pembuat'])[0] . '<br>' . explode(" ", $value['petugas_tgl_pembuat'])[1] : null);
                } else {
                    $value['petugas'] = $value['pembuat_nama'] . '<br>' . explode(" ", $value['tgl_pembuatan'])[0] . '<br>' . explode(" ", $value['tgl_pembuatan'])[1];
                }

                if($jenis == DocoConstants::PARAM_DFTR[DocoConstants::WS_RAJAL] || $jenis == DocoConstants::PARAM_DFTR[DocoConstants::WS_IGD]) {
                    $dokterPengganti = $value['dokter_pengganti'];
                    $value['nama_pegawai'] = !empty($dokterPengganti) ? $dokterPengganti : $value['nama_pegawai'];
                }

                $value['limit_tagihan'] = array_key_exists('limit_tagihan', $value) ? number_format($value['limit_tagihan'],0,',','.') : '';
                $value['primary'] = $primaryKey;
                $data[$key] = $value;
            }
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
        $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
        return $result;
    }
}
