<?php


namespace Doco\ranap\controllers;


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
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

use app\modules\ranap\models\AsesmenAwalForm;
use app\modules\ranap\models\DietPasienForm;
use app\modules\ranap\components\traits\PemeriksaanAsesmenAwalTrait;
use app\modules\ranap\components\traits\PemeriksaanAsesmenMedisTrait;
use app\modules\ranap\components\traits\PemeriksaanRekonsiliasiObatTrait;
use app\modules\ranap\components\traits\PemeriksaanCpptTrait;
use app\modules\ranap\components\traits\PemeriksaanDischargePlanningTrait;
use app\modules\ranap\components\traits\PemeriksaanImplementasiTrait;
use app\modules\ranap\components\traits\PemberianObatTrait;
use app\modules\ranap\components\traits\PemeriksaanPemintaanKonsulTrait;
use app\modules\ranap\components\traits\ResumeMedisTrait;
use app\modules\ranap\components\traits\ResumeMedisRiTrait;
use app\modules\ranap\components\traits\PemeriksaanPermintaanMakanTrait;
use app\modules\ranap\components\traits\PemeriksaanPartografTrait;
use app\modules\ranap\components\traits\UploadDokumenViewTrait;
use app\components\Traits\CathlabTrait;
use app\components\Traits\HistoryPatientTrait;
use app\components\Traits\TindakanBmhpTrait;
use app\components\Traits\GiziTrait;
use app\components\Traits\HistoryFisioTrait;
use app\modules\ranap\components\traits\PemberianInfusTrait;
use app\modules\ranap\components\traits\PemeriksaanPenunjangTrait;
use app\modules\ranap\components\traits\PemeriksaanResepturTrait;
use app\modules\ranap\components\traits\HistoryAssesmenKeperawatanTrait;
use app\components\Traits\Pelayanan\NursingNoteTrait;
use app\components\Traits\LaporanTindakanTrait;
use app\modules\ranap\components\traits\PagtTrait;
use app\components\Traits\Pelayanan\TerraMedikTrait;
use app\components\Traits\SuratKeteranganTrait;
use app\components\Traits\ICareTrait;
use app\modules\ranap\components\traits\AsmedRanapTrait;
use app\components\Traits\MonitoringTtvTrait;
use app\components\Traits\ObservasiEwsTrait;
use app\components\Traits\SbarTrait;

class PemeriksaanRawatInapController extends DocoController
{
	protected $_title = "Ranap :: Asesmen Keperawatan";
    protected $_module = '/ranap/asesmen-keperawatan';
    protected $_controller = '/ranap/informasi';
    protected $_page;
    protected $_restRanap;
    protected $_restRajal;
    protected $_restIgd;
    protected $_restGizi;
    protected $_restApotek;
    protected $_id_ruangan;
    protected $_instalasi_id;
    protected $_pegawai_id;
    protected $_pasien_id;
    protected $_pasienadmisi_id;
    protected $_pendaftaran_id;
    protected $_kelaspelayanan_id;
    protected $_jeniskelamin;
    protected $_data_pasien;
    protected $_list_data;
    protected $_user_identity;
    protected $_discharge_plan;
    protected $_rencana_pulang;
    protected $_obat_darirumah;
    protected $_restDefault;

    public $_cacheRekon;


    protected $allowAction = ['*'];

    //traits
    use PemeriksaanAsesmenAwalTrait;
    use PemeriksaanAsesmenMedisTrait;
    use PemeriksaanRekonsiliasiObatTrait;
    use PemeriksaanPemintaanKonsulTrait;
    use PemeriksaanCpptTrait;
    use PemeriksaanDischargePlanningTrait;
    use PemeriksaanImplementasiTrait;
    use PemberianObatTrait;
    use ResumeMedisTrait;
    use ResumeMedisRiTrait;
    use PemeriksaanPermintaanMakanTrait;
    use PemeriksaanPartografTrait;
    use CathlabTrait;
    use HistoryPatientTrait;
    use PemeriksaanPenunjangTrait;
    use TindakanBmhpTrait;
    use PemeriksaanResepturTrait;
    use NursingNoteTrait;
    Use GiziTrait;
    Use LaporanTindakanTrait;
    use UploadDokumenViewTrait;
    use HistoryAssesmenKeperawatanTrait;
    use HistoryFisioTrait;
    use PemberianInfusTrait;
	use TerraMedikTrait;
    use SuratKeteranganTrait;
    use ICareTrait;

    use PagtTrait;
    use AsmedRanapTrait;
    use MonitoringTtvTrait;
    use ObservasiEwsTrait;
    use SbarTrait;

    public function init()
    {
        parent::init();
        $session = Yii::$app->session;
        $userIdentity = Yii::$app->session->get('user_identity');
        $docoVars = Yii::$app->docoVars;
        $this->_restRanap = Yii::$app->docoRest->ranap;
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_restIgd = Yii::$app->docoRest->igd;
        $this->_restGizi = Yii::$app->docoRest->gizi;
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_id_ruangan = $docoVars->workspace('ruangan_id') ? $docoVars->workspace('ruangan_id') : 1;
        $this->_instalasi_id = $docoVars->workspace('instalasi_id') ? $docoVars->workspace('instalasi_id') : 1;
        $this->_pegawai_id = $docoVars->user('id_pegawai') ? $docoVars->user('id_pegawai') : 1;
        $this->_page = Yii::t('fe', 'Pemeriksaan pasien rawat jalan');
        $this->_restDefault = $this->_restRanap;
        $data_pasien = [];
        $request = Yii::$app->request;
        $pendId = $request->get('id', null);
        $pendaftaran_id = (new PelayananHelpers)->decryptId($pendId);
        $data_pasien = [];
		$temp_pasien = $session['pasien-pendaftaran-id-'.$pendId];
		$cache_data_pasien = Yii::$app->cache->get('pasien-pendaftaran-id-'. $pendId);
		$cache_riwayat_pasien = isset($cache_data_pasien['pasien_id']) ? Yii::$app->cache->get('data-riwayat-pasien-'. $cache_data_pasien['pasien_id']) : null;
        try {
            if(((!empty($cache_data_pasien) && !isset($cache_data_pasien['pendaftaran_id'])) || !$cache_data_pasien && !empty($pendId)) || empty($cache_data_pasien) ){
                $response = $this->_restRanap->get(
                    'pemeriksaan-rawat-inap/get-pasien'
                    , [
                    'query' => [
                        'id' => $pendaftaran_id,
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
                    if(isset($data_pasien['is_stoptitipan'])){
                        if ($data_pasien['is_stoptitipan'] == false) {
                            if ($data_pasien['is_pasientitipan']) {
                                $data_pasien['titipan'] = true;
                                $data_pasien['kelas_ditagihkan'] = strtoupper($data_pasien['kelas_ditagihkan_nama']);
                            }
                        }
                    }
                }

                Yii::$app->cache->set('pasien-pendaftaran-id-'. $pendId, $data_pasien, 3600);
				$riwayat_pasien = ArrayHelper::getValue($data_pasien, 'riwayat_pasien');
				if($riwayat_pasien){
                    Yii::$app->cache->set('data-riwayat-pasien-'. $data_pasien['pasien_id'], $data_pasien['riwayat_pasien'], 3600);
				}
            }else{
                $data_pasien = Yii::$app->cache->get('pasien-pendaftaran-id-'. $pendId);
                if (isset($data_pasien['pasien_id'])) {
                    $data_pasien['riwayat_pasien'] = Yii::$app->cache->get('data-riwayat-pasien-' . $data_pasien['pasien_id']);
                }
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
        $this->_pendaftaran_id = $pendId;
        $this->_kelaspelayanan_id = isset($data_pasien['kelaspelayanan_id']) ? $data_pasien['kelaspelayanan_id'] : null;
        $this->_jeniskelamin = !empty($data_pasien['jeniskelamin']) ? $data_pasien['jeniskelamin'] : null;
        $this->_data_pasien = !empty($data_pasien) ? $data_pasien : [];
        $this->_rencana_pulang = isset($data_pasien['rencana_pulang']) ? $data_pasien['rencana_pulang'] : null;
        // $this->_discharge_plan = isset($data_pasien['discharge_plan']) ? $data_pasien['discharge_plan'] : null;
        $this->_discharge_plan = 1;
        $this->_obat_darirumah = isset($data_pasien['obat_darirumah']) ? $data_pasien['obat_darirumah'] : null;
        // kebutuhan kelompok pegawai
        $this->_user_identity = $userIdentity;

        // kebutuhan rekonsiliasi obat
        $ruangan_id = $docoVars->workspace("ruangan_id");
        $this->_cacheRekon = 'rekon-obat-' . $userIdentity['id_pegawai'] . '-'. $ruangan_id;

        // cathlab requirement
        $this->instalasi = 'ranap';
        $this->restGeneral = $this->_restRanap;
        $this->type = 'RI';
    }

    public function actions()
    {
        $actions = parent::actions();
        $newActions = [
            'fisioterapi-show-modal-available-program' => 'Doco\ranap\actions\PemeriksaanRawatInap\Fisioterapi\ShowModalAvailableProgramAction'
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

    public function getStatusPeriksa($pendaftaran_id)
    {
        $getPasien = $this->_restRanap->get('pemeriksaan-rawat-inap/get-pasien?id='.$pendaftaran_id, ['form_params' => []]);
        $DataBody = json_decode($getPasien->getBody(), True);
        $data_pasien = $DataBody['response']['data'];
        $result = false;
        if(!empty($data_pasien)){
            if($data_pasien['pasienpulang_id'] != NULL){
                $result = true;
            }
        }
        // echo "<pre>";var_dump($result);die();
        return $result;
    }

    public function actionPeriksa($id)
    {

        $title = Yii::t('fe', 'Pemeriksaan pasien');
        $sub_title = $this->_page;
        $rencana_pulang = $this->_rencana_pulang;
        $discharge_plan = $this->_discharge_plan;
        $obat_darirumah = $this->_obat_darirumah;
        $jeniskasuspenyakit = null;
        $userIdentity = Yii::$app->session->get('user_identity');
        $classKelompokpegawai = 'hidden';
        if(isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS){
            $classKelompokpegawai = '';
        }
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $this->_pendaftaran_id = $pendaftaran_id;
        $model = new AsesmenAwalForm;
        $cpptId = Yii::$app->request->get('cpptId', null);
        $resumeTab = Yii::$app->request->get('resume', null);
        $cathlabTabs = isset($this->_data_pasien['hasCathlab']) && $this->_data_pasien['hasCathlab'] ? true : false;
        $showTtvTab = isset($this->_data_pasien['showTtvTab']) && $this->_data_pasien['showTtvTab'] ? true : false;
        $showEwsTab = isset($this->_data_pasien['showEwsTab']) && $this->_data_pasien['showEwsTab'] ? true : false;
        $showSbarTab = isset($this->_data_pasien['showSbarTab']) && $this->_data_pasien['showSbarTab'] ? true : false;
        if($this->_data_pasien['status_ranap'] == 440){
            $updateStatusPasien = $this->helper->guzzleExec($this->_restRanap, [
                'url' => 'inf-pasien-ranap/update-status-periksa',
                'payload' => [
                    'query' => [
                        'id' => $id
                    ]
                ]
            ]);
            $this->_data_pasien['status_ranap'] = $updateStatusPasien['status_ranap'];
            $this->_data_pasien['stat_ranap'] = $updateStatusPasien['stat_ranap'];
            $this->_data_pasien['status_periksa_nama'] = $updateStatusPasien['stat_ranap'];

            $cache = Yii::$app->cache;
            $cacheData = $cache->get('pasien-pendaftaran-id-' . $id);
            $cacheData['status_ranap'] = $updateStatusPasien['status_ranap'];
            $cacheData['stat_ranap'] = $updateStatusPasien['stat_ranap'];
            $cacheData['status_periksa_nama'] = $updateStatusPasien['stat_ranap'];
            $cache->set('pasien-pendaftaran-id-' . $id, $cacheData, 3600);
            $cache->delete('gizi-pendaftaran-id-' . $id);
        }
        try{
            $data_pasien = $this->_data_pasien;
            $pasien_id = $data_pasien['pasien_id'];
            $pasienadmisi_id = $data_pasien['pasienadmisi_id'];
            $showribbon = false;
            $disabled = (!empty($data_pasien['pasienpulang_id']) || $data_pasien['is_stopakomodasi'] == true) ? true : false;
            if (($data_pasien["group_carabayar"] === DocoConstants::GROUP_BPJS) && ($data_pasien["status_monitor"] === 'SUDAH DIMONITOR')){
                $showribbon = true;
                $tagihan_rs = $data_pasien["tagihan_rs"];
                $tarif_inacbg = $data_pasien["tarif_inacbg"];
                $hitung_tarifbpjs = (($tarif_inacbg!=0)?($tagihan_rs/$tarif_inacbg)*100:0);

                if ($hitung_tarifbpjs < 75) {
                    $ribbon = 'ribbon3';
                    $pesan = 'Alokasi < 75%';
                }else if (($hitung_tarifbpjs >= 75) && ($hitung_tarifbpjs < 100)) {
                    $ribbon = 'ribbon2';
                    $pesan = 'Alokasi >= 75% & < 100%';
                }else if ($hitung_tarifbpjs >= 100) {
                    $ribbon = 'ribbon1';
                    $pesan = 'Alokasi >= 100%';
                }
            }

            /* trello: https://trello.com/c/zZlSPNTI */
            if (!empty($data_pasien['konsul_dokter_id'])) {
                if (!($data_pasien['jenis_konsul'] == DocoConstants::JNS_KNSL_AR && $data_pasien['jenis_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU)) {
                    $data_pasien['nama_pegawai'] = $data_pasien['admisi_dokter'];
                }
            }

            $pasien_id = (!empty($data_pasien['pasien_id'])) ? DocoHelpers::encrypt($data_pasien['pasien_id']) : null;
            $kelaspelayanan_id = (!empty($data_pasien['kelaspelayanan_id'])) ? DocoHelpers::encrypt($data_pasien['kelaspelayanan_id']) : null;
            $response = $this->_restRanap->get('pemeriksaan-rawat-inap/get-bundle-data-asesmen-nyeri-to-periksa-cppt',
            ['query'=>['pendaftaran_id'=>$pendaftaran_id,'pasienadmisi_id'=>$pasienadmisi_id]]);
            $response = json_decode($response->getBody(), true);
            $resume_medis = false;
            $info_cppt = $response['response']['info_cppt'];
            if($info_cppt['is_instruksi_pulang'] == true){
                $resume_medis = true;
            }

            $jeniskasuspenyakit = isset($data_pasien['jeniskasuspenyakit_id']) ? $data_pasien['jeniskasuspenyakit_id'] : null;

            $diagnosa_nama = '-';
            if (isset($data_pasien['diagnosa_nama']) && $data_pasien['diagnosa_nama'] != '') {
                $diagnosa_nama = explode('_', $data_pasien['diagnosa_nama']);

                if (isset($diagnosa_nama[1])) {
                    $diagnosa_nama = $diagnosa_nama[1];
                } else {
                    $diagnosa_nama = $diagnosa_nama[0];
                }
            }
            $thirdApp = Yii::$app->params->thirdApp;
            $tabs = Yii::$app->params->ranapTabs;
            $iCare = Yii::$app->params->iCare;
            $skor = empty($data_pasien['skor']) ? 0 : $data_pasien['skor'];
            $hasAccessIcare = DocoHelpers::checkButtonAccess('/ranap/pemeriksaan-rawat-inap', 'icare');
						$ruanganperiksa_id = DocoHelpers::encrypt(ArrayHelper::getValue($data_pasien, 'ruangan_id', null));

            $query_params_icare = [
                'icare_identifier' => ArrayHelper::getValue($data_pasien, 'icare_identifier'),
                'no_pendaftaran' => ArrayHelper::getValue($data_pasien, 'no_pendaftaran'),
                'no_rekam_medik' => ArrayHelper::getValue($data_pasien, 'no_rekam_medik'),
                'nama_pasien' => ArrayHelper::getValue($data_pasien, 'nama_pasien'),
                'id_pegawai' => $userIdentity['id_pegawai'],
            ];
            return $this->render('periksa', get_defined_vars());
        } catch (RequestException $e){
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(400,Yii::t("fe","Terdapat kesalahan"));
        } catch (\Exception $e){
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(400,Yii::t("fe","Terdapat kesalahan"));
        }
    }

    private function initListDataAllow()
    {
        $result = [
            'initAllow' => true,
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
            'data_jenisinstruksi' => [],
            'is_titipan' => [],
            'data_titipan' => [],
        ];

        return $result;
    }

    /**
     *
     * private function
     *
     */
    private function getListDataAllow()
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
            'data_jenisinstruksi' => []
        ];

        try {
            $response = $this->_restRanap->get('allow/allow-get-list-data?id_ruangan=' . @$this->_id_ruangan .'&kelaspelayanan_id='. @$this->_kelaspelayanan_id.'&penjamin_id='.@$this->_data_pasien['penjamin_id'].'&pendaftaran_id='.@$this->_data_pasien['pendaftaran_id']);

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
            $data_obatalkes = $this->getListStokObatAlkes(@$this->_id_ruangan, @$this->_data_pasien['penjamin_id'], @$this->_kelaspelayanan_id);
            $data_satuantindakan = empty($body['response']['data-satuantindakan']) ? [] : $body['response']['data-satuantindakan'];
            $data_ruanganapotek = empty($body['response']['data-ruanganapotek']) ? [] : $body['response']['data-ruanganapotek'];
            $data_signa = empty($body['response']['data-signa']) ? [] : $body['response']['data-signa'];
            $data_konfig = empty($body['response']['data-konfigfarmasi']) ? [] : $body['response']['data-konfigfarmasi'];
            $count = isset($body['response']['count-riwayat']) ? $body['response']['count-riwayat'] : 0;
            $data_jenisinstruksi = empty($body['response']['data-jenisinstruksi']) ? [] : $body['response']['data-jenisinstruksi'];

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
                'data_jenisinstruksi' => $data_jenisinstruksi
            ];

            return $result;
        } catch (RequestException $e) {
            return $this->initListDataAllow();
        } catch (\Exception $e) {
            return $this->initListDataAllow();
        }
    }

    // Get data tindakan bmhp
    public function getDataTindakanBmhp($id)
    {
        // Get data tindakan bmhp
        $response = $this->_restRanap->get('pemeriksaan-rawat-inap/get-data-tindakan-bmhp?id='.$id, []);
        $body = json_decode($response->getBody(), true);
        $body = $body['response'];

        // Declare data temp
        $data = [];
        $tempPaket = [];

        // Check data
        if (!empty($body)) {
            // Loop
            foreach ($body as $key => $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    // Update stok obat alkes
    public function actionUpdateStokObatAlkes($id, $ruangan_id, $type = 'update')
    {
        // Try catch
        try {
            // Response
            $response = $this->_restRanap->post('pemeriksaan-rawat-inap/cppt-update-stok-obat-alkes?id='.$id.'&ruangan_id='.$ruangan_id.'&type='.$type, ['form_params' => Yii::$app->request->post()]);
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

    public function getListStokObatAlkes($ruanganId, $penjaminId, $kelas, $page = null, $keyword = null, $group_jenis = null)
    {
        $params = [
            'instalasi_id' => @$this->_instalasi_id,
            'ruangan_id' => $ruanganId,
            'penjamin_id' =>$penjaminId,
            'group_jenisobat' => $group_jenis,
            'kelaspelayanan_id' => $kelas,
            'page' => $page,
            'keyword' => $keyword
        ];

        try {
            /** Get list stok obat alkes dari apotek */
            $response = $this->_restApotek->get('allow/get-list-stok-apotek', [
                'query' => $params,
            ]);

            $body = json_decode($response->getBody(), true);
            $response = isset($body['response']['data']) ? $body['response']['data'] : [];

            return $response;
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

        public function actionModalHistoryTerraMedikSoap()
    {
        $request      = Yii::$app->request;
        $get          = $request->get();
        $pasien_terra = $get['pasien_id'];

        try {
            $title = Yii::t('fe', 'Arsip Riwayat Pasien');
            return $this->renderAjax('__modal_view_terra_medik_soap', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    // Get data
    public function actionGetDataHistoryCpptTerraMedik()
    {
        // Try catch
        try {
            // Inisiasi
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params                     = Yii::$app->request;
            $yiiRestfulParams           = DocoDatatableHelper::convertToRestfulParams($params->get());
            $draw                       = $params->get('draw', 1);
            $data                       = [];
            $pasien_terra               = DocoHelpers::decrypt($params->get('pasien_terra'));

            // Inisiasi result
            $result                    = [];
            $result['data']            = $data;
            $result['draw']            = $draw;
            $result['recordsTotal']    = 0;
            $result['recordsFiltered'] = 0;

            // Get request
            $request = $this->_restRanap->get('pemeriksaan-rawat-inap/history-cppt-terra-medik?pasien_terra='.$pasien_terra.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $response = json_decode($request->getBody(), true);

            // Inisiasi nomor
            $no = $params->get('start', 1);

            // Loop untuk membuat array dari response
            foreach ($response['response']["data"] as $key => $value) {
                $no++;
                // Assign data
                $dokter = isset($value['nama_dokter']) ? $value['nama_dokter'] : null;
                $data[$key]['primary'] = DocoHelpers::encrypt($value['riwayatsoap_id']);
                $data[$key]['no'] = $no;
                $data[$key]['ruang'] = $value['tipe_pendaftaran'].'<br>'.date('d-m-Y / H:i:s', strtotime($value['tgl_soap'])).'<br>'.$dokter;
                $data[$key]['soap'] = $value['soap'];
                $data[$key]['resep'] = $value['resep'];
                $data[$key]['dokter_id'] = $value['dokter_id'];

            }
            $result['data'] = $data;
            $result['recordsTotal'] = $response['response']["total"];
            $result['recordsFiltered'] = $response['response']["total"];

            return $result;
        } catch (RequestException $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

	// Get data
	public function actionGetDataHistoryResepTerraMedik()
	{
		// Try catch
		try {
			// Inisiasi
			Yii::$app->response->format = Response::FORMAT_JSON;
			$params                     = Yii::$app->request;
			$yiiRestfulParams           = DocoDatatableHelper::convertToRestfulParams($params->get());
			$draw                       = $params->get('draw', 1);
			$data                       = [];
			$pasien_terra               = DocoHelpers::decrypt($params->get('pasien_terra'));

			// Inisiasi result
			$result                    = [];
			$result['data']            = $data;
			$result['draw']            = $draw;
			$result['recordsTotal']    = 0;
			$result['recordsFiltered'] = 0;

			// Get request
			$request = $this->_restRanap->get('pemeriksaan-rawat-inap/history-resep-terra-medik?pasien_terra=' . $pasien_terra . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
			$response = json_decode($request->getBody(), true);

			// Inisiasi nomor
			$no = $params->get('start', 1);

			// Group by the array
			$array_group_by = ArrayHelper::index($response['response']["data"], null, [function($element){
						return $element['kode_trans'];
				}, 'no_resep']);

			foreach ($array_group_by as $key => $val_index) {
				foreach ($val_index as $key => $val) {
					$no++;
					array_push($data, [
						'id' => $val[0]['kode_trans'],
						'no' => $no,
						'no_rm' => $val[0]['no_rm'],
						'no_resep' => $val[0]['no_resep'],
						'nama_pasien' => $val[0]['nama_pasien'],
						'dokter_id' => $val[0]['dokter_id'],
						'detail' => ArrayHelper::getColumn($val, function ($element) {
								return [
									'nama_barang' => $element['nama_barang'],
									'satuan' => $element['satuan'],
									'jumlah' => $element['jumlah'],
									'signa' => $element['signa']
								];
							 })
					]);
				}

			}

			$result['data'] = $data;
			$result['recordsTotal'] = count($data);
			$result['recordsFiltered'] = count($data);

			return $result;
		} catch (RequestException $e) {
			$this->logError($e);
			return DocoHelpers::dataTabelsException($e->getMessage());
		} catch (\Exception $e) {
			$this->logError($e);
			return DocoHelpers::dataTabelsException($e->getMessage());
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
        $dokterList = $this->helper->guzzleExec($this->_restRanap, [
            'url' => 'pemeriksaan-rawat-inap/all-dokter-list',
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

    /** 
     * cek status periksa ranap
     */
    public function getStatusPeriksaRanap()
    {
        $result = false;
        if (isset($this->_data_pasien['pasienpulang_id']) && $this->_data_pasien['pasienpulang_id'] != null) {
            $result = true;
        }
        return $result;
    }

    /** 
     * get list data allow 
     * di pindah dari init ke fucntion 
     * untuk mengurangi load data tab cppt
     */
    public function getRanapListDataAllow()
    {
        $session = Yii::$app->session;
        if(!$session['ranap-list-data-allow-'.$this->_pendaftaran_id]){
            $this->_list_data = $this->getListDataAllow();
            $session['ranap-list-data-allow-'.$this->_pendaftaran_id] = $this->_list_data;
        }else{
            $this->_list_data = $session['ranap-list-data-allow-'.$this->_pendaftaran_id];
        }
    }
}
