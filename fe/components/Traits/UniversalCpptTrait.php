<?php

namespace app\components\Traits;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoConstants;
use app\Components\DocoHelpers;
use app\components\Pelayanan\PelayananHelpers;
use app\components\DHtml;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use yii\helpers\Html;

// models
use app\modules\ranap\models\CpptForm;
use app\modules\igd\models\CpptForm as CpptFormIgd;
use app\modules\ranap\models\VerbalOrderRanapForm;
use app\modules\ranap\models\InstruksiForm;
use app\modules\ranap\models\InstruksiTindakanForm;
use app\modules\ranap\models\ObatAlkesPasienForm;
use app\modules\ranap\models\ResepturForm;
use app\modules\ranap\models\ResepturDetailForm;
use app\modules\ranap\models\ResepturNrDetailForm;
use app\modules\ranap\models\TindakankomponenForm;
use app\modules\ranap\models\TindakanPelayananForm;
use app\modules\rajal\models\SoapRjForm;

/**
 *
 * Base Url From Separate CPPT
 *
 * igd = get-data-asesmen-dpjp
 * ranap = get-data-cppt
 * rajal = get-data-cppt
 *
 */

trait UniversalCpptTrait
{
    public $serviceRest;
    public $mainUrl;
    public $frontendUrl;
    public $backendUrl;
    public $saveEndpoint;
    public $_type;

    protected $_id_ruangan;
    protected $_ruangan_name;
    protected $_instalasi_id;
    protected $_pegawai_id;
    protected $_loginpemakai_id;
    protected $_pasien_id;
    protected $_pendaftaran_id;
    protected $_kelaspelayanan_id;
    protected $_jeniskelamin;
    protected $_data_pasien;
    protected $_statusPeriksa;
    protected $_default_depo;
    protected $_ruangan_odc;
    protected $_pasienadmisi_id;
    protected $_rencana_pulang;
    protected $_discharge_plan;
    protected $_obat_darirumah;
    protected $_user_identity;
    protected $_cacheRekon;
    protected $_list_data;
    protected $_restGeneral;
    protected $_restIgd;
    protected $_instalasiasal_id;
    protected $_instalasi;
    protected $_ruanganasal_id;
    protected $_ruangan_id;
    protected $_data_pegawai;
    protected $_data_riwayat_pasien;
    protected $_penunjangId;
    protected $_no_masukpenunjang;

    private function initServiceByType()
    {
        $serviceRest = null;
        switch ($this->_type) {
            case DocoConstants::INSTALASI_ID_RJ:
                $serviceRest = Yii::$app->docoRest->rajal;
                break;
            case DocoConstants::INSTALASI_ID_RI:
                $serviceRest = Yii::$app->docoRest->ranap;
                break;
            case DocoConstants::INSTALASI_ID_RD:
                $serviceRest = Yii::$app->docoRest->igd;
                break;
        }
        if (empty($serviceRest)) {
            return $this->responseJson(400, 'Service REST CPPT not set');
        }
        $this->serviceRest = $serviceRest;
    }

    private function initData()
    {
        $request = Yii::$app->request;
        $penunjangId = (new PelayananHelpers)->decryptId($request->get('id'));
        $this->setDefaultData($penunjangId);
        $pendId = $this->_pendaftaran_id;
        switch ($this->_type) {
            case DocoConstants::INSTALASI_ID_RJ:
                $this->initRajal($pendId);
                break;
            case DocoConstants::INSTALASI_ID_RI:
                $this->initRanap($pendId);
                break;
            case DocoConstants::INSTALASI_ID_RD:
                $this->initIgd($pendId);
                break;
        }
    }

    private function initRajal($pendId)
    {
        $this->_id_ruangan = Yii::$app->request->get('ruangan_id') ? Yii::$app->request->get('ruangan_id') : (Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1);
        $this->_ruangan_name = Yii::$app->docoVars->workspace('ruangan_name');
        $this->_instalasi_id = Yii::$app->docoVars->workspace('instalasi_id') ? Yii::$app->docoVars->workspace('instalasi_id') : 1;
        $this->_pegawai_id = Yii::$app->docoVars->user('id_pegawai') ? Yii::$app->docoVars->user('id_pegawai') : 1;
        $this->_loginpemakai_id = Yii::$app->docoVars->user('loginpemakai_id') ? Yii::$app->docoVars->user('loginpemakai_id') : 1;
        $data_pasien = [];

        $pendaftaran_id = DocoHelpers::decrypt($pendId);
        $data_pasien = [];
        $temp_pasien = Yii::$app->cache->get('pasien-pendaftaran-id-'.$pendId);
        $cache_riwayat_pasien = isset($temp_pasien['pasien_id']) ? Yii::$app->cache->get('data-riwayat-pasien-'. $temp_pasien['pasien_id']) : null;
        try {
            $cache = Yii::$app->cache;
            $cachePas = $cache->get('pasien-pendaftaran-id-' . $pendId);
            if (!empty($pendaftaran_id) && empty($cachePas) || (!empty($pendaftaran_id) && !$cache_riwayat_pasien)) {
                $resApi = $this->guzzleExec($this->serviceRest, [
                    'url' => 'tra-pemeriksaan/get-pasien',
                    'payload' => [
                        'query' => [
                            'id' => $pendaftaran_id,
                            'cppt' => true,
                            'konsulpoli_id' => Yii::$app->request->get('konsulpoliId', null),
                            'is_jenis' => Yii::$app->request->get('is_jenis', 'rj')
                        ]
                    ]
                ]);
                $data_pasien = $resApi['registration'];
                $data_pasien['askep'] = $resApi['askep'];
                if (!empty($data_pasien['askep'])) {
                    $addictionArray = explode(",", $data_pasien['askep']['ketergantungan_jenis']);
                    $data_pasien['askep']['status_merokok'] = in_array('rokok', $addictionArray);
                    $data_pasien['askep']['riwayat_penyakit_keluarga'] = '';
                    $arrayHistory = explode(",", $data_pasien['askep']['riwayat_penyakit_keluarga_list']);
                    $totalDisease = count($arrayHistory);
                    foreach ($arrayHistory as $indexDisease => $disease) {
                        $data_pasien['askep']['riwayat_penyakit_keluarga'] .= ucwords($disease) . (($indexDisease + 1) >= $totalDisease ? '' : ', ');
                    }
                } else {
                    $data['askep'] = [
                        'status_merokok' => false,
                        'riwayat_penyakit_keluarga' => '-',
                        'status_ekonomi' => '-'
                    ];
                }
                $data_pasien['cppt'] = $resApi['cppt'];
                $data_pasien['alergi'] = isset($resApi['askep']['alergi']) ? $resApi['askep']['alergi'] : null;

                // set session
                $cache->set('pasien-pendaftaran-id-' . $pendId, $data_pasien, 3600);

                if(isset($data_pasien['pasien_id']) && isset($data_pasien['riwayat_pasien'])){
                    Yii::$app->cache->set('data-riwayat-pasien-'. $data_pasien['pasien_id'], $data_pasien['riwayat_pasien'], 3600);
                }
            } else {
                $data_pasien = $cachePas;
                $data_pasien['riwayat_pasien'] = Yii::$app->cache->get('data-riwayat-pasien-' . $data_pasien['pasien_id']);
            }
            $data_pasien['ruangan_id'] = $this->_id_ruangan;
            $data_pasien['ruangan_name'] = $this->_ruangan_name;
        } catch (RequestException $e) {
            $data_pasien = [];
            $this->logError($e);
        } catch (Exception $e) {
            $data_pasien = [];
            $this->logError($e);
        }
        $this->_ruangan_odc = isset($data_pasien['ruangan_odc']) ? $data_pasien['ruangan_odc'] : null;
        $this->_pasien_id = isset($data_pasien['pasien_id']) ? $data_pasien['pasien_id'] : null;
        $this->_pendaftaran_id = $pendId;
        $this->_kelaspelayanan_id = isset($data_pasien['kelaspelayanan_id']) ? $data_pasien['kelaspelayanan_id'] : null;
        $this->_jeniskelamin = !empty($data_pasien['jeniskelamin']) ? $data_pasien['jeniskelamin'] : null;
        $this->_data_pasien = !empty($data_pasien) ? $data_pasien : [];
        $this->_statusPeriksa = isset($data_pasien['status_periksa']) ? (($data_pasien['status_periksa'] == DocoConstants::STATUS_PULANG || $data_pasien['status_periksa'] == DocoConstants::STATUS_RUJUK_RAWAT_INAP) ? $data_pasien['pasien_id'] : false) : false;
        // cathlab requirement
        // $this->instalasi = 'rajal';
        // $this->_restGeneral = $this->serviceRest;
    }

    private function initRanap($pendId)
    {
        $cache = Yii::$app->cache;
        $userIdentity = Yii::$app->session->get('user_identity');
        $docoVars = Yii::$app->docoVars;
        $this->_id_ruangan = Yii::$app->request->get('ruangan_id') ? Yii::$app->request->get('ruangan_id') : (Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1);
        $this->_instalasi_id = $docoVars->workspace('instalasi_id') ? $docoVars->workspace('instalasi_id') : 1;
        $this->_pegawai_id = $docoVars->user('id_pegawai') ? $docoVars->user('id_pegawai') : 1;
        $data_pasien = [];
		$temp_pasien = $cache->get('pasien-pendaftaran-id-' . $pendId);
		$cache_riwayat_pasien = isset($temp_pasien['pasien_id']) ? Yii::$app->cache->get('data-riwayat-pasien-'. $temp_pasien['pasien_id']) : null;
        try {
            if( (!$temp_pasien && !empty($pendId)) || empty($temp_pasien)){
                $response = $this->serviceRest->get(
                    'pemeriksaan-rawat-inap/get-pasien'
                    , [
                    'query' => [
                        'id' => (new PelayananHelpers)->decryptId($pendId),
                        'cppt' => true
                    ]
                ]);
                $response = json_decode($response->getBody(), true);
                $data_pasien = $response["response"]["data"];
                // cppt
                $data_pasien['cppt'] = !empty($response['response']['cppt']) ? $response['response']['cppt'] : [];

                // askep riwayat_penyakit_keluarga
                if (isset($data_pasien['r_penyakitkeluarga']) && $data_pasien['r_penyakitkeluarga'] != '') {
                    $r_penyakitkeluarga = json_decode($data_pasien['r_penyakitkeluarga']);

                    if (!empty($r_penyakitkeluarga)) {
                        foreach ($r_penyakitkeluarga as $key => $value) {
                            $r_penyakitkeluarga[$key] = $value->text;
                        }
                    }

                    $r_penyakitkeluarga = implode(', ', $r_penyakitkeluarga);
                    $data_pasien['askep']['riwayat_penyakit_keluarga'] = $r_penyakitkeluarga;
                } else {
                    $r_penyakitkeluarga = '-';
                    $data_pasien['askep']['riwayat_penyakit_keluarga'] = '-';
                }

                // askep status_merokok
                if (isset($data_pasien['is_merokok']) && $data_pasien['is_merokok'] == true) {
                    $merokok = Yii::t('fe', 'Ya, ').$data_pasien['jml_rokok'].Yii::t('fe', ' batang rokok perhari');
                    $data_pasien['askep']['status_merokok'] = $merokok;
                } else {
                    $merokok = Yii::t('fe', 'Tidak');
                    $data_pasien['askep']['status_merokok'] = '';
                }

                // askep status ekonomi (blank for now)
                $data_pasien['askep']['status_ekonomi'] = '-';

                $data_pasien['titipan'] = false;
                $data_pasien['kelas_ditagihkan'] = '';

                if (isset($data_pasien['pindahkamar_id']) && $data_pasien['pindahkamar_id']) {
                    if ($data_pasien['is_stoppasientitipan'] == false) {
                        if ($data_pasien['is_pasientitipan_pk']) {
                            $data_pasien['titipan'] = true;
                            $data_pasien['kelas_ditagihkan'] = strtoupper($data_pasien['kelas_ditagihkan_nama']);
                        }
                    }
                } else {
                    if ($data_pasien['is_stoptitipan'] == false) {
                        if ($data_pasien['is_pasientitipan']) {
                            $data_pasien['titipan'] = true;
                            $data_pasien['kelas_ditagihkan'] = strtoupper($data_pasien['kelas_ditagihkan_nama']);
                        }
                    }
                }

                $cache->set('pasien-pendaftaran-id-' . $pendId, $data_pasien, 3600);
				$riwayat_pasien = ArrayHelper::getValue($data_pasien, 'riwayat_pasien');
				if($riwayat_pasien){
						Yii::$app->cache->set('data-riwayat-pasien-'. $data_pasien['pasien_id'], $data_pasien['riwayat_pasien'], 3600);
				}
            }else{
                $data_pasien = $temp_pasien;
                $cache->set('pasien-pendaftaran-id-' . $pendId, $data_pasien, 3600);
                $data_pasien['riwayat_pasien'] = Yii::$app->cache->get('data-riwayat-pasien-' . ArrayHelper::getValue($data_pasien, 'pasien_id',''));
            }
        } catch (RequestException $e) {
            $data_pasien = [];
            $this->logError($e);
            throw new \yii\web\HttpException(400,Yii::t("fe","Tidak Ada Data Pasien"));
        } catch (\Exception $e) {
            $data_pasien = [];
            $this->logError($e);
            throw new \yii\web\HttpException(400,Yii::t("fe","Tidak Ada Data Pasien"));
        }
        $this->_pasien_id = isset($data_pasien['pasien_id']) ? $data_pasien['pasien_id'] : null;
        $this->_pasienadmisi_id = isset($data_pasien['pasienadmisi_id']) ? $data_pasien['pasienadmisi_id'] : null;
        $this->_kelaspelayanan_id = isset($data_pasien['kelaspelayanan_id']) ? $data_pasien['kelaspelayanan_id'] : null;
        $this->_jeniskelamin = !empty($data_pasien['jeniskelamin']) ? $data_pasien['jeniskelamin'] : null;
        $this->_data_pasien = !empty($data_pasien) ? $data_pasien : [];
        $this->_rencana_pulang = isset($data_pasien['rencana_pulang']) ? $data_pasien['rencana_pulang'] : null;
        $this->_discharge_plan = 1;
        $this->_obat_darirumah = isset($data_pasien['obat_darirumah']) ? $data_pasien['obat_darirumah'] : null;
        // kebutuhan kelompok pegawai
        $this->_user_identity = $userIdentity;

        // kebutuhan rekonsiliasi obat
        $ruangan_id = $docoVars->workspace("ruangan_id");
        $this->_cacheRekon = 'rekon-obat-' . $userIdentity['id_pegawai'] . '-'. $ruangan_id;

        // if(!$session['ranap-list-data-allow-'.$pendId]){
        //     $this->_list_data = $this->getListDataAllow();
        //     $session['ranap-list-data-allow-'.$pendId] = $this->_list_data;
        // }else{
        //     $this->_list_data = $session['ranap-list-data-allow-'.$pendId];
        // }

        // cathlab requirement
        $this->_instalasi = 'ranap';
        $this->_restGeneral = $this->serviceRest;
    }

    private function initIgd($pendId)
    {
        try {
            $docoVars = Yii::$app->docoVars;
            $this->_user_identity = Yii::$app->session->get('user_identity');
            if (empty($this->_user_identity)) return false;
            $pendaftaran_id = (new PelayananHelpers)->decryptId($pendId);
            $this->_ruangan_id = Yii::$app->request->get('ruangan_id') ? Yii::$app->request->get('ruangan_id') : (Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1);
            $this->_instalasi_id = Yii::$app->docoVars->workspace('instalasi_id') ? Yii::$app->docoVars->workspace('instalasi_id') : null;
            $this->_pegawai_id = $docoVars->user('id_pegawai') ? $docoVars->user('id_pegawai') : 1;

            $data_pasien_igd = Yii::$app->cache->get('data-pasien-igd-' . $pendaftaran_id);
            $data_pegawai = Yii::$app->cache->get('data-pegawai-' . $this->_pegawai_id);

            $data_riwayat_pasien = [];
            if(isset($data_pasien_igd['pasien_id'])){
                $data_riwayat_pasien = Yii::$app->cache->get('data-riwayat-pasien-' . isset($data_pasien_igd['pasien_id']) ? $data_pasien_igd['pasien_id'] : '');
            }
            if ((empty($data_pasien_igd) || empty($data_pegawai) || $data_pasien_igd === false || $data_pegawai === false) && !empty($pendaftaran_id)) {
                $resultApi = $this->guzzleExec($this->serviceRest, [
                    'url' => 'pemeriksaan-igd/get-api',
                    'payload' => [
                        'query' => [
                            'id' => $pendaftaran_id,
                            'pegawai_id' => $this->_pegawai_id,
                            'cppt' => true
                        ]
                    ]
                ]);

                // CPPT
                $resultApi['data_pasien']['cppt'] = !empty($resultApi['cppt']) ? $resultApi['cppt'] : [];

                $cacheDataPasien = isset($resultApi['data_pasien']) ? $resultApi['data_pasien'] : [];
                Yii::$app->cache->set('data-pasien-igd-' . $pendaftaran_id, $cacheDataPasien, 3600);
                $data_pasien_igd = $cacheDataPasien;

                $cacheDataPegawai = isset($resultApi['data_pegawai']) ? $resultApi['data_pegawai'] : [];
                Yii::$app->cache->set('data-pegawai-' . $this->_pegawai_id, $cacheDataPegawai, 3600);
                $data_pegawai = $cacheDataPegawai;

                $cacheDataRiwayat = isset($resultApi['data_riwayat_pasien']) ? $resultApi['data_riwayat_pasien'] : [];
                $cacheDataRiwayat = isset($resultApi['data_riwayat_pasien']) ? $resultApi['data_riwayat_pasien'] : [];
                if(isset($data_pasien_igd['pasien_id'])){
                    Yii::$app->cache->set('data-riwayat-pasien-' . $data_pasien_igd['pasien_id'], $cacheDataRiwayat, 30);
                }
                $data_riwayat_pasien = $cacheDataRiwayat;
            }

            $this->_data_pasien = $data_pasien_igd;
            $this->_pasien_id = isset($this->_data_pasien['pasien_id']) ? $this->_data_pasien['pasien_id'] : null;;
            $this->_jeniskelamin = !empty($data_pasien_igd['jeniskelamin']) ? $data_pasien_igd['jeniskelamin'] : null;
            $this->_kelaspelayanan_id = !empty($data_pasien_igd['kelaspelayanan_id']) ? $data_pasien_igd['kelaspelayanan_id'] : null;
            $this->_data_pegawai = $data_pegawai;
            $this->_data_riwayat_pasien = $data_riwayat_pasien;
            // cathlab requirement
            // $this->instalasi = 'igd';
            // $this->baseUrl = 'pemeriksaan-igd';
            // $this->_restGeneral = $this->serviceRest;
        } catch(\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terjadi kesalahan"));
        }
    }

    private function runCppt()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $this->_pendaftaran_id;
        switch ($this->_type) {
            case DocoConstants::INSTALASI_ID_RJ:
                $cppt = $this->CpptRajal($request, $pendaftaran_id);
                break;
            case DocoConstants::INSTALASI_ID_RI:
                $cppt = $this->CpptRanap();
                break;
            case DocoConstants::INSTALASI_ID_RD:
                $cppt = $this->CpptIgd($pendaftaran_id);
                break;
        }
        return $cppt;
    }

    private function CpptRajal($request, $pendaftaran_id)
    {
        $statepulang = $request->get('state', null);
        $data_pasien = $this->_data_pasien;
        $model = new SoapRjForm;
        $model->scenario = 'soap_cppt';
        // get existing data SOAP
        $soapExist = $this->guzzleExec($this->serviceRest, [
            'url' => 'cppt/soap',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id),
                    'pegawai_id' => $this->_pegawai_id
                ]
            ]
        ]);
        $diagnose = [
            'primary' => isset($soapExist['data']['a_diag_utama']) ? $soapExist['data']['a_diag_utama'] : null,
            'secondary' => isset($soapExist['data']['a_diag_penyerta']) ? $soapExist['data']['a_diag_penyerta'] : null,
        ];

        $model->attributes = $soapExist['data'];
        $model->tgl_soaprj = isset($soapExist['data']['tgl_soaprj']) ? date('Y-m-d H:i:00', strtotime($soapExist['data']['tgl_soaprj'])) : date('Y-m-d H:i:00');
        $model->a_diag_utama = null;
        $model->a_diag_penyerta = null;
        $model->soaprj_id = isset($soapExist['data']['soaprj_id']) ? $soapExist['data']['soaprj_id'] : null;

        $response = $this->serviceRest->get('allow/pelayanan-config-button');
        $pelayananConfigButton = json_decode($response->getBody(), true);
        $pelayananConfigButton = isset($pelayananConfigButton['response']['data']) ? $pelayananConfigButton['response']['data'] : [];
        $isPerawat = Yii::$app->docoVars->user('kelompokpegawai_id') == DocoConstants::KELOMPOK_KEPERAWATAN;

        if (!empty($pelayananConfigButton)) {
            foreach ($pelayananConfigButton as $key => $pelayanan) {
                $dataPelayanan = [];
                $dataPelayanan['name'] = $pelayanan['nama_fitur'];
                $dataPelayanan['title'] = $pelayanan['title'];
                $pelayanan['additional_data'] = json_decode($pelayanan['additional_data'], true);
                $additional = empty($pelayanan['additional_data']) ? [] : $pelayanan['additional_data'];
                $dataPelayanan = array_merge($dataPelayanan, $additional);
                if ($isPerawat && !$pelayanan['is_perawat']) {
                    $dataPelayanan['disabled'] = true;
                } else if (!$isPerawat && !$pelayanan['is_dokter']) {
                    $dataPelayanan['disabled'] = true;
                }

                if ( isset($pelayanan['additional_condition'])) {
                    $pelayanan['additional_condition'] = json_decode($pelayanan['additional_condition'], true);
                    foreach ($pelayanan['additional_condition']['data_pasien'] as $attribute => $value) {
                        if (is_array($value['values'])) {
                            foreach ($value['values'] as $val) {
                                if (isset($data_pasien[$attribute])) {
                                    if ($data_pasien[$attribute] == $val) {
                                        $dataPelayanan[$value['attr']] = $value['attr_value'];
                                    }
                                }
                            }
                        } else {
                            if (isset($data_pasien[$attribute])) {
                                if ($data_pasien[$attribute] == $value['values']) {
                                    $dataPelayanan[$value['attr']] = $value['attr_value'];
                                }
                            }
                        }
                    }
                }
                $pelayananConfigButton[$key] = $dataPelayanan;
            }
        }

        $rest_time_reset = $this->serviceRest->get('allow/time-reset-suggest-soap?kode_lookup='.DocoConstants::TIME_RESET_SUGGEST_SOAP);
        $rest_time_reset = json_decode($rest_time_reset->getBody(), true);
        $time_reset = isset($rest_time_reset['response']['data']) ? $rest_time_reset['response']['data']: 1440;

        $config_soap = $this->serviceRest->get('allow/get-konfig-system');
        $config_soap = json_decode($config_soap->getBody(), true);
        $config_soap = isset($config_soap['response']['hide_instruksi_soap']) ? $config_soap['response']['hide_instruksi_soap']: true;

        $pasien_encrypt_id = $data_pasien['pasien_id'];
        // $ruanganCppt = $this->getRuanganCppt($pendaftaran_id);

        $res_hasil_rad = Yii::$app->docoRest->radiologi->get('hasil-rad/get-total-hasil-radiologi', [
            'query' => [
                'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id),
                'pasienadmisi_id' => null,
                'is_read' => false,
                'is_with_admisi' => true,
            ],
        ]);

        $body_hasil_rad = json_decode($res_hasil_rad->getBody(), true);
        $total_belum_baca_rad = isset($body_hasil_rad['response']['total_hasil_radiologi']) ? $body_hasil_rad['response']['total_hasil_radiologi'] : 0;

        return $this->renderAjax('//universal-cppt/rajal/__cppt', [
            'encrytedPendaftaranId' => $pendaftaran_id,
            'model' => $model,
            'soapDiagnosa' => $diagnose,
            'tgl_pendaftaran' => $this->_data_pasien['tgl_pendaftaran'],
            'instalasi_id' => $this->_instalasi_id,
            'patientData' => $this->_data_pasien,
            'pasien_id' => $this->_data_pasien['pasien_id'],
            'pasienpulang_id' => $this->_data_pasien['pasienpulang_id'],
            'pelayananConfigButton' => $pelayananConfigButton,
            'id_pegawai' => $this->_pegawai_id,
            'time_reset' => $time_reset,
            'isPerawat' => $isPerawat,
            'pasien_encrypt_id' => $pasien_encrypt_id,
            'total_belum_baca_rad' => $total_belum_baca_rad,
            'id_ruangan' => $this->_id_ruangan,
            'config_soap' => $config_soap,
            '_universalCpptUrl' => $this->_universalCpptUrl,
            'ruangan_asal_id' => $this->_ruanganasal_id,
            '_no_masukpenunjang' => $this->_no_masukpenunjang,
            'ruangancppt_id' => $this->_id_ruangan,
        ]);
    }

    private function CpptRanap()
    {
        try {
            $userIdentity = $this->_user_identity;
            $classKelompokpegawai = 'hidden';
            $is_perawat = false;
            if (isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
                $classKelompokpegawai = '';
                $is_perawat = true;
            }
            $isEditTindakan = false;
            $isEditBmhp = false;
            $id_jenisinstruksi = 0;
            $data_pasien = $this->_data_pasien;

            // Deklarasi model
            $model = new CpptForm;
            $modelInstruksi = new InstruksiForm;
            $modelInstruksiTindakan = new InstruksiTindakanForm;
            $modelTindakanPelayanan = new TindakanPelayananForm;
            $modelTindakanKomponen = new TindakankomponenForm;
            $modelBmhp = new ObatAlkesPasienForm;
            $modelReseptur = new ResepturForm;
            $modelResepturDetailRacikan = new ResepturDetailForm;
            $modelResepturDetailNonRacikan = new ResepturNrDetailForm;
            $modelVerbalOrder = new VerbalOrderRanapForm;
            $disabled = (!empty($this->_data_pasien['pasienpulang_id']) || $this->_data_pasien['is_stopakomodasi'] == true) ? true : false;

            $listRuangan[$this->_data_pasien['ruangan_id'] . '@#' . @$this->_data_pasien['kamarruangan_id'] . '@#' . @$this->_data_pasien['kamartempattidur_id'] . '@#' . $this->_data_pasien['kamarruangan_nokamar'] . ' | ' . $this->_data_pasien['no_tempattidur']] = $this->_data_pasien['ruangan_nama'] . ' | ' . $this->_data_pasien['kamarruangan_nokamar'] . ' | ' . $this->_data_pasien['no_tempattidur'];

            // Get list data
            $listData = $this->getListData($this->_pasien_id, $this->_pegawai_id, $this->_id_ruangan, $this->_data_pasien['pendaftaran_id'], $this->_pasienadmisi_id);
            $tempListRuangan = ArrayHelper::map($listData['listRuangan'], 'ruangan_id', 'ruangan_nama');
            $listDiagnosa = ArrayHelper::map($listData['listDiagnosa'], 'diagnosa_id', 'diagnosa_nama');
            $listPemberiInstruksi = ArrayHelper::map($listData['listPemberiInstruksi'], 'pegawai_id', 'nama_pegawai');
            $listDataSigna = ArrayHelper::map($listData['listDataSigna'], 'signa_id', 'signa_nama');
            $listDataApotek = ArrayHelper::map($listData['listDataApotek'], 'ruangan_id', 'ruangan_nama');
            $listDataTerapi = ArrayHelper::map($listData['listDataTerapi'], 'lookup_id', 'lookup_name');
            $pegawai = $listData['pegawai'];
            $statusPulang = $listData['statusPulang'];
            $isExistTindakan = isset($listData['isExistTindakan']) ? $listData['isExistTindakan'] : 0;

            $autofill_diagnose = [];
            // Set default value ke model
            if (!is_null(@$listData['cpptInactive'])) {
                $model->scenario = CpptForm::SUBMIT_AUTO;
                $model->attributes = $listData['cpptInactive'];
                $model->a_diag_utama = null;
                $model->a_diag_penyerta = null;
                $autofill_diagnose = [
                    'primary' => ArrayHelper::getValue($listData, 'cpptInactive.a_diag_utama'),
                    'secondary' => ArrayHelper::getValue($listData, 'cpptInactive.a_diag_penyerta'),
                ];
            }
            $model->pendaftaran_id = $this->_data_pasien['pendaftaran_id'];
            $model->pasienadmisi_id = $this->_data_pasien['pasienadmisi_id'];
            $model->pasien_id = $this->_data_pasien['pasien_id'];
            $model->ruangan_id = $this->_data_pasien['ruangan_id'] . '@#' . @$this->_data_pasien['kamarruangan_id'] . '@#' . @$this->_data_pasien['kamartempattidur_id'] . '@#' . $this->_data_pasien['kamarruangan_nokamar'] . ' | ' . $this->_data_pasien['no_tempattidur'];
            $model->pegawai_id = $this->_pegawai_id;
            $model->tgl_cppt = date('Y-m-d H:i:s');

            // Set model verbal order
            $modelVerbalOrder->pendaftaran_id = $this->_data_pasien['pendaftaran_id'];
            $modelVerbalOrder->pasienadmisi_id = $this->_data_pasien['pasienadmisi_id'];
            $modelVerbalOrder->pasien_id = $this->_data_pasien['pasien_id'];
            $modelVerbalOrder->ruangan_id = $this->_data_pasien['ruangan_id'];
            $modelVerbalOrder->pegawai_id = $this->_pegawai_id;
            $modelVerbalOrder->tgl_cppt = date('Y-m-d H:i:s');

            // Set model reseptur
            $modelReseptur->pasienadmisi_id = $this->_data_pasien['pasienadmisi_id'];
            $modelReseptur->berat_badan = $this->_data_pasien['berat_badan'];
            $modelReseptur->tinggi_badan = $this->_data_pasien['tinggi_badan'];
            $modelReseptur->luas_tubuh = $this->_data_pasien['luas_permukaantubuh'];
            $modelReseptur->pegawai_id = $this->_data_pasien['dokter_admisi_id'];
            $modelReseptur->tglreseptur = date('d F, Y', strtotime("NOW"));

            // Set default value ke model instruksi
            $modelInstruksi->tgl_instruksi = date('Y-m-d H:i:s', strtotime('NOW'));

            // Assign temp list ruangan ke list ruangan
            // Cek temp list ruangan
            if (!empty($listData['listRuangan'])) {
                // Looping
                foreach ($listData['listRuangan'] as $value) {
                    // Set temp list ruangan
                    $listRuangan[$value['ruangan_id'] . '@#' . $value['kamarruangan_id'] . '@#' . $value['kamartempattidur_id'] . '@#' . $value['kamarruangan_nokamar'] . ' | ' . $value['no_tempattidur']] = $value['ruangan_nama'] . ' | ' . $value['kamarruangan_nokamar'] . ' | ' . $value['no_tempattidur'];
                }
            }

            // Add to session
            $session = Yii::$app->session;
            // Check session tindakan
            if (!isset($session['tindakan'])) {
                // Set session
                $session->set('tindakan', $this->_list_data['data_tindakanruangan']);
            }

            // Check session paket
            if (!isset($session['paket'])) {
                // Set session
                $session->set('paket', $this->_list_data['data_paket']);
            }

            // Set empty value
            $listJenisInstruksi = [];

            // Cek jenis instruksi
            if (!empty($this->_list_data['data_jenisinstruksi'])) {
                // Loop
                foreach ($this->_list_data['data_jenisinstruksi'] as $value) {
                    // Cek loginan
                    // if(isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
                    //     // Cek instruksi
                    //     if ($value['lookup_name'] == 'TINDAKAN') {
                    //         // Set
                    //         $listJenisInstruksi[] = $value;
                    //     }
                    // }
                    // else {
                    //     // Set
                    // }
                    $listJenisInstruksi[] = $value;
                }
            }
            $hide = 'show()';
            $status_disabled = $this->getStatusPeriksa($this->_data_pasien['pendaftaran_id']);
            if ($status_disabled == true) {
                $hide = 'hide()';
            }
            if (isset($listData['suggestion'])) {
                $model->subject = $listData['suggestion']['subject'] != '' ? $listData['suggestion']['subject'] : $model->subject;
                $model->object = $listData['suggestion']['object'] != '' ? $listData['suggestion']['object'] : $model->object;
                $model->planning = $listData['suggestion']['planning'] != '' ? $listData['suggestion']['planning'] : $model->planning;
            }
            $lastCppt = DocoHelpers::encrypt($listData['lastCppt']);

            $response = $this->serviceRest->get('allow/pelayanan-config-button');
            $pelayananConfigButton = json_decode($response->getBody(), true);
            $pelayananConfigButton = isset($pelayananConfigButton['response']['data']) ? $pelayananConfigButton['response']['data'] : [];
            $isPerawat = Yii::$app->docoVars->user('kelompokpegawai_id') == DocoConstants::KELOMPOK_KEPERAWATAN;

            if (!empty($pelayananConfigButton)) {
                foreach ($pelayananConfigButton as $key => $pelayanan) {
                    $dataPelayanan = [];
                    $dataPelayanan['name'] = $pelayanan['nama_fitur'];
                    $dataPelayanan['title'] = $pelayanan['title'];
                    $pelayanan['additional_data'] = json_decode($pelayanan['additional_data'], true);
                    $additional = empty($pelayanan['additional_data']) ? [] : $pelayanan['additional_data'];
                    $dataPelayanan = array_merge($dataPelayanan, $additional);
                    if ($isPerawat && !$pelayanan['is_perawat']) {
                        $dataPelayanan['disabled'] = true;
                    } else if (!$isPerawat && !$pelayanan['is_dokter']) {
                        $dataPelayanan['disabled'] = true;
                    }
                    if ($this->_data_pasien['is_stopakomodasi']) {
                        $dataPelayanan['disabled'] = true;
                    }

                    $pelayanan['additional_condition'] = json_decode($pelayanan['additional_condition'], true);
                    if (isset($pelayanan['additional_condition']['akses']) && is_array($pelayanan['additional_condition']['akses'])) {
                        $hasAkses = false;
                        foreach ($pelayanan['additional_condition']['akses'] as $module => $akses) {
                            if (DHtml::hasAkses($module, $akses)) {
                                $hasAkses = true;
                            }
                        }
                        if (!$hasAkses) {
                            $dataPelayanan['disabled'] = true;
                        }
                    }

                    if (isset($pelayanan['additional_condition']['data_pasien']) && is_array($pelayanan['additional_condition']['data_pasien'])) {
                        foreach ($pelayanan['additional_condition']['data_pasien'] as $attribute => $value) {
                            if(is_array($value['values'])) {
                                foreach ($value['values'] as $val) {
                                    if (
                                        $data_pasien[$attribute] == $val 
                                        && ((isset($value['is_stopakomodasi']) && $value['is_stopakomodasi'] == $data_pasien['is_stopakomodasi']) || !isset($value['is_stopakomodasi']))
                                    ) {
                                        $dataPelayanan[$value['attr']] = $value['attr_value'];
                                    }
                                }
                            } else {
                                if(
                                    $data_pasien[$attribute] == $value['values']
                                    && ((isset($value['is_stopakomodasi']) && $value['is_stopakomodasi'] == $data_pasien['is_stopakomodasi']) || !isset($value['is_stopakomodasi']))
                                ) {
                                    $dataPelayanan[$value['attr']] = $value['attr_value'];
                                }
                            }
                        }
                    }

                    $pelayananConfigButton[$key] = $dataPelayanan;
                }
            }

            $rest_time_reset = $this->serviceRest->get('allow/time-reset-suggest-soap?kode_lookup=' . DocoConstants::TIME_RESET_SUGGEST_SOAP);
            $rest_time_reset = json_decode($rest_time_reset->getBody(), true);
            $time_reset = isset($rest_time_reset['response']['data']) ? $rest_time_reset['response']['data'] : 1440;
            $pasien_encrypt_id = DocoHelpers::encrypt($data_pasien['pasien_id']);
            $res_hasil_rad = Yii::$app->docoRest->radiologi->get('hasil-rad/get-total-hasil-radiologi', [
                'query' => [
                    'pendaftaran_id' => $this->_data_pasien['pendaftaran_id'],
                    'pasienadmisi_id' => null,
                    'is_read' => false,
                    'is_with_admisi' => true,
                ],
            ]);

            $body_hasil_rad = json_decode($res_hasil_rad->getBody(), true);
            $total_belum_baca_rad = isset($body_hasil_rad['response']['total_hasil_radiologi']) ? $body_hasil_rad['response']['total_hasil_radiologi'] : 0;

            $pasienId = ArrayHelper::getValue($this->_data_pasien, 'pasien_id');
            $hasFisioterapiModule  = Yii::$app->docoRest->fisioterapi->getConfig('base_uri')->getPath();
            $isMustCheckProgramFisio = false;
            if (!empty($hasFisioterapiModule)) {
                $isMustCheckProgramFisio = $this->checkProgramFisio($pasienId);
            }

            $config_soap = $this->serviceRest->get('allow/get-konfig-system');
            $config_soap = json_decode($config_soap->getBody(), true);
            $config_soap = isset($config_soap['response']['hide_instruksi_soap']) ? $config_soap['response']['hide_instruksi_soap']: true;

            return $this->renderAjax('//universal-cppt/index', [
                'is_perawat' => $is_perawat,
                'id_ruangan' => $this->_id_ruangan,
                'id_instalasi' => $this->_instalasi_id,
                'pendaftaran_id' => $this->_pendaftaran_id,
                'pasien_id' => $this->_pasien_id,
                'model' => $model,
                'modelInstruksi' => $modelInstruksi,
                'modelInstruksiTindakan' => $modelInstruksiTindakan,
                'modelBmhp' => $modelBmhp,
                'modelTindakanKomponen' => $modelTindakanKomponen,
                'modelTindakanPelayanan' => $modelTindakanPelayanan,
                'modelReseptur' => $modelReseptur,
                'modelResepturDetailRacikan' => $modelResepturDetailRacikan,
                'modelResepturDetailNonRacikan' => $modelResepturDetailNonRacikan,
                'listRuangan' => $listRuangan,
                'listDiagnosa' => $listDiagnosa,
                'listPemberiInstruksi' => $listPemberiInstruksi,
                'pegawai' => $pegawai,
                'listDataSigna' => $listDataSigna,
                'listDataApotek' => $listDataApotek,
                'listDataTerapi' => $listDataTerapi,
                'listPemberiInstruksi' => $listPemberiInstruksi,
                'data_pasien' => $this->_data_pasien,
                'modelBmhp' => $modelBmhp,
                'modelTindakanKomponen' => $modelTindakanKomponen,
                'modelTindakanPelayanan' => $modelTindakanPelayanan,
                'modelVerbalOrder' => $modelVerbalOrder,
                'data_tindakanruangan' => $this->_list_data['data_tindakanruangan'],
                'data_paket' => $this->_list_data['data_paket'],
                'data_dokter' => $this->_list_data['data_dokter'],
                'data_perawat' => $this->_list_data['data_perawat'],
                'data_obatalkes' => $this->_list_data['data_obatalkes'],
                'data_satuantindakan' => $this->_list_data['data_satuantindakan'],
                'konfig' => $this->_list_data['konfig_farmasi'],
                'data_jenisinstruksi' => $listJenisInstruksi,
                'count_riwayat' => $isExistTindakan,
                'data_tindakanbmhp' => [],
                'classKelompokpegawai' => $classKelompokpegawai,
                'isEditTindakan' => $isEditTindakan,
                'isEditBmhp' => $isEditBmhp,
                'id_jenisinstruksi' => $id_jenisinstruksi,
                'statusPulang' => $statusPulang,
                'status_disabled' => $status_disabled,
                'hide' => $hide,
                'disabled' => $disabled,
                'lastCppt' => $lastCppt,
                'tgl_pendaftaran' => $this->_data_pasien['tgl_pendaftaran'],
                'cpptInactive' => @$listData['cpptInactive'],
                'autofill_diagnose' => $autofill_diagnose,
                'isStopAkomodasi' => $this->_data_pasien['is_stopakomodasi'] ? 1 : 0,
                'pelayananConfigButton' => $pelayananConfigButton,
                'time_reset' => $time_reset,
                'pasien_encrypt_id' => $pasien_encrypt_id,
                'total_belum_baca_rad' => $total_belum_baca_rad,
                'is_must_check_program_fisio' => $isMustCheckProgramFisio,
                'config_soap' => $config_soap,
                '_universalCpptUrl' => $this->_universalCpptUrl,
                '_no_masukpenunjang' => $this->_no_masukpenunjang,
                'ruangancppt_id' => $this->_id_ruangan,
            ]);
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    private function CpptIgd($id, $cppt_id=null, $draft_id = 1)
    {
        try {
            $data_pasien = $this->_data_pasien;
            $userIdentity = $this->_user_identity;
            $classPerawat = 'hidden';
            $classDokter = 'hidden';
            $buttonVerbalOrder = 'hidden';
            $isPerawat = false;
            if(isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN){
                $isPerawat = true;
                $classPerawat = '';
                $buttonVerbalOrder = '';
            }

            if(isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS){
                $classDokter = '';
                $buttonVerbalOrder = '';
            }

            $params = Yii::$app->request;
            $post = $params->post('CpptForm');
            $optDiagUtama = [];
            $valDiagPnyrt = $optDiagPnyrt = [];
            $model = new CpptFormIgd;
            $model->pendaftaran_id = $data_pasien['pendaftaran_id'];
            $model->pasien_id = $data_pasien['pasien_id'];
            $model->pegawai_id = $this->_pegawai_id;
            $model->ruangan_id = $this->_ruangan_id;
            $model->cppt_id = $cppt_id;
            $model->is_active = true;
            $pegawai_id = $this->_pegawai_id;
            if (!empty($post['cppt_id'])) {
                $model->cppt_id = DocoHelpers::decrypt($post['cppt_id']);
            }

            if (isset($post['cppt_id'])) unset($post['cppt_id']);
            $model->tgl_cppt = date('d/m/Y H:i:s', strtotime('NOW'));
            $formName = substr(strrchr(get_class($model), "\\"), 1);

            if ($draft_id == 0) {
                $model->is_active = false;
            }

            //SOAP ke db untuk data lama atau baru
            if ($model->load($post, '')) {
                $post['subject'] = nl2br($post['subject']);
                $post['object'] = nl2br($post['object']);
                $post['planning'] = nl2br($post['planning']);
                $post['is_active'] = $model->is_active;

                if (!$post['a_diag_penyerta']) {
                    $model->a_diag_penyerta = [];
                }
                if (in_array($model->a_diag_utama, $model->a_diag_penyerta)) {
                    return DocoHelpers::responseTemplate(
                        422,
                        'Error',
                        [],
                        [
                            'title' => Yii::t('fe', 'Peringatan!'),
                            'text' => Yii::t('fe', 'Diagnosa utama dan diagnosa penyerta tidak boleh sama.'),
                            'message' => Yii::t('fe', 'Diagnosa utama dan diagnosa penyerta tidak boleh sama.'),
                        ]
                    );
                }

                if($model->validate()) {
                    $request = $this->serviceRest->post('asesmen-dpjp/create-soap', [
                        'form_params' => $post,
                        'query' => [
                            'cppt_id' => $model->cppt_id
                        ]
                    ]);
                    $response = json_decode($request->getBody(), true);

                    return json_encode($response);
                } else {
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }

            //soap get sugest data for edit
            } else {
                if (!empty($cppt_id)) {
                    $request = $this->serviceRest->get('asesmen-dpjp/get-soap', [
                        'query' => [
                            'cppt_id' => DocoHelpers::decrypt($cppt_id)
                        ]
                    ]);
                } else if ($cppt_id == null) {
                    $request = $this->serviceRest->get('asesmen-dpjp/get-soap-draft', [
                        'query' => [
                            'pendaftaran_id' => DocoHelpers::decrypt($id),
                            'pegawai_id'     => $this->_pegawai_id
                        ]
                    ]);
                }

                $response = json_decode($request->getBody(), true);
                $dataCppt = isset($response['response']['data']) ? $response['response']['data'] : [];
                $diagUtama = !empty($dataCppt['a_diag_utama']) ? json_decode($dataCppt['a_diag_utama'], true) : [];
                $diagPenyerta = !empty($dataCppt['a_diag_penyerta']) ? json_decode($dataCppt['a_diag_penyerta'], true) : [];

                $idDiagUtama = $textDiagUtama = null;
                if (isset($diagUtama['id']) && $diagUtama['text']) {
                    $idDiagUtama = $diagUtama['id'] . '_' . $diagUtama['text'];
                    $textDiagUtama = $diagUtama['text'];
                }

                foreach ($diagPenyerta as $value) {
                    if (isset($value['id']) && isset($value['text'])) {
                        $idDiag = $value['id'] . '_' . $value['text'];
                        $textDiag = $value['text'];
                        $valDiagPnyrt[] = $idDiag;
                        $optDiagPnyrt[] = [$idDiag => $textDiag];
                    } else if (isset($value['text'])) {
                        $valDiagPnyrt[] = $value['text'];
                        $optDiagPnyrt[] = [$value['text'] => $value['text']];
                    }
                }

                $model->a_diag_utama = $idDiagUtama;
                $model->a_diag_penyerta = $valDiagPnyrt;
                $optDiagUtama = [$idDiagUtama => $textDiagUtama];
                $model->subject = isset($dataCppt['subject']) ? strip_tags($dataCppt['subject']) : null;
                $model->object = isset($dataCppt['object']) ? strip_tags($dataCppt['object']) : null;
                $model->planning = isset($dataCppt['planning']) ? strip_tags($dataCppt['planning']) : null;
                $model->catatan_dokter = isset($dataCppt['catatan_dokter']) ? strip_tags($dataCppt['catatan_dokter']) : null;
                $model->instruksi = isset($dataCppt['instruksi']) ? strip_tags($dataCppt['instruksi']) : null;
                $model->tgl_cppt = isset($dataCppt['tgl_cppt']) ? date('d/m/Y H:i:s', strtotime($dataCppt['tgl_cppt'])) : null;
                $requestLastCppt = $this->serviceRest->get('asesmen-dpjp/get-last-cppt', [
                    'query' => [
                        'pendaftaran_id' => DocoHelpers::decrypt($id),
                        'pegawai_id'     => $this->_pegawai_id
                    ]
                ]);
                $lastCppt = json_decode($requestLastCppt->getBody(), true);
                $lastCppt = DocoHelpers::encrypt($lastCppt['response']);
            }

            $response = $this->serviceRest->get('allow/pelayanan-config-button');
            $pelayananConfigButton = json_decode($response->getBody(), true);
            $pelayananConfigButton = isset($pelayananConfigButton['response']['data']) ? $pelayananConfigButton['response']['data'] : [];
            if(!empty($pelayananConfigButton)) {
                foreach ($pelayananConfigButton as $key => $pelayanan) {
                    $dataPelayanan = [];
                    $dataPelayanan['name'] = $pelayanan['nama_fitur'];
                    $dataPelayanan['title'] = $pelayanan['title'];
                    $pelayanan['additional_data'] = json_decode($pelayanan['additional_data'], true);
                    $additional = empty($pelayanan['additional_data']) ? [] : $pelayanan['additional_data'];
                    $dataPelayanan = array_merge($dataPelayanan, $additional);
                    if($isPerawat && !$pelayanan['is_perawat']) {
                        $dataPelayanan['disabled'] = true;
                    } else if(!$isPerawat && !$pelayanan['is_dokter']) {
                        $dataPelayanan['disabled'] = true;
                    }

                    $pelayanan['additional_condition'] = json_decode($pelayanan['additional_condition'], true);
                    foreach ($pelayanan['additional_condition']['data_pasien'] as $attribute => $value) {
                        if(is_array($value['values'])) {
                            foreach ($value['values'] as $val) {
                                if($data_pasien[$attribute] == $val) {
                                    $dataPelayanan[$value['attr']] = $value['attr_value'];
                                }
                            }
                        } else {
                            if($data_pasien[$attribute] == $value['values']) {
                                $dataPelayanan[$value['attr']] = $value['attr_value'];
                            }
                        }
                    }
                    $pelayananConfigButton[$key] = $dataPelayanan;
                }
            }
            $rest_time_reset = $this->serviceRest->get('allow/time-reset-suggest-soap?kode_lookup='.DocoConstants::TIME_RESET_SUGGEST_SOAP);
            $rest_time_reset = json_decode($rest_time_reset->getBody(), true);
            $time_reset = isset($rest_time_reset['response']['data']) ? $rest_time_reset['response']['data']: 1440;
            $pasien_encrypt_id = DocoHelpers::encrypt($data_pasien['pasien_id']);
            $res_hasil_rad = Yii::$app->docoRest->radiologi->get('hasil-rad/get-total-hasil-radiologi', [
                'query' => [
                    'pendaftaran_id' => DocoHelpers::decrypt($id),
                    'pasienadmisi_id' => null,
                    'is_read' => false,
                    'is_with_admisi' => true,
                ],
            ]);

            $body_hasil_rad = json_decode($res_hasil_rad->getBody(), true);
            $total_belum_baca_rad = isset($body_hasil_rad['response']['total_hasil_radiologi']) ? $body_hasil_rad['response']['total_hasil_radiologi'] : 0;

            $id_ruangan = $this->_ruangan_id;

            $config_soap = $this->serviceRest->get('allow/get-konfig-system');
            $config_soap = json_decode($config_soap->getBody(), true);
            $config_soap = isset($config_soap['response']['hide_instruksi_soap']) ? $config_soap['response']['hide_instruksi_soap']: true;
            $_no_masukpenunjang = $this->_no_masukpenunjang;

            return $this->renderAjax('//universal-cppt/asesmen-dpjp/index', get_defined_vars());
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionCppt()
    {
        $this->_restIgd = Yii::$app->docoRest->igd; /** Kebutuhan run cppt ranap */
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $penunjangId = (new PelayananHelpers)->decryptId($id);
        $this->_penunjangId = $penunjangId;
        $this->setDefaultData();
        $this->initServiceByType();
        $this->initData();
        return $this->runCppt();
    }

    public function setDefaultData()
    {
        $penunjangId = $this->_penunjangId;
        $response = $this->_restDcms->get('allow/get-data-penunjang', ['query' => ['id' => $penunjangId]]);
        $body = json_decode($response->getBody(), TRUE);
        $data = isset($body['response']['data']) ? $body['response']['data'] : [];
        if(!empty($data)){
            $this->_pendaftaran_id = !empty($data['pendaftaran_id']) ? (new PelayananHelpers)->encryptId($data['pendaftaran_id']) : null;
            $this->_instalasiasal_id = isset($data['is_ranap']) && $data['is_ranap'] == true ? DocoConstants::INSTALASI_ID_RI : ArrayHelper::getValue($data, 'instalasiasal_id', null);
            $this->_type = $this->_instalasiasal_id;
            $this->_ruanganasal_id = !empty($data['ruanganasal_id']) ? $data['ruanganasal_id'] : null;
            $this->_no_masukpenunjang = !empty($data['no_masukpenunjang']) ? $data['no_masukpenunjang'] : null;
        }

        return $data;
    }
    
    public function setTypePendaftaran($pendaftaran_id, $no_masukpenunjang)
    {
        $response = $this->_restDcms->get('allow/get-penunjang-by-pendaftaran', ['query' => [
            'id' => $pendaftaran_id,
            'no_masukpenunjang' => $no_masukpenunjang,
        ]]);
        $body = json_decode($response->getBody(), TRUE);
        $data = isset($body['response']['data']) ? $body['response']['data'] : [];
        if(!empty($data)){
            $this->_type = isset($data['is_ranap']) && $data['is_ranap'] == true ? DocoConstants::INSTALASI_ID_RI : ArrayHelper::getValue($data, 'instalasiasal_id', null);
        }
    }

    private function getListData($pasienId, $pegawaiId, $ruanganId, $pendaftaranId, $pasienadmisiId = null)
    {
        try {
            $request = $this->serviceRest->get('cppt/get-list-data', [
                'form_params' => [],
                'query' => [
                    'pasien_id' => $pasienId,
                    'pegawai_id' => $pegawaiId,
                    'ruangan_id' => $ruanganId,
                    'pendaftaran_id' => $pendaftaranId,
                    'pasienadmisi_id' => $pasienadmisiId
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            $bodyResponse = $response['response'];
            return $bodyResponse;
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    private function getStatusPeriksa($pendaftaran_id)
    {
        $getPasien = $this->serviceRest->get('pemeriksaan-rawat-inap/get-pasien?id='.$pendaftaran_id, ['form_params' => []]);
        $DataBody = json_decode($getPasien->getBody(), True);
        $data_pasien = $DataBody['response']['data'];
        $result = false;
        if(!empty($data_pasien)){
            if($data_pasien['pasienpulang_id'] != NULL){
                $result = true;
            }
        }
        return $result;
    }

    private function checkProgramFisio($pasienId)
    {
        $helpers = new DocoHelpers();
        $response = $helpers->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'method' => 'GET',
            'url' => 'program-fisioterapi/check-sisa-program-ranap',
            'payload' => [
                'query' => ['pasien_id' => $pasienId]
            ],
        ]);
        $countSisaAllProgram = ArrayHelper::getValue($response, 'data.count_sisa_all_program', 0);
        $isMustConfirm = false;
        if ($countSisaAllProgram > 0) {
            $isMustConfirm = true;
        }
        return $isMustConfirm;
    }

    public function getCpptRajal($params, $pendaftaran_id)
    {
        // Try catch
        try {
            // Inisiasi
            Yii::$app->response->format = Response::FORMAT_JSON;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params->get());

            $draw = $params->get('draw', 1);
            $data = [];
            $source = $params->get('source', 'cppt');
            $kelompokpegawai_id = $params->get('kelompokpegawai_id', null);
            $ruangan_id = $params->get('ruangan_id', null);
            $pegawai_id = $params->get('pegawai_id', null);
            $tgl_cppt = $params->get('tanggal_cppt', null);

            // Inisiasi result
            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            // Get request
            $request = $this->serviceRest->get('cppt/index?pendaftaran_id=' . $pendaftaran_id . '&ruangan_id=' . $ruangan_id . '&pegawai_id=' . $pegawai_id . '&' . http_build_query($yiiRestfulParams) . '&source=' . $source .'&tgl_cppt='.$tgl_cppt."&filter_kelompokpegawai_id={$kelompokpegawai_id}&filter_pasien=true", ['form_params' => []]);
            $response = json_decode($request->getBody(), true);

            // Inisiasi nomor
            $no = $params->get('start', 1);
            $numbering = 0;
            // Loop untuk membuat array dari response
            $instruksiDpjp = [];

            if (isset($response['response']["data"]['soap'])) {
                foreach ($response['response']["data"]['soap'] as $k_data => $v_data) {
                    $no++;
                    $is_icd_x = isset($v_data['is_icd_x']) ? $v_data['is_icd_x'] : true;
                    $tgl_edit = isset($v_data['created_date']) ? date('d/m/Y / H:i:s', strtotime($v_data['created_date'])) : '';
                    // hapus btn cppt belum bisa muncul karena hak akses
                    $hapusCpptButton = DHtml::button('<b><i class=\'fa fa-trash\'></i></b> Hapus', [
                        'akses' => 'delete-diagnosa',
                        'data-soaprj_id' => $v_data['soaprj_id'],
                        'class' => 'btn btn-danger btn-labeled btn-xs btn-delete-cppt'
                    ]);
                    
                    $kelompokpegawai_id = $v_data['kelompokpegawai_soap_id'] != null ?  $v_data['kelompokpegawai_soap_id'] : $v_data['kelompokpegawai_id'];
                    $spesialis_nama = $kelompokpegawai_id == DocoConstants::KELOMPOK_MEDIS ? strtolower($v_data['spesialis_nama']) : '';

                    $cpptData = [
                        'no' => '<p >'.$no.'</p>',
                        'primary' => DocoHelpers::encrypt($pendaftaran_id),
                        'ruangan' => '<p >'. @$v_data['ruangan_nama'] . '</p><hr> <p >' . (isset($v_data['tgl_soaprj'])
                            ? date('d/m/Y / H:i:s', strtotime($v_data['tgl_soaprj'])) : '-') . '</p><hr><p >' . @$v_data['kelompokpegawai_nama'] . ($spesialis_nama != '' ? ' - ' . ucwords(strtolower($spesialis_nama)) : '') . '</p><br><p>' . @$v_data['nama_pegawai'].'</p>',
                        'tgl_soaprj' => '<p >'.strtotime($v_data['tgl_soaprj']).'</p>',
                        'penatalaksanaan' => $this->getPenatalaksanaanRajal($v_data),
                        'ruangan_nama' => @$v_data['ruangan_nama'],
                        'instruksi' => '<div style="white-space: pre-line" >' . htmlspecialchars(str_replace('<br />'," ", @$v_data['verbal_instruksi'])) . '</div>',
                        'aksi' => $v_data['last_modified_by'] == ($this->_loginpemakai_id) ? (($v_data['is_pulang'] == false) ? (($v_data['is_deleted_soap']) ? '<p > Data sudah di ubah oleh <br>'.$v_data['pegawai_update_nama'].' - <br>'.$tgl_edit.'</p>' : "
                        <button class='btn btn-info btn-labeled btn-xs edit-cppt' data-soaprj_id='".$v_data['soaprj_id']."' data-is_icd_x='".$v_data['is_icd_x']."'><b><i class='fa fa-edit'></i></b> Edit</button>
                        <button class='btn btn-danger btn-labeled btn-xs batal-edit-cppt' data-soaprj_id='".$v_data['soaprj_id']."'><b><i class='fa fa-close'></i></b> Batal Edit</button>
                        ".$hapusCpptButton."
                        ") : (($v_data['is_pulang'] == true) ? (($v_data['is_deleted_soap']) ? '<p > Data sudah di ubah oleh <br>'.$v_data['pegawai_update_nama'].' - <br>'.$tgl_edit.'</p>' : '' ) : '')) : '',
                        'rawData' => [
                            'soaprj_id' => [
                                'formId' => 'soaprjform-soaprj_id',
                                'value' => $v_data['soaprj_id']
                            ],
                            'pendaftaran_id' => [
                                'formId' => 'soaprjform-pendaftaran_id',
                                'value' => DocoHelpers::encrypt($pendaftaran_id),
                            ],
                            'diagnosa_utama' => [
                                'formId' => 'soaprjform-a_diag_utama',
                                'value' => !empty($v_data['a_diag_utama']) ? json_decode($v_data['a_diag_utama'], true) : []
                            ],
                            'diagnosa_penyerta' => [
                                'formId' => 'soaprjform-a_diag_penyerta',
                                'value' => !empty($v_data['a_diag_penyerta']) ? json_decode($v_data['a_diag_penyerta'], true) : []
                            ],
                            'subject' => [
                                'formId' => 'soaprjform-subject',
                                'value' => str_replace('<br />'," ", $v_data['subject'])
                            ],
                            'object' => [
                                'formId' => 'soaprjform-object',
                                'value' => str_replace('<br />'," ", $v_data['object'])
                            ],
                            'planning' => [
                                'formId' => 'soaprjform-planning',
                                'value' => str_replace('<br />'," ", $v_data['planning'])
                            ],
                            'catatan_dokter' => [
                                'formId' => 'soaprjform-catatan_dokter',
                                'value' => str_replace('<br />'," ", ArrayHelper::getValue($v_data, 'catatan_dokter', null))
                            ],
                            'tgl_soaprj' => [
                                'formId' => 'soaprjform-tgl_soaprj',
                                'value' => ArrayHelper::getValue($v_data, 'tgl_soaprj', null)
                            ],
                            'instruksi' => [
                                'formId' => 'soaprjform-instruksi',
                                'value' => str_replace('<br />'," ", ArrayHelper::getValue($v_data, 'verbal_instruksi', null))
                            ],
                            'is_deleted_soap' => [
                                'value' => ArrayHelper::getValue($v_data, 'is_deleted_soap', null)
                            ],
                            'is_dokter' => [
                                'value' => $kelompokpegawai_id == DocoConstants::KELOMPOK_MEDIS ? true : false
                            ],
                            'is_icd_x' => [
                                'value' => $is_icd_x
                            ],
                        ]
                    ];
                    $data[] = $cpptData;
                }
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $response['response']['totalCount'];
            $result['recordsFiltered'] = $response['response']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
    
    public function getCpptRanap($params, $pendaftaran_id)
    {
        // Try catch
        try {
            // Inisiasi
            Yii::$app->response->format = Response::FORMAT_JSON;
            $userIdentity = Yii::$app->session->get('user_identity');
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params->get());
            $draw = $params->get('draw', 1);
            $data = $dataPagt = $newData = $data_cppt = [];
            $ruangan_id = $params->get('ruangan_id', null);
            $pegawai_id = $params->get('pegawai_id', null);
            $kelompokpegawai_id = $params->get('kelompokpegawai_id', null);
            $tgl_cppt = $params->get('tanggal_cppt', null);
            $order = 0;

            // Inisiasi result
            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            // Menghilangkan q
            if (isset($yiiRestfulParams['q'])) {
                // Unset q
                unset($yiiRestfulParams['q']);
            }

            // Menghilangkan filters
            if (isset($yiiRestfulParams['filters'])) {
                // Unset filters
                unset($yiiRestfulParams['filters']);
            }

            if(isset($yiiRestfulParams['order']) && $yiiRestfulParams['order'] == 'tgl_soaprj DESC'){
                $order = 1;
            }
            // Get request
            $response = $this->serviceRest->get('cppt/index?pendaftaran_id='.$pendaftaran_id.'&'. '&ruangan_id=' . $ruangan_id . '&pegawai_id=' . $pegawai_id . '&' . http_build_query($yiiRestfulParams) .'&tgl_cppt='.$tgl_cppt."&filter_kelompokpegawai_id={$kelompokpegawai_id}", ['form_params' => []]);
            $response = json_decode($response->getBody(), true);
            // Inisiasi nomor
            $no = $params->get('start', 1);

            $hide = 'show()';
            $status_disabled = $this->getStatusPeriksa($pendaftaran_id);
            if ($status_disabled == true) {
                $hide = 'hide()';
            }


            // Loop untuk membuat array dari response
            if (!empty($response['response']['data'])) {
                $html = '';
                foreach ($response['response']["data"] as $key => $value) {
                    // Assign data
                    $no++;
                    $value['no'] = $no;
                    $data[$key] = $value;
                    $data[$key]['user_identity'] = $userIdentity;
                    if ($data[$key]['is_gizi']) {
                        $tgl_cppt = $value['tgl_cppt'];
                        $data[$key]['primary'] = DocoHelpers::encrypt($value['pagt_id']);
                        $data[$key]['another_format_tgl_pagt'] = date('Y-m-d', strtotime($value['origin_tgl_cppt'])) . ' ' . date('H:i', strtotime($value['origin_tgl_cppt']));
                        $data[$key]['profesi'] = $value['kelompokpegawai_nama'] . '<br>' . $value['pegawai_nama'];
                        $data[$key]['ruangan'] = $tgl_cppt . '<hr/>' . $data[$key]['profesi'];
                        $data[$key]['penatalaksanaan'] = isset($value['hasil_asesmen']) ? $value['hasil_asesmen'] : '';
                        $data[$key]['is_verifikasi'] = $value['is_verifikasi'];
                        $data[$key]['tgl_cppt'] = $tgl_cppt;
                        $data[$key]['subject'] = '';
                        $data[$key]['object'] = '';
                        $data[$key]['a_diag_utama'] = '';
                        $data[$key]['a_diag_penyerta'] = '';
                        $data[$key]['planning'] = '';
                        $data[$key]['catatan_dokter'] = '';
                        $data[$key]['catatan_perawat'] = '';
                        $data[$key]['instruksi_soap'] = '';
                        $data[$key]['instruksi'] = '';
                        $data[$key]['is_deleted'] = false;
                        $data[$key]['origin_tgl_cppt'] = date('Y-m-d', strtotime($value['origin_tgl_cppt'])) . ' ' . date('H:i', strtotime($value['origin_tgl_cppt']));;
                        $data[$key]['is_cppt'] = false;
                        $data[$key]['pegawai_verifikasi_nama'] = $value['pegawai_verifikasi_nama'];
                        $data[$key]['tgl_verifikasi'] = !empty($valueGizi['tgl_verifikasi']) ? date('d/m/Y / H:i:s', strtotime($value['tgl_verifikasi'])) : '-';
                        if ($data[$key]['is_verifikasi']) {
                            $html .= '<span>' . $data[$key]['pegawai_verifikasi_nama'] . ' ' . $data[$key]['tgl_verifikasi'] . '</span>';
                            $html .= '<br>';
                        } else {
                            $pegawai_id = $this->_pegawai_id;
                            $dpjp = $value['dokteradmisi_id'];
                            if ($pegawai_id == $dpjp && $this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
                                $html .= Html::button('<b><i class="fa fa-check-square"></i></b>' . Yii::t('fe', 'Verifikasi DPJP'), [
                                    'class' => 'btn btn-info btn-labeled btn-xs btn-verifikasi-dpjp',
                                    'style' => 'margin-top:10px',
                                    'data-id' => $value['pagt_id'],
                                    'data-cpptid' => DocoHelpers::encrypt($value['pagt_id']),
                                    'data-ispagt' => 1,
                                    'data-confirm-message' => Yii::t('fe', 'Apakah anda yakin akan memverifikasi PAGT tersebut?'),
                                ]);
                            }
                        }
                        $data[$key]['verifikasi'] = $html;
                    } else {
                        $data[$key]['primary'] = DocoHelpers::encrypt($value['origin_cppt_id']);
                        $data[$key]['tgl_cppt'] = date('d/m/Y / H:i:s', strtotime($value['tgl_cppt']));
                        $data[$key]['another_format_tgl_cppt'] = date('Y-m-d', strtotime($value['tgl_cppt'])) . ' ' . date('H:i', strtotime($value['tgl_cppt']));
                        $data[$key]['profesi'] = $value['kelompokpegawai_nama'] . '<br>' . $value['nama_pegawai'];
                        $data[$key]['ruangan'] = $value['ruangan_nama'] . ' ' . $value['no_tempattidur'] . ' ' . $value['kamarruangan_nokamar'] . '<hr/>' . $data[$key]['tgl_cppt'] . '<hr/>' . $data[$key]['profesi'];
                        $data[$key]['tgl_soaprj'] = $value['tgl_cppt'];
                        $data[$key]['penatalaksanaan'] = $this->getPenatalaksanaan($value);
                        $data[$key]['verifikasi'] = $this->getVerifikasi($value, [], $status_disabled);
                        $data[$key]['is_verifikasi'] = $value['is_verifikasi'];
                        $data[$key]['subject'] = str_replace('<br />'," ", $value['subject']);
                        $data[$key]['object'] = str_replace('<br />'," ", $value['object']);
                        $data[$key]['a_diag_utama'] = $value['a_diag_utama'];
                        $data[$key]['a_diag_penyerta'] = $value['a_diag_penyerta'];
                        $data[$key]['planning'] = str_replace('<br />'," ", $value['planning']);
                        $data[$key]['catatan_dokter'] = str_replace('<br />'," ", $value['catatan_dokter']);
                        $data[$key]['catatan_perawat'] = str_replace('<br />'," ", $value['catatan_perawat']);
                        $data[$key]['instruksi'] = str_replace('<br />'," ", $value['instruksi']);
                        $data[$key]['instruksi_soap'] = '<div style="white-space: pre-line">' . $value['instruksi'] . '</div>';
                        $data[$key]['is_deleted'] = $value['is_deleted'];
                        $data[$key]['origin_tgl_cppt'] = $value['tgl_cppt'];
                        $data[$key]['origin_cppt_id'] = $this->helper->encrypt($value['origin_cppt_id']);
                        $data[$key]['is_cppt'] = true;
                        $data[$key]['is_dokter'] = $value['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS ? true : false;
                        $data[$key]['is_icd_x'] = isset($value['is_icd_x']) ? $value['is_icd_x'] : true;
                    }
                }
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $response['response']['totalCount'];
            $result['recordsFiltered'] = $response['response']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDataCppt()
    {
        try {
            $params = Yii::$app->request;
            $pendaftaran_id = DocoHelpers::decrypt($params->get('id'));
            $no_masukpenunjang = $params->get('no_masukpenunjang');
            $this->_loginpemakai_id = Yii::$app->docoVars->user('loginpemakai_id') ? Yii::$app->docoVars->user('loginpemakai_id') : 1;
            $this->setTypePendaftaran($pendaftaran_id, $no_masukpenunjang);
            $this->initServiceByType();
            switch ($this->_type) {
                case DocoConstants::INSTALASI_ID_RJ:
                    return $this->getCpptRajal($params, $pendaftaran_id);
                    break;
                case DocoConstants::INSTALASI_ID_RI:
                    return $this->getCpptRanap($params, $pendaftaran_id);
                    break;
            }
        } catch (\Exception $e) {
            Yii::error([
                'File' => $e->getFile(),
                'Line' => $e->getLine(),
                'Message' => $e->getMessage()
            ]);
            return [
                'File' => $e->getFile(),
                'Line' => $e->getLine(),
                'Message' => $e->getMessage()
            ];
        }
    }

    private function getPenatalaksanaanRajal($data)
    {
        $pegawai_id = $this->_pegawai_id;
        $userIdentity = Yii::$app->session->get('user_identity');
        // Deklarasi html
        $html = '<table border="0" cellpadding="0" cellspacing="0">';
        // Cek subjek
        if (isset($data['subject']) && $data['subject'] != '') {
            // normalize string
            $data['subject'] = str_replace('<br />'," ", $data['subject']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b >Subjektif :</b><br/><div style="white-space: pre-line" >' . htmlspecialchars($data['subject']) . '</div></td>';
            $html .= '</tr>';
            // $html .= '<tr>';
            // $html .= '<td></td>';
            // $html .= '</tr>';
        }

        // Cek objek
        if (isset($data['object']) && $data['object'] != '') {
            // normalize string
            $data['object'] = str_replace('<br />'," ", $data['object']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b >Objektif :</b><br/><div style="white-space: pre-line" >' . htmlspecialchars($data['object']) . '</div></td>';
            $html .= '</tr>';
            // $html .= '<tr>';
            // $html .= '<td></td>';
            // $html .= '</tr>';
        }

        // Cek subject
        if ((isset($data['a_diag_utama']) && $data['a_diag_utama'] != '') || (isset($data['a_diag_penyerta']) && $data['a_diag_penyerta'] != '')) {
            $html .= '<tr style="line-height:130%;">';
            if (isset($data['a_diag_utama']) && $data['a_diag_utama'] != '') {
                $diag_utama = json_decode($data['a_diag_utama'], true);
                if (!empty($diag_utama['text'])) {
                    if($data['is_icd_x']){
                        $html .= '<td><b>Diagnosa Utama:</b><br/>'.wordwrap(@$diag_utama['text'],70,"\n") .'</td>';
                    }
                    else{
                        $html .= '<td><b>Diagnosa Utama:</b><br/><div style="white-space: pre-line">'.htmlspecialchars($diag_utama['text']).'</div></td>';
                    }
                    $html .= '</tr>';
                }
            }
            // Cek asesmen
            if (isset($data['a_diag_penyerta']) && $data['a_diag_penyerta'] != '') {
                // Encode
                if (!is_array($data['a_diag_penyerta'])) {
                    $diagnosaPenyerta = json_decode($data['a_diag_penyerta'], true);
                    if (!is_array($diagnosaPenyerta)) {
                        $diagnosaPenyerta = json_decode($diagnosaPenyerta, true);
                    }
                } else {
                    $diagnosaPenyerta = $data['a_diag_penyerta'];
                }
                // Cek diagnosa
                if ($diagnosaPenyerta != '') {
                    $html .= '<tr style="line-height:130%;">';
                    $html .= '<td><b >' . Yii::t('fe', 'Diagnosa Penyerta') . ' :</b><br/>';
                    foreach ($diagnosaPenyerta as $valueDiagnosaPenyerta) {
                        $html .= '&nbsp;&nbsp;&nbsp;- ' . $this->helper->purifyHtml(@$valueDiagnosaPenyerta['text']) . '<br/>';
                    }
                    $html .= '</td></tr>';
                }
            }
        }

        // Cek planning
        if (isset($data['planning']) && $data['planning'] != '') {
            // normalize string
            $data['planning'] = str_replace('<br />'," ", $data['planning']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b >Planning :</b><br/><div style="white-space: pre-line" >' . htmlspecialchars($data['planning']) . '</div></td>';
            $html .= '</tr>';
        }

        // Cek catatan dokter
        if (isset($data['catatan_dokter']) && $data['catatan_dokter'] != '') {
            // normalize string
            $data['catatan_dokter'] = str_replace('<br />'," ", $data['catatan_dokter']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>' . Yii::t('fe', 'Catatan Dokter') . ' :</b><br/><div style="white-space: pre-line">' . htmlspecialchars($data['catatan_dokter']) . '</div></td>';
            $html .= '</tr>';
        }

        // Cek Verbal Order
        if (isset($data['pemberi_instruksi_nama']) && !empty($data['pemberi_instruksi_nama'])) {
            // Set html
            // if ($pegawai_id == $data['pemberi_instruksi_id'] && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
            if ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
                $html .= '<tr style="line-height:130%;">';
                $html .= '<td><b>Pemberi Instruksi :</b><br/>' . $this->helper->purifyHtml($data['pemberi_instruksi_nama']) . '</td>';
                $html .= '</tr>';
            }
            // }
        }

        // Set end tag html
        $html .= '</table>';

        // Return
        return $html;
    }

    private function getPenatalaksanaan($data)
    {
        // Check if related cppt has ordered lab/rad/reseptur
        $hasLab = !empty($data['is_lab']) && $data['is_lab'] ? '* Pasien dilakukan pemeriksaan laboratorium <br/>' : '';
        $hasRad = !empty($data['is_rad']) && $data['is_rad'] ? '* Pasien dilakukan pemeriksaan radiologi <br/>' : '';
        $hasResep = !empty($data['is_reseptur']) && $data['is_reseptur'] ? '* Pasien diberikan resep <br/>' : '';
        $hasKonsul = !empty($data['is_konsul']) && $data['is_konsul'] ? '* Pasien dikonsulkan <br/>' : '';

        // Deklarasi html
        $html = '<div class="wrapper"><table border="0" cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse;">';

        // Cek subjek
        if (isset($data['subject']) && $data['subject'] != '') {
            // normalize string
            $data['subject'] = str_replace('<br />'," ", $data['subject']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Subjektif :</b><br><div style="white-space: pre-line">' . htmlspecialchars($data['subject']) . '</div></td>';
            $html .= '</tr>';
            // $html .= '<tr>';
            // $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;"></td>';
            // $html .= '</tr>';
        }

        // Cek objek
        if (isset($data['object']) && $data['object'] != '') {
            // normalize string
            $data['object'] = str_replace('<br />'," ", $data['object']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Objektif :</b><br/><div style="white-space: pre-line">' . htmlspecialchars($data['object']) . '</div></td>';
            $html .= '</tr>';
            // $html .= '<tr>';
            // $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;"></td>';
            // $html .= '</tr>';
        }

        // Cek subject
        if (($data['subject'] != '') && ($data['object'] != '') && ($data['planning'] != '')) {
            // Cek asesmen
            if (isset($data['a_diag_utama']) && $data['a_diag_utama'] != '') {
                $diag_utama = json_decode($data['a_diag_utama'], TRUE);
                // $diag_utama = $data['a_diag_utama'];
                // Set html
                $html .= '<tr style="line-height:130%;">';

                if($data['is_icd_x']){
                    $html .= '<td><b>Asesmen Diagnosa Utama :</b><br/>' . @$diag_utama['text'] . '</td>';
                }
                else{
                    $html .= '<td><b>Asesmen Diagnosa Utama:</b><br/><div style="white-space: pre-line">'.htmlspecialchars($diag_utama['text']).'</div></td>';
                }
                $html .= '</tr>';
                // $html .= '<tr>';
                // $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;"></td>';
                // $html .= '</tr>';
            }

            // Cek asesmen
            if (isset($data['a_diag_penyerta']) && $data['a_diag_penyerta'] != '') {
                // Encode
                $diagnosaPenyerta = json_decode($data['a_diag_penyerta'], TRUE);
                if (is_string($diagnosaPenyerta)) {
                    $diagnosaPenyerta = json_decode($diagnosaPenyerta, TRUE);
                }
                // $diagnosaPenyerta = $data['a_diag_penyerta'];

                // Cek diagnosa
                if ($diagnosaPenyerta != '') {
                    // Inisialisasi counter
                    $counter = 0;

                    // Loop
                    $html .= '<tr style="line-height:130%;">';
                    $html .= '<td> <b>' . Yii::t('fe', 'Diagnosa Penyerta') . '</b><br/>';
                    foreach ($diagnosaPenyerta as $valueDiagnosaPenyerta) {
                        // Cek counter
                        $html .= '&nbsp;&nbsp;&nbsp;- ' . @$valueDiagnosaPenyerta['text'] . '<br/>';

                        // Plus the counter
                        $counter++;
                    }
                    $html .= '<td/></tr>';
                } else {
                    // Set strip
                    $html .= '<td>-</td>';
                }

                // Close tag
                $html .= '</tr>';
            }
        } else {
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;">' . $data['instruksi'] . '<br>' . $data['pegawai_instruksi'] . '</td>';
            $html .= '</tr>';
        }

        // Cek penanda order penunjang
        if (!empty($hasLab) || !empty($hasRad) || !empty($hasResep) || isset($data['planning'])) {
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Planning:</b> <br/>';

            // Cek planning
            if (isset($data['planning']) && $data['planning'] != '') {
                // normalize string
                $data['planning'] = str_replace('<br />'," ", $data['planning']);
                // Set html
                $html .= '<div style="white-space: pre-line">' . htmlspecialchars($data['planning']) . '</div>';
                // $html .= '<tr>';
                // $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;"></td>';
                // $html .= '</tr>';
            }

            $html .= '<br/>' . $hasLab . $hasRad . $hasResep . $hasKonsul;
            $html .= '</td></tr>';
        }

        // Cek catatan dokter
        if (isset($data['catatan_dokter']) && $data['catatan_dokter'] != null) {
            // normalize string
            $data['catatan_dokter'] = str_replace('<br />'," ", $data['catatan_dokter']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td style="vertical-align: top;"><b>' . Yii::t('fe', 'Catatan') . '</b><br/><div style="white-space: pre-line">' . htmlspecialchars($data['catatan_dokter']) . '</div></td>';
            $html .= '</tr>';
            // $html .= '<tr>';
            // $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;"></td>';
            // $html .= '</tr>';
        }

        // Cek catatan perawat
        if (isset($data['catatan_perawat']) && $data['catatan_perawat'] != null) {
            // normalize string
            $data['catatan_perawat'] = str_replace('<br />'," ", $data['catatan_perawat']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td style="vertical-align: top;"><b>' . Yii::t('fe', 'Catatan') . '</b><br/><div style="white-space: pre-line">' . htmlspecialchars($data['catatan_perawat']) . '</div></td>';
            $html .= '</tr>';
            // $html .= '<tr>';
            // $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;"></td>';
            // $html .= '</tr>';
        }

        // Cek instruksi pulang
        if (isset($data['is_instruksi_pulang']) && $data['is_instruksi_pulang'] == true) {
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td>' . Yii::t('fe', 'Instruksi Pulang') . '<br/>' . Yii::t('fe', 'Ya') . '</td>';
            $html .= '</tr>';
            // $html .= '<tr>';
            // $html .= '<td></td>';
            // $html .= '</tr>';
        }

        // Set end tag html
        $html .= '</table></div>';

        // Return
        return $html;
    }

    private function getVerifikasi($data, $data_instruksi, $status_disabled)
    {
        $pegawai_id = $this->_pegawai_id;
        $dpjp = $data['dokteradmisi_id'];
        $pemberi_instruksi = $data['pemberi_instruksi_id'];
        $html = '';
        if (!$data['is_deleted']) {
            // cek verifikasi verbal order
            if ($data['instruksi']) {
                if ($data['is_verifikasi_verbal']) {
                    $html .= '<span>' . $data['pegawai_verifikasi_verbal'] . ' ' . date('d/m/Y H:i:s', strtotime($data['tgl_verif_verbal'])) . '</span>';
                } else {
                    if ($pegawai_id == $pemberi_instruksi && $this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
                        $html .= Html::button('<b><i class="fa fa-check-square"></i></b>' . Yii::t('fe', 'Verifikasi verbal order'), [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-verifikasi-verbal',
                            'style' => 'margin-top:10px',
                            'data-id' => $data['cppt_id'],
                            'disabled' => $status_disabled,
                            'data-cpptid' => DocoHelpers::encrypt($data['cppt_id']),
                            'data-confirm-message' => Yii::t('fe', 'Apakah anda yakin akan memverifikasi verbal order tersebut?'),
                        ]);
                    }
                }
                $html .= '<br>';
            }

            // cek verifikasi dpjp
            if ($data['is_verifikasi']) {
                $html .= '<span>' . $data['pegawai_verifikasi'] . ' ' . date('d/m/Y H:i:s', strtotime($data['tgl_verifikasi'])) . '</span>';
                $html .= '<br>';
            } else {
                if ($pegawai_id == $dpjp && $this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS && empty($pemberi_instruksi)) {
                    $html .= Html::button('<b><i class="fa fa-check-square"></i></b>' . Yii::t('fe', 'Verifikasi DPJP'), [
                        'class' => 'btn btn-info btn-labeled btn-xs btn-verifikasi-dpjp',
                        'style' => 'margin-top:10px',
                        'data-id' => $data['origin_cppt_id'],
                        'data-xx' => $status_disabled,
                        'disabled' => $status_disabled,
                        'data-cpptid' => DocoHelpers::encrypt($data['origin_cppt_id']),
                        'data-confirm-message' => Yii::t('fe', 'Apakah anda yakin akan memverifikasi CPPT tersebut?'),
                    ]);
                    $html .= '<br>';
                }
            }

            // cek verifikasi untuk button tambah
            // jika telah verifikasi dpjp button tambah terapi tidak muncul
            // Button terapi dipindah keluar (Aktifkan buat memunculkan kembali)
            // if($data['is_verifikasi'] != true && $data['pegawai_verifikasi'] == null){
            //     $userIdentity = $this->_user_identity;
            //     $html .= Html::button('<b><i class="fa fa-plus"></i></b>'.Yii::t('fe', 'Tambah Terapi'), [
            //         'class' => 'btn btn-info btn-labeled btn-xs btn-tambah-terapi',
            //         'style' => 'margin-top:10px',
            //         'data-id' => $data['cppt_id'],
            //         'data-xx' => $status_disabled,
            //         'data-cpptid' => DocoHelpers::encrypt($data['cppt_id']),
            //         'disabled' => (($this->_pegawai_id == $data['pegawai_id']) && ($status_disabled != 'true') && !empty($data['pasienadmisi_id'])) ? false : true,
            //     ]);
            //     $html .= '<br>';
            // }
            // End Button terapi dipindah keluar (Aktifkan buat memunculkan kembali)
            if ($pegawai_id == $data['pegawai_id'] && empty($pemberi_instruksi)) {
                $html .= Html::button('<b><i class="fa fa-pencil"></i></b>' . Yii::t('fe', 'Edit SOAP'), [
                    'class' => $data['tipe'] != 'RI' ? 'hidden' : 'btn btn-info btn-labeled btn-xs btn-edit-soap',
                    'data-cpptid' => DocoHelpers::encrypt($data['origin_cppt_id']),
                    'data-is_icd_x' => isset($data['is_icd_x']) ? $data['is_icd_x'] : true,
                    'disabled' => $data['tipe'] != 'RI' || $data['is_verifikasi'] ? true : false
                ]);
                $html .= Html::button('<b><i class="fa fa-times"></i></b>' . Yii::t('fe', 'Batal Edit SOAP'), [
                    'class' => 'btn btn-danger btn-labeled btn-xs btn-cancel-edit-soap hidden'
                ]);
            }
            $html .= '<br>';
        } else {
            $tgl_edit = isset($data['created_date']) ? date('d/m/Y / H:i:s', strtotime($data['created_date'])) : '';
            $html .= '<p> Data sudah di ubah oleh <br>' . $data['pegawai_update_nama'] . ' - <br>' . $tgl_edit . '</p>';
        }

        return $html;
    }
}