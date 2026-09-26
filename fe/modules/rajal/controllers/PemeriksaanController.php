<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 16:59:18
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-14 13:57:41
 * @Last Modified time: 2018-03-07 18:05:28
 * @Last Modified time: 2018-09-04 11:11:40
 * @Description:
 */

namespace Doco\rajal\controllers;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\Services\SuratKematianService;
use app\components\Pelayanan\PelayananHelpers;

use app\components\Traits\HistoryPatientTrait;
use app\components\Traits\TindakanBmhpTrait;
use app\components\Traits\HistoryFisioTrait;
use app\modules\rajal\models\InformasiForm;
use app\modules\rajal\models\AnamnesaForm;
use app\modules\rajal\models\TindakanPelayananForm;
use app\modules\rajal\models\ObatAlkesPasienForm;
use app\modules\rajal\models\HasilPemeriksaanLabForm;
use app\modules\rajal\models\PemeriksaanFisikForm;
use app\modules\rajal\models\ResepturForm;
use app\modules\rajal\models\ResepturDetailForm;
use app\modules\rajal\models\PasienMorbiditasForm;
use app\modules\rajal\models\PasienDirujukKeluarForm;
use app\modules\rajal\models\PembebasanTarifForm;
use app\modules\rajal\models\KonsulpoliForm;
use app\modules\rajal\models\TindakankomponenForm;
use app\modules\rajal\models\BuatJanjiPoliForm;
use app\modules\rajal\models\PasienPulangForm;
use app\modules\rajal\models\PendaftaranForm;
use app\modules\rajal\models\JenazahForm;
use app\modules\rajal\models\InstruksiPenunjangForm;
use app\modules\rajal\models\AsesmenKeperawatan;
use app\extensions\rajal\models\ModelAsesmenKeperawatanAdhy;

use app\modules\rajal\components\traits\PemeriksaanTindakanTrait;
use app\modules\rajal\components\traits\PemeriksaanFisikTrait;
use app\modules\rajal\components\traits\PemeriksaanDiagnosaTrait;
use app\modules\rajal\components\traits\PemeriksaanResepturTrait;
use app\modules\rajal\components\traits\PemeriksaanPenunjangTrait;
use app\modules\rajal\components\traits\PemeriksaanSoapTrait;
use app\modules\rajal\components\traits\PemeriksaanCpptTrait;
use app\modules\rajal\components\traits\PemeriksaanResumeMedisTrait;
use app\modules\rajal\components\traits\PemulanganPasienTrait;
use app\modules\rajal\components\traits\PermintaanMakanTrait;
use \Datetime;
use yii\helpers\Json;
use app\components\Traits\CathlabTrait;
use app\components\Traits\LaporanTindakanTrait;
use app\components\Traits\GiziTrait;
use app\components\Services\AksesFormService;
use app\components\Traits\RujukanPasienTrait;
use app\components\Traits\Pelayanan\NursingNoteTrait;
use app\modules\rajal\components\traits\RujukBalikTrait;
use app\components\Traits\FisioterapiTrait;
use app\components\Traits\Pelayanan\TerraMedikTrait;
use app\components\Traits\SuratKeteranganTrait;
use app\components\Traits\ICareTrait;
use GuzzleHttp\Psr7\Request;
use app\components\Traits\MonitoringTtvTrait;
use app\components\Traits\ObservasiEwsTrait;
use app\components\Traits\SbarTrait;

class PemeriksaanController extends DocoController
{
    protected $_title = "Rajal :: Pemeriksaan pasien rawat jalan";
    protected $_module = '/rajal/pemeriksaan';
    protected $_controller = '/rajal/informasi';
    protected $_page;
    protected $_restRajal;
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
    protected $_restMaster;
    protected $_restJenazah;
    protected $_statusPeriksa;
    protected $_default_depo;
    protected $_ruangan_odc;
    protected $_restIgd;
    // protected $allowAction = [
    //     'cetak-resume',
    //     'cetak-tindakan',
    //     'cetak-anamnesa',
    //     'get-data-history-cppt-terra-medik',
    //     'all-dokter-list',
    //     'export-pdf-reseptur',
    //     'cetak-list-cppt',
    //     'export-pdf-diagnosa',
    //     'export-pdf-periksa-fisik',
    //     'panggil-antrian',
    //     'konsulpoli',
    //     'get-data-konsul-poli',
    // ];
    protected $_restDefault;
    protected $_restRanap;
    protected $_restRm;
    protected $_restAntrian;
    protected $allowAction = ['*'];
    // traits
    use PemeriksaanTindakanTrait;
    use PemeriksaanFisikTrait;
    use PemeriksaanDiagnosaTrait;
    use PemeriksaanResepturTrait;
    use PemeriksaanPenunjangTrait;
    use PemeriksaanSoapTrait;
    use PemeriksaanCpptTrait;
    use PemeriksaanResumeMedisTrait;
    use PemulanganPasienTrait;
    use PermintaanMakanTrait;
    use HistoryPatientTrait;
    use CathlabTrait;
    use LaporanTindakanTrait;
    use TindakanBmhpTrait;
    use GiziTrait;
    use RujukanPasienTrait;
    use NursingNoteTrait;
    use HistoryFisioTrait;
    use RujukBalikTrait;
    use FisioterapiTrait;
    use TerraMedikTrait;
    use SuratKeteranganTrait;
    use ICareTrait;
    use MonitoringTtvTrait;
    use ObservasiEwsTrait;
    use SbarTrait;

    public function init()
    {
        // dump(Yii::$app->request->referrer);
        // die;
        parent::init();
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restJenazah = Yii::$app->docoRest->jenazah;
        $this->_restDefault = Yii::$app->docoRest->rajal;
        $this->_restIgd = Yii::$app->docoRest->igd;
        $this->_restRanap = Yii::$app->docoRest->ranap;
        $this->_restRm = Yii::$app->docoRest->rm;
        $this->_restAntrian = Yii::$app->docoRest->antrian;
        $this->_id_ruangan = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
        $this->_ruangan_name = Yii::$app->docoVars->workspace('ruangan_name');
        $this->_instalasi_id = Yii::$app->docoVars->workspace('instalasi_id') ? Yii::$app->docoVars->workspace('instalasi_id') : 1;
        $this->_pegawai_id = Yii::$app->docoVars->user('id_pegawai') ? Yii::$app->docoVars->user('id_pegawai') : 1;
        $this->_loginpemakai_id = Yii::$app->docoVars->user('loginpemakai_id') ? Yii::$app->docoVars->user('loginpemakai_id') : 1;
        $this->_page = Yii::t('fe', 'Pemeriksaan pasien rawat jalan');
        $data_pasien = [];

        $request = Yii::$app->request;
        $pendId = $request->get('pendaftaran_id', null);
        if (empty($pendId)) {
            $pendId = $request->get('id', null);
        }
        if (is_numeric($pendId)) {
            $pendId = $this->helper->encrypt($pendId);
        }
        $pendaftaran_id = DocoHelpers::decrypt($pendId);
        $konsulpoli_id = PelayananHelpers::coalesce([$request->get('konsulpoliId'), $request->get('konsulpoli_id')], '');
        // $session_pendaftaran_key = implode('-', array_filter(['pasien-pendaftaran-id', $pendId, $konsulpoli_id])); // concat string to pasien-pendaftaran-id-$pendaId-$konsulpoli_id
        $cache_pendaftaran_key = implode('-', array_filter(['pasien-pendaftaran-id', $pendId, $konsulpoli_id]));
        $data_pasien = [];
        $temp_pasien = Yii::$app->session->get($cache_pendaftaran_key);
        $cache_riwayat_pasien = isset($temp_pasien['pasien_id']) ? Yii::$app->cache->get('data-riwayat-pasien-'. $temp_pasien['pasien_id']) : null;
        try {
            $cache = Yii::$app->cache;
            $cachePas = $cache->get($cache_pendaftaran_key);
            if (!empty($pendaftaran_id) && empty($cachePas) || (!empty($pendaftaran_id) && !$cache_riwayat_pasien)) {
                $resApi = $this->guzzleExec($this->_restRajal, [
                    'url' => 'tra-pemeriksaan/get-pasien',
                    'payload' => [
                        'query' => [
                            'id' => $pendaftaran_id,
                            'cppt' => true,
                            'konsulpoli_id' => PelayananHelpers::coalesce([$request->get('konsulpoliId'), $request->get('konsulpoli_id')], null),
                            'is_jenis' => Yii::$app->request->get('is_jenis', 'rj')
                        ]
                    ]
                ]);
                $data_pasien = $resApi['registration'];
                $data_pasien['askep'] = $resApi['askep'];
                if (!empty($data_pasien['askep'])) {
                    $addictionArray =  explode(",", $data_pasien['askep']['ketergantungan_jenis']);
                    $data_pasien['askep']['status_merokok'] = in_array('rokok', $addictionArray);
                    $data_pasien['askep']['riwayat_penyakit_keluarga'] = '';
                    $arrayHistory = explode(",", $data_pasien['askep']['riwayat_penyakit_keluarga_list']);
                    $totalDisease = count($arrayHistory);
                    foreach ($arrayHistory as $indexDisease => $disease) {
                        $data_pasien['askep']['riwayat_penyakit_keluarga'] .= ucwords($disease) . (($indexDisease + 1) >= $totalDisease ? '' : ', ');
                    }
                } else {
                    $data_pasien['askep'] = [
                        'status_merokok' => false,
                        'riwayat_penyakit_keluarga' => '-',
                        'status_ekonomi' => '-'
                    ];
                }
                $data_pasien['cppt'] = $resApi['cppt'];
                $data_pasien['alergi'] = isset($resApi['askep']['alergi']) ? $resApi['askep']['alergi'] : null;

                // set session
                $cache->set($cache_pendaftaran_key, $data_pasien, 3600);

                if(isset($data_pasien['pasien_id']) && isset($data_pasien['riwayat_pasien'])){
                    Yii::$app->cache->set('data-riwayat-pasien-'. $data_pasien['pasien_id'], $data_pasien['riwayat_pasien'], 3600);
                }
            } else {
                $data_pasien = $cachePas;
                $data_pasien['riwayat_pasien'] = Yii::$app->cache->get('data-riwayat-pasien-' . $data_pasien['pasien_id']);
            }
            $data_pasien['ruangan_id'] =  isset($data_pasien['ruangan_id']) ?  $data_pasien['ruangan_id'] : $this->_id_ruangan;
            $data_pasien['ruangan_name'] =   isset($data_pasien['ruangan_nama']) ?  $data_pasien['ruangan_nama'] : $this->_ruangan_name;
        } catch (RequestException $e) {
            $data_pasien = [];
            $this->logError($e);
        } catch (\Exception $e) {
            $data_pasien = [];
            $this->logError($e);
        }
        $this->_ruangan_odc = isset($data_pasien['ruangan_odc']) ? $data_pasien['ruangan_odc'] : null;
        $this->_pasien_id = isset($data_pasien['pasien_id']) ? $data_pasien['pasien_id'] : null;
        $this->_pendaftaran_id = $pendId;
        $this->_kelaspelayanan_id = isset($data_pasien['kelaspelayanan_id']) ? $data_pasien['kelaspelayanan_id'] : null;
        $this->_jeniskelamin = !empty($data_pasien['jeniskelamin']) ? $data_pasien['jeniskelamin'] : null;
        $this->_data_pasien = !empty($data_pasien) ? $data_pasien : [];
        $this->type = 'RJ';
        $this->_statusPeriksa = isset($data_pasien['status_periksa'])
            ? ($data_pasien['status_periksa'] == DocoConstants::STATUS_PULANG
                || $data_pasien['status_periksa'] == DocoConstants::STATUS_RUJUK_RAWAT_INAP)
            ? $data_pasien['pasien_id'] : false : false;
        // cathlab requirement
        $this->instalasi = 'rajal';
        $this->restGeneral = $this->_restRajal;
        $this->_restGeneralRanap = $this->_restRanap;
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\PageCache',
                'only' => ['periksa', 'anamnesa', 'diagnosa', 'tindakan', 'konsulpoli'],
                'duration' => 60,
                'variations' => [
                    \Yii::$app->language,
                ]
            ],
        ];
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        $pendaftaran_id = DocoHelpers::decrypt($request->get('id'));

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;

        try {
            $response = $this->_restRajal->get('tra-pemeriksaan/index?id=' . $pendaftaran_id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);

            $no = $request->get('start', 1);
            foreach ($body['response']["data"] as $key => $value) {
                $data[$key] = $value;
            }

            //aksi
            $data['rowNum'] = 1;
            $data['persalinan'] = $this->getAksi($pendaftaran_id, 'persalinan');
            $data['kelahiranbayi'] = $this->getAksi($pendaftaran_id, 'kelahiranbayi');
            $data['anamnesa'] = $this->getAksi($pendaftaran_id, 'anamnesa');
            $data['pemeriksaanfisik'] = $this->getAksi($pendaftaran_id, 'pemeriksaanfisik');
            $data['konsulpoli'] = $this->getAksi($pendaftaran_id, 'konsulpoli');
            $data['tindakan'] = $this->getAksi($pendaftaran_id, 'tindakan');
            $data['terapi'] = $this->getAksi($pendaftaran_id, 'terapi');
            $data['alkes'] = $this->getAksi($pendaftaran_id, 'alkes');
            $data['diagnosa'] = $this->getAksi($pendaftaran_id, 'diagnosa');
            $data['operasi'] = $this->getAksi($pendaftaran_id, 'operasi');

            $data['penunjang'] = $this->getPenunjang($pendaftaran_id);

            $datas[] = $data;

            $result['data'] = $datas;
            $result['recordsTotal'] = $body['response']['count'];
            $result['recordsFiltered'] = $body['response']['count'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    /*=========================================
    =            list pasien rajal            =
    =========================================*/

    public function actionIndex()
    {
        $id_ruangan = DocoHelpers::encrypt($this->_id_ruangan);
        $status = $this->_status;

        // data select
        $list_data             = $this->getListDataPasien();
        $data_pegawai          = $list_data["data_pegawai"];
        $data_penjamin         = $list_data["data_penjamin"];
        $data_statusperiksa    = $list_data["data_statusperiksa"];
        $data_ruangan          = $list_data["data_ruangan"];
        $data_jenis_kelamin    = $list_data["data_jenis_kelamin"];
        $data_cara_bayar       = $list_data["data_cara_bayar"];
        $data_lantai           = $list_data["data_lantai"];
        $status_batal_periksa  = DocoConstants::STATUS_PERIKSA_BTL_PERIKSA;
        $status_antrian_poli   = DocoConstants::STATUS_PERIKSA_ANTR_POLI;
        $status_diperiksa      = DocoConstants::STATUS_PERIKSA_DIPERIKSA;
        $status_periksa_pulang = DocoConstants::STATUS_PERIKSA_PULANG;
        $kelompok_medis        = DocoConstants::KELOMPOK_MEDIS;
        $kelompokpegawai_id    = Yii::$app->docoVars->user("kelompokpegawai_id");

        $response        = $this->_restRajal->get('tra-pemeriksaan/cara-bayar-list', ['form_params' => []]);
        $list_cara_bayar = json_decode($response->getBody(), True)['response'];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetPenjamin($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRajal->get('penjamin?advanced-filter[carabayar_m.carabayar_nama]=' . $parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $assign_id ? $value['penjamin_id'] : $value['penjamin_nama'],
                    'name' => $value['penjamin_nama']
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

    public function actionGetCarabayar($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?advanced-filter[penjamin_m.penjamin_nama]=' . $parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRajal->get('cara-bayar' . $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $assign_id ? $value['carabayar_id'] : $value['carabayar_nama'],
                    'name' => $value['carabayar_nama']
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

    public function actionGetDataListPasien()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

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
        $kelompokpegawai_id = Yii::$app->docoVars->user("kelompokpegawai_id");
        $yiiRestfulParams['kelompokpegawai_id'] = $kelompokpegawai_id;
        $yiiRestfulParams['dokter_umum'] = !is_null(Yii::$app->docoVars->user('gelarbelakang')) && $kelompokpegawai_id == DocoConstants::KELOMPOK_MEDIS ? true : false;
        // if(Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS ){
        //     $yiiRestfulParams['advanced-filter']['pegawai_id'] = $userIdentity['id_pegawai'];
        //     $yiiRestfulParams['advanced-filter']['kelompok_medis'] = DocoConstants::KELOMPOK_MEDIS;
        // }

        // $yiiRestfulParams['advanced-filter']['ruangan_id'] = $this->_id_ruangan;
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        try {
            $response = $this->_restRajal->get('tra-pemeriksaan/list-pasien?ruangan_id=' . $this->_id_ruangan . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start', 1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $primary = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['rowNum'] = $no;
                $value['primary'] = $primary;
                // $value['aksi'] = $this->getAksiListPasien($value);
                $value['tgl_pendaftaran'] = date('d F, Y H:i:s', strtotime($value['tgl_pendaftaran']));
                if (($value['status_periksa'] == 1) || ($value['status_periksa'] == 2)) {
                    $noAntrian = $this->getNoAntrianPasien($value);
                } else {
                    $noAntrian = '-';
                }
                $value['no_antrian'] = $noAntrian;
                $value['ruanganId'] = DocoHelpers::encrypt($value['ruangan_id']);
                $value['no_pendaftaran'] = strtoupper($value['nama_pasien']) . ' (' . substr($value['jenis_kelamin'], 0, 1) . ')<br>No. Registrasi : ' . $value['no_pendaftaran'] . '<br>No. RM : ' . $value['no_rekam_medik'];
                $value['carabayar_nama'] = 'Cara Bayar : ' . $value['carabayar_nama'] . '<br>Penjamin : ' . $value['penjamin_nama'];
                $value['ruangan_nama'] = 'Ruangan : ' . $value['ruangan_nama'] . '<br>Ruangan Asal : ' . $value['ruanganasal_nama'];
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

    private function getAksiListPasien($data)
    {
        $pendaftaran_id = DocoHelpers::encrypt($data['pendaftaran_id']);
        // $labelStatus = 'padding-left: 10px !important;font-weight: bold;';
        $labelStatus = 'background:#26A65B;color:white;';
        $return_data = '';
        $userIdentity = Yii::$app->session->get('user_identity');
        if (Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS) { // logindokter
            if ($data['status_periksa'] == DocoConstants::STATUS_PERIKSA) {
                $return_data .= $this->getPeriksaTagihanPulang($pendaftaran_id);
            } elseif ($data['status_periksa'] == DocoConstants::STATUS_BATAL_PERIKSA) {
                $return_data .= '<span class="badge" style="' . $labelStatus . '">' . \Yii::t('fe', 'Dibatalkan') . '</span>&nbsp;&nbsp;';
            } elseif ($data['status_periksa'] == DocoConstants::STATUS_PULANG || $data['status_periksa'] == DocoConstants::STATUS_RUJUK_RAWAT_INAP) {
                $return_data .= $this->getViewTagihan($pendaftaran_id);
            } else {
                $return_data .= '<span style="' . $labelStatus . '">' . \Yii::t('fe', '-') . '</span>&nbsp;&nbsp;';
            }
        } else {
            if ($data['status_periksa'] == DocoConstants::STATUS_ANTRIAN_POLI) { // antrian poli
                $return_data .= Html::button(
                    '<i class="fa fa-stethoscope"></i>',
                    [
                        'class' => 'btn btn-info btn-xs periksa',
                        'action' => Url::to([$this->_controller . '/confirm-periksa', 'pendaftaran_id' => $pendaftaran_id]),
                        'data-tooltip' => "tooltip",
                        'data-placement' => 'left',
                        'data-original-title' => Yii::t('fe', 'Periksa'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop'
                    ]
                );
                $return_data .= '&nbsp;&nbsp;';
                $return_data .= Html::button(
                    '<i class="fa fa-close"></i>',
                    [
                        'class' => 'btn btn-danger btn-xs batal',
                        'action' => Url::to([$this->_controller . '/batal-periksa', 'pendaftaran_id' => $pendaftaran_id]),
                        'data-tooltip' => "tooltip",
                        'data-placement' => 'left',
                        'data-original-title' => Yii::t('fe', 'Batal'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop'
                    ]
                );
            } elseif ($data['status_periksa'] == DocoConstants::STATUS_PERIKSA) {
                $return_data .= $this->getPeriksaTagihanPulang($pendaftaran_id);
            } elseif ($data['status_periksa'] == DocoConstants::STATUS_BATAL_PERIKSA) {
                $return_data .= '<span class="badge" style="' . $labelStatus . '">' . \Yii::t('fe', 'Dibatalkan') . '</span>&nbsp;&nbsp;';
            } elseif ($data['status_periksa'] == DocoConstants::STATUS_PULANG || $data['status_periksa'] == DocoConstants::STATUS_RUJUK_RAWAT_INAP) {
                $return_data .= $this->getViewTagihan($pendaftaran_id);
            } else {
                $return_data .= '<span style="' . $labelStatus . '">' . \Yii::t('fe', '-') . '</span>&nbsp;&nbsp;';
            }
        }


        // tombol periksa
        /*
        // tombol rencana kontrol
        if ($data['konsulpoli_id'] != null || $data['status_periksa'] == 2){ // sudah buat janji poli
            $return_data .= Html::button(
                '<i class="fa fa-plus-square"></i>', [
                    'class' => 'btn btn-info btn-xs kontrol',
                    'action' => Url::to(['pemeriksaan/rencana-kontrol', 'pendaftaran_id' => $pendaftaran_id]),
                    'data-tooltip' => "tooltip",
                    'data-placement' => 'left',
                    'data-original-title' => Yii::t('fe', 'Buat rencana kontrol'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop'
                ]
            );
        }else{ // belum buat janji poli
            $return_data .= Html::button(
                '<i class="fa fa-plus-square"></i>', [
                    'class' => 'btn btn-info btn-xs kontrol',
                    'disabled' => 'disabled',
                    'data-tooltip' => "tooltip",
                    'data-placement' => 'left',
                    'data-original-title' => Yii::t('fe', 'Sudah buat janji'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop'
                ]
            );
        }
        */

        return $return_data;
    }

    private function getNoAntrianPasien($data)
    {
        $pendaftaran_id = DocoHelpers::encrypt($data['pendaftaran_id']);

        $return_data = '';
        if (!empty($data['no_antrian'])) {
            $return_data = '<button type="button" class="btn btn-info btn-labeled btn-xs data-filter antrian" data-antrian="' . $data['no_antrian'] . '" data-id="' . $data['pendaftaran_id'] . '" data-pegawaiid="' . $data['pegawai_id'] . '" data-namapasien="' . $data['nama_pasien'] . '" data-ruanganid="' . $data['ruangan_id'] . '"><b><i class="fa fa-volume-up"></i></b>' . $data['no_antrian'] . '</button>';
        }
        $return_data .= '&nbsp;&nbsp;';

        return $return_data;
    }

    public function actionConfirmPeriksa($pendaftaran_id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($pendaftaran_id);
        $title = Yii::t('fe', 'Periksa');
        $sub_title = $this->_page;
        $list_data = $this->getListData();
        $data_pegawai = $list_data["data_pegawai"];
        $is_disabled = 'true';
        $modelPendaftaran = new PendaftaranForm;
        $form_name = substr(strrchr(get_class($modelPendaftaran), "\\"), 1);
        try {
            $data_pasien = $this->_data_pasien;

            $modelPendaftaran->attributes = $data_pasien;

            if ($request->post()) {
                $data = $request->post('PendaftaranForm');
                $modelPendaftaran->pegawai_id = $data["pegawai_id"];
                $modelPendaftaran->pendaftaran_id = $id;

                $response = $this->_restRajal->post('tra-pemeriksaan/ubah-dokter', [
                    'form_params' => $modelPendaftaran->attributes
                ]);
                $response = json_decode($response->getBody(), true);

                $this->redirect(['pemeriksaan/periksa?id=' . $pendaftaran_id]);
                return DocoHelpers::responseTemplate(
                    200,
                    Yii::t('fe', 'Proses Berhasil'),
                    [],
                    ['title' => Yii::t('fe', 'Proses Berhasil'), 'text' => Yii::t('fe', 'Data Berhasil Disimpan')]
                );
            } else {
                return $this->renderAjax('_periksa', get_defined_vars());
            }
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
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
            $data_pasien = $this->_data_pasien;

            $modelInformasi->attributes = $data_pasien;
            $modelJanjiPoli->tgl_jadwal = date('d F, Y', strtotime($data_pasien['tgl_buatjanji']));


            if ($request->post()) {
                $data = $request->post('BuatJanjiPoliForm');

                $modelJanjiPoli->load($request->post());
                $modelJanjiPoli->ruangan_id = $this->_id_ruangan;
                $modelJanjiPoli->hari_jadwal = date("l", strtotime($modelJanjiPoli->tgl_jadwal));
                $modelJanjiPoli->tgl_buatjanji =  date("Y-m-d H:i:s", strtotime($modelJanjiPoli->tgl_jadwal));
                $modelJanjiPoli->by_phone = 0;
                $modelJanjiPoli->created_date = date('Y-m-d H:i:s', strtotime('NOW'));
                $modelJanjiPoli->is_deleted = 0;
                $modelJanjiPoli->is_active = 1;
                $modelJanjiPoli->is_active = 1;
                $modelJanjiPoli->status_janjipoli = '357';
                $modelJanjiPoli->carabayar_id = $data_pasien['carabayar_id'];
                $modelJanjiPoli->penjamin_id = $data_pasien['penjamin_id'];

                if ($modelJanjiPoli->validate()) {
                    $response = $this->_restRajal->post('tra-pemeriksaan/buat-janji-poli', [
                        'form_params' => $modelJanjiPoli->attributes
                    ]);
                    $response = json_decode($response->getBody(), true);

                    return DocoHelpers::response($response, false);
                } else {
                    $errors = DocoHelpers::parseError($modelJanjiPoli->errors, $form_name);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            } else {
                return $this->renderAjax('/informasi/_rencanakontrol', get_defined_vars());
            }
        } catch (\Exception $e) {
            echo '<pre>';
            print_r($e->getMessage());
            exit;
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (RequestException $e) {
            echo '<pre>';
            print_r($e->getMessage());
            exit;
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    //action pemulangan pasien
    public function actionPemulanganPasien($id, $konsulpoli_id = null)
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $id;
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $konsulpoli_id = empty($konsulpoli_id) ? null : DocoHelpers::decrypt($konsulpoli_id);
        $title = Yii::t('fe', 'Pulang');
        $cache = Yii::$app->cache;
        $modelpulangpasien = new PasienPulangForm;
        $modelInformasi = new InformasiForm;
        $modelJenazah = new JenazahForm;
        $form_name = substr(strrchr(get_class($modelpulangpasien), "\\"), 1);
        $jeniskelamin = $hubkeluarga = [];
        $status_update = $this->_statusPeriksa;
        if ($request->post()) {
            $requestPost = $request->post();
            $requestPost['user'] = Yii::$app->docoVars->user("nama");
            $date_pulang = str_replace('/', '-', $requestPost['PasienPulangForm']['tglpasienpulang']);
            $requestPost['PasienPulangForm']['tglpasienpulang'] = $requestPost['PasienPulangForm']['tglpasienpulang'] != '' ? date('Y-m-d H:i:s', strtotime($date_pulang)) : '';

            $date_meninggal = str_replace('/', '-', $requestPost['PasienPulangForm']['tgl_meninggal']);;
            $requestPost['PasienPulangForm']['tgl_meninggal'] = $requestPost['PasienPulangForm']['tgl_meninggal'] != '' ? date('Y-m-d H:i:s', strtotime($date_meninggal)) : '';

            //Perubahan format tanggal
            $requestPost['PasienPulangForm']['tgl_kremasi'] = $requestPost['PasienPulangForm']['tgl_kremasi'] != '' ? date_format(date_create_from_format('d/m/Y H:i:s', $requestPost['PasienPulangForm']['tgl_kremasi']), 'Y-m-d H:i:s') : '';
            $requestPost['PasienPulangForm']['waktu_pemeriksaan_jenazah'] = $requestPost['PasienPulangForm']['waktu_pemeriksaan_jenazah'] != '' ? date_format(date_create_from_format('d/m/Y H:i:s', $requestPost['PasienPulangForm']['waktu_pemeriksaan_jenazah']), 'Y-m-d H:i:s') : '';

            $requestPost['PasienPulangForm']['nosep'] = $requestPost['PasienPulangForm']['nosep'];
            $modelpulangpasien->load($requestPost);
            $modelpulangpasien->is_meninggal = ($modelpulangpasien->carakeluar_id == 4) ? true : false;
            if ($modelpulangpasien->validate()) {
                if ($modelpulangpasien->persetujuanpelayanan) {
                    $modelJenazah->attributes = $request->post('JenazahForm', []);
                    if (!$modelJenazah->validate()) {
                        $formjenazahname = substr(strrchr(get_class($modelJenazah), "\\"), 1);
                        $errors = DocoHelpers::parseError($modelJenazah->errors, $formjenazahname);
                        return DocoHelpers::responseTemplate(422, 'Error', $errors);
                    }
                    $listOrderJenazah = $listAlatJenazah = [];
                    if ($cache->get($id . '-tindakan')) {
                        foreach ($cache->get($id . '-tindakan') as $key => $value) {
                            foreach ($value as $k => $val) {
                                if ($k == "additional_data") {
                                    $value[$k] = json_decode($value[$k]);
                                }
                            }
                            $listOrderJenazah['tindakan'][] = $value;
                        }
                    }
                    if ($cache->get($id . '-obat')) {
                        $listOrderJenazah['obat'] = $cache->get($id . '-obat');
                    }
                    if ($cache->get($id . '-linen')) {
                        $listAlatJenazah['linen'] = $cache->get($id . '-linen');
                    }
                    if ($cache->get($id . '-alat')) {
                        $listAlatJenazah['alat'] = $cache->get($id . '-alat');
                    }
                    $modelJenazah->list_order = json_encode($listOrderJenazah);
                    $modelJenazah->list_linen = json_encode($listAlatJenazah);
                }

                $modelRujukanPulang = [];
                if ($request->post('RujukanPulangForm')) {
                    $modelRujukanPulang = $request->post('RujukanPulangForm');
                }

                $resepturPrb = $request->post('reseptur_prb', []);

                // yii::error($modelpulangpasien->attributes);exit;
                $response = $this->_restRajal->post('tra-pemeriksaan/pemulangan-pasien', [
                    'form_params' => [
                        'modelpulangpasien' => $modelpulangpasien->attributes,
                        'modeljenazah' => $modelJenazah->attributes,
                        'konsulpoliId' => $konsulpoli_id,
                        'modelRujukanPulang' => $modelRujukanPulang,
                        'resepturPrb' => $resepturPrb,
                        'nosep' => $requestPost['PasienPulangForm']['nosep'],
                        'user' => $requestPost['user']
                    ]
                ]);
                $response = json_decode($response->getBody(), true);

                if ($response['metadata']['status'] == 422) {
                    $error = isset($response['response']['message']) ? $response['response']['message'] : $response['response']['text'];
                    $result = $this->helper->macroResponseJson(422, $error);
                    return $result;
                }
                Yii::$app->cache->set('pasien-pendaftaran-id-' . $id, [], 3600);
                return DocoHelpers::responseTemplate(
                    200,
                    Yii::t('fe', 'message_berhasil'),
                    [],
                    ['title' => Yii::t('fe', 'message_berhasil'), 'text' => Yii::t('fe', 'Data berhasil disimpan')]
                );
            } else {
                $errors = DocoHelpers::parseError($modelpulangpasien->errors, $form_name);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        try {
            $data_bundle_pasien_pulang = $this->helper->guzzleExec($this->_restRajal, [
                'url' => 'tra-pemeriksaan/bundle-pasien-pulang',
                'payload' => [
                    'query' => [
                        'id' => $pendaftaran_id,
                        'konsulpoli_id' => $konsulpoli_id
                    ]
                ]
            ]);
            if (ArrayHelper::getValue($data_bundle_pasien_pulang, 'metadata.status', 500) != 200) {
                return DocoHelpers::responseTemplate(422, ArrayHelper::getValue($data_bundle_pasien_pulang, 'metadata.message', 'error'));
            }
            $response = ArrayHelper::getValue($data_bundle_pasien_pulang, 'response.data');
            $data_pasien = $response['datapasien'];
            $caraKeluar = $response['carakeluar'];
            $modelpulangpasien->catatan_tindakan = $response['catatan_tindakan'];
            $konfig_keramat_spri = $response['konfig_keramat_spri'];
            $jeniskelamin = ArrayHelper::map($response['jeniskelamin'], 'lookup_id', 'lookup_name');
            $hubkeluarga = ArrayHelper::map($response['hubungan_keluarga'], 'lookup_id', 'lookup_name');
            $morbiditas = $response['morbiditas']['state'];
            $message = $response['morbiditas']['message'];
            $stateMessage = (!empty($response['morbiditas']['message']) ? $response['morbiditas']['message'] . ', Apakah anda yakin untuk menyimpan data ini ?' : 'Apakah anda yakin untuk menyimpan data ini ?');
            $tglpendaftaran = $data_pasien['tgl_pendaftaran'];
            $modelInformasi->attributes = $data_pasien;
            return $this->renderAjax('pulang/_pulang', get_defined_vars());
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
    private function getListDataPasien()
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restRajal->get('tra-pemeriksaan/get-list-data', [
                'query' => [
                    'id_ruangan' => $this->_id_ruangan,
                    'instalasi_id' => $this->_instalasi_id
                ]
            ]);
            $body = json_decode($response->getBody(), TRUE);

            $data_statusperiksa = empty($body['response']['data-statusperiksa']) ? [] : $body['response']['data-statusperiksa'];
            $data_pegawai = empty($body['response']['data-pegawai']) ? [] : $body['response']['data-pegawai'];
            $data_penjamin = empty($body['response']['data-penjamin']) ? [] : $body['response']['data-penjamin'];
            $data_ruangan = empty($body['response']['data-ruangan']) ? [] : $body['response']['data-ruangan'];
            $data_cara_bayar = empty($body['response']['data-ruangan']) ? [] : $body['response']['data-cara-bayar'];
            $data_jenis_kelamin = empty($body['response']['data-jenis-kelamin']) ? [] : $body['response']['data-jenis-kelamin'];

            $data_lantai = empty($body['response']['data-lantai']) ? [] : $body['response']['data-lantai'];

            $result = [
                'data_statusperiksa' => $data_statusperiksa,
                'data_pegawai' => $data_pegawai,
                'data_penjamin' => $data_penjamin,
                'data_ruangan' => $data_ruangan,
                'data_jenis_kelamin' => $data_jenis_kelamin,
                'data_cara_bayar' => $data_cara_bayar,
                'data_lantai' => $data_lantai
            ];

            return $result;
        } catch (RequestException $e) {
            Yii::error([
                'err' => $e->getMessage()
            ]);
            return [
                'data_statusperiksa' => [],
                'data_pegawai' => [],
                'data_penjamin' => [],
                'data_ruangan' => [],
                'data_jenis_kelamin' => [],
                'data_cara_bayar' => [],
                'data_lantai' => []
            ];
        } catch (\Exception $e) {
            Yii::error([
                'err' => $e->getMessage()
            ]);
            return [
                'data_statusperiksa' => [],
                'data_pegawai' => [],
                'data_penjamin' => [],
                'data_ruangan' => [],
                'data_jenis_kelamin' => [],
                'data_cara_bayar' => [],
                'data_lantai' => []
            ];
        }
    }

    private function getPeriksaTagihanPulang($pendaftaran_id)
    {
        $return_data = '';
        $return_data .= Html::button(
            '<i class="fa fa-stethoscope"></i>',
            [
                'class' => 'btn btn-info btn-xs periksa',
                'data-action' => Url::to(['/rajal/pemeriksaan/periksa', 'id' => $pendaftaran_id]),
                'data-tooltip' => "tooltip",
                'data-type' => DocoConstants::STATUS_PERIKSA,
                'data-placement' => 'left',
                'data-original-title' => Yii::t('fe', 'Periksa'),
            ]
        );
        $return_data .= '&nbsp;&nbsp;';
        $return_data .= Html::a(
            '<i class="fa fa-file-pdf-o"></i>',
            Url::to([$this->_module . '/export-pdf-rincian-tagihan', 'pendaftaran_id' => $pendaftaran_id]),
            [
                'class' => 'btn btn-turquoise btn-xs',
                'data-tooltip' => "tooltip",
                'data-placement' => 'left',
                'data-original-title' => Yii::t('fe', 'Rincian Tagihan'),
            ]
        );

        $return_data .= '&nbsp;&nbsp;';
        $return_data .= Html::button(
            '<i class="fa fa-home"></i>',
            [
                'class' => 'btn btn-info btn-xs kontrol',
                'action' => Url::to(['pemeriksaan/pemulangan-pasien', 'pendaftaran_id' => $pendaftaran_id]),
                'data-tooltip' => "tooltip",
                'data-placement' => 'left',
                'data-original-title' => Yii::t('fe', 'Pemulangan pasien'),
                'data-toggle' => 'modal',
                'data-target' => '#modal_backdrop'
            ]
        );
        $return_data .= '&nbsp;&nbsp;';
        $id = DocoHelpers::decrypt($pendaftaran_id);
        $response = $this->_restRajal->get('tra-pemeriksaan/get-info-status-riwayat?pendaftaran_id=' . $id, ['form_params' => []]);
        $body = json_decode($response->getBody(), True);
        $data = $body['response'];
        if (isset($data)) {
            if ($data['status_rj'] == 'BELUM SELESAI') {
                $return_data .= Html::button(
                    '<i class="fa fa-close"></i>',
                    [
                        'class' => 'btn btn-danger btn-xs batal',
                        'action' => Url::to([$this->_controller . '/batal-periksa', 'pendaftaran_id' => $pendaftaran_id]),
                        'data-tooltip' => "tooltip",
                        'data-placement' => 'left',
                        'data-original-title' => Yii::t('fe', 'Batal'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop'
                    ]
                );
            }
        }

        return $return_data;
    }

    public function actionCheckTransaction($pendaftaran_id)
    {

        $id = DocoHelpers::decrypt($pendaftaran_id);
        $response = $this->_restRajal->get('tra-pemeriksaan/get-info-status-riwayat?pendaftaran_id=' . $id, ['form_params' => []]);
        $body = json_decode($response->getBody(), True);
        $data = $body['response'];
        if (isset($data)) {
            if ($data['status_rj'] == 'BELUM SELESAI') {
                return json_encode(['status' => 200]);
            } else {
                return json_encode(['status' => 500]);
            }
        }
    }

    private function getViewTagihan($pendaftaran_id)
    {
        $return_data = '';
        $return_data .= Html::a(
            '<i class="fa fa-file-pdf-o"></i>',
            Url::to([$this->_module . '/export-pdf-rincian-tagihan', 'pendaftaran_id' => $pendaftaran_id]),
            [
                'class' => 'btn btn-turquoise btn-xs',
                'data-tooltip' => "tooltip",
                'data-placement' => 'left',
                'data-original-title' => Yii::t('fe', 'Rincian Tagihan'),
            ]
        );
        $return_data .= '&nbsp;&nbsp;';
        $return_data .= Html::button(
            '<i class="fa fa-eye"></i>',
            [
                'class' => 'btn btn-info btn-xs view',
                'data-action' => Url::to(['/rajal/pemeriksaan/periksa', 'id' => $pendaftaran_id]),
                'data-tooltip' => "tooltip",
                'data-type' => DocoConstants::STATUS_PULANG . '-' . DocoConstants::STATUS_RUJUK_RAWAT_INAP,
                'data-placement' => 'left',
                'data-original-title' => Yii::t('fe', 'Lihat detail'),
            ]
        );
        $return_data .= '&nbsp;&nbsp;';
        return $return_data;
    }

    /*=====  End of list pasien rajal  ======*/

    public function actionPeriksa($id, $ruanganId = null, $konsulpoliId = null)
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Pemeriksaan Pasien');
        $sub_title = $this->_page;
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $statepulang = $request->get('state', null);
        $ruangan_id = $request->get('ruanganId', null);
        $ruangan_id = DocoHelpers::decrypt($ruangan_id);
        $is_nurse = Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_KEPERAWATAN ? 1 : 0;
        try {
            $patientData = $this->_data_pasien;
            $pasien_id = isset($patientData['pasien_id']) ? $patientData['pasien_id'] : null;
            $pendaftaran_id = DocoHelpers::decrypt($id);
            $pegawai_id = isset($patientData['pegawai_id']) ? $patientData['pegawai_id'] : null;
            $ruangan_pasien_id = isset($patientData['ruangan_id']) ? $patientData['ruangan_id'] : $ruangan_id;

            // data select
            $list_data = $this->getListDataPeriksa($pendaftaran_id, $ruangan_pasien_id, $pegawai_id, $konsulpoliId);
            $data_permintaan_konsul = isset($list_data['data_permintaan_konsul']) ? $list_data['data_permintaan_konsul'] : '';
            $status_approve = isset($data_permintaan_konsul['status_approve_id']) ? $data_permintaan_konsul['status_approve_id'] : null;
            $status_konsul_id = isset($data_permintaan_konsul['status_konsul_id']) ? $data_permintaan_konsul['status_konsul_id'] : null;
            $default_status_approve = isset($list_data['default_status_approve']) ? $list_data['default_status_approve'] : null;
            $approved = ($status_approve == $default_status_approve) ? false : true;
            $cekDataKonsul = !empty($data_permintaan_konsul) ? 'show' : 'none';
            $userIdentity = Yii::$app->session->get('user_identity');
            $data_penjamin = isset($list_data['data_penjamin']) ? $list_data['data_penjamin'] : null;
            $data_pegawai = isset($list_data['data_pegawai']) ? $list_data['data_pegawai'] : null;
            $data_statusperiksa = isset($list_data['data_statusperiksa']) ? $list_data['data_statusperiksa'] : null;
            $data_diagnosa = isset($list_data['data_diagnosa']) ? $list_data['data_diagnosa'] : null;
            $pasien_id = (!empty($patientData['pasien_id'])) ? DocoHelpers::encrypt($patientData['pasien_id']) : null;
            $pegawai_id = (!empty($patientData['pegawai_id'])) ? DocoHelpers::encrypt($patientData['pegawai_id']) : null;
            $kelaspelayanan_id = (!empty($patientData['kelaspelayanan_id'])) ? DocoHelpers::encrypt($patientData['kelaspelayanan_id']) : null;
            $encrytedPendaftaranId = $id;
            $konsulpoliId = $konsulpoliId;
            $pasienpulang_id = isset($patientData['pasienpulang_id']) ? $patientData['pasienpulang_id'] : null;
            $hasCathlab = isset($this->_data_pasien['hasCathlab']) ? $this->_data_pasien['hasCathlab'] : false;
            $showTtvTab = isset($this->_data_pasien['showTtvTab']) && $this->_data_pasien['showTtvTab'] ? true : false;
            $showEwsTab = isset($this->_data_pasien['showEwsTab']) && $this->_data_pasien['showEwsTab'] ? true : false;
            $showSbarTab = isset($this->_data_pasien['showSbarTab']) && $this->_data_pasien['showSbarTab'] ? true : false;
            $id_ruangan = $this->_id_ruangan;
            $ruanganperiksa_id = DocoHelpers::encrypt(ArrayHelper::getValue($this->_data_pasien, 'ruangan_id', null));

            $query_params_icare = [
                'icare_identifier' => ArrayHelper::getValue($patientData, 'icare_identifier'),
                'no_pendaftaran' => ArrayHelper::getValue($patientData, 'no_pendaftaran'),
                'no_rekam_medik' => ArrayHelper::getValue($patientData, 'no_rekam_medik'),
                'nama_pasien' => ArrayHelper::getValue($patientData, 'nama_pasien'),
                'id_pegawai' => $userIdentity['id_pegawai'],
            ];

            return $this->render('periksa', get_defined_vars());
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    public function getListDataPeriksa($pendaftaran_id, $ruangan_id, $pegawai_id, $konsulpoli_id = null)
    {
        $result = [
            'data_statusperiksa' => [],
            'data_pegawai' => [],
            'data_penjamin' => [],
            'data_diagnosa' => [],
            'data_permintaan_konsul' => [],
            'default_status_approve' => [],
            'has_access_icare' => false,
            'thirdapp' => Yii::$app->params->thirdApp,
            'tabs' => Yii::$app->params->rajalTabs,
            'icare' => Yii::$app->params->iCare,
        ];
        try {
            $response = $this->_restRajal->get('allow/get-data-periksa-pasien?id_ruangan=' . $ruangan_id . '&pendaftaran_id=' . $pendaftaran_id .'&pegawai_id='.$pegawai_id.'&konsulpoli_id='. $konsulpoli_id);
            $body = json_decode($response->getBody(), true);
            $data_statusperiksa = empty($body['response']['data-statusperiksa']) ? [] : $body['response']['data-statusperiksa'];
            $data_pegawai = empty($body['response']['data-pegawai']) ? [] : $body['response']['data-pegawai'];
            $data_penjamin = empty($body['response']['data-penjamin']) ? [] : $body['response']['data-penjamin'];
            $data_diagnosa = empty($body['response']['data-diagnosa']) ? [] : $body['response']['data-diagnosa'];
            $data_permintaan_konsul = empty($body['response']['data_permintaan_konsul']) ? [] : $body['response']['data_permintaan_konsul'];
            $default_status_approve = empty($body['response']['default_status_approve']) ? null : $body['response']['default_status_approve'];
            $hasAccessIcare = DocoHelpers::checkButtonAccess('/rajal/pemeriksaan', 'icare');

            $result['data_statusperiksa'] = $data_statusperiksa;
            $result['data_pegawai'] = $data_pegawai;
            $result['data_penjamin'] = $data_penjamin;
            $result['data_diagnosa'] = $data_diagnosa;
            $result['data_permintaan_konsul'] = $data_permintaan_konsul;
            $result['default_status_approve'] = $default_status_approve;
            $result['has_access_icare'] = $hasAccessIcare;

            return $result;
        } catch (RequestException $e) {
            $this->logError($e);
            return $result;
        } catch (\Exception $e) {
            $this->logError($e);
            return $result;
        }
    }

    /**
     *
     * Tab data
     *
     */

    public function actionGetDataAnamnesa()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        $pendaftaran_id = DocoHelpers::decrypt($request->get('id'));

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;

        try {
            $response = $this->_restRajal->get('tra-pemeriksaan/get-anamnesa?id=' . $pendaftaran_id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);

            $no = $request->get('start', 1);

            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $primaryKey = $value['anamesa_id'];
                    $primaryKey = DocoHelpers::encrypt($value['anamesa_id']);

                    $value['aksi'] = Html::a(
                        '<i class="fa fa-trash" aria-hidden="true"></i>',
                        '#',
                        [
                            'class' => 'btn btn-danger btn-xs data-delete',
                            'action' => Url::to([$this->_module . '/delete-anamnesa', 'id' => $primaryKey]),
                            'data-popup' => "tooltip",
                            'data-placement' => 'left',
                            'title' => \Yii::t('fe', 'Hapus'),
                        ]
                    );
                    $data[$key] = $value;
                }
                $result['data'] = $data;
                $result['recordsTotal'] = count($data);
                $result['recordsFiltered'] = count($data);

                return $result;
            } else {
                return DocoHelpers::dataTabelsException();
            }
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function getStatusPeriksa($pendaftaran_id, $dataKunjungan = [])
    {
        if (!is_numeric($pendaftaran_id)) {
            $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        }

        if (empty($dataKunjungan)) {
            $getInfoKunjungan = $this->_restRajal->get('tra-pemeriksaan/get-info-kunjungan-rajal', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id
                ]
            ]);
            $DataBody = json_decode($getInfoKunjungan->getBody(), True);
            $dataKunjungan = $DataBody['response'];
        }

        $result = false;
        if (isset($dataKunjungan['status_periksa'])) {
            if (
                $dataKunjungan['status_periksa'] == DocoConstants::STATUS_PULANG ||
                $dataKunjungan['status_periksa'] == DocoConstants::STATUS_RUJUK_RAWAT_INAP
            ) {
                $result = true;
            }
        }

        return $result;
    }

    public function actionAnamnesa()
    {

        $request        = Yii::$app->request;
        $isRajal        = $request->get('type', 'rj') == 'rj';
        $id             = $request->get('id', null);
        $pasien_id      = $request->get('pasien_id', null);
        $pegawai_id     = $request->get('pegawai_id', null);
        $status         = $request->get('status', 0);
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $pasien_id      = DocoHelpers::decrypt($pasien_id);
        $pegawai_id     = DocoHelpers::decrypt($pegawai_id);
        $userIdentity = Yii::$app->session->get('user_identity');
        return Yii::$app->docoPlugin->execute($this,'asesmen_keperawatan');

    }

    public function actionSaveAsesmenKeperawatanAdhy()
    {
        $payload        = Yii::$app->request->post();
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id', null);
        $status         = Yii::$app->request->get('status', null);
        $numberField    = ['berat_badan', 'tinggi_badan', 'td', 'nadi', 'rr', 'suhu'];
        $payload['pendaftaran_id'] = $pendaftaran_id;
        $payload['tgl_anamnesis'] = date_format(date_create_from_format('d/m/Y', $payload['tgl_anamnesis']), 'Y-m-d') . ' ' . date('h:i:s');

        foreach ($numberField as $eachField) {
            if (isset($payload[$eachField])) {
                $payload[$eachField] = trim(str_replace('.', '', $payload[$eachField]));
            }
        }
        $modelValidation = new ModelAsesmenKeperawatanAdhy;
        $modelValidation->attributes = $payload;

        if (!$modelValidation->validate()) {
            return $this->helper->macroResponseJson(422, 'Silakan cek kembali inputan.', $this->helper->mapErrorForm($modelValidation->errors, 'AsesmenKeperawatan'));
        } else {
            $result = $this->guzzleExec($this->_restRajal, [
                'url' => 'asesmen-keperawatan/save-asesmen-adhy',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'formdata' => $payload
                    ]
                ]
            ]);
            $cache = Yii::$app->cache;
            $pendId = $this->helper->encrypt($result['data']['pendaftaran_id']);
            $cacheData = $cache->get('pasien-pendaftaran-id-' . $pendId);

            $cacheData['askep']['riwayat_penyakit_keluarga'] = $result['data']['riwayat_penyakit_keluarga_list'];
            $cacheData['askep']['status_merokok'] = $result['data']['status_merokok'];
            $cacheData['askep']['status_ekonomi'] = $result['data']['status_ekonomi'];

            $cache->set('pasien-pendaftaran-id-' . $pendId, $cacheData, 3600);
            Yii::$app->cache->delete('data-riwayat-pasien-'. $this->_data_pasien['pasien_id']);
            return $this->helper->response($result, 200);
        }
    }

    public function actionDeleteAnamnesa($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restRajal->delete('tra-pemeriksaan/delete-anamnesa?id=' . $id);
            $response = json_decode($response->getBody(), true);

            return DocoHelpers::response($response, false, true);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(),
                json_decode($e->getResponse()->getBody()->getContents())->message,
                []
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionCetakAnamnesa($pendaftaran_id)
    {
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $path = Yii::getAlias("@download") . "/cetak-anamnesa-{$pendaftaran_id}.pdf";
        $url = "tra-pemeriksaan/cetak-anamnesa?id=" . $pendaftaran_id;
        try {
            $response = $this->_restRajal->get($url, [
                'save_to' => $path,
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /**
     *
     * Lihat data riwayat pasien
     *
     */
    public function actionViewRiwayatPasien()
    {
        $request = Yii::$app->request;
        $type = $request->get('aksi', null);
        $pendaftaran_id = $request->get('id', null);
        $pasienmasukpenunjang_id = $request->get('pasienmasukpenunjang_id', null);
        $title = 'Lihat data';

        switch ($type) {
            case 'penunjang':
                # code...
                break;

            default:
                # code...
                break;
        }

        return $this->renderPartial('__view_riwayatpasien', [
            'title' => $title,
        ]);
    }

    /**
     *
     * private function
     *
     */
    public function getListData()
    {
        $result = [
            'data_statusperiksa' => [],
            'data_pegawai' => [],
            'data_penjamin' => [],
            'data_jabatan' => [],
            'data_diagnosa' => [],
            'data_kelompokdiagnosa' => [],
            'data_diagnosaruangan' => [],
            'data_rujukankeluar' => [],
            'data_jadwalpoli' => [],
            'data_tindakanruangan' => [],
            'data_paket' => [],
            'data_dokter' => [],
            'data_perawat' => [],
            'data_diagnosaimunisasi' => [],
            'data_obatalkes' => [],
            'data_satuantindakan' => [],
            'data_ruanganapotek' => [],
            'data_signa' => [],
            'konfig_farmasi' => [],
            'count_riwayat' => [],
            'data_template' => [],
            'thirdapp' => Yii::$app->params->thirdApp,
            'tabs' => Yii::$app->params->rajalTabs,
            'icare' => Yii::$app->params->iCare,
        ];
        try {
            $response = $this->_restRajal->get('allow/allow-get-list-data?id_ruangan=' . $this->_id_ruangan . '&kelaspelayanan_id=' . $this->_kelaspelayanan_id . '&penjamin_id=' . $this->_data_pasien['penjamin_id'] . '&pendaftaran_id=' . $this->_data_pasien['pendaftaran_id'] . '&pegawai_id=' . $this->_pegawai_id);
            $body = json_decode($response->getBody(), true);
            $data_statusperiksa = empty($body['response']['data-statusperiksa']) ? [] : $body['response']['data-statusperiksa'];
            $data_pegawai = empty($body['response']['data-pegawai']) ? [] : $body['response']['data-pegawai'];
            $data_penjamin = empty($body['response']['data-penjamin']) ? [] : $body['response']['data-penjamin'];
            $data_jabatan = empty($body['response']['data-jabatan']) ? [] : $body['response']['data-jabatan'];
            $data_diagnosa = empty($body['response']['data-diagnosa']) ? [] : $body['response']['data-diagnosa'];
            $data_kelompokdiagnosa = empty($body['response']['data-kelompokdiagnosa']) ? [] : $body['response']['data-kelompokdiagnosa'];
            $data_jadwalpoli = empty($body['response']['data-jadwalpoli']) ? [] : $body['response']['data-jadwalpoli'];
            $data_tindakanruangan = empty($body['response']['data-tindakanruangan']) ? [] : $body['response']['data-tindakanruangan'];
            $data_paket = empty($body['response']['data-paket']) ? [] : $body['response']['data-paket'];
            $data_diagnosaruangan = empty($body['response']['data-diagnosaruangan']) ? [] : $body['response']['data-diagnosaruangan'];
            $data_rujukankeluar = empty($body['response']['data-rujukankeluar']) ? [] : $body['response']['data-rujukankeluar'];
            $data_dokter = empty($body['response']['data-dokter']) ? [] : $body['response']['data-dokter'];
            $data_perawat = empty($body['response']['data-perawat']) ? [] : $body['response']['data-perawat'];
            $data_diagnosaimunisasi = empty($body['response']['data-diagnosaimunisasi']) ? [] : $body['response']['data-diagnosaimunisasi'];
            $data_obatalkes = empty($body['response']['data-obatalkes']) ? [] : $body['response']['data-obatalkes'];
            $data_satuantindakan = empty($body['response']['data-satuantindakan']) ? [] : $body['response']['data-satuantindakan'];
            $data_ruanganapotek = empty($body['response']['data-ruanganapotek']) ? [] : $body['response']['data-ruanganapotek'];
            $data_signa = empty($body['response']['data-signa']) ? [] : $body['response']['data-signa'];
            $data_konfig = empty($body['response']['data-konfigfarmasi']) ? [] : $body['response']['data-konfigfarmasi'];
            $count = $body['response']['count-riwayat'];
            $data_permintaan_konsul = empty($body['response']['data_permintaan_konsul']) ? [] : $body['response']['data_permintaan_konsul'];

            $default_status_approve = empty($body['response']['default_status_approve']) ? null : $body['response']['default_status_approve'];
            $hasAccessIcare = DocoHelpers::checkButtonAccess('/rajal/pemeriksaan', 'icare');

            $result = [
                'data_statusperiksa' => $data_statusperiksa,
                'data_pegawai' => $data_pegawai,
                'data_penjamin' => $data_penjamin,
                'data_jabatan' => $data_jabatan,
                'data_diagnosa' => $data_diagnosa,
                'data_kelompokdiagnosa' => $data_kelompokdiagnosa,
                'data_diagnosaruangan' => $data_diagnosaruangan,
                'data_rujukankeluar' => $data_rujukankeluar,
                'data_jadwalpoli' => $data_jadwalpoli,
                'data_tindakanruangan' => $data_tindakanruangan,
                'data_paket' => $data_paket,
                'data_dokter' => $data_dokter,
                'data_perawat' => $data_perawat,
                'data_diagnosaimunisasi' => $data_diagnosaimunisasi,
                'data_obatalkes' => $data_obatalkes,
                'data_satuantindakan' => $data_satuantindakan,
                'data_ruanganapotek' => $data_ruanganapotek,
                'data_signa' => $data_signa,
                'konfig_farmasi' => $data_konfig,
                'count_riwayat' => $count,
                'data_permintaan_konsul' => $data_permintaan_konsul,
                'default_status_approve' => $default_status_approve,
                'thirdapp' => Yii::$app->params->thirdApp,
                'tabs' => Yii::$app->params->rajalTabs,
                'icare' => Yii::$app->params->iCare,
                'has_access_icare' => $hasAccessIcare,
            ];

            return $result;
        } catch (RequestException $e) {
            $this->logError($e);
            return $result;
        } catch (\Exception $e) {
            $this->logError($e);
            return $result;
        }
    }

    private function getAksi($id, $type = null)
    {
        $pendaftaran_id = DocoHelpers::encrypt($id);

        $return_data = '';
        $url = '';
        switch ($type) {
            case 'persalinan':
                $url = Url::home() . 'rajal/pemeriksaan/view-riwayat-pasien?id=' . $pendaftaran_id . '&aksi=persalinan';
                break;

            case 'kelahiranbayi':
                $url = Url::home() . 'rajal/pemeriksaan/view-riwayat-pasien?id=' . $pendaftaran_id . '&aksi=kelahiranbayi';
                break;

            case 'anamnesa':
                $url = Url::home() . 'rajal/pemeriksaan/view-riwayat-pasien?id=' . $pendaftaran_id . '&aksi=anamnesa';
                break;

            case 'pemeriksaanfisik':
                $url = Url::home() . 'rajal/pemeriksaan/view-riwayat-pasien?id=' . $pendaftaran_id . '&aksi=pemeriksaanfisik';
                break;

            case 'konsulpoli':
                $url = Url::home() . 'rajal/pemeriksaan/view-riwayat-pasien?id=' . $pendaftaran_id . '&aksi=konsulpoli';
                break;

            case 'tindakan':
                $url = Url::home() . 'rajal/pemeriksaan/view-riwayat-pasien?id=' . $pendaftaran_id . '&aksi=tindakan';
                break;

            case 'terapi':
                $url = Url::home() . 'rajal/pemeriksaan/view-riwayat-pasien?id=' . $pendaftaran_id . '&aksi=terapi';
                break;

            case 'alkes':
                $url = Url::home() . 'rajal/pemeriksaan/view-riwayat-pasien?id=' . $pendaftaran_id . '&aksi=alkes';
                break;

            case 'diagnosa':
                $url = Url::home() . 'rajal/pemeriksaan/view-riwayat-pasien?id=' . $pendaftaran_id . '&aksi=diagnosa';
                break;

            case 'operasi':
                $url = Url::home() . 'rajal/pemeriksaan/view-riwayat-pasien?id=' . $pendaftaran_id . '&aksi=operasi';
                break;

            default:
                $return_data .= '&nbsp;';
                break;
        }

        $return_data .= Html::a(
            '<i class="fa fa-eye"></i>',
            $url,
            [
                'class' => 'btn btn-info btn-xs',
                'data-popup' => "tooltip",
                'data-placement' => 'left',
                'data-original-title' => Yii::t('fe', 'Lihat'),
                'data-toggle' => 'modal',
                'data-target' => '#modal_backdrop'
            ]
        );


        return $return_data;
    }

    private function getPenunjang($pendaftaran_id)
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restRajal->get('tra-pemeriksaan/get-penunjang?id=' . $pendaftaran_id . '&id_ruangan=' . $this->_id_ruangan);
            $body = json_decode($response->getBody(), true);

            $data = empty($body['response']['data']) ? [] : $body['response']['data'];

            $result = '<table>';
            if ($data) {
                foreach ($data as $key => $value) {
                    $id = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
                    $result .= '<tr>';
                    $result .= '<td>';
                    $result .= $value['ruangan_nama'];
                    $result .= '</td>';
                    $result .= '<td>&nbsp;</td>';
                    $result .= '<td>';
                    $result .= Html::a(
                        '<i class="fa fa-eye "></i>',
                        Url::home() . 'rajal/pemeriksaan/view-riwayat-pasien?pasienmasukpenunjang_id=' . $id . '&aksi=penunjang',
                        [
                            'class' => 'btn btn-info btn-xs',
                            'data-popup' => "tooltip",
                            'data-placement' => 'left',
                            'data-original-title' => Yii::t('fe', 'Lihat'),
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop'
                        ]
                    );
                    $result .= '</td>';
                    $result .= '</tr>';
                }
            }
            $result .= '</table>';

            return $result;
        } catch (RequestException $e) {
            return '';
        } catch (\Exception $e) {
            return '';
        }
    }

    public function actionRujukanPasien()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $pasien_id = $request->get('pasien_id', null);
        $pasien_id = DocoHelpers::decrypt($pasien_id);

        $list_data = $this->getListData();
        $data_diagnosa = $list_data['data_diagnosa'];
        $data_pegawai = $list_data['data_pegawai'];
        $data_rujukankeluar = $list_data['data_rujukankeluar'];
        $modelPasienDirujukKeluar = new PasienDirujukKeluarForm;

        $is_update = false;

        try {
            $response = $this->_restRajal->get('tra-pemeriksaan/get-rujukan-pasien?id=' . $pendaftaran_id . '&id_ruangan=' . $this->_id_ruangan);
            $body = json_decode($response->getBody(), true);
            $data = $body['response']['data'];

            if (!empty($data)) {
                $data['diagnosasementara_ruj'] = !empty($data['diagnosasementara_ruj']) ? json_decode($data['diagnosasementara_ruj']) : null;

                $modelPasienDirujukKeluar->attributes = $data;
                $is_update = true;
                $pasiendirujukkeluar_id = $data['pasiendirujukkeluar_id'];
            }

            if ($post = $request->post()) {
                $post['PasienDirujukKeluarForm']['diagnosasementara_ruj'] = !empty($post['PasienDirujukKeluarForm']['diagnosasementara_ruj']) ? json_encode($post['PasienDirujukKeluarForm']['diagnosasementara_ruj']) : null;

                $modelPasienDirujukKeluar->load($post);
                $modelPasienDirujukKeluar->pendaftaran_id = $pendaftaran_id;
                $modelPasienDirujukKeluar->pasien_id = $pasien_id;
                $modelPasienDirujukKeluar->ruanganasal_id = $this->_id_ruangan;
                $modelPasienDirujukKeluar->tglberlakusurat = date('Y-m-d');
                $modelPasienDirujukKeluar->tgldirujuk = date_format(date_create_from_format('d/m/Y', $modelPasienDirujukKeluar->tgldirujuk), 'Y-m-d');
                $modelPasienDirujukKeluar->sampaidengan = date_format(date_create_from_format('d/m/Y', $modelPasienDirujukKeluar->sampaidengan), 'Y-m-d');


                if ($modelPasienDirujukKeluar->validate()) {
                    if ($is_update) {
                        $response = $this->_restRajal->post('tra-pemeriksaan/update-rujukan-pasien?id=' . $pasiendirujukkeluar_id, [
                            'form_params' => $modelPasienDirujukKeluar->attributes
                        ]);
                        $response = json_decode($response->getBody(), true);
                    } else {
                        $response = $this->_restRajal->post('tra-pemeriksaan/create-rujukan-pasien', [
                            'form_params' => $modelPasienDirujukKeluar->attributes
                        ]);
                        $response = json_decode($response->getBody(), true);
                    }

                    return DocoHelpers::response($response, false, true);
                } else {
                    return DocoHelpers::response(['message' => $modelPasienDirujukKeluar->errors], 500);
                }
            }
        } catch (Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }

        if ($modelPasienDirujukKeluar->tgldirujuk) {
            $modelPasienDirujukKeluar->tgldirujuk = date('d/m/Y', strtotime($modelPasienDirujukKeluar->tgldirujuk));
        }
        if ($modelPasienDirujukKeluar->sampaidengan) {
            $modelPasienDirujukKeluar->sampaidengan = date('d/m/Y', strtotime($modelPasienDirujukKeluar->sampaidengan));
        }


        return $this->renderPartial('__rujukanpasien', [
            'pendaftaran_id' => $pendaftaran_id,
            'data_pegawai' => $data_pegawai,
            'data_diagnosa' => $data_diagnosa,
            'data_rujukankeluar' => $data_rujukankeluar,
            'modelPasienDirujukKeluar' => $modelPasienDirujukKeluar
        ]);
    }

    public function actionPembebasanTarif()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);

        $list_data = $this->getListData();
        $data_diagnosa = $list_data['data_diagnosa'];
        $data_pegawai = $list_data['data_pegawai'];
        $data_jabatan = $list_data['data_jabatan'];
        $modelPembebasanTarif = new PembebasanTarifForm;
        $total_tarifpelayanan = $this->actionGetTotalTagihanPembebasanTarif(DocoHelpers::encrypt($pendaftaran_id));
        $modelPembebasanTarif->total_tarifpelayanan = strlen($total_tarifpelayanan) > 0 ? $total_tarifpelayanan : -1;
        try {

            $is_update = false;

            if ($post = $request->post()) {
                $pembebasantarif_id = !empty($post['PembebasanTarifForm']['pembebasantarif_id']) ? $post['PembebasanTarifForm']['pembebasantarif_id'] : null;
                if (strlen($pembebasantarif_id) > 0) {
                    $is_update = true;
                }

                $post['PembebasanTarifForm']['tgl_pembebasantarif'] = !empty($post['PembebasanTarifForm']['tgl_pembebasantarif']) ? DocoHelpers::convDateTime($post['PembebasanTarifForm']['tgl_pembebasantarif']) : null;

                $modelPembebasanTarif->load($post);
                $modelPembebasanTarif->pendaftaran_id = $pendaftaran_id;

                if ($modelPembebasanTarif->validate()) {
                    if ($is_update) {
                        $response = $this->_restRajal->post('tra-pemeriksaan/update-pembebasan-tarif?id=' . $pembebasantarif_id, [
                            'form_params' => $modelPembebasanTarif->attributes
                        ]);
                        $response = json_decode($response->getBody(), true);
                    } else {
                        $response = $this->_restRajal->post('tra-pemeriksaan/create-pembebasan-tarif', [
                            'form_params' => $modelPembebasanTarif->attributes
                        ]);
                        $response = json_decode($response->getBody(), true);
                    }
                    return DocoHelpers::response($response, false, true);
                } else {
                    return DocoHelpers::response(['message' => $modelPembebasanTarif->errors], 500);
                }
            }
        } catch (Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }

        return $this->renderAjax('__pembebasantarif', [
            'pendaftaran_id' => $pendaftaran_id,
            'data_pegawai' => $data_pegawai,
            'data_diagnosa' => $data_diagnosa,
            'data_jabatan' => $data_jabatan,
            'modelPembebasanTarif' => $modelPembebasanTarif
        ]);
    }

    public function actionGetTotalTagihanPembebasanTarif($pendaftaran_id)
    {
        $request = Yii::$app->request;
        $res = -1;
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        try {
            $response = $this->_restRajal->get('tra-pemeriksaan/get-pembebasan-tarif-total-tagihan?id=' . $pendaftaran_id);
            $body = json_decode($response->getBody(), true);
            $data = isset($body['response']['data']) ? $body['response']['data'] : -1;
            $res = $data;
        } catch (Exception $e) {
            $res = -1;
        }
        return json_encode($res);
    }

    public function actionGetDataPembebasanTarif()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        $pendaftaran_id = $request->get('id');

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;

        try {
            $response = $this->_restRajal->get('tra-pemeriksaan/get-pembebasan-tarif?id=' . $pendaftaran_id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);

            if (!empty($body['response']['data'])) {
                $jml = count($body['response']['data']);
                foreach ($body['response']['data'] as $key => $value) {
                    $primaryKey = $value['pembebasantarif_id'];
                    $primaryKey = DocoHelpers::encrypt($value['pembebasantarif_id']);

                    if (strlen($value['tgl_pembebasantarif']) > 0) {
                        $value['tgl_pembebasantarif'] = DocoHelpers::convDateTime($value['tgl_pembebasantarif']);
                    }

                    $value['aksi'] = '';
                    if ($jml == ($key + 1)) {
                        $value['aksi'] = Html::button(
                            '<i class="fa fa-pencil" aria-hidden="true"></i>',
                            [
                                'class' => 'btn btn-info btn-xs data-ubah-pembebasan',
                                'action' => Url::to([$this->_module . '/update-pembebasan-tarif', 'id' => $primaryKey]),
                                'data-popup' => "tooltip",
                                'data-placement' => 'left',
                                'title' => \Yii::t('fe', 'Ubah'),
                            ]
                        );
                        $value['aksi'] .= Html::a(
                            '<i class="fa fa-trash" aria-hidden="true"></i>',
                            '#',
                            [
                                'class' => 'btn btn-danger btn-xs data-delete-pembebasan',
                                'action' => Url::to([$this->_module . '/delete-pembebasan-tarif', 'id' => $primaryKey]),
                                'data-popup' => "tooltip",
                                'data-placement' => 'left',
                                'title' => \Yii::t('fe', 'Hapus'),
                            ]
                        );
                    }
                    $data[$key] = $value;
                }
                $result['data'] = $data;
                $result['recordsTotal'] = count($data);
                $result['recordsFiltered'] = count($data);

                return $result;
            } else {
                return DocoHelpers::dataTabelsException();
            }
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    private function getModelKonsul()
    {
        return Yii::$app->docoPlugin->execute($this,'konsul_poli');
    }

    /**
     *
     * fungsi aksi konsul poli
     *
     */
    public function actionKonsulpoli()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $pasien_id = $request->get('pasien_id', null);
        $is_modal = $request->get('is_modal', null);
        $preview_only = $request->get('preview_only', false);
        $session = Yii::$app->session;

        $list_data = $this->getListData();
        $data_jadwalpoli = $list_data['data_jadwalpoli'];
        $modelKonsulpoli = $this->getModelKonsul();

        $rawClassForm = explode(chr(92), get_class($modelKonsulpoli));
        $classForm = $rawClassForm[(count($rawClassForm) - 1)];

        try {
            if ($post = $request->post()) {
                $data = $post[$classForm];
                $modelKonsulpoli->attributes = $data;
                // fill extra data model
                $modelKonsulpoli->pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
                $modelKonsulpoli->pasien_id = is_numeric($pasien_id) ? $pasien_id : DocoHelpers::decrypt($pasien_id);
                $modelKonsulpoli->tgl_konsulpoli = ($data['tgl_konsulpoli']) ? date('Y-m-d', strtotime($data['tgl_konsulpoli'])).' '.date('H:i:s') : date('Y-m-d H:i:s');
                $modelKonsulpoli->asalpoliklinikkonsul_id = $this->_id_ruangan;
                $modelKonsulpoli->status_periksa = null;
                $modelKonsulpoli->ruangan_id = null;
                // $modelKonsulpoli->pegawai_id = null;
                if ($modelKonsulpoli->validate()) {
                    $response = $this->_restRajal->post('tra-pemeriksaan/create-konsulpoli', [
                        'form_params' => $modelKonsulpoli->attributes
                    ]);
                    $response = json_decode($response->getBody(), true);
                    return DocoHelpers::response($response, false, 'KonsulpoliForm');
                } else {
                    $errors = DocoHelpers::parseError($modelKonsulpoli->errors, 'KonsulpoliForm');
                    return DocoHelpers::response([
                        'response' => [
                            'data' => $errors
                        ]
                    ], 422);
                }
            } else {
                if($preview_only == true) {
                    return $this->renderAjax('__preview_konsulpoli', [
                        'pendaftaran_id'  => $pendaftaran_id,
                        'modelKonsulpoli' => $modelKonsulpoli,
                        'data_jadwalpoli' => $data_jadwalpoli,
                        'preview_only' => $preview_only,
                        'infoPasien' => [
                            'nama_pasien' => ArrayHelper::getValue($this->_data_pasien, 'nama_pasien', '-'),
                            'penjamin_nama' => ArrayHelper::getValue($this->_data_pasien, 'penjamin_nama', '-'),
                            'kelaspelayanan_nama' => ArrayHelper::getValue($this->_data_pasien, 'kelaspelayanan_nama', '-'),
                            'tgl_pendaftaran' => isset($this->_data_pasien['tgl_pendaftaran']) && !empty($this->_data_pasien['tgl_pendaftaran']) ? date('Y-m-d', strtotime($this->_data_pasien['tgl_pendaftaran'])) : null,
                        ],
                    ]);
                }
                if ($is_modal == true) {
                    return $this->renderAjax('__modal_konsulpoli', [
                        'pendaftaran_id'  => $pendaftaran_id,
                        'modelKonsulpoli' => $modelKonsulpoli,
                        'data_jadwalpoli' => $data_jadwalpoli,
                        'infoPasien' => [
                            'nama_pasien' => ArrayHelper::getValue($this->_data_pasien, 'nama_pasien', '-'),
                            'penjamin_nama' => ArrayHelper::getValue($this->_data_pasien, 'penjamin_nama', '-'),
                            'kelaspelayanan_nama' => ArrayHelper::getValue($this->_data_pasien, 'kelaspelayanan_nama', '-'),
                            'tgl_pendaftaran' => isset($this->_data_pasien['tgl_pendaftaran']) && !empty($this->_data_pasien['tgl_pendaftaran']) ? date('Y-m-d', strtotime($this->_data_pasien['tgl_pendaftaran'])) : null,
                        ],
                    ]);
                }
                return $this->renderAjax('__konsulpoli', [
                    'pendaftaran_id' => $pendaftaran_id,
                    'modelKonsulpoli' => $modelKonsulpoli,
                    'data_jadwalpoli' => $data_jadwalpoli,
                    'infoPasien' => [
                        'nama_pasien' => ArrayHelper::getValue($this->_data_pasien, 'nama_pasien', '-'),
                        'penjamin_nama' => ArrayHelper::getValue($this->_data_pasien, 'penjamin_nama', '-'),
                        'kelaspelayanan_nama' => ArrayHelper::getValue($this->_data_pasien, 'kelaspelayanan_nama', '-'),
                        'tgl_pendaftaran' => isset($this->_data_pasien['tgl_pendaftaran']) && !empty($this->_data_pasien['tgl_pendaftaran']) ? date('Y-m-d', strtotime($this->_data_pasien['tgl_pendaftaran'])) : null,
                    ],
                ]);
            }
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionUpdatePembebasanTarif($id = null)
    {
        $request = Yii::$app->request;
        $model = new PembebasanTarifForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restRajal->get('tra-pemeriksaan/view-pembebasan-tarif?id=' . $id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        $val = [];

        foreach ($model->attributes as $key => $value) {
            if ($key == 'tgl_pembebasantarif') {
                $value = strlen($value) > 0 ? DocoHelpers::convDateTime($value) : $value;
            }
            $cat = $formName . '[' . $key . ']';
            $val[$cat] = $value;
        }
        $val['PembebasanTarifForm[pembebasantarif_id]'] = $id;
        return json_encode($val);
    }

    public function actionDeletePembebasanTarif($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restRajal->delete('tra-pemeriksaan/delete-pembebasan-tarif?id=' . $id);
            $response = json_decode($response->getBody(), true);

            return DocoHelpers::response($response, false, true);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(),
                json_decode($e->getResponse()->getBody()->getContents())->message,
                []
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    private function getRiwayatDiagnosaPasien($pasien_id = null)
    {
        try {
            if (!$pasien_id) {
                return ['data_diagnosa' => []];
            }

            $data_request = $this->_restRajal->get('allow/allow-get-diagnosa-pasien?pasien_id=' . $pasien_id);
            $body = json_decode($data_request->getBody(), TRUE);
            $data_diagnosa = $body['response']['data-diagnosa'];

            return [
                'data_diagnosa' => $data_diagnosa
            ];
        } catch (Exception $e) {
            return [
                'data_diagnosa' => [],
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionRiwayatPasien()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id') ? DocoHelpers::decrypt($request->get('id')) : null;
        $pasien_id = $request->get('pasien_id') ? DocoHelpers::decrypt($request->get('pasien_id')) : null;

        try {
            $response = $this->_restRajal->get('tra-pemeriksaan/index?pasien_id=' . $pasien_id);
            $response = json_decode($response->getBody(), true);
            $data_riwayatpasien = $response["response"]["data"];

            return $this->renderAjax('_pasien_riwayat', [
                'pendaftaran_id' => $pendaftaran_id,
                'pasien_id' => $pasien_id,
                'data_riwayatpasien' => $data_riwayatpasien,
            ]);
        } catch (Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    // Export pdf
    public function actionExportPdfPeriksaFisik($pemeriksaanfisik_id)
    {
        $request = Yii::$app->request;
        \app\components\EsignHelpers::previewEsign([
            'type' => 'Pemeriksaan Fisik',
            'transaksi_id' => $pemeriksaanfisik_id,
        ]);
        // Download path
        $path = Yii::getAlias("@download") . "/pemeriksaan_fisik_" . uniqid() . ".pdf";

        try {
            // Response

            $response = $this->_restRajal->get('tra-pemeriksaan/export-pdf-periksa-fisik?pemeriksaanfisik_id=' . $pemeriksaanfisik_id, [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);

            if (!empty($body['response']['pendaftaran_id']) 
                && !empty($body['response']['key_report'])) {
                
                $pendaftaran_id = $body['response']['pendaftaran_id'];
                $key_report = $body['response']['key_report'];
                return Yii::$app->report->exec("{$key_report}?pendaftaran_id={$pendaftaran_id}");
            }
            
            return DocoHelpers::previewPdf($path, null, true);
            // Download pdf
            // return DocoHelpers::downloadPdf($response, $path, 'pemeriksaan-fisik');
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdfRincianTagihan($pendaftaran_id)
    {
        $request = Yii::$app->request;
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $path = Yii::getAlias("@download") . "/rincian_tagihan.pdf";

        try {
            $response = $this->_restRajal->get('tra-pemeriksaan/print-rincian?id=' . $pendaftaran_id, ['save_to' => $path]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
            // return DocoHelpers::previewPdf($response, $path, 'rincian_tagihan');
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdfSuratKematian($pendaftaran_id)
    {
        $request = Yii::$app->request;
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $path = Yii::getAlias("@download") . "/surat_kematian.pdf";
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];

        try {
            return DocoHelpers::previewPdf((new SuratKematianService)->execute($pendaftaran_id, $this->_id_ruangan));
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    // Update pegawai
    public function actionUpdatePegawai()
    {
        $post = Yii::$app->request->post();

        try {
            // Post
            $response = $this->_restRajal->request('POST', 'tra-pemeriksaan/update-pegawai', ['form_params' => $post]);
            $body = json_decode($response->getBody(), TRUE);
            $model = $body['response']['data'];
            $pendaftaranId = DocoHelpers::encrypt($model['pendaftaran_id']);

            return $this->redirect(['pemeriksaan/periksa?id=' . $pendaftaranId]);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /**
     * @author Randy Vianda Putra
     * @todo Panggil Antrian
     * @copyright 7 May 2018 aweutist
     */
    public function actionPanggilAntrian()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $post = Yii::$app->request->post();

        try {
            $no_antrian = $post['no_antrian'];
            $teks_panggil = DocoHelpers::convertAntrian($no_antrian);
            // $response['teks_panggil'] = $teks_panggil;

            // set ke display antrian poli
            // $data_display["panggil_antrian_poli"] = [
            //     'no_antrian' =>  empty($post['no_antrian']) ? 0 : $post['no_antrian'],
            //     'nama_pasien' =>  empty($post['nama_pasien']) ? 0 : $post['nama_pasien'],
            //     'ruangan_id' => empty($post['ruangan_id']) ? 0 : $post['ruangan_id'],
            //     'pegawai_id' => empty($post['pegawai_id']) ? 0 : $post['pegawai_id'],
            // ];
            $payload = [
                'antrian_id' => ArrayHelper::getValue($post,'antrian_id'),
                'pendaftaran_id' => $post['pendaftaran_id']
            ];

            // Yii::$app->redis->executeCommand('PUBLISH', [
            //     'channel' => 'display-antrian',
            //     'message' => Json::encode(['data' => $data_display])
            // ]);
            //end set antrian
            $response = $this->guzzleExec($this->_restAntrian,
            [
                'method' => 'POST',
                'url' => 'panggil-antrian/poliklinik',
                'payload' => [
                    'form_params' => $payload,
                ],
            ]);

            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    // Get data tindakan bmhp
    public function getDataTindakanBmhp($id)
    {
        // Try catch
        try {
            // Get data tindakan bmhp
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $response = $this->_restRajal->get('tra-pemeriksaan/get-data-tindakan-bmhp?id=' . $id . '&ruangan_id=' . $ruangan_id, []);
            $body = json_decode($response->getBody(), true);
            $body = $body['response'];
            // Declare data temp
            $data = [];
            $tempPaket = [];

            // Check data
            if (!empty($body)) {
                // Loop
                foreach ($body as $key => $value) {
                    // Check paket
                    if ($value['tipe_pelayanan'] == 'PAKET') {
                        // Check
                        if (!in_array($value['tipepaket_id'], $tempPaket)) {
                            // Assign to temp
                            $data[] = $value;
                            $tempPaket[] = $value['tipepaket_id'];
                        }
                    } else if ($value['tipe_pelayanan'] == 'TINDAKAN') {
                        // Check
                        if ($value['tipepaket_id'] == '') {
                            // Assign to temp
                            $data[] = $value;
                        }
                    } else if ($value['tipe_pelayanan'] == 'BMHP') {
                        // Assign to temp
                        $data[] = $value;
                    }
                }
            }

            return $data;
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    // Update stok obat alkes
    public function actionUpdateStokObatAlkes($id, $ruangan_id, $type = 'update')
    {
        // Try catch
        try {
            // Response
            $response = $this->_restRajal->post('tra-pemeriksaan/update-stok-obat-alkes?id=' . $id . '&ruangan_id=' . $ruangan_id . '&type=' . $type, ['form_params' => Yii::$app->request->post()]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response'];

            // Return
            return json_encode($data);
        } catch (RequestException $e) {
            // Response
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Response
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    private function getListDataReseptur()
    {
        $id_ruangan = $this->_id_ruangan;
        $kelaspelayanan_id = $this->_kelaspelayanan_id;
        $penjamin_id = $this->_data_pasien['penjamin_id'];
        $pendaftaran_id = $this->_data_pasien['pendaftaran_id'];
        $pegawai_id = $this->_pegawai_id;

        $response = $this->_restRajal->get('allow/allow-get-list-data-reseptur?', [
            'query' => [
                'id_ruangan' => $id_ruangan,
                'kelaspelayanan_id' => $kelaspelayanan_id,
                'penjamin_id' => $penjamin_id,
                'pendaftaran_id' => $pendaftaran_id,
                'pegawai_id' => $pegawai_id,
            ]
        ]);

        $body = json_decode($response->getBody(), true);
        $data_ruanganapotek = empty($body['response']['data-ruanganapotek']) ? [] : $body['response']['data-ruanganapotek'];
        $data_signa = empty($body['response']['data-signa']) ? [] : $body['response']['data-signa'];
        $data_konfig = empty($body['response']['data-konfigfarmasi']) ? [] : $body['response']['data-konfigfarmasi'];
        $data_template = $body['response']['data-template'];
        $diagnosa = isset($body['response']['data-diagnosa']) ? $body['response']['data-diagnosa'] : null;
        $getPendaftaran = isset($body['response']['data-pendaftaran']) ? $body['response']['data-pendaftaran'] : null;
        $default_depo = isset($body['response']['default_depo']) ? $body['response']['default_depo'] : null;


        $result = [
            'data_ruanganapotek' => $data_ruanganapotek,
            'data_signa' => $data_signa,
            'konfig_farmasi' => $data_konfig,
            'data_template' => $data_template,
            'diagnosa' => $diagnosa,
            'pendaftaran' => $getPendaftaran,
            'default_depo' => $default_depo,
        ];

        return $result;
    }

    public function actionExportPdf($jenis)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $all_ruangan = $request->get('all_ruangan');
        $idR = null;
        if (!$all_ruangan) {
            $idR = Yii::$app->docoVars->workspace("ruangan_id");
        }
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
        $userIdentity = Yii::$app->session->get('user_identity');
        if (Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS) {
            $yiiRestfulParams['advanced-filter']['pegawai_id'] = $userIdentity['id_pegawai'];
            $yiiRestfulParams['advanced-filter']['kelompok_medis'] = DocoConstants::KELOMPOK_MEDIS;
        }
        // $yiiRestfulParams['advanced-filter']['ruangan_id'] = $this->_id_ruangan;
        $path = Yii::getAlias("@download") . "/informasi-pasien-rawat-jalan.pdf";
        try {
            $response = $this->_restRajal->get('tra-pemeriksaan/export-pdf?jenis=' . $jenis . '&ruangan=' . Yii::$app->docoVars->workspace("ruangan_id") . '&idruangan=' . $idR . '&' . http_build_query($yiiRestfulParams), [
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

    public function actionExportExcel($jenis)
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
        $kelompokpegawai_id = Yii::$app->docoVars->user("kelompokpegawai_id");
        $yiiRestfulParams['kelompokpegawai_id'] = $kelompokpegawai_id;
        $yiiRestfulParams['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");

        $path = Yii::getAlias("@download") . "/laporan-diagnosa-pasien.xlsx";

        $response = $this->_restRajal->get('tra-pemeriksaan/export-excel', [
            'query' => $yiiRestfulParams,
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path, true);
    }

    public function actionGetJadwalPoli()
    {
        $ruangan_id = $this->_id_ruangan;
        $api = 'allow/get-jadwal-poli?ruangan_id=' . $ruangan_id;
        $data_id = 'ruangan_id';
        $data_name = ['ruangan_nama'];
        $getRest = $this->_restRajal;

        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 10;
        $offset = ($page - 1) * 10;
        $parsing_data = [];
        $get = null;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $parsing_data = [
            'query' => $get,
            'term' => $request->get('q'),
            'page' => $page,
            'offset' => $offset,
            'limit' => $limit,
            'ruangan_id' => $ruangan_id
        ];
        try {
            $result = $getRest->get($api, [
                'query' => $parsing_data
            ]);
            $result = json_decode($result->getBody(), true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $listString = [];
                foreach ($data_name as $name) {
                    $realName = isset($value[$name]) ? $value[$name] : " ";
                    $listString[] = $realName;
                }
                $text = implode(" - ", $listString);
                $response[] = [
                    'id' => $value['jadwalbukapoli_id'],
                    'text' => $text,
                    'datavalue' => $value
                ];
            }
        } catch (RequestException $e) {
            $response['message'] = $e->getMessage();
        }
        return DocoHelpers::response([
            'result' => $response,
            'total_count' => count($response),
            'incomplete_results' => false,
            'pagination' => ['more' => count($response) === $limit ? true : false]
        ]);
    }

    public function actionGetDataKonsulPoli()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $pendaftaran_id = $request->get('id');
        $preview_only = $request->get('preview_only', false);
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $response = $this->_restRajal->request('get', 'tra-pemeriksaan/get-konsul-poli?id=' . $pendaftaran_id . '&preview_only='.$preview_only.'&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
        $data = [];
        $body = json_decode($response->getBody(), TRUE);
        $no = $request->get('start', 1);

        foreach ($body['response']['data'] as $key => $value) {
            $konsulpoli_id = DocoHelpers::encrypt($value['konsulpoli_id']);
            $no++;
            $value['rowNum'] = $no;
            $value['tgl_konsulpoli'] = date('d-M-Y H:i:s', strtotime($value['tgl_konsulpoli']));
            $value['tgl_selesaikonsul'] = !empty($value['tgl_selesaikonsul']) ? date('d-M-Y H:i:s', strtotime($value['tgl_selesaikonsul'])) : '';
            $value['nama_dokter'] = !empty($value['nama_dokter']) ? '<b>' . $value['ruangan_tujuan'] . '</b> </br> ' . $value['nama_dokter'] . ' </br> ' . $value['catatan_dokter_konsul'] : '';
            $value['ruangan_asal'] = '<b>' . $value['ruangan_asal'] . '</b> </br> ' . $value['dok_mengkonsul'];
            $value['action'] = $this->generateButtonKonsulPoli($value);
            $data[$key] = $value;
        }

        $return = [
            'data' => $data,
            'draw' => $request->get('draw'),
            'recordsTotal' => $body['response']['_meta']['totalCount'],
            'recordsFiltered' => $body['response']['_meta']['totalCount']
        ];
        return DocoHelpers::response($return);
    }

    public function actionPermintaanKonsul()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $konsulpoli_id = $request->get('konsulpoli_id', null);
        $pasien_id = $request->get('pasien_id', null);
        $session = Yii::$app->session;

        $list_data = $this->getListData();
        $data_permintaan_konsul = $list_data['data_permintaan_konsul'];
        $model = new KonsulpoliForm;

        try {
            if ($post = $request->post()) {
                $data = $post['KonsulpoliForm'];
                $model->scenario = 'update';
                $model->attributes = $data_permintaan_konsul;
                $model->tgl_konsulpoli = !empty($data_permintaan_konsul["tgl_konsulpoli"]) ? $data_permintaan_konsul["tgl_konsulpoli"] : date('Y-m-d H:i:s');
                $model->pasien_id = !empty($pasien_id) ? $pasien_id : $data_permintaan_konsul["pasien_id"];
                $model->jawaban_konsul = $data['jawaban_konsul'];
                $model->pegawai_id = Yii::$app->docoVars->user('id_pegawai');
                $model->pendaftaran_id = $pendaftaran_id;
                $model->konsulpoli_id = !empty($konsulpoli_id) ? $konsulpoli_id : DocoHelpers::encrypt($data_permintaan_konsul["konsulpoli_id"]);
                $model->ruangan_id = Yii::$app->session->get('active_workspace')['ruangan_id'];
                $response = $this->_restRajal->post('tra-pemeriksaan/update-permintaan-konsul', [
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response, false, 'KonsulpoliForm');
            } else {
                $tgl_konsulpoli = date('j M Y', strtotime($data_permintaan_konsul['tgl_konsulpoli']));
                $dok_mengkonsul = $data_permintaan_konsul['dok_mengkonsul'];
                $ruangan_tujuan = $data_permintaan_konsul['ruangan_tujuan'];
                $catatan_dokter_konsul = $data_permintaan_konsul['catatan_dokter_konsul'];
                $jawaban_konsul = $data_permintaan_konsul['jawaban_konsul'];
                $model->jawaban_konsul = $jawaban_konsul;

                return $this->renderAjax('__permintaankonsul', [
                    'pendaftaran_id' => $pendaftaran_id,
                    'model' => $model,
                    'tgl_konsulpoli' => $tgl_konsulpoli,
                    'dok_mengkonsul' => $dok_mengkonsul,
                    'ruangan_tujuan' => $ruangan_tujuan,
                    'catatan_dokter_konsul' => $catatan_dokter_konsul,
                    'jawaban_konsul' => $jawaban_konsul
                ]);
            }
        } catch (\Exception $e) {
            $this->logError($e);
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionCetakRincianTagihan($id)
    {
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/cetak-rincian-tagihan-{$pendaftaran_id}.pdf";
        $url = "tra-pemeriksaan/cetak-rincian-tagihan?id=" . $pendaftaran_id;
        try {
            $response = $this->_restRajal->get($url, [
                'save_to' => $path,
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /**
     * List dropdown of doctor
     *
     * @param Integer $page
     * @return JSON
     * @author Aris Munandar
     **/
    public function actionDokterList()
    {
        return $this->helper->guzzleExec($this->_restRajal, [
            'url' => 'tra-pemeriksaan/dokter-list',
            'returnResponse' => true,
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
        ]);
    }

    /**
     * List dropdown of nurse
     *
     * @param Integer $page
     * @return JSON
     * @author Aris Munandar
     **/
    public function actionPerawatList()
    {
        return $this->helper->guzzleExec($this->_restRajal, [
            'url' => 'tra-pemeriksaan/perawat-list',
            'returnResponse' => true,
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
        ]);
    }

    /**
     * List dropdown of nurse
     *
     * @param Integer $page
     * @return JSON
     * @author Aris Munandar
     **/
    public function actionSukuList()
    {
        return $this->helper->guzzleExec($this->_restRajal, [
            'url' => 'tra-pemeriksaan/suku-list',
            'returnResponse' => true,
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
        ]);
    }

    public function actionSaveAsesmenKeperawatan()
    {
        $payload        = Yii::$app->request->post();
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id', null);
        $status         = Yii::$app->request->get('status', null);
        $numberField    = ['berat_badan', 'tinggi_badan', 'td', 'nadi', 'rr', 'suhu'];
        $payload['pendaftaran_id'] = $pendaftaran_id;
        $payload['tgl_anamnesis'] = date_format(date_create_from_format('d/m/Y', $payload['tgl_anamnesis']), 'Y-m-d') . ' ' . date('h:i:s');

        foreach ($numberField as $eachField) {
            if (isset($payload[$eachField])) {
                $payload[$eachField] = trim(str_replace('.', '', $payload[$eachField]));
            }
        }
        $modelValidation = new AsesmenKeperawatan;
        $modelValidation->attributes = $payload;
        if (!$modelValidation->validate()) {
            return $this->helper->macroResponseJson(422, 'Silakan cek kembali inputan.', $this->helper->mapErrorForm($modelValidation->errors, 'AsesmenKeperawatan'));
        } else {
            $result = $this->guzzleExec($this->_restRajal, [
                'url' => 'asesmen-keperawatan/save-asesmen',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'formdata' => $payload
                    ]
                ]
            ]);
            $cache = Yii::$app->cache;
            $pendId = $this->helper->encrypt($result['data']['pendaftaran_id']);
            $cacheData = $cache->get('pasien-pendaftaran-id-' . $pendId);

            $cacheData['askep']['riwayat_penyakit_keluarga'] = $result['data']['riwayat_penyakit_keluarga_list'];
            $cacheData['askep']['status_merokok'] = $result['data']['status_merokok'];
            $cacheData['askep']['status_ekonomi'] = $result['data']['status_ekonomi'];
            Yii::$app->cache->delete('data-riwayat-pasien-'. $this->_data_pasien['pasien_id']);

            $cache->set('pasien-pendaftaran-id-' . $pendId, $cacheData, 3600);
            return $this->helper->response($result, 200);
        }
    }

    /**
     * List dropdown of doctor
     *
     * @param Integer $page
     * @return JSON
     * @author Aris Munandar
     **/
    public function actionAllDokterList()
    {
        $dokterList = $this->helper->guzzleExec($this->_restRajal, [
            'url' => 'tra-pemeriksaan/all-dokter-list',
            'returnResponse' => true,
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
        ]);

        $payload = Yii::$app->request->get('payload', []);
        if ($payload['page'] == 1 && !empty($dokterList['data'])) {
            $data = $dokterList['data'];
            $allData = [
                'id' => '%',
                'text' => \Yii::t('fe', 'Semua Dokter')
            ];
            array_unshift($data, $allData);
            $dokterList['data'] = $data;
        }

        return $dokterList;
    }

    private function generateButtonKonsulPoli($data)
    {
        $konsulpoli_id = DocoHelpers::encrypt(ArrayHelper::getValue($data, 'konsulpoli_id'));
        $rencanakontrol_id = DocoHelpers::encrypt(ArrayHelper::getValue($data, 'rencanakontrol_id'));
        $html = '';
        $html .= "<button class='btn btn-xs btn-info btn-labeled' onclick=cetakKonsul('".$konsulpoli_id."') type='button'><b class='fa fa-file-pdf-o'></b> Permintaan Konsultasi</button>";
        if (ArrayHelper::getValue($data, 'status_konsul_id') == DocoConstants::STATUS_KONSUL_DIJAWAB) {
            $html .= "<button class='btn btn-xs btn-info btn-labeled' onclick=cetakJawabanKonsul('".$konsulpoli_id."') type='button'><b class='fa fa-file-pdf-o'></b> Jawaban Konsultasi</button>";
        }

        if (!empty(ArrayHelper::getValue($data, 'rencanakontrol_id'))) {
            $html .= "<button class='btn btn-xs btn-info btn-labeled' onclick=cetakRencanaKontrol('".$rencanakontrol_id."') type='button'><b class='fa fa-file-pdf-o'></b> Surat Rencana Kontrol</button>";
        }
        return $html;
    }
}
