<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoConstansId;
use Doco\Services\Cache;

use app\modules\v1\models\Cppt;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\LookupKeperawatan;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\AsesmenAwal;
use app\modules\v1\models\AsesmenMedis;
use app\modules\v1\models\AsesmenAwalT;
use app\modules\v1\models\Tindakankomponen;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\StokObatAlkesR;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\InfoTarifRs;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\InfoPasienRjView;
use app\modules\v1\models\RiwayatTindakanView;
use app\modules\v1\models\RiwayatInstruksiTindakanView;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\Instruksi;
use app\modules\v1\models\InstruksiTindakan;
use app\modules\v1\models\InstruksiTindakanBmhp;
use app\modules\v1\models\GolonganOperasi;
use app\modules\v1\models\PaketDetailView;
use app\modules\v1\models\InfoPasienRanap;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\SkriningGizi;
use app\modules\v1\models\SkriningGiziView;
use app\modules\v1\models\Gcs;
use app\modules\v1\models\MetodeGcs;
use app\modules\v1\models\Pekerjaan;
use app\modules\v1\models\Pendidikan;
use app\modules\v1\models\Suku;
use app\modules\v1\models\InfoStokObatAlkesFn;
use app\modules\v1\models\ResumeMedisRIT;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\RiwayatSoapTerra;
use app\modules\v1\models\RiwayatResepTerra;
use app\modules\v1\models\PermintaanMakan;
use app\modules\v1\models\InfoInstruksiView;
use app\modules\v1\models\ResikoJatuh;
use app\modules\v1\models\AsesmenResikoJatuh;
use app\modules\v1\models\AsesmenResikoJatuhSydneyRD;
use app\modules\v1\models\AsesmenResikoJatuhDumptyRD;
use app\modules\v1\models\AsesmenResikoJatuhMorseRD;
use app\modules\v1\models\FGetinstruksi;
use app\modules\v1\models\MasukKamar;
use app\modules\v1\models\PasienAdmisi;
use DateTime;
use Doco\models\bpjs\Bpjs;
use Doco\Traits\TindakanBmhpTrait;
use Doco\Traits\GiziTrait;
use Doco\Traits\PasienTrait;
use Doco\models\InfoStokObatAlkesFnr;
use Doco\models\InfoStokObatAlkesFnrNew;
use Doco\models\Pasien;
use Doco\Traits\TerraMedikTrait;
use Doco\Traits\SuratKeteranganTrait;
use Doco\Traits\ICareTrait;
use SirsCore\businessLogic\MonitoringTtvLogic;
use Doco\Traits\ObservasiEwsTrait;
use Doco\Traits\SbarTrait;

class PemeriksaanRawatInapController extends DocoActiveController
{
    use TindakanBmhpTrait;
    use GiziTrait;
    use TerraMedikTrait;
    use PasienTrait;
    use SuratKeteranganTrait;
    use ICareTrait;
    use ObservasiEwsTrait;
    use SbarTrait;

    public $modelClass = 'app\modules\v1\models\InfoRiwayatPasienView';

    protected $askepModel;

    /**
     * this function will override init of controller
     *
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function init()
    {
        parent::init();
        $this->type = 'RI';
        $this->resumeModel = new ResumeMedisRIT;
        $this->infoPasienRiModel = new InfoPasienRiView;
        $this->askepModel = new AsesmenAwal;
    }

    /**
     *
     * @see Fungsi get pasien
     * @return array, activeQueryRecords data pasien
     *
     */
    public function actionGetPasien()
    {
        // $cache = Yii::$app->cache;
        // $data = $cache->get('$key');
        try {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $query = $this->getDataPasien($pendaftaran_id)->limit(1)->asArray()->one();
        if(empty($query)){
            $query = $this->getDataPasienRS($pendaftaran_id)->limit(1)->asArray()->one();
        }
        $countCathlabData = 0;
        if (!empty($pendaftaran_id)) {
            $constCathlab = $this->constans->actionGetId('kdtindakan_cathlab');
            $countCathlabData = (new \app\modules\v1\models\InstruksiTindakan)->find()->select([
                'instruksitindakan_t.daftartindakan_id',
                'daftartindakan_m.kelompoktindakan_id',
            ])
                ->rightJoin('daftartindakan_m', 'daftartindakan_m.daftartindakan_id = instruksitindakan_t.daftartindakan_id')
                ->rightJoin('instruksi_t', 'instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id')
                ->rightJoin('cppt_t', 'instruksi_t.cppt_id = cppt_t.cppt_id')
                ->andWhere([
                    'cppt_t.pendaftaran_id' => $pendaftaran_id,
                    'daftartindakan_m.kelompoktindakan_id' => $constCathlab,
                ])->andWhere([
                    'IS NOT', 'kamar_tempattidur', new \yii\db\Expression('null')
                ])->count();
            // Cek kelas pelayanan khusus [HCU, ICU, ICCU]
            $kelasKhusus = $this->constans->actionGetAdditional('kelas_khusus', true);
            if (in_array($query['kelaspelayanan_id'], $kelasKhusus)) {
                if (isset($query['is_pasientitipan']) && !$query['is_pasientitipan']) {
                    $subQuery = MasukKamar::find()
                        ->select([
                            'pasienadmisi_id',
                            'MAX(masukkamar_id) AS masukkamar_id'
                        ])->groupBy('pasienadmisi_id');
                    $masukkamar_id = PasienAdmisi::find()
                        ->select(['masukkamar.masukkamar_id'])
                        ->innerJoin(['masukkamar' => $subQuery], 'pasienadmisi_t.pasienadmisi_id = masukkamar.pasienadmisi_id')
                        ->where([
                            'pasienadmisi_t.pasienadmisi_id' => $query['pasienadmisi_id']
                        ])
                        ->scalar();

                    $prevKelasPelayanan = MasukKamar::find()
                        ->select([
                            new \yii\db\Expression('COALESCE(masukkamar_t.kelaspelayanan_id, pasienadmisi_t.kelaspelayanan_id) AS kelaspelayanan_id')
                        ])
                        ->innerJoin('pasienadmisi_t', 'masukkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id')
                        ->where([
                            '<>', 'masukkamar_id', $masukkamar_id
                        ])
                        ->andWhere([
                            'pasienadmisi_t.pasienadmisi_id' => $query['pasienadmisi_id']
                        ])
                        ->andWhere([
                        'NOT IN', 'masukkamar_t.kelaspelayanan_id', $kelasKhusus
                        ])
                        ->orderBy([
                            'masukkamar_id' => SORT_DESC
                        ])
                        ->limit(1)->scalar();
                    $query['previous_kelas_pelayanan'] = $prevKelasPelayanan ? $prevKelasPelayanan : null;
                }
            }
            $query['riwayat_pasien'] = $this->getRiwayatPasienTerbaru($query['pasien_id']); //@use PasienTrait function
        }
        $query['hasCathlab'] = $countCathlabData ? true : false;
        $query['icare_identifier'] = null;

        $showTtvTab = false;
        $showEwsTab = false;
        $showSbarTab = false;
        $getKonfigSystem = Cache::getKonfigSistem();
        $konfigObservation = ArrayHelper::getValue($getKonfigSystem, 'konfig_observasi_ews', null);
        $konfigSystem = ArrayHelper::getValue($getKonfigSystem, 'konfig_monitoring_ttv', null);
        $konfigSbar = ArrayHelper::getValue($getKonfigSystem, 'konfig_sbar', null);

        if(!is_null($konfigSystem) && !is_array($konfigSystem)) {
            $konfigSystem = json_decode($konfigSystem, true);
        }

        if (isset($konfigSystem['RI']) && $konfigSystem['RI'] == true) {
            $showTtvTab = true;
        }
        
        if (!is_null($konfigObservation) && !is_array($konfigObservation)) {
            $konfigObservation = json_decode($konfigObservation, true);
        }

        if (isset($konfigObservation['RI']) && $konfigObservation['RI'] == true) {
            $showEwsTab = true;
        }

        if (!is_null($konfigSbar) && !is_array($konfigSbar)) {
            $konfigSbar = json_decode($konfigSbar, true);
        }

        if (isset($konfigSbar['RI']) && $konfigSbar['RI'] == true) {
            $showSbarTab = true;
        }

        $query['showTtvTab'] = $showTtvTab ? true : false;
        $query['showEwsTab'] = $showEwsTab ? true : false;
        $query['showSbarTab'] = $showSbarTab ? true : false;

        if(!empty($pendaftaran_id)){
            // get no kartu BPJS untuk kebutuhan iCare
            $lastDataBpjs = Bpjs::find()->select(['bpjs_id', 'pendaftaran_id', 'nokartuasuransi'])
                ->where(['pendaftaran_id' => $pendaftaran_id])
                ->orderBy(['bpjs_id' => SORT_DESC])
                ->asArray()->one();
            
            if(isset($lastDataBpjs['nokartuasuransi']) && !empty($lastDataBpjs['nokartuasuransi'])) {
                $query['icare_identifier'] = $lastDataBpjs['nokartuasuransi'];
            } else {
                $pasien = Pasien::find()->select(['pasien_id', 'no_rekam_medik', 'no_identitas_pasien', 'nopeserta_bpjs'])
                    ->where(['no_rekam_medik' => $query['no_rekam_medik']])
                    ->asArray()->one();
                $query['icare_identifier'] = isset($pasien['nopeserta_bpjs']) ? $pasien['nopeserta_bpjs'] : $pasien['no_identitas_pasien'];
            }
        }
        if ($request->get('cppt') && !empty($pendaftaran_id)) {
            $cppt = Cppt::find()->select(['a_diag_utama'])
                ->where(['pendaftaran_id' => $pendaftaran_id])
                ->orderBy(['tgl_cppt' => SORT_DESC])
                ->one();
            return [
                'data' => $query,
                'cppt' => $cppt
            ];
        } else {
            return [
                'data' => $query
            ];
        }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            Yii::error([
                'err' => $e->getMessage()
            ]);
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            Yii::error([
                'err' => $e->getMessage()
            ]);
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @see Fungsi get data pasienpendaftaran pendaftaran_t
     * @var params integer id = primary key pendaftaran_id
     * @return array, activeQueryRecords
     *
     */
    private function getDataPasien($id = null)
    {
        $model = InfoPasienRiView::find()
            ->select([
                '*',
                'ruangan_nama as kamarruangan_nama',
                'stat_ranap as status_periksa_nama',
                'dokter_admisi as nama_pegawai',
                'instalasi_id'
            ]);
        
        // this block code will run if $id is null
        // find last pendaftaran_id for pasien rawat inap
        if(empty($id)) {
            $getLastPendaftaran = Pendaftaran::find()
                ->select(['pendaftaran_id'])
                ->where(['is not', 'pasienadmisi_id', null])
                ->orderBy(['pendaftaran_id' => SORT_DESC])
                ->limit(1)
                ->one();

            $id = $getLastPendaftaran->pendaftaran_id;
        }

        $model->where(['pendaftaran_id' => $id]);

        return $model;
    }

    private function getDataPasienRS($id = null)
    {
        $model = InfoPasienRjView::find()
            ->select([
                '*',
                'ruangan_nama as kamarruangan_nama',
            ]);

        // this block code will run if $id is null
        // find last pendaftaran_id for pasien rawat jalan
        if(empty($id)) {
            $getLastPendaftaran = Pendaftaran::find()
                ->select(['pendaftaran_id'])
                ->where(['pasienadmisi_id' => null, 'is_aps' => false])
                ->orderBy(['pendaftaran_id' => SORT_DESC])
                ->limit(1)
                ->one();
            
            $id = $getLastPendaftaran->pendaftaran_id;
        }

        $model->where(['pendaftaran_id' => $id]);

        return $model;
    }

    public function actionGetBundleDataAsesmenKeperawatan()
    {
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id', null);
        $pasienadmisi_id = Yii::$app->request->get('pasienadmisi_id', null);

        if (is_null($pendaftaran_id)) {
            return $this->responseJson(404, 'Data Tidak Ditemukan');
        }
        $getAsesmenKeperawatan = $this->getDataAsesmenAwal($pendaftaran_id, $pasienadmisi_id);
        $getAsesmenDetail = null;
        if (!empty($getAsesmenKeperawatan)) {
            $getAsesmenKeperawatan = $getAsesmenKeperawatan->getArrayAttributes();
            if (isset($getAsesmenKeperawatan['pegawaiverifikasigizi_id']) && !empty($getAsesmenKeperawatan['pegawaiverifikasigizi_id'])) {
                $getAsesmenKeperawatan['pegawaiverifikasigizi_nama'] = Pegawai::find()->select(['nama_pegawai'])->where(['pegawai_id' => $getAsesmenKeperawatan['pegawaiverifikasigizi_id']])->scalar();
            }
            $getAsesmenDetail = AsesmenResikoJatuh::find()
                ->where(['asesmenawal_id' => $getAsesmenKeperawatan['asesmenawal_id']])
                ->orderBy([
                    'asesmenrdresikojatuh_id' => SORT_DESC
                ])
                ->limit(3)
                ->asArray()
                ->all();
            for ($i = 0; $i < sizeof($getAsesmenDetail); $i++) {
                $getAsesmenDetail[$i]['skala_nyeri'] = str_replace('-undefined', '', $getAsesmenDetail[$i]['skala_nyeri']);
            }
            $getSuggestData = Pendaftaran::find()
                ->select([
                    'pendaftaran_t.tgl_pendaftaran as tgl_datang',
                    'triase_t.keluhan_utama',
                    'triase_t.alergi_obat',
                    'triase_t.alergi_lainnya',
                    'triase_t.hasil_triase as kategori_triase_sehari',
                    'triase_t.pernafasan',
                    'triase_t.sirkulasi',
                    'triase_t.tekanan_darah as tensi',
                    'triase_t.nadi as detak_nadi',
                    'triase_t.suhu as suhu_tubuh',
                    'triase_t.gcseye_id',
                    'triase_t.gcsverbal_id',
                    'triase_t.gcsmotorik_id',
                    'triase_t.hasil_gcs',
                    'triase_t.is_kapitis',
                ])
                ->rightJoin('pasien_m', 'pasien_m.pasien_id = pendaftaran_t.pasien_id')
                ->join('JOIN', 'carabayar_m', 'carabayar_m.carabayar_id = pendaftaran_t.carabayar_id')
                ->join('LEFT JOIN', 'triase_t', 'triase_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->where([
                    'pendaftaran_t.pendaftaran_id' => $pendaftaran_id
                ])
                ->asArray()
                ->one();
            foreach ($getSuggestData as $key => $value) {
                if (empty($getAsesmenKeperawatan[$key])) {
                    $getAsesmenKeperawatan[$key] = $value;
                }
            }
        } else {
            $getAsesmenKeperawatan = Pendaftaran::find()
                ->select([
                    'pasien_m.agama as agama_id',
                    'pasien_m.pekerjaan_id',
                    'pendaftaran_t.transportasi as caramasuk_id',
                    'pasien_m.pendidikan_id',
                    'pendaftaran_t.tgl_pendaftaran as tgl_datang',
                    'pendaftaran_t.tgl_pendaftaran',
                    'pendaftaran_t.pasien_id',
                    'pendaftaran_t.carabayar_id',
                    'carabayar_m.carabayar_nama',
                    'pendaftaran_t.pendaftaran_id',
                    'triase_t.keluhan_utama',
                    'triase_t.alergi_obat',
                    'triase_t.alergi_lainnya',
                    'triase_t.hasil_triase as kategori_triase_sehari',
                    'triase_t.pernafasan',
                    'triase_t.sirkulasi',
                    'triase_t.tekanan_darah as tensi',
                    'triase_t.nadi as detak_nadi',
                    'triase_t.suhu as suhu_tubuh',
                    'triase_t.gcseye_id',
                    'triase_t.gcsverbal_id',
                    'triase_t.gcsmotorik_id',
                    'triase_t.hasil_gcs',
                    'triase_t.is_kapitis',
                ])
                ->rightJoin('pasien_m', 'pasien_m.pasien_id = pendaftaran_t.pasien_id')
                ->join('JOIN', 'carabayar_m', 'carabayar_m.carabayar_id = pendaftaran_t.carabayar_id')
                ->join('LEFT JOIN', 'triase_t', 'triase_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->where([
                    'pendaftaran_t.pendaftaran_id' => $pendaftaran_id
                ])
                ->asArray()
                ->one();
            $getAsesmenKeperawatan['kategori_triase_disaster'] = null;
            if ($getAsesmenKeperawatan['pernafasan'] == 'henti_napas-resusitasi' && $getAsesmenKeperawatan['sirkulasi'] == 'henti_jantung-resusitasi') {
                $getAsesmenKeperawatan['kategori_triase_disaster'] = 'hitam';
            }
            $asmedRecord = AsesmenMedis::find()
                ->select([
                    'asesmenmedis_t.pendaftaran_id',
                    'asesmenmedis_t.r_penyakitsekarang',
                    'asesmenmedis_t.r_penyakitdahulu',
                    'asesmenmedis_t.r_alergiobat',
                    'asesmenmedis_t.created_date as asmed_date',
                ])
                ->join('JOIN', 'pendaftaran_t', 'asesmenmedis_t.pendaftaran_id=pendaftaran_t.pendaftaran_id')
                ->andWhere([
                    'pendaftaran_t.pasien_id' => $getAsesmenKeperawatan['pasien_id']
                ])
                ->andWhere([
                    '!=', 'asesmenmedis_t.pendaftaran_id', $getAsesmenKeperawatan['pendaftaran_id']
                ])
                ->orderBy([
                    'asesmenmedis_t.created_date' => SORT_DESC
                ])
                ->asArray()
                ->one();
            if (!empty($asmedRecord)) {
                $asmedRecord['r_penyakitdahulu'] = empty($asmedRecord['r_penyakitdahulu']) ? [] : json_decode($asmedRecord['r_penyakitdahulu'], true);
                $asmedRecord['r_penyakitdahulu'] = ArrayHelper::getColumn($asmedRecord['r_penyakitdahulu'], 'penyakit');
                $asmedRecord['r_penyakitdahulu'] = implode(", ", $asmedRecord['r_penyakitdahulu']);

                if (!empty($asmedRecord['r_alergiobat'])) {
                    $json = json_decode($asmedRecord['r_alergiobat'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $asmedRecord['r_alergiobat'] = $asmedRecord['alergi_obat']  = is_array($json) ? implode(", ", $json) : $json;
                    } else {
                        $asmedRecord['alergi_obat'] = $asmedRecord['r_alergiobat'];
                    }
                }
            }
            $getAsesmenKeperawatan = array_merge($getAsesmenKeperawatan, (!empty($asmedRecord) ? $asmedRecord : [
                'r_penyakitsekarang' => null,
                'r_penyakitdahulu' => null,
                'r_alergiobat' => null,
                'asmed_date' => null,
            ]));
        }

        // GCS
        $getGcs = Gcs::find()
            ->select([
                'gcs_id as id',
                'gcs_nama as text',
                'gcs_namalainnya',
                'gcs_nilaimin',
                'gcs_nilaimax',
                'is_kapitis',
            ])->orderBy([
                'gcs_nilaimin' => SORT_ASC
            ])
            ->asArray()
            ->all();
        $getMetodeGcs = MetodeGcs::find()
            ->select([
                'metodegcs_id as id',
                'metodegcs_nama as text',
                'metodegcs_singkatan',
                'metodegcs_nilai',
            ])
            ->asArray()
            ->all();
        $metodeGcs = [];
        foreach ($getMetodeGcs as $item => $metode) {
            $metode['text'] = $metode['text'] . " - " . $metode['metodegcs_nilai'];
            $metodeGcs[$metode['metodegcs_singkatan']][] = $metode;
        }

        // MASTER
        $getAgama = $this->getLookupByType(['agama'])
            ->select([
                'lookup_id as id',
                'lookup_name as text',
                'lookup_type',
                'lookup_value',
            ])
            ->orderBy(['lookup_type' => SORT_ASC, 'lookup_urutan' => SORT_ASC])
            ->asArray()
            ->all();
        $getPekerjaan = Pekerjaan::find()
            ->select([
                'pekerjaan_id as id',
                'pekerjaan_nama as text'
            ])
            ->asArray()
            ->all();

        // $getCaraMasuk = CaraMasuk::find()
        //     ->select([
        //         'caramasuk_id as id',
        //         'caramasuk_nama as text',
        //     ])
        //     ->asArray()
        //     ->all();

        $getCaraMasuk = $this->getLookupByType(['transportasi'])
            ->select([
                'lookup_id as id',
                'lookup_name as text',
                'lookup_type',
                'lookup_value',
            ])
            ->orderBy(['lookup_type' => SORT_ASC, 'lookup_urutan' => SORT_ASC])
            ->asArray()
            ->all();
        $getPendidikan = Pendidikan::find()
            ->select([
                'pendidikan_id as id',
                'pendidikan_nama as text',
            ])
            ->asArray()
            ->all();
        $getSuku = Suku::find()
            ->select([
                'suku_id as id',
                'suku_nama as text'
            ])
            ->asArray()
            ->all();

        $enable_pulang = (new DocoConstansId)->actionGetAdditional('konfig_edit_form_pelayanan');

        return [
            'asesmenkeperawatan' => $getAsesmenKeperawatan,
            'riwayat' => $getAsesmenDetail,
            'gcs' => $getGcs,
            'agama' => $getAgama,
            'metodegcs' => $metodeGcs,
            'pekerjaan' => $getPekerjaan,
            'caramasuk' => $getCaraMasuk,
            'pendidikan' => $getPendidikan,
            'suku' => $getSuku,
            'enable_pulang' => $enable_pulang,
        ];
    }

    /**
     * function for save asesmen keperawatan
     *
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveAsesmen()
    {
        $request = Yii::$app->request;
        $dataKeperawatan = $request->post('formdata', []);
        $dataResikoJatuh = isset($dataKeperawatan['resiko_jatuh']) && !empty($dataKeperawatan['resiko_jatuh']) ? $dataKeperawatan['resiko_jatuh'] : [];

        $pendaftaran_id = !isset($dataKeperawatan['pendaftaran_id']) || empty($dataKeperawatan['pendaftaran_id']) ? null : $dataKeperawatan['pendaftaran_id'];
        $pasienadmisi_id = !isset($dataKeperawatan['pasienadmisi_id']) || empty($dataKeperawatan['pasienadmisi_id']) ? null : $dataKeperawatan['pasienadmisi_id'];

        if (is_null($pendaftaran_id)) {
            return $this->responseJson(402, 'attribut pendaftaran_id tidak boleh kosong!');
        } else {
            $cekPendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            if (empty($cekPendaftaran)) {
                return $this->responseJson(404, 'Data Pendaftaran Tidak Ditemukan');
            }
        }
        $transaction = Yii::$app->db->beginTransaction();
        $model = AsesmenAwal::find()->where(['pendaftaran_id' => $pendaftaran_id, 'pasienadmisi_id' => $pasienadmisi_id])->one();
        if (empty($model)) {
            $model = new AsesmenAwal;
            $model->pendaftaran_id = $pendaftaran_id;
            $model->pasienadmisi_id = $pasienadmisi_id;
        }

        $dataKeperawatan['ruangan_id'] = empty($model->ruangan_id) ? Yii::$app->jwt->ruangan_id : $model->ruangan_id;
        $dataKeperawatan['tgl_asesmen'] = date('Y-m-d H:i:s');
        $model->mappingDataAttributes($dataKeperawatan);
        if (!$model->save()) {
            $transaction->rollBack();
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }

        MonitoringTtvLogic::feedData($dataKeperawatan, DocoConstants::ASESMEN_KEPERAWATAN, DocoConstants::INSTALASI_RAWAT_INAP);

        $asesmenawal_id = $model->getPrimaryKey();

        if (!empty($dataResikoJatuh)) {
            $modelResikoJatuh = new AsesmenResikoJatuh;
            $modelResikoJatuh->asesmenawal_id = $asesmenawal_id;
            $modelResikoJatuh->tgl_pengkajian = date('Y-m-d H:i:s');
            $modelResikoJatuh->attributes = $dataResikoJatuh;
            if (!$modelResikoJatuh->save()) {
                $transaction->rollBack();
                return $this->responseJson(500, 'Terjadi Kesalahan pada server');
            }
        }

        $transaction->commit();
        return $this->responseJson(200, 'Simpan Asesmen Keperawatan Berhasil!');
    }

    public function actionCetakAskep($pendaftaran_id, $pasienadmisi_id)
    {
        $configData = Yii::$app->request->post('array_config');
        $askepData = AsesmenAwal::find()
            ->select([
                'asesmenawal_t.*',
                'pendidikan_m.pendidikan_nama',
                'pekerjaan_m.pekerjaan_nama',
                'suku_m.suku_nama',
                'caramasuk.lookup_name as cara_datang',
                'agama_t.lookup_name as agama',
                'eye.metodegcs_nama as e_nama',
                'eye.metodegcs_nilai as e_nilai',
                'motorik.metodegcs_nama as m_nama',
                'motorik.metodegcs_nilai as m_nilai',
                'verbal.metodegcs_nama as v_nama',
                'verbal.metodegcs_nilai as v_nilai',
            ])
            ->leftJoin('pekerjaan_m', '(asesmenawal_t.additional_data::json->>\'pekerjaan_id\')::INTEGER=pekerjaan_m.pekerjaan_id')
            ->leftJoin('pendidikan_m', '(asesmenawal_t.additional_data::json->>\'pendidikan_id\')::INTEGER=pendidikan_m.pendidikan_id')
            ->leftJoin('suku_m', '(asesmenawal_t.additional_data::json->>\'suku_id\')::INTEGER=suku_m.suku_id')
            ->leftJoin('lookup_m as caramasuk', '(asesmenawal_t.additional_data::json->>\'caramasuk_id\')::INTEGER=caramasuk.lookup_id')
            ->leftJoin('lookup_m as agama_t', '(asesmenawal_t.additional_data::json->>\'agama_id\')::INTEGER=agama_t.lookup_id')
            ->leftJoin('metodegcs_m as eye', '(asesmenawal_t.additional_data::json->>\'gcseye_id\')::INTEGER=eye.metodegcs_id')
            ->leftJoin('metodegcs_m as motorik', '(asesmenawal_t.additional_data::json->>\'gcsmotorik_id\')::INTEGER=motorik.metodegcs_id')
            ->leftJoin('metodegcs_m as verbal', '(asesmenawal_t.additional_data::json->>\'gcsverbal_id\')::INTEGER=verbal.metodegcs_id')
            ->where(compact('pendaftaran_id', 'pasienadmisi_id'))->asArray()->one();

        $askepData = AsesmenAwal::getStaticAdditional($askepData);
        $result = InfoPasienRanap::find()->where(compact('pendaftaran_id', 'pasienadmisi_id'))->asArray()->one();
        $model = AsesmenAwal::find()->where(compact('pendaftaran_id', 'pasienadmisi_id'))->one();
        $model = $model->getArrayAttributes();
        if (isset($model['pegawaiverifikasigizi_id']) && !empty($model['pegawaiverifikasigizi_id'])) {
            $model['pegawaiverifikasigizi_nama'] = Pegawai::find()->select(['nama_pegawai'])->where(['pegawai_id' => $model['pegawaiverifikasigizi_id']])->scalar();
        }
        $modelResiko = AsesmenResikoJatuh::find()->where(['asesmenawal_id' => $askepData['asesmenawal_id']])->orderBy(['tgl_pengkajian' => SORT_DESC])->limit(4)->asArray()->all();
        $print = new DocoPrint('RD-Askep');
        // Define variables
        $perasaan_klien = $dukungan_sosial = $hubungan_pasien = $keluarga_lain = $keadaan_emosi = $bahasa_dipakai = $media = $transportasi = $orang_merawat = $sarana_kesehatan = $tujuan_pulang = $masuk_ke = $capilary_refill = $perfusi = $akral = $pendarahan = $pupil = '';

        // Checkbox Anamnesa
        $allo_text = !empty($askepData['asesmen_allo_text']) ? $askepData['asesmen_allo_text'] : '';
        $asesmen_auto = isset($askepData['asesmen_auto']) && $askepData['asesmen_auto'] == 1 ? '<input checked="checked" type="checkbox" /> Auto Anamnesa' : '<input type="checkbox" /> Auto Anamnesa';
        $asesmen_allo = isset($askepData['asesmen_allo']) && $askepData['asesmen_allo'] == 1 ? '<input checked="checked" type="checkbox" /> Allo Anamnesa <br>' . $allo_text : '<input type="checkbox" /> Allo Anamnesa';
        $anamnesa = $asesmen_auto . '<br>' . $asesmen_allo;
        // Alergi
        if ($askepData['is_alergi']) {
            $alergi = '<input type="checkbox" /> Tidak <br> <input checked="checked" type="checkbox" /> Ya';
            isset($askepData['is_alergiobat']) ? $alergi .= '<br>Obat: ' . (isset($askepData['alergi_obat']) ? $askepData['alergi_obat'] : '-') : '';
            isset($askepData['is_alergilainnya']) ? $alergi .= '<br>Lainnya: ' . (isset($askepData['alergi_lainnya']) ? $askepData['alergi_lainnya'] : '-') : '';
        } else {
            $alergi = '<input checked="checked" type="checkbox" /> Tidak <br> <input type="checkbox" /> Ya';
        }

        // A-B-C
        // Airway
        $jalan_napas = '<table style="width: 100%;">';
        foreach ($configData['jalan_napas'] as $key => $value) {
            $jalan_napas .= '<tr>';
            $jalan_napas .=  isset($askepData['jalur_nafas']) && strpos($askepData['jalur_nafas'], $key) !== false ? '<td><input checked="checked" type="checkbox" /> ' . $value . '</td>' : '<td><input type="checkbox" /> ' . $value . '</td>';
            if ($key == 'bersih_sumbatan') {
                foreach ($configData['jalan_napas_bersih'] as $key => $value) {
                    $jalan_napas .= isset($askepData['jalan_nafas_bersin']) && strpos($askepData['jalan_nafas_bersin'], $key) !== false ? '<tr><td>&nbsp;&nbsp;&nbsp;<input checked="checked" type="checkbox" /> ' . $value . '</td></tr>' : '<tr><td>&nbsp;&nbsp;&nbsp;<input type="checkbox" /> ' . $value . '</td></tr>';
                }
            }
            ($key == 'oksigen' && isset($askepData['jalur_nafas_oksigen'])) ? $jalan_napas .= '<tr><td>&nbsp;&nbsp;&nbsp;' . $askepData['jalur_nafas_oksigen'] . ' L/menit</td></tr>' : null;
            $jalan_napas .= '</tr>';
        }
        $jalan_napas .= '</table>';
        // Breathing
        $pernapasan = '<table style="width: 100%;">';
        foreach ($configData['pernapasan'] as $key => $value) {
            $pernapasan .= '<tr>';
            $pernapasan .= isset($askepData['pernafasan']) && strpos($askepData['pernafasan'], $key) !== false ? '<td><input checked="checked" type="checkbox" /> ' . $value . '</td>' : '<td><input type="checkbox" /> ' . $value . '</td>';
            ($key == 'spontan' && isset($askepData['pernafasan_spontan'])) ? $pernapasan .= '<tr><td>&nbsp;&nbsp;&nbsp;' . $askepData['pernafasan_spontan'] . ' x/menit</td></tr>' : null;
            ($key == 'takipnea' && isset($askepData['pernafasan_takipnea'])) ? $pernapasan .= '<tr><td>&nbsp;&nbsp;&nbsp;' . $askepData['pernafasan_takipnea'] . ' x/menit</td></tr>' : null;
            ($key == 'gargling' && isset($askepData['pernafasan_gargling'])) ? $pernapasan .= '<tr><td>&nbsp;&nbsp;&nbsp;' . $askepData['pernafasan_gargling'] . ' x/menit</td></tr>' : null;
            $pernapasan .= '</tr>';
        }
        $pernapasan .= '</table>';

        // Circulation
        // Checkbox Capilary Refill
        foreach ($configData['capilary_refill'] as $key => $value) {
            $capilary_refill .= isset($askepData['capilary_refill']) && strpos($askepData['capilary_refill'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Checkbox Perfusi
        foreach ($configData['perfusi'] as $key => $value) {
            $perfusi .= isset($askepData['perfusi']) && strpos($askepData['perfusi'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Checkbox Akral
        foreach ($configData['akral'] as $key => $value) {
            $akral .= isset($askepData['akral']) && strpos($askepData['akral'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Pendarahan
        if (isset($askepData['pendarahan']) && $askepData['pendarahan'] == 1) {
            $pendarahan = '<input type="checkbox" /> Tidak <br> <input checked="checked" type="checkbox" /> Ya';
            isset($askepData['pendarahan_cc']) ? $pendarahan .= ' : ' . $askepData['pendarahan_cc'] : '';
        } else {
            $pendarahan = '<input checked="checked" type="checkbox" /> Tidak <br> <input type="checkbox" /> Ya';
        }

        // EKG
        $jenis_resiko_jatuh = isset($askepData['jenis_resiko_jatuh']) ? 'Form ' . ucwords(str_replace('-', ' ', $askepData['jenis_resiko_jatuh'])) : null;
        $skrining_nyeri = isset($askepData['is_nyeri']) && $askepData['is_nyeri'] ? 'Ya' : 'Tidak';
        // Metode GCS
        $gcsEye = isset($askepData['gcseye_id']) ? $askepData['e_nama'] . ' - ' . $askepData['e_nilai'] : null;
        $gcsVerbal = isset($askepData['gcsverbal_id']) ? $askepData['v_nama'] . ' - ' . $askepData['v_nilai'] : null;
        $gcsMotorik = isset($askepData['gcsmotorik_id']) ? $askepData['m_nama'] . ' - ' . $askepData['m_nilai'] : null;

        // Pupil
        foreach ($configData['pupil'] as $key => $value) {
            $pupil .= isset($askepData['pupil']) && strcmp($askepData['pupil'], $key) == 0 ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }

        // Reaksi Pupil
        $reaksi_pupil = '<table style="width: 100%">';
        $reaksi_pupil .= '<tr>';
        foreach ($configData['reaksi_pupil'] as $key => $value) {
            $reaksi_pupil .= isset($askepData['reaksi_pupil_lainnya']) && strpos($askepData['reaksi_pupil_lainnya'], $key) !== false ? '<td><input checked="checked" type="checkbox" /> ' . $value . '</td>' : '<td><input type="checkbox" /> ' . $value . '</td>';
        }
        $reaksi_pupil .= '</tr>';
        $reaksi_pupil .= '<tr>';
        foreach ($configData['reaksi_pupil_lainnya'] as $key => $value) {
            $reaksi_pupil .= isset($askepData['reaksi_pupil']) && strpos($askepData['reaksi_pupil'], $key) !== false ? '<td><input checked="checked" type="checkbox" /> ' . $value . '</td>' : '<td><input type="checkbox" /> ' . $value . '</td>';
            $key == 'os' && isset($askepData['pupil_os']) ? ($reaksi_pupil .= '<td> : ' . $askepData['pupil_os'] . '</td>') : null;
            $key == 'od' && isset($askepData['pupil_od']) ? ($reaksi_pupil .= '<td> : ' . $askepData['pupil_od'] . '</td>') : null;
        }
        $reaksi_pupil .= '</tr></table>';

        // Data Psikososial
        // Checkbox Perasaan Klien
        foreach ($configData['perasaan_klien'] as $key => $value) {
            $perasaan_klien .= isset($askepData['perasaan_klien']) && strpos($askepData['perasaan_klien'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Checkbox Dukungan Sosial
        foreach ($configData['sosial_support'] as $key => $value) {
            $dukungan_sosial .= isset($askepData['sosial_support']) && strpos($askepData['sosial_support'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Lainnya
        isset($askepData['sosial_support']) && strpos($askepData['sosial_support'], '00') !== false ? $dukungan_sosial .= ': ' . ucwords(substr($askepData['sosial_support'], strpos($askepData['sosial_support'], '00') + 3)) : null;
        // Checkbox Hubungan Pasien
        foreach ($configData['hubungan_pasien'] as $key => $value) {
            $hubungan_pasien .= isset($askepData['hubungan_pasien']) && strpos($askepData['hubungan_pasien'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Checkbox Keluarga Lain
        foreach ($configData['keluarga_lain'] as $key => $value) {
            $keluarga_lain .= isset($askepData['keluarga_lain']) && strpos($askepData['keluarga_lain'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        isset($askepData['keluarga_lain']) && strpos($askepData['keluarga_lain'], '00') !== false ? $keluarga_lain .= ': ' . ucwords(substr($askepData['keluarga_lain'], strpos($askepData['keluarga_lain'], '00') + 3)) : null;
        // Checkbox Keadaan Emosi
        foreach ($configData['keadaan_emosi'] as $key => $value) {
            $keadaan_emosi .= isset($askepData['keadaan_emosi']) && strpos($askepData['keadaan_emosi'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Kultural
        $kultural = isset($askepData['suku_id']) ? $askepData['suku_nama'] : null;

        // Dukungan Edukasi
        // Checkbox Bahasa
        foreach ($configData['kebutuhan_edukasi']['bahasa'] as $key => $value) {
            $bahasa_dipakai .= isset($askepData['bahasa_dipakai']) && strpos($askepData['bahasa_dipakai'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        isset($askepData['bahasa_dipakai_lainnya']) ? $bahasa_dipakai .= ': ' . ucwords(str_replace(',', ', ', $askepData['bahasa_dipakai_lainnya'])) : null;
        // Dukungan Penerjemah
        $dukungan_penerjemah = isset($askepData['is_penerjemah']) && $askepData['is_penerjemah'] ? 'Ya' : 'Tidak';
        // Checkbox Media
        foreach ($configData['kebutuhan_edukasi']['media'] as $key => $value) {
            $media .= isset($askepData['media']) && strpos($askepData['media'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        isset($askepData['media_lainnya']) ? $media .= ': ' . ucwords(str_replace(',', ', ', $askepData['media_lainnya'])) : null;

        // Hambatan Edukasi
        $hambatan_edukasi = '<table style="width: 100%;">';
        foreach ($configData['kebutuhan_edukasi']['hambatan'] as $key => $value) {
            $hambatan_edukasi .= '<tr>';
            $hambatan_edukasi .= isset($askepData['identifikasi_hambatan']) && strpos($askepData['identifikasi_hambatan'], $key) !== false ? '<td><input checked="checked" type="checkbox" /> ' . $value . '</td>' : '<td><input type="checkbox" /> ' . $value . '</td>';
            $hambatan_edukasi .= '</tr>';
        }
        $hambatan_edukasi .= '</table>';

        // Kebutuhan Edukasi
        $sistem_rujukan = isset($askepData['is_sistem_rujukan']) && $askepData['is_sistem_rujukan'] ? 'Ya' : 'Tidak';
        $kesediaan_pasien = isset($askepData['is_ketersediaan_pasien']) && $askepData['is_ketersediaan_pasien'] ? 'Ya' : 'Tidak';
        $kemampuan_membaca = isset($askepData['is_kemampuan_membaca']) && $askepData['is_kemampuan_membaca'] ? 'Mampu' : 'Tidak Mampu';
        if (isset($askepData['is_dibutuhkan_penerjemah']) && $askepData['is_dibutuhkan_penerjemah']) {
            $kebutuhan_penerjemah = '<input type="checkbox" /> Tidak <br> <input checked="checked" type="checkbox" /> Ya';
            isset($askepData['penerjemah_bahasa']) ? $kebutuhan_penerjemah .= '<br>' . ucwords($askepData['penerjemah_bahasa']) : '';
        } else {
            $kebutuhan_penerjemah = '<input checked="checked" type="checkbox" /> Tidak <br> <input type="checkbox" /> Ya';
        }
        if (isset($askepData['is_hambatan_emotional']) && $askepData['is_hambatan_emotional']) {
            $hambatan_emotional = '<input type="checkbox" /> Tidak <br> <input checked="checked" type="checkbox" /> Ya';
            if (isset($askepData['hambatan_emotional_lainnya'])) {
                $he_pendidikan = strpos($askepData['hambatan_emotional_lainnya'], 'pendidikan') !== false ? '<input checked="checked" type="checkbox" /> Tingkat Pendidikan' : '<input type="checkbox" /> Tingkat Pendidikan';
                $he_bahasa = strpos($askepData['hambatan_emotional_lainnya'], 'bahasa') !== false ? '<input checked="checked" type="checkbox" /> Bahasa' : '<input type="checkbox" /> Bahasa';
                $he_fisik =
                    strpos($askepData['hambatan_emotional_lainnya'], 'fisi') !== false ? '<input checked="checked" type="checkbox" /> Keterbatasan Fisik' : '<input type="checkbox" /> Keterbatasan Fisik';
                $hambatan_emotional .= '<br>&emsp;' . $he_pendidikan . '<br>&emsp;' . $he_bahasa . '<br>&emsp;' . $he_fisik;
            }
        } else {
            $hambatan_emotional = '<input checked="checked" type="checkbox" /> Tidak <br> <input type="checkbox" /> Ya';
        }
        if (isset($askepData['is_keterbatasan_fisik']) && $askepData['is_keterbatasan_fisik']) {
            $keterbatasan_fisik = '<input type="checkbox" /> Tidak <br> <input checked="checked" type="checkbox" /> Ya';
            isset($askepData['keterbatasan_fisik']) ? $keterbatasan_fisik .= '<br>' . ucwords($askepData['keterbatasan_fisik']) : '';
        } else {
            $keterbatasan_fisik = '<input checked="checked" type="checkbox" /> Tidak <br> <input type="checkbox" /> Ya';
        }

        // Diagnosa Keperawatan
        $diagnosa_keperawatan = '<ul>';
        if (isset($askepData['diagnosa_keperawatan']) && !empty($askepData['diagnosa_keperawatan'])) {
            $diagKeperawatan = json_decode($askepData['diagnosa_keperawatan'], true);
            foreach ($diagKeperawatan as $keydetail => $val) {
                if (isset($val['text'])) {
                    $diagnosa_keperawatan .= '<li>';
                    $diagnosa_keperawatan .= isset($val['text']) ? (isset($val['kode']) ? $val['kode'] . ' - ' . $val['text'] : $val['text']) : '-';
                    $diagnosa_keperawatan .= '</li>';
                }
            }
        }
        $diagnosa_keperawatan .= '</ul>';

        // Masuk Ke
        foreach ($configData['masuk_ke'] as $key => $value) {
            $masuk_ke .= isset($askepData['masuk_ke']) && strpos($askepData['masuk_ke'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }

        // Rencana Pemulangan
        // Checkbox Tujuan Pulang
        foreach ($configData['pemulangan']['tujuan_pulang'] as $key => $value) {
            $tujuan_pulang .= isset($askepData['tujuan_pulang']) && strpos($askepData['tujuan_pulang'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        isset($askepData['tujuan_pulang_lainnya']) ? $tujuan_pulang .= ': ' . ucwords(str_replace(',', ', ', $askepData['tujuan_pulang_lainnya'])) : null;
        // Checkbox Transportasi
        foreach ($configData['pemulangan']['transportasi'] as $key => $value) {
            $transportasi .= isset($askepData['transportasi']) && strpos($askepData['transportasi'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Checkbox Orang Merawat
        foreach ($configData['pemulangan']['orang_merawat'] as $key => $value) {
            $orang_merawat .= isset($askepData['orang_merawat']) && strpos($askepData['orang_merawat'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Checkbox Sarana Kesehatan
        foreach ($configData['pemulangan']['sarana_kesehatan'] as $key => $value) {
            $sarana_kesehatan .= isset($askepData['sarana_kesehatan']) && strpos($askepData['sarana_kesehatan'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }

        // Assign attributes
        $print->attributes = [
            // Asesmen Keperawatan
            '#jenis_keperawatan#' => 'Rawat Inap',
            '#tgl_datang#' => isset($askepData['tgl_datang']) ? date('d/m/Y H:i:s', strtotime($askepData['tgl_datang'])) : null,
            '#tgl_keluar#' => isset($askepData['tgl_keluar']) ? date('d/m/Y H:i:s', strtotime($askepData['tgl_keluar'])) : null,
            // Identitas Pasien
            '#tinggi_badan#' => isset($askepData['tinggi_badan']) ? $askepData['tinggi_badan'] : null,
            '#berat_badan#' => isset($askepData['berat_badan']) ? $askepData['berat_badan'] : null,
            '#bb_ideal#' => isset($askepData['bb_ideal']) ? $askepData['bb_ideal'] : null,
            '#imt#' => isset($askepData['imt']) ? $askepData['imt'] : null,
            '#ket_imt#' => isset($askepData['ket_imt']) ? $askepData['ket_imt'] : null,
            '#agama#' => isset($askepData['agama']) ? $askepData['agama'] : null,
            '#pendidikan#' => isset($askepData['pendidikan_nama']) ? $askepData['pendidikan_nama'] : null,
            '#pekerjaan#' => isset($askepData['pekerjaan_nama']) ? $askepData['pekerjaan_nama'] : null,
            '#cara_datang#' => isset($askepData['cara_datang']) ? $askepData['cara_datang'] : null,

            // Alasan Masuk IGD
            '#anamnesa#' => isset($anamnesa) ? $anamnesa : ' - ',
            '#keluhan#' => isset($askepData['keluhan']) ? $askepData['keluhan'] : null,
            '#r_penyakitsaatini#' => isset($askepData['r_penyakitsaatini']) ? $askepData['r_penyakitsaatini'] : null,
            '#r_penyakitdahulu#' => isset($askepData['r_penyakitdahulu']) ? $askepData['r_penyakitdahulu'] : null,
            '#r_pengobatan#' => isset($askepData['r_pengobatan']) ? $askepData['r_pengobatan'] : null,
            '#alergi#' => isset($alergi) ? $alergi : null,

            // A-B-C
            '#jalan_napas#' => isset($jalan_napas) ? $jalan_napas : null,
            '#pernapasan#' => isset($pernapasan) ? $pernapasan : null,
            // C
            '#tensi#' => isset($askepData['tensi']) ? $askepData['tensi'] : null,
            '#nadi#' => isset($askepData['detak_nadi']) ? $askepData['detak_nadi'] : null,
            '#hasil_nadi#' => isset($askepData['hasil_nadi']) ? $askepData['hasil_nadi'] : null,
            '#capilary_refill#' => isset($capilary_refill) ? $capilary_refill : null,
            '#perfusi#' => isset($perfusi) ? $perfusi : null,
            '#pendarahan#' => isset($pendarahan) ? $pendarahan : null,
            '#akral#' => isset($akral) ? $akral : null,
            '#suhu#' => isset($askepData['suhu_tubuh']) ? $askepData['suhu_tubuh'] : null,

            // Kategori Triase
            '#kategori_sehari#' => isset($askepData['kategori_triase_sehari']) ? ucwords($askepData['kategori_triase_sehari']) : null,
            '#kategori_disaster#' => isset($askepData['kategori_triase_disaster']) ? ucwords($askepData['kategori_triase_disaster']) : null,

            // GCS
            '#gcs_eye#' => isset($gcsEye) ? $gcsEye : null,
            '#gcs_verbal#' => isset($gcsVerbal) ? $gcsVerbal : null,
            '#gcs_motorik#' => isset($gcsMotorik) ? $gcsMotorik : null,
            '#hasil_metode_gcs#' => isset($askepData['hasil_gcs']) ? $askepData['hasil_gcs'] : null,

            // EKG
            '#pupil#' => isset($pupil) ? $pupil : null,
            '#reaksi_pupil#' => isset($reaksi_pupil) ? $reaksi_pupil : null,
            '#kepala#' => isset($askepData['kepala']) ? ucwords($askepData['kepala']) : null,
            '#abdomen#' => isset($askepData['abdomen']) ? ucwords($askepData['abdomen']) : null,
            '#maksilofacial#' => isset($askepData['maksilofacial']) ? ucwords($askepData['maksilofacial']) : null,
            '#parineum#' => isset($askepData['parineum']) ? ucwords($askepData['parineum']) : null,
            '#tulang_leher#' => isset($askepData['tulan_leher']) ? ucwords($askepData['tulan_leher']) : null,
            '#muskuloskeletal#' => isset($askepData['muskuloskeletal']) ? ucwords($askepData['muskuloskeletal']) : null,
            '#paru_paru#' => isset($askepData['paru_paru']) ? ucwords($askepData['paru_paru']) : null,
            '#extremitas#' => isset($askepData['extremitas']) ? ucwords($askepData['extremitas']) : null,
            '#risiko_jatuh#' => isset($askepData['hasil_resiko_jatuh']) ? $askepData['hasil_resiko_jatuh'] : null,
            '#jenis_risiko_jatuh#' => isset($jenis_resiko_jatuh) ? $jenis_resiko_jatuh : null,
            '#risiko_decubitus#' => isset($askepData['nilai_decubitus']) ? ucwords($askepData['nilai_decubitus']) : null,
            '#luka_bakar#' => isset($askepData['nilai_luka_bakar']) ? ucwords($askepData['nilai_luka_bakar']) : null,

            // Skrining Nyeri
            '#skrining_nyeri#' => isset($skrining_nyeri) ? $skrining_nyeri : null,
            '#skala_nyeri#' => isset($askepData['is_nyeri']) && $askepData['is_nyeri'] ? $this->renderPartial('_skala_nyeri', compact('model', 'modelResiko', 'configData')) : null,

            // Skrining Fungsional
            '#personal_hygiene#' => isset($askepData['personal_hygiene']) ? ucwords($askepData['personal_hygiene']) : null,
            '#mandi#' => isset($askepData['mandi']) ? ucwords($askepData['mandi']) : null,
            '#makan#' => isset($askepData['makan']) ? ucwords($askepData['makan']) : null,
            '#toileting#' => isset($askepData['toileting']) ? ucwords($askepData['toileting']) : null,
            '#menaiki_tangga#' => isset($askepData['menaiki_tangga']) ? ucwords($askepData['menaiki_tangga']) : null,
            '#memakai_pakaian#' => isset($askepData['memakai_pakaian']) ? ucwords($askepData['memakai_pakaian']) : null,
            '#kontrol_bab#' => isset($askepData['kontrol_bab']) ? ucwords($askepData['kontrol_bab']) : null,
            '#kontrol_bak#' => isset($askepData['kontrol_bak']) ? ucwords($askepData['kontrol_bak']) : null,
            '#ambulasi_kursi_roda#' => isset($askepData['ambulasi_kursi_roda']) ? ucwords($askepData['ambulasi_kursi_roda']) : null,
            '#transfer_kursi_tempat_tidur#' => isset($askepData['transfer_kursi_tempat_tidur']) ? ucwords($askepData['transfer_kursi_tempat_tidur']) : null,
            '#total_skor#' => isset($askepData['total_skor']) ? ucwords($askepData['total_skor']) : null,
            '#kategori#' => isset($askepData['kategori']) ? ucwords($askepData['kategori']) : null,
            '#catatan#' => isset($askepData['catatan']) ? ucwords($askepData['catatan']) : null,

            // Skrining Gizi
            '#skrining_gizi#' => $this->renderPartial('_skrining_gizi', compact('model', 'configData')),

            // Data Psikososial
            '#perasaan_klien#' => isset($perasaan_klien) ? $perasaan_klien : null,
            '#dukungan_sosial#' => isset($dukungan_sosial) ? $dukungan_sosial : null,
            '#hubungan_pasien#' => isset($hubungan_pasien) ? $hubungan_pasien : null,
            '#keluarga_lain#' => isset($keluarga_lain) ? $keluarga_lain : null,
            '#keadaan_emosi#' => isset($keadaan_emosi) ? $keadaan_emosi : null,
            '#kultural#' => isset($kultural) ? $kultural : null,

            // Identifikasi Kebutuhan Edukasi
            // Dukungan Edukasi
            '#bahasa_dipakai#' => isset($bahasa_dipakai) ? $bahasa_dipakai : null,
            '#dukungan_penerjemah#' => isset($dukungan_penerjemah) ? $dukungan_penerjemah : null,
            '#media#' => isset($media) ? $media : null,
            // Hambatan Edukasi
            '#hambatan_edukasi#' => isset($hambatan_edukasi) ? $hambatan_edukasi : null,
            // Kebutuhan Edukasi
            '#sistem_rujukan#' => isset($sistem_rujukan) ? $sistem_rujukan : null,
            '#kebutuhan_penerjemah#' => isset($kebutuhan_penerjemah) ? $kebutuhan_penerjemah : null,
            '#materi#' => isset($askepData['materi']) ? $askepData['materi'] : null,
            '#edukator#' => isset($askepData['edukator']) ? $askepData['edukator'] : null,
            '#hambatan_emotional#' => isset($hambatan_emotional) ? $hambatan_emotional : null,
            '#kesediaan_pasien#' => isset($kesediaan_pasien) ? $kesediaan_pasien : null,
            '#kemampuan_membaca#' => isset($kemampuan_membaca) ? $kemampuan_membaca : null,
            '#keterbatasan_fisik#' => isset($keterbatasan_fisik) ? $keterbatasan_fisik : null,
            '#bahasa#' => isset($askepData['bahasa']) ? $askepData['bahasa'] : null,

            // Diagnosa Keperawatan
            '#diagnosa_keperawatan#' => isset($diagnosa_keperawatan) ? $diagnosa_keperawatan : null,

            // Masuk Ke
            '#masuk_ke#' => isset($masuk_ke) ? $masuk_ke : null,

            // Rencana Pemulangan
            '#tujuan_pulang#' => isset($tujuan_pulang) ? $tujuan_pulang : null,
            '#transportasi#' => isset($transportasi) ? $transportasi : null,
            '#orang_merawat#' => isset($orang_merawat) ? $orang_merawat : null,
            '#sarana_kesehatan#' => isset($sarana_kesehatan) ? $sarana_kesehatan : null,

            '#status_periksa#' => isset($result['status_periksa']) ? $result['status_periksa'] : null,
            '#jeniskasuspenyakit_nama#' => isset($result['jeniskasuspenyakit_nama']) ? $result['jeniskasuspenyakit_nama'] : null,
            '#umur#' => isset($result['umur']) ? $result['umur'] : null,
            '#kelaspelayanan_nama#' => isset($result['kelaspelayanan_nama']) ? $result['kelaspelayanan_nama'] : null,
            '#tgl_pendaftaran#' => isset($result['tgl_pendaftaran']) ? date('d/m/Y H:i:s', strtotime($result['tgl_pendaftaran'])) : null,
            '#poliklinik#' => isset($result['ruangan_nama']) ? $result['ruangan_nama'] : null,
            '#no_pendaftaran#' => isset($result['no_pendaftaran']) ? $result['no_pendaftaran'] : null,
            '#no_rm#' => isset($result['no_rekam_medik']) ? $result['no_rekam_medik'] : null,
            '#nama_pasien#' => isset($result['nama_pasien']) ? $result['nama_pasien'] : null,
            '#jenis_kelamin#' => isset($result['jenis_kelamin']) ? $result['jenis_kelamin'] : null,
            '#tanggal_lahir#' => isset($result['tanggal_lahir']) ? date('d/m/Y  ', strtotime($result['tanggal_lahir'])) : null,
            '#cara_bayar#' => isset($result['carabayar_nama']) ? $result['carabayar_nama'] : null,
            '#penjamin#' => isset($result['penjamin_nama']) ? $result['penjamin_nama'] : null,
            '#dokter_pemeriksa#' => isset($result['dokter_admisi']) ? $result['dokter_admisi'] : null,
            '#tanggal_periksa#' => isset($result['tglperiksafisik']) ? date('d F Y', strtotime($result['tglperiksafisik'])) : null,
            '#tgl_cetak#' => date('d F Y'),
        ];

        // Print output
        $print->Output();
    }

    /**
     * function for save sydney
     *
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveSydney()
    {
        $request     = Yii::$app->request;
        $dataSydney  = $request->post('formdata', []);

        $pendaftaran_id = !isset($dataSydney['pendaftaran_id']) || empty($dataSydney['pendaftaran_id']) ? null : $dataSydney['pendaftaran_id'];

        if (is_null($pendaftaran_id)) {
            return $this->responseJson(402, 'attribut pendaftaran_id tidak boleh kosong!');
        } else {
            $cekPendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            if (empty($cekPendaftaran)) {
                return $this->responseJson(404, 'Data Pendaftaran Tidak Ditemukan');
            }
        }

        $transaction = Yii::$app->db->beginTransaction();
        AsesmenResikoJatuhSydneyRD::updateAll(['is_deleted' => true], ['pendaftaran_id' => $pendaftaran_id]);
        $model = new AsesmenResikoJatuhSydneyRD;
        $model->attributes = $dataSydney;
        $model->is_mandiri = empty($dataSydney['is_mandiri']) ? '0-tidak' : $dataSydney['is_mandiri'];
        $model->skor_mandiri = empty($dataSydney['skor_mandiri']) ? 0 : $dataSydney['skor_mandiri'];
        $model->is_bantuan_sedikit = empty($dataSydney['is_bantuan_sedikit']) ? '0-tidak' : $dataSydney['is_bantuan_sedikit'];
        $model->skor_bantuan_sedikit = empty($dataSydney['skor_bantuan_sedikit']) ? 0 : $dataSydney['skor_bantuan_sedikit'];
        $model->is_bantuan_nyata = empty($dataSydney['is_bantuan_nyata']) ? '0-tidak' : $dataSydney['is_bantuan_nyata'];
        $model->skor_bantuan_nyata = empty($dataSydney['skor_bantuan_nyata']) ? 0 : $dataSydney['skor_bantuan_nyata'];
        $model->is_bantuan_total = empty($dataSydney['is_bantuan_total']) ? '0-tidak' : $dataSydney['is_bantuan_total'];
        $model->skor_bantuan_total = empty($dataSydney['skor_bantuan_total']) ? 0 : $dataSydney['skor_bantuan_total'];

        $model->is_mobilitas_mandiri = empty($dataSydney['is_mobilitas_mandiri']) ? '0-tidak' : $dataSydney['is_mobilitas_mandiri'];
        $model->skor_mobilitas_mandiri = empty($dataSydney['skor_mobilitas_mandiri']) ? 0 : $dataSydney['skor_mobilitas_mandiri'];
        $model->is_mobilitas_bantuan = empty($dataSydney['is_mobilitas_bantuan']) ? '0-tidak' : $dataSydney['is_mobilitas_bantuan'];
        $model->skor_mobilitas_bantuan = empty($dataSydney['skor_mobilitas_bantuan']) ? 0 : $dataSydney['skor_mobilitas_bantuan'];
        $model->is_kursi_roda = empty($dataSydney['is_kursi_roda']) ? '0-tidak' : $dataSydney['is_kursi_roda'];
        $model->skor_kursi_roda = empty($dataSydney['skor_kursi_roda']) ? 0 : $dataSydney['skor_kursi_roda'];
        $model->is_imobilisasi = empty($dataSydney['is_imobilisasi']) ? '0-tidak' : $dataSydney['is_imobilisasi'];
        $model->skor_imobilisasi = empty($dataSydney['skor_imobilisasi']) ? 0 : $dataSydney['skor_imobilisasi'];

        $model->pendaftaran_id = $pendaftaran_id;
        if (!$model->save()) {
            $transaction->rollBack();
            Yii::error([
                'error-data' => $model->getErrors()
            ]);
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }

        $transaction->commit();
        return $this->responseJson(200, 'Simpan Asesmen Keperawatan Berhasil!');
    }

    /**
     * function for handle show sydney data
     *
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetSydney()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(422, 'No pendaftaran tidak boleh kosong!');
        }
        $model = AsesmenResikoJatuhSydneyRD::find()
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy(['created_date' => SORT_DESC])
            ->asArray()
            ->one();
        return $this->responseJson(200, 'Data Sydney Berhasil didapatkan', $model);
    }

    /**
     * function for save sydney
     *
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveDumpty()
    {
        $request     = Yii::$app->request;
        $dataDumpty  = $request->post('formdata', []);

        $pendaftaran_id = !isset($dataDumpty['pendaftaran_id']) || empty($dataDumpty['pendaftaran_id']) ? null : $dataDumpty['pendaftaran_id'];

        if (is_null($pendaftaran_id)) {
            return $this->responseJson(402, 'attribut pendaftaran_id tidak boleh kosong!');
        } else {
            $cekPendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            if (empty($cekPendaftaran)) {
                return $this->responseJson(404, 'Data Pendaftaran Tidak Ditemukan');
            }
        }

        $transaction = Yii::$app->db->beginTransaction();

        $model = new AsesmenResikoJatuhDumptyRD;
        $model->attributes     = $dataDumpty;
        $model->pendaftaran_id = $pendaftaran_id;
        $model->tanggal        = date('Y-m-d');;
        $model->jam            = date('H:i');;
        if (!$model->save()) {
            $transaction->rollBack();
            Yii::error([
                'error-data' => $model->getErrors()
            ]);
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }

        $transaction->commit();
        return $this->responseJson(200, 'Simpan Humpty Dumpty Berhasil!');
    }

    /**
     * function for handle show dumpty data
     *
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetDumpty()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(422, 'No pendaftaran tidak boleh kosong!');
        }
        $model = AsesmenResikoJatuhDumptyRD::find()
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy(['tanggal' => SORT_DESC, 'jam' => SORT_DESC])
            ->asArray()
            ->one();
        return $this->responseJson(200, 'Data Dumpty Berhasil didapatkan', $model);
    }

    /**
     * function for handle show history dumpty data
     *
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetHistoryDumpty()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(422, 'No pendaftaran tidak boleh kosong!');
        }

        $getGroupHistory =  AsesmenResikoJatuhDumptyRD::find()
            ->select(['count(asesmenrdresikojatuhdumpty_id)', 'tanggal'])
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->groupBy('tanggal')
            ->orderBy(['tanggal' => SORT_DESC])
            ->asArray()
            ->all();

        $getHistory = AsesmenResikoJatuhDumptyRD::find()
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy(['tanggal' => SORT_DESC, 'jam' => SORT_DESC, 'asesmenrdresikojatuhdumpty_id' => SORT_DESC])
            ->asArray()
            ->all();

        return [
            'history' => $getHistory,
            'group'   => $getGroupHistory,
        ];
    }

    /**
     * function for handle show history morse data
     *
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetHistoryMorse()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(422, 'No pendaftaran tidak boleh kosong!');
        }

        $getGroupHistory =  AsesmenResikoJatuhMorseRD::find()
            ->select(['count(asesmenrdresikojatuhmorse_id)', 'tanggal'])
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->groupBy('tanggal')
            ->orderBy(['tanggal' => SORT_DESC])
            ->asArray()
            ->all();

        $getHistory = AsesmenResikoJatuhMorseRD::find()
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy(['tanggal' => SORT_DESC, 'jam' => SORT_DESC, 'asesmenrdresikojatuhmorse_id' => SORT_DESC])
            ->asArray()
            ->all();

        return [
            'history' => $getHistory,
            'group'   => $getGroupHistory,
        ];
    }


    public function actionResikoJatuhList()
    {
        $page = Yii::$app->request->get('page', 1);
        $query = ResikoJatuh::find()
            ->select([
                'resikojatuh_id as id',
                'resikojatuh_nama as text'
            ]);
        $term = Yii::$app->request->get('term');
        if (!empty($term)) {
            $query = $query->andWhere([
                'ilike',
                'resikojatuh_nama',
                $term
            ]);
        }
        return $query
            ->limit(11)
            ->offset(($page - 1) * 10)
            ->asArray()
            ->all();
    }

    /**
     * function for save sydney
     *
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveMorse()
    {
        $request     = Yii::$app->request;
        $dataMorse  = $request->post('formdata', []);

        $pendaftaran_id = !isset($dataMorse['pendaftaran_id']) || empty($dataMorse['pendaftaran_id']) ? null : $dataMorse['pendaftaran_id'];

        if (is_null($pendaftaran_id)) {
            return $this->responseJson(402, 'attribut pendaftaran_id tidak boleh kosong!');
        } else {
            $cekPendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            if (empty($cekPendaftaran)) {
                return $this->responseJson(404, 'Data Pendaftaran Tidak Ditemukan');
            }
        }

        $transaction = Yii::$app->db->beginTransaction();

        $model = new AsesmenResikoJatuhMorseRD;
        $model->attributes     = $dataMorse;
        $model->pendaftaran_id = $pendaftaran_id;
        $model->tanggal        = date('Y-m-d');;
        $model->jam            = date('H:i');;
        if (!$model->save()) {
            $transaction->rollBack();
            Yii::error([
                'error-data' => $model->getErrors()
            ]);
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }

        $transaction->commit();
        return $this->responseJson(200, 'Simpan Skala Morse Berhasil!');
    }

    /**
     * function for handle show morse data
     *
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetMorse()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(422, 'No pendaftaran tidak boleh kosong!');
        }
        $model = AsesmenResikoJatuhMorseRD::find()
            ->select([
                'resikojatuh_m.resikojatuh_nama',
                'asesmenrdresikojatuhmorse_t.*'
            ])
            ->leftJoin('resikojatuh_m', 'resikojatuh_m.resikojatuh_id = asesmenrdresikojatuhmorse_t.resikojatuh_id')
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy(['tanggal' => SORT_DESC, 'jam' => SORT_DESC])
            ->asArray()
            ->one();
        return $this->responseJson(200, 'Data Morse Berhasil didapatkan', $model);
    }

    public function actionGetBundleDataAsesmenNyeri()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $pasien_id = $request->get('pasien_id');
        $pasienadmisi_id = $request->get('pasienadmisi_id');
        $data_skrining = SkriningGizi::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $data_asesmen = $this->getDataAsesmenAwal($pendaftaran_id, $pasienadmisi_id);
        $data_lookup = $this->getListLookupKeperawatan(['asmen_dari', 'asmen_masukdengan', 'asmen_ketergantungan', 'asmen_penyakit_kel', 'sgizi_bb_turun', 'sgizi_porsi', 'sgizi_sakitberat']);

        $diagnosa = null;
        if ($data_asesmen != null && isset($data_asesmen['diagnosa_masuk']) && $data_asesmen['diagnosa_masuk'] != '') {
            // var_dump($data_asesmen['diagnosa_masuk']); die();
            // $dataDiag = json_decode($data_asesmen['diagnosa_masuk'], true);
            $dataDiag = $data_asesmen['diagnosa_masuk'];

            if (isset($dataDiag['id'])) {
                $diagnosa = [
                    'diagnosa_kode' => '',
                    'diagnosa_id' => $dataDiag['id'],
                    'diagnosa_nama' => $dataDiag['text']
                ];
                // $diagnosa = DiagnosaView::find()->where(['diagnosa_id' => $dataDiag['id']])->one();
            } else {
                $diagnosa = [
                    'diagnosa_kode' => '', 'diagnosa_nama' => isset($dataDiag['text']) ? $dataDiag['text'] : ''
                ];
            }
        }

        return [
            'data_asesmen_awal' => $data_asesmen,
            'data_asmen_riwayat' => $this->getContentDynamicField($data_asesmen, 'asmen_riwayat'),
            'list_asesmen_diambildari' => ArrayHelper::map($data_lookup['asmen_dari'], 'lookupkeperawatan_id', 'lookup_name'),
            'list_masuk_dengan' => ArrayHelper::map($data_lookup['asmen_masukdengan'], 'lookupkeperawatan_id', 'lookup_name'),
            'list_obat_darirumah' => ArrayHelper::map($data_lookup['asmen_masukdengan'], 'lookupkeperawatan_id', 'lookup_name'),
            'list_ketergantungan' => ArrayHelper::map($data_lookup['asmen_ketergantungan'], 'lookupkeperawatan_id', 'lookup_name'),
            'list_r_penyakit_kel' => ArrayHelper::map($data_lookup['asmen_penyakit_kel'], 'lookupkeperawatan_id', 'lookup_name'),
            'list_jenis_operasi' => ArrayHelper::map($this->getJenisOperasi(), 'golonganoperasi_id', 'golonganoperasi_nama'),
            'list_skgizi_bbturun' => $data_lookup['sgizi_bb_turun'],
            'list_skgizi_porsi' => $data_lookup['sgizi_porsi'],
            'list_skgizi_sakitberat' => $data_lookup['sgizi_sakitberat'],
            'info_cppt' => $this->getDataCpptInstruksiPulang($pendaftaran_id, $pasienadmisi_id),
            'diagnosa' => $diagnosa,
            'data_skrining' => $data_skrining
        ];
    }

    public function getListLookupKeperawatan($list)
    {
        $result = LookupKeperawatan::find()->select(['lookupkeperawatan_id', 'lookup_name', 'lookup_type', 'lookup_value']);
        $result->where(['IN', 'lookup_type', $list]);
        $data = $result->asArray()->all();
        $groupLookup = [];
        foreach ($data as $value) {
            $groupLookup[$value['lookup_type']][] = $value;
        }
        return $groupLookup;
    }

    public function getJenisOperasi()
    {
        $query = GolonganOperasi::find()->where(['is_active' => TRUE]);
        return $query->asArray()->all();
    }

    public function getLookupKeperawatanByType($type = null)
    {
        $result = LookupKeperawatan::find();

        if ($type) {
            $result->where(['lookup_type' => $type]);
        }

        return $result;
    }

    public function actionViewDiagnosisMasuk()
    {
        $request = Yii::$app->request;
        $post = $request->post();

        $result = Diagnosa::find();

        $result->select(['diagnosa_id', 'diagnosa_kode', 'CONCAT(diagnosa_kode,\' - \',diagnosa_nama) as diagnosa_nama']);
        if (!empty($post['keyword'])) {
            $term = $post['keyword'];
            $result->andWhere(['like', 'LOWER(diagnosa_kode)', $term]);
            $result->orWhere(['like', 'LOWER(diagnosa_nama)', $term]);
        }
        return $result->asArray()->all();
    }

    public function getDataAsesmenAwal($pendaftaran_id, $pasienadmisi_id)
    {
        $model = AsesmenAwal::find()->where(['pendaftaran_id' => $pendaftaran_id, 'pasienadmisi_id' => $pasienadmisi_id])->one();
        if (empty($model)) return null;

        return $model;
    }

    /**
     *
     * @see Fungsi get data tindakan bmhp
     * @return array response
     *
     */
    public function actionGetDataTindakanBmhp()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $instruksi_id = $request->get('instruksi_id', 0);
            $cppt_id = $request->get('cppt_id', 0);

            if ($id != null) {
                $data = RiwayatInstruksiTindakanView::find()
                    ->where([
                        'pendaftaran_id' => $id,
                        'cppt_id' => $cppt_id,
                        'instruksi_id' => $instruksi_id
                    ])
                    ->all();
                $data_instruksi = Instruksi::find()->where(['instruksi_id' => $instruksi_id])->one();
                return [
                    'riwayat_instruksi' => $data,
                    'data_instruksi' => $data_instruksi
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function getContentDynamicField($model, $field)
    {
        if (empty($model)) return null;

        if ($model->hasAttribute($field)) {
            if (!empty($model->{$field})) {
                return json_decode($model->{$field}, TRUE);
            }
        }

        return null;
    }

    public function actionSimpanSementara()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!isset($post)) {
                throw new \yii\base\ErrorException("Data Tidak Ditemukan", 500);
            }

            $assesmenAwalForm = $AsesmenDynamicModel = [];
            if (isset($post['AsesmenAwalForm'])) {
                $assesmenAwalForm = $post['AsesmenAwalForm'];
            }

            /*comment sementara*/
            // if(!isset($post['field-name'])){
            //     throw new \yii\base\ErrorException("Nama Field Tidak Ditemukan", 500);
            // }

            if (!isset($post['pendaftaran_id'])) {
                throw new \yii\base\ErrorException("Pendaftaran ID Tidak Ditemukan", 500);
            }

            if (!isset($post['pasien_id'])) {
                throw new \yii\base\ErrorException("Pasien ID Tidak Ditemukan", 500);
            }

            if (!isset($post['pasienadmisi_id'])) {
                throw new \yii\base\ErrorException("Pasien Admisi ID Tidak Ditemukan", 500);
            }

            $modelAsesmen = AsesmenAwal::find()->where(['pendaftaran_id' => $post['pendaftaran_id'], 'pasienadmisi_id' => $post['pasienadmisi_id']])->one();
            $isNew = false;
            if (is_null($modelAsesmen)) {
                $modelAsesmen = new AsesmenAwal;
                $isNew = true;
            }

            if (isset($post['AsesmenDynamicModel'])) {
                $asesmenDynamicModel = $post['AsesmenDynamicModel'];
            }

            $modelAsesmen->pendaftaran_id = $post['pendaftaran_id'];
            $modelAsesmen->pasienadmisi_id = $post['pasienadmisi_id'];
            $modelAsesmen->attributes = $assesmenAwalForm;

            if (isset($assesmenAwalForm['diagnosa_masuk'])) {
                $tmpValDiag = json_decode($assesmenAwalForm['diagnosa_masuk'], true);
                if (!isset($tmpValDiag['text'])) {
                    $valDiag = [];
                    $splitVal = explode('_', $assesmenAwalForm['diagnosa_masuk']);
                    if (count($splitVal) > 1) {
                        $valDiag['id'] = $splitVal[0];
                        $splitTxt = explode('-', $splitVal[1]);
                        if (count($splitTxt) > 1) {
                            $valDiag['kode'] = $splitTxt[0];
                            $valDiag['nama'] = $splitTxt[1];
                        }
                        $valDiag['text'] = $splitVal[1];
                    } else {
                        $valDiag['text'] = $assesmenAwalForm['diagnosa_masuk'];
                    }
                    $modelAsesmen->diagnosa_masuk = ($valDiag);
                } else {
                    $modelAsesmen->diagnosa_masuk = $assesmenAwalForm['diagnosa_masuk'];
                }
                unset($assesmenAwalForm['diagnosa_masuk']);
            }

            $modelAsesmen->ketergantungan = is_array($modelAsesmen->ketergantungan) ? implode(',', $modelAsesmen->ketergantungan) : null;
            $modelAsesmen->r_penyakit_kel = is_array($modelAsesmen->r_penyakit_kel) ? implode(',', $modelAsesmen->r_penyakit_kel) : null;
            $modelAsesmen->hasil_rad = null;
            if (isset($assesmenAwalForm['hasil_rad_opsional'])) {
                $modelAsesmen->hasil_rad = is_array($assesmenAwalForm['hasil_rad_opsional']) ? implode(',', $assesmenAwalForm['hasil_rad_opsional']) : null;
            }
            $modelAsesmen->hasil_lab = null;
            if (isset($assesmenAwalForm['hasil_lab_opsional'])) {
                $modelAsesmen->hasil_lab = is_array($assesmenAwalForm['hasil_lab_opsional']) ? implode(',', $assesmenAwalForm['hasil_lab_opsional']) : null;
            }
            $modelAsesmen->hasil_lainnya = null;
            if (isset($assesmenAwalForm['hasil_lainnya_opsional'])) {
                $modelAsesmen->hasil_lainnya = is_array($assesmenAwalForm['hasil_lainnya_opsional']) ? implode(',', $assesmenAwalForm['hasil_lainnya_opsional']) : null;
            }
            /*comment sementara [under construction BA]()*/

            // if($modelAsesmen->hasAttribute($post['field-name'])){
            //     $modelAsesmen->{$post['field-name']} = json_encode($asesmenDynamicModel);
            // }

            if (!$modelAsesmen->validate()) {
                throw new \yii\db\Exception('Gagal Validasi Asesmen', $modelAsesmen->getErrors(), 500);
            }
            if ($isNew) {
                if (!$modelAsesmen->save()) {
                    throw new \yii\db\Exception('Gagal Simpan Asesmen', $modelAsesmen->getErrors(), 500);
                }
            } else {
                if (!$modelAsesmen->update()) {
                    throw new \yii\db\Exception('Gagal Simpan Asesmen', $modelAsesmen->getErrors(), 500);
                }
            }

            $transaction->commit();

            return [
                'message' => 'Proses Berhasil!',
                'text' => 'Data telah disimpan sementara ',
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'text' => 'Gagal Validasi Data',
                'errorInfo' => $e->errorInfo
            ];
        } catch (\yii\base\ErrorException $e) {
            $transaction->rollBack();
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'data' => $e->getMessage(),
                'message' => $e->getMessage(),
                'text' => 'Kesalahan 1internal1',
                'errorInfo' => $e->getName()
            ];
        } catch (\yii\base\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'text' => 'Kesalahan 2internal2',
                'errorInfo' => $e->getName()
            ];
        }
    }

    public function actionCpptCreateTindakan()
    {
        // Declare connection
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        // Try catch
        try {
            // Get request
            $request = Yii::$app->request;

            // Check request
            if ($request->post()) {
                // Get post
                $post = $request->post();

                $data_instruksi = $post['InstruksiForm'];
                $data['instruksi_tindakan'] = isset($post['InstruksiTindakanForm']) ? $post['InstruksiTindakanForm'] : [];
                $data['bmhp_alkes'] = isset($post['InstruksiTindakanBmhpForm']) ? $post['InstruksiTindakanBmhpForm'] : [];
                $data['pendaftaran_id'] = isset($post['pendaftaran_id']) ? $post['pendaftaran_id'] : $data['instruksi_tindakan']['pendaftaran_id'];
                $pasienadmisi_id = isset($post['pasienadmisi_id']) ? $post['pasienadmisi_id'] : 0;
                $ruangan_id = isset($post['ruangan_id']) ? $post['ruangan_id'] : 0;
                $kelaspelayanan_id = isset($post['kelaspelayanan_id']) ? $post['kelaspelayanan_id'] : 0;
                $penjamin_id = isset($post['penjamin_id']) ? $post['penjamin_id'] : 0;
                $jeniskasuspenyakit_id = isset($post['jeniskasuspenyakit_id']) ? $post['jeniskasuspenyakit_id'] : 0;
                $instalasi_id = isset($post['instalasi_id']) ? $post['instalasi_id'] : 0;

                // generate cppt
                if ($data_instruksi['cppt_id'] == '0') {
                    $getCppt = AllowController::actionCreateSoap($post['ruangan'], $data['pendaftaran_id'], $post['pegawai_ruangan'], $post['pasien_id'], $pasienadmisi_id, [
                        'is_dokter' => isset($post['is_dokter']) && $post['is_dokter'] ? true : false
                    ]);
                    $data_instruksi['cppt_id'] = $getCppt;
                }

                // Unset data tindakan pelayanan
                unset($data['instruksi_tindakan']['tgl_tindakan']);
                unset($data['instruksi_tindakan']['daftartindakan_id']);
                unset($data['instruksi_tindakan']['qty']);
                unset($data['instruksi_tindakan']['tarif_satuan']);
                unset($data['instruksi_tindakan']['tarif_cyto']);
                unset($data['instruksi_tindakan']['tarif_tindakan']);
                unset($data['instruksi_tindakan']['dokterdpjp_id']);
                unset($data['instruksi_tindakan']['dokterdelegasi_id']);
                unset($data['instruksi_tindakan']['perawat1_id']);
                unset($data['instruksi_tindakan']['perawat2_id']);
                unset($data['instruksi_tindakan']['pendaftaran_id']);

                // Unset data obat alkes
                unset($data['bmhp_alkes']['obat']);
                unset($data['bmhp_alkes']['daftartindakan_id']);
                unset($data['bmhp_alkes']['obatalkes_id']);
                unset($data['bmhp_alkes']['qty']);
                unset($data['bmhp_alkes']['harga_jumlah']);
                unset($data['bmhp_alkes']['perawat1_id']);
                unset($data['bmhp_alkes']['perawat2_id']);
                unset($data['bmhp_alkes']['obatalkes_nama']);
                unset($data['bmhp_alkes']['hargasatuan_oa']);
                unset($data['bmhp_alkes']['harga_netto']);


                $data_tindakan = isset($data['instruksi_tindakan']) ? $data['instruksi_tindakan'] : [];
                $data_bmhpalkes = isset($data['bmhp_alkes']) ? $data['bmhp_alkes'] : [];
                $pendaftaran_id = isset($data['pendaftaran_id']) ? $data['pendaftaran_id'] : null;

                unset($data_bmhpalkes['depo_id']);

                // Declare sme variables
                $instruksitindakan = [];
                $instruksibmhp = [];
                $tindakanKomponen = [];

                // Assign data ke attribut instruksi
                if (!empty($data_instruksi['instruksi_id'])) {
                    $modelInstruksi = Instruksi::find(true)->where(['instruksi_id' => $data_instruksi['instruksi_id']])->one();
                } else {
                    if (isset($data_instruksi['instruksi_id'])) {
                        unset($data_instruksi['instruksi_id']);
                    }
                    $modelInstruksi = new Instruksi;
                }
                $modelInstruksi->attributes = $data_instruksi;
                // Parse cppt id dan tgl instruksi
                $modelInstruksi->cppt_id = (int)$modelInstruksi->cppt_id;
                $modelInstruksi->jenis_instruksi = DocoConstants::J_INST_TIND;
                $modelInstruksi->tgl_instruksi = date('Y-m-d H:i:s');

                if (!$modelInstruksi->save()) {
                    throw new \yii\db\Exception('Error Simpan Instruksi ', $modelInstruksi->getErrors(), 500);
                }

                $id_instruksi = $modelInstruksi->getPrimaryKey();
                $id_cppt = $modelInstruksi->cppt_id;
                $list_idInstruksiTindakan = [];
                foreach ($data_tindakan as $key => $value) {
                    if (isset($value['komponentarif_id'])) {
                        unset($value['komponentarif_id']);
                    }
                    if (isset($value['is_ubah_deleted']) && $value['is_ubah_deleted'] == 1) {
                        if (isset($value['instruksitindakan_id'])) {
                            (new InstruksiTindakan)->delete(['instruksitindakan_id' => $value['instruksitindakan_id']]);
                        }
                    } else {
                        if (!isset($value['instruksitindakan_id'])) {
                            if (isset($value['tipe']) && isset($value['tipepaket_id'])) {
                                if ($value['tipe'] == 'paket') {
                                    if (isset($value['daftartindakan_id'])) {
                                        unset($value['daftartindakan_id']);
                                    }
                                }
                            }
                            $value['qty_sisa'] = $value['qty'];
                            $value['instruksi_id'] = $id_instruksi;
                            $value['pasienadmisi_id'] = $pasienadmisi_id;
                            $value['status_implementasi'] = '454';
                            $value['tgl_tindakan'] = date('Y-m-d H:i:s');
                            $mInstruksiTindakan = new InstruksiTindakan;
                            $mInstruksiTindakan->attributes = $value;
                            if (!$mInstruksiTindakan->save()) {
                                throw new \yii\db\Exception('Gagal Simpan Instruksi Tindakan ' . @$key, $mInstruksiTindakan->getErrors(), 500);
                            }
                            if (isset($value['id_instruksi_tindakan']) && $value['id_instruksi_tindakan'] != '' && $value['id_instruksi_tindakan'] != 0) {
                                $list_idInstruksiTindakan[$value['id_instruksi_tindakan']] = $mInstruksiTindakan->getPrimaryKey();
                            }
                        } else {
                            $mInstruksiTindakanUpdate = InstruksiTindakan::find(true)->where(['instruksitindakan_id' => $value['instruksitindakan_id']])->one();
                            if ($value['qty'] > $mInstruksiTindakanUpdate->qty) {

                                $ditambahkan = $value['qty'] - $mInstruksiTindakanUpdate->qty;
                                $mInstruksiTindakanUpdate->qty_sisa = $mInstruksiTindakanUpdate->qty_sisa + $ditambahkan;
                                $mInstruksiTindakanUpdate->qty = $mInstruksiTindakanUpdate->qty + $ditambahkan;

                                if (!$mInstruksiTindakanUpdate->update()) {
                                    throw new \yii\db\Exception('Gagal Update Instruksi Tindakan ' . @$key, $mInstruksiTindakanUpdate->getErrors(), 500);
                                }
                            }
                        }
                    }
                }

                $konfigfarmasi = $connection->createCommand("
                        SELECT hargaygdigunakan FROM konfigfarmasi_k
                    ")->queryOne();
                foreach ($data_bmhpalkes as $key => $value) {
                    if (isset($value['is_ubah_deleted']) && $value['is_ubah_deleted'] == 1) {
                        if (isset($value['instruksitindakanbmhp_id'])) {
                            $stokObatR = StokObatAlkesR::find()
                                ->where(['obatalkes_id' => $value['obatalkes_id']])
                                ->andWhere(['ruangan_id' => $value['ruangan_id']])
                                ->one();

                            if (empty($stokObatR)) {
                                throw new \yii\base\ErrorException("Info Stok Obat Tidak Ada", 500);
                            }
                            $stokObatR->qty_tersedia = (int) $stokObatR->qty_tersedia +  (int) $value['qty'];
                            $stokObatR->qty_dipesan = (int) $stokObatR->qty_dipesan -  (int) $value['qty'];
                            if (!$stokObatR->update()) {
                                throw new \yii\db\Exception('Gagal Update Stok Dipesan ' . @$key, $stokObatR->getErrors(), 500);
                            }
                            (new InstruksiTindakanBmhp)->delete(['instruksitindakanbmhp_id' => $value['instruksitindakanbmhp_id']]);
                            continue;
                        }
                    }
                    if (isset($value['instruksitindakanbmhp_id']) && $value['instruksitindakanbmhp_id'] != '') {

                        $mInstruksiTindakanBmhpUpdate = InstruksiTindakanBmhp::find(true)->where(['instruksitindakanbmhp_id' => $value['instruksitindakanbmhp_id']])->one();
                        $is_update_bmhp = false;
                        if ($value['daftartindakan_id'] == '') {
                            $is_update_bmhp = true;
                            $mInstruksiTindakanBmhpUpdate->daftartindakan_id = null;
                            $mInstruksiTindakanBmhpUpdate->instruksitindakan_id = null;
                        }
                        if ($value['qty'] > $mInstruksiTindakanBmhpUpdate->qty) {
                            $is_update_bmhp = true;
                            $ditambahkan = $value['qty'] - $mInstruksiTindakanBmhpUpdate->qty;
                            $mInstruksiTindakanBmhpUpdate->qty_sisa = $mInstruksiTindakanBmhpUpdate->qty_sisa + $ditambahkan;
                            $mInstruksiTindakanBmhpUpdate->qty = $mInstruksiTindakanBmhpUpdate->qty + $ditambahkan;
                            $stokObatR = StokObatAlkesR::find()
                                ->where(['obatalkes_id' => $value['obatalkes_id']])
                                ->andWhere(['ruangan_id' => $value['ruangan_id']])
                                ->one();

                            if (empty($stokObatR)) {
                                throw new \yii\base\ErrorException("Info Stok Obat Tidak Ada", 500);
                            }
                            $stokObatR->qty_tersedia = (int) $stokObatR->qty_tersedia -  (int) $ditambahkan;
                            $stokObatR->qty_dipesan = (int) $stokObatR->qty_dipesan +  (int) $ditambahkan;
                            if (!$stokObatR->update()) {

                                throw new \yii\db\Exception('Gagal Update Stok Dipesan ' . @$key, $stokObatR->getErrors(), 500);
                            }
                        }
                        if ($is_update_bmhp) {
                            if (!$mInstruksiTindakanBmhpUpdate->update()) {
                                throw new \yii\db\Exception('Gagal Update Instruksi Tindakan ' . @$key, $mInstruksiTindakanBmhpUpdate->getErrors(), 500);
                            }
                        }
                        continue;
                    }
                    $value['instruksi_id'] = $id_instruksi;
                    $value['pasienadmisi_id'] = $pasienadmisi_id;
                    $value['tgl_pelayanan'] = date('Y-m-d H:i:s');
                    $value['status_implementasi'] = '454';
                    $value['kelaspelayanan_id'] = $kelaspelayanan_id;
                    $value['instalasi_id'] = $instalasi_id;
                    $value['jeniskasuspenyakit_id'] = $jeniskasuspenyakit_id;
                    if (!isset($value['obatalkes_id'])) {
                        throw new \yii\base\ErrorException("Obat Alkes Id Tidak Di set", 500);
                    }
                    if (isset($value['obatalkes_id']) && $value['obatalkes_id'] == '') {
                        throw new \yii\base\ErrorException("Obat Alkes Id Kosong", 500);
                    }

                    /* Diganti menggunakan fungsi */
                    // $infoObat = InfoStokObatAlkesView::find()->where([
                    //     'ruangan_id' => $ruangan_id,
                    //     'obatalkes_id' => $value['obatalkes_id']
                    // ])->asArray()->one();

                    // if($konfigfarmasi == "MAX"){
                    //     $hargadipakai = $infoObat['hargamaksimum'];
                    // }elseif ($konfigfarmasi == "MIN") {
                    //     $hargadipakai = $infoObat['hargaminimum'];
                    // }else{
                    //     $hargadipakai = $infoObat['hargaratarata'];
                    // }

                    $infoObat = (new InfoStokObatAlkesFnrNew(['extParam' => [$penjamin_id, $kelaspelayanan_id, $value['ruangan_id']]]))->find()->select([
                        'satuankecil_id',
                        'hargaygdipakai',
                        'harganetto_ygdipakai as harganetto',
                    ])->where([
                        'obatalkes_id' => $value['obatalkes_id']
                    ])->asArray()->one();

                    if (empty($infoObat)) {
                        throw new \yii\base\ErrorException("Info Obat Tidak Ada", 500);
                    }

                    $value['satuankecil_id'] = $infoObat['satuankecil_id'];
                    $value['is_ditagihkan'] = isset($value['is_ditagihkan']) && $value['is_ditagihkan'] != '' ? $value['is_ditagihkan'] : false;
                    if ($value['is_ditagihkan'] == 0 || $value['is_ditagihkan'] == false) {
                        $value['harga_netto'] = 0;
                        $value['harga_jualsatuan'] = 0;
                        $value['harga_jumlah'] = 0;
                    } else {
                        $value['harga_netto'] = $infoObat['harganetto'];
                        $value['harga_jualsatuan'] = ceil($infoObat['hargaygdipakai']);
                        $value['harga_jumlah'] = $value['harga_jualsatuan'] * $value['qty'];
                    }

                    $value['qty_sisa'] = $value['qty'];
                    if (isset($value['id_instruksi_tindakan']) && $value['id_instruksi_tindakan'] != '' && $value['id_instruksi_tindakan'] != 0) {
                        if (isset($list_idInstruksiTindakan[$value['id_instruksi_tindakan']])) {
                            $value['instruksitindakan_id'] = $list_idInstruksiTindakan[$value['id_instruksi_tindakan']];
                        }
                    }
                    $instruksibmhp[] = $value;

                    $stokObatR = StokObatAlkesR::find()
                        ->where(['obatalkes_id' => $value['obatalkes_id']])
                        ->andWhere(['ruangan_id' => $value['ruangan_id']])
                        ->one();

                    if (empty($stokObatR)) {
                        throw new \yii\base\ErrorException("Info Stok Obat Tidak Ada", 500);
                    }
                    $stokObatR->qty_tersedia = (int) $stokObatR->qty_tersedia -  (int) $value['qty'];
                    $stokObatR->qty_dipesan = (int) $stokObatR->qty_dipesan +  (int) $value['qty'];
                    if (!$stokObatR->update()) {
                        throw new \yii\db\Exception('Gagal Update Stok Dipesan ' . @$key, $stokObatR->getErrors(), 500);
                    }
                }

                // \Yii::$app->response->statusCode = 500;
                // return ['data'=>$instruksibmhp];
                if (!empty($instruksibmhp)) {
                    InstruksiTindakanBmhp::batchInsert($instruksibmhp);
                }
                // resume medis
                // ResumeMedisRIT::updateResume($data['pendaftaran_id'], 'tindakan');

                // Commit
                $transaction->commit();

                // Return
                return ['message' => 'Data Berhasil di simpan', 'pendaftaran_id' => $pendaftaran_id, 'cppt_id' => $this->helper->encrypt($modelInstruksi->cppt_id)];
            }
        } catch (\yii\db\Exception $e) {
            // Rollback transaction
            $transaction->rollBack();

            // Status code
            \Yii::$app->response->statusCode = 500;
            // get log
            Yii::error([
                'Message' => $e->getMessage(),
                'Line' => $e->getLine(),
                'File' => $e->getFile(),
            ]);
            // Return message
            return [
                'message' => $e->getMessage(),
                'text' => 'Gagal Validasi Data'
            ];
        } catch (\yii\base\ErrorException $e) {
            // Rollback transaction
            $transaction->rollBack();

            // Status code
            \Yii::$app->response->statusCode = 500;
            // get log
            Yii::error([
                'Message' => $e->getMessage(),
                'Line' => $e->getLine(),
                'File' => $e->getFile(),
            ]);

            // Return message
            return [
                'message' => $e->getMessage(),
                'text' => 'Kesalahan Internal'
            ];
        }
    }

    /**
     *
     * @see Fungsi get tindakan ruangan
     * @return object
     *
     */
    public function actionCpptGetTindakanRuangan($ruanganId = null, $kelasPelayananId = null, $id = null, $penjaminId = null, $komponenTarifId = DocoConstants::KOMPONEN_TARIF)
    {
        // Try catch
        try {
            // Get data
            $model = InfoTarifRs::find()->where(['daftartindakan_id' => $id]);

            // Check condition
            if ($ruanganId != null) {
                // Add condition
                $model->andWhere(['ruangan_id' => $ruanganId]);
            }

            // Check condition
            if ($kelasPelayananId != null) {
                // Add condition
                $model->andWhere(['kelaspelayanan_id' => $kelasPelayananId]);
            }

            // Check condition
            if ($penjaminId != null) {
                // Add condition
                $model->andWhere(['penjamin_id' => $penjaminId]);
            }

            // Check condition
            if ($komponenTarifId != null) {
                // Add condition
                $model->andWhere(['komponentarif_id' => $komponenTarifId]);
            }

            // Return model
            return $model->one();
        } catch (\yii\db\Exception $e) {
            // Exception
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Exception
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @see Fungsi get tindakan ruangan
     * @return object
     *
     */
    public function actionCpptGetPaketRuangan($ruanganId = null, $kelasPelayananId = null, $id = null, $penjaminId = null)
    {
        // Try catch
        try {
            $result = InfoTarifRs::find()
                ->where(['infotarifrs_v.tipepaket_id' => $id])
                ->leftJoin('tariftindakan_m', 'tariftindakan_m.tariftindakan_id = infotarifrs_v.tariftindakan_id');

            if ($ruanganId) {
                $result->andWhere(['infotarifrs_v.ruangan_id' => $ruanganId]);
            }

            if ($kelasPelayananId) {
                $result->andWhere(['infotarifrs_v.kelaspelayanan_id' => $kelasPelayananId]);
            }

            if ($penjaminId) {
                $result->andWhere(['infotarifrs_v.penjamin_id' => $penjaminId]);
            }

            $result->andWhere(['infotarifrs_v.komponentarif_id' => DocoConstants::KOMPONEN_TARIF]);

            $result->andWhere('infotarifrs_v.tipepaket_id IS NOT NULL');

            $data_paket = $result->asArray()->one();
            if ($data_paket) {
                $result = PaketDetailView::find()->where(['tipepaket_id' => $data_paket['tipepaket_id']])->asArray()->all();
                $data_paket['paketDetail'] = $result;
            }
            return $data_paket;
        } catch (\yii\db\Exception $e) {
            // Exception
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Exception
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionCpptGetObatAlkes()
    {
        $request = Yii::$app->request;
        $group_jenis = $request->get('group_jenis');
        $ruangan_id = $request->get('ruangan_id');

        $model = new InfoStokObatAlkesView;
        $query = $model::find()
            ->where([
                'ruangan_id' => $ruangan_id,
                'group_jenisobat' => $group_jenis
            ]);
        $data = $query->all();

        return empty($data) ? [] : $data;
    }

    /**
     * @todo Fungsi untuk mendapatkan obatalkes by function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCpptGetObatAlkesFn()
    {
        $request = Yii::$app->request;
        $group_jenis = $request->get('group_jenis');
        $ruangan_id = $request->get('ruangan_id');

        $model = InfoStokObatAlkesFn::find()->select([
            'obatalkes_id',
            'obatalkes_nama',
            'qty_tersedia',
            'harganetto',
            'hargamaksimum',
            'hargaminimum',
            'hargaratarata',
            'ruangan_id',
            'hargaygdipakai',
            'jml_hargajual',
            'group_jenisobat',
        ])->where([
            'ruangan_id' => $ruangan_id,
        ]);

        if ($group_jenis) {
            $model->andWhere(['group_jenisobat' => $group_jenis]);
        }
        $model = $model->asArray()->all();
        return empty($model) ? [] : $model;
    }

    /**
     *
     * @see Fungsi update stok obat alkes
     * @return array response
     *
     */
    public function actionCpptUpdateStokObatAlkes()
    {
        // Try catch
        try {
            // Get request
            $request = Yii::$app->request;
            $id = $request->get('id');
            $ruangan_id = $request->get('ruangan_id');
            $type = $request->get('type');
            $data = $request->post();

            // Check request
            if ($id != null && $ruangan_id != null && $type != '') {
                // Get data
                $model = StokObatAlkesR::find()->where(['obatalkes_id' => $id])->andWhere(['ruangan_id' => $ruangan_id])->one();

                // Check model
                if (!empty($model)) {
                    // Check type
                    if ($type == 'update') {
                        // Calculate qty tersedia
                        $model->qty_tersedia = floatval($model->qty_tersedia) - floatval($data['jumlah']);
                    } else {
                        // Calculate qty tersedia
                        $model->qty_tersedia = floatval($model->qty_tersedia) + floatval($data['jumlah']);
                    }

                    // Save
                    if ($model->save()) {
                        // Return model
                        return $model;
                    } else {
                        // Return errors
                        return $model->errors;
                    }
                }
            }
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
        }
    }

    private function getDataCpptInstruksiPulang($pendaftaran_id, $pasienadmisi_id)
    {
        $sqlExist = "SELECT EXISTS( SELECT *
                FROM cppt_t
                WHERE is_instruksi_pulang = TRUE
                AND is_instruksi_pulang IS NOT NULL
                AND pasienadmisi_id = '{$pasienadmisi_id}' AND pendaftaran_id = '{$pendaftaran_id}'
                ORDER BY cppt_id DESC  LIMIT 1) AS is_exist
        ";
        $is_instruksi_pulang = Yii::$app->db->createCommand($sqlExist)->queryOne();
        $sql = "SELECT *
                FROM cppt_t
                WHERE is_instruksi_pulang = TRUE
                AND is_instruksi_pulang IS NOT NULL
                AND pasienadmisi_id = '{$pasienadmisi_id}' AND pendaftaran_id = '{$pendaftaran_id}'
                ORDER BY cppt_id DESC  LIMIT 1
        ";
        $data = null;
        if ($is_instruksi_pulang == TRUE) {
            $data = Yii::$app->db->createCommand($sql)->queryOne();
        }
        return [
            'is_instruksi_pulang' => $is_instruksi_pulang['is_exist'],
            'data_cppt' => $data
        ];
    }

    /**
     * @controller actionCetakPdfAsesmenAwal
     * @attribute #identitas_namapasien# => Identitas Pasien: Nama Pasien
     * @attribute #identitas_norekammedik# => Identitas Pasien: No Rekam Medik
     * @attribute #identitas_tanggallahir# => Identitas Pasien: Tanggal Lahir
     * @attribute #identitas_jeniskelamin# => Identitas Pasien: Jenis Kelamin
     * @attribute #identitas_umur# => Identitas Pasien: Umur
     * @attribute #identitas_ruangan# => Identitas Pasien: Ruang Rawat
     * @attribute #identitas_kelas# => Identitas Pasien: Kelas Pelayanan
     * @attribute #identitas_dokterdpjp# => Identitas Pasien: Dokter DPJP
     * @attribute #identitas_penjamin# => Identitas Pasien: Nama Penjamin
     * @attribute #waktu_tiba# => waktu pasien tiba
     * @attribute #asal_masuk# => asal masuk pasien
     * @attribute #tanggal_asesmen# => tanggal dilakukan asesmen
     * @attribute #asesmen_diambil_dari# => asesmen diambil dari
     * @attribute #masuk_dengan# => masuk dengan
     * @attribute #obat_darirumah# => Obat dari rumah
     * @attribute #hasil_pemeriksaan# => hasil pemeriksaan
     * @attribute #table_hasil_pemeriksaan# => table hasil pemeriksaan
     * @attribute #content_riwayat_kesehatan# => Content Riwayat Kesehatan
     * @attribute #content_skrining_gizi# => Content Screening Gizi
     * @attribute #tgl_cetak# => tanggal cetak
     * @attribute #nama_user# => nama user cetak
     **/
    public function actionCetakPdfAsesmenAwal()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id', 0);
            $pasienadmisi_id = $request->get('pasienadmisi_id', 0);

            $modelHeader = new InfoPasienRanap;
            $queryHeader = $modelHeader::find()
                ->andWhere([
                    'pendaftaran_id' => $pendaftaran_id,
                    'pasienadmisi_id' => $pasienadmisi_id,
                ]);
            $resultHeader = $queryHeader->asArray()->one();

            // Directory Creation
            $header1 = array(
                Yii::t('app', "Nama pasien") => $resultHeader ? $resultHeader['nama_pasien'] : '',
                Yii::t('app', "No rekam medik") => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
                Yii::t('app', "Tanggal lahir") => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d-m-Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
                Yii::t('app', "Jenis kelamin") => $resultHeader ? $resultHeader['jenis_kelamin'] : '',
                Yii::t('app', "Umur") => $resultHeader ? $resultHeader['umur'] : '',
                Yii::t('app', "Ruangan / kelas") => $resultHeader ? $resultHeader['ruangan_nama'] . ' / ' . $resultHeader['kelas_pelayanan'] : '',
                Yii::t('app', "Dokter DPJP") => $resultHeader ? $resultHeader['dokter_admisi'] : '',
                Yii::t('app', "Penjamin") => $resultHeader ? $resultHeader['penjamin_nama'] : '',
            );
            $masuk_dengan = '';
            $diambil_dari = '';

            $ket_haid = new Expression("CASE
                    WHEN asesmenawal_t.haid_teratur = TRUE THEN 'Teratur'
                    ELSE 'Tidak Teratur'
                    END AS ket_haid");
            $ket_pernahdirawat = new Expression("CASE
                    WHEN asesmenawal_t.pernah_dirawat = TRUE THEN 'Ya'
                    ELSE 'Tidak'
                    END AS ket_pernahdirawat");
            $ket_pernahtindakan = new Expression("CASE
                    WHEN asesmenawal_t.pernah_tindakan = TRUE THEN 'Ya'
                    ELSE 'Tidak'
                    END AS ket_pernahtindakan");
            $ket_pernahalergi = new Expression("CASE
                    WHEN asesmenawal_t.r_alergi = TRUE THEN 'Ya'
                    ELSE 'Tidak'
                    END AS ket_pernahalergi");
            $ket_transfusi = new Expression("CASE
                    WHEN asesmenawal_t.transfusi = TRUE THEN 'Ya'
                    ELSE 'Tidak'
                    END AS ket_transfusi");
            $data_riwayatkesehatan = (new \yii\db\Query())
                ->select([
                    'asesmenawal_t.keluhan_utama',
                    'asesmenawal_t.r_kes_sekarang',
                    'asesmenawal_t.r_kehamilan_g',
                    'asesmenawal_t.r_kehamilan_p',
                    'asesmenawal_t.r_kehamilan_a',
                    // 'concat(diagnosa_m.diagnosa_kode, \' - \', diagnosa_m.diagnosa_namalainnya) AS diagnosa_nama',
                    '(asesmenawal_t.diagnosa_masuk::json->>\'text\') as diagnosa_nama',
                    'asesmenawal_t.hpht',
                    'asesmenawal_t.ketergantungan',
                    'asesmenawal_t.r_penyakit_kel',
                    'asesmenawal_t.penyakit_kel_lain',
                    'asesmenawal_t.pernah_dirawat',
                    'asesmenawal_t.tgl_dirawat',
                    'asesmenawal_t.alasan_dirawat',
                    'asesmenawal_t.pernah_tindakan',
                    'asesmenawal_t.tgl_tindakan',
                    'golonganoperasi_m.golonganoperasi_nama',
                    'asesmenawal_t.r_alergi',
                    'asesmenawal_t.nama_alergi',
                    'asesmenawal_t.transfusi',
                    'asesmenawal_t.reaksi',
                    'asesmenawal_t.haid_teratur',
                    $ket_haid,
                    $ket_pernahdirawat,
                    $ket_pernahtindakan,
                    $ket_pernahalergi,
                    $ket_transfusi
                ])
                ->from('asesmenawal_t')
                // ->leftJoin('diagnosa_m','asesmenawal_t.diagnosa_masuk = diagnosa_m.diagnosa_id')
                // ->leftJoin('diagnosa_m','(asesmenawal_t.diagnosa_masuk::json->>\'id\')::INTEGER = diagnosa_m.diagnosa_id')
                ->leftJoin('golonganoperasi_m', 'asesmenawal_t.jeniskegiatantindakan_id = golonganoperasi_m.golonganoperasi_id')
                ->where([
                    'pendaftaran_id' => $pendaftaran_id,
                    'pasienadmisi_id' => $pasienadmisi_id
                ])
                ->one();
            $list_asmenketergantungan = [];
            $list_asmenpenyakitkel = [];
            $mLookupKetergantungan = LookupKeperawatan::find()->where(['lookup_type' => 'asmen_ketergantungan'])->asArray()->all();
            $lookup_ketergantungan = ArrayHelper::map($mLookupKetergantungan, 'lookupkeperawatan_id', 'lookup_name');
            $mLookupPenyakitkel = LookupKeperawatan::find()->where(['lookup_type' => 'asmen_penyakit_kel'])->asArray()->all();
            $lookup_penyakitkel = ArrayHelper::map($mLookupPenyakitkel, 'lookupkeperawatan_id', 'lookup_name');
            if (isset($data_riwayatkesehatan['ketergantungan'])) {
                $list_idketergantungan = explode(',', $data_riwayatkesehatan['ketergantungan']);
                foreach ($list_idketergantungan as $idketergantungan) {
                    if (isset($lookup_ketergantungan[$idketergantungan]))
                        $list_asmenketergantungan[] = $lookup_ketergantungan[$idketergantungan];
                }
            }
            $list_asmenpenyakitkellainnya = '';
            if (isset($data_riwayatkesehatan['r_penyakit_kel'])) {
                $list_idpenyakitkel = explode(',', $data_riwayatkesehatan['r_penyakit_kel']);
                foreach ($list_idpenyakitkel as $idpenyakitkel) {
                    if (isset($lookup_penyakitkel[$idpenyakitkel])) {
                        if ($idpenyakitkel == 14) {
                            $list_asmenpenyakitkellainnya = @$data_riwayatkesehatan['penyakit_kel_lain'];
                            $list_asmenpenyakitkel[] = $lookup_penyakitkel[$idpenyakitkel] . ':' . $list_asmenpenyakitkellainnya;
                        } else {
                            $list_asmenpenyakitkel[] = $lookup_penyakitkel[$idpenyakitkel];
                        }
                    }
                }
            }
            $list_asmenketergantungan = count($list_asmenketergantungan) > 0 ? implode(',', $list_asmenketergantungan) : '';
            $list_asmenpenyakitkel = count($list_asmenpenyakitkel) > 0 ? implode(',', $list_asmenpenyakitkel) : '';
            $mAsesmen = AsesmenAwal::find()->where([
                'pendaftaran_id' => $pendaftaran_id,
                'pasienadmisi_id' => $pasienadmisi_id
            ])->one();
            if (empty($mAsesmen)) {
                $mAsesmen = new AsesmenAwal;
            } else {
                $mLookupMasukDengan = LookupKeperawatan::find()->where(['lookup_type' => 'asmen_masukdengan'])->asArray()->all();
                $lookup_masukdengan = ArrayHelper::map($mLookupMasukDengan, 'lookupkeperawatan_id', 'lookup_name');
                if ($mAsesmen->masuk_dengan == 6) {
                    $masuk_dengan = @$lookup_masukdengan[$mAsesmen->masuk_dengan] . ':' . @$mAsesmen->masuk_denganlain;
                } else {
                    $masuk_dengan = @$lookup_masukdengan[$mAsesmen->masuk_dengan];
                }

                $mLookupDiambildari = LookupKeperawatan::find()->where(['lookup_type' => 'asmen_dari'])->asArray()->all();
                $lookup_diambildari = ArrayHelper::map($mLookupDiambildari, 'lookupkeperawatan_id', 'lookup_name');
                if ($mAsesmen->asesmen_diambildari == 2) {
                    $diambil_dari = @$lookup_diambildari[$mAsesmen->asesmen_diambildari];
                    $diambil_dari .= isset($mAsesmen->diambildari_nama) ? ', Nama: ' . $mAsesmen->diambildari_nama : '';
                    $diambil_dari .= isset($mAsesmen->diambildari_hub) ? ', Hubungan: ' . $mAsesmen->diambildari_hub : '';
                } else {
                    $diambil_dari = @$lookup_diambildari[$mAsesmen->asesmen_diambildari];
                }
            }
            $data_hasilpemeriksaan = [];
            $data_hasilpemeriksaan['laboratorium'] = isset($mAsesmen->hasil_lab) ? explode(',', $mAsesmen->hasil_lab) : [];
            $data_hasilpemeriksaan['radiologi'] = isset($mAsesmen->hasil_rad) ? explode(',', $mAsesmen->hasil_rad) : [];
            $data_hasilpemeriksaan['lainnya'] = isset($mAsesmen->hasil_lainnya) ? explode(',', $mAsesmen->hasil_lainnya) : [];

            // rizal
            // get data screening gizi
            $data_skrininggizi = SkriningGiziView::find()
                ->andWhere(['pendaftaran_id' => $pendaftaran_id])
                ->asArray()->one();

            $print = new DocoPrint();
            $print->attributes = [
                '#identitas_namapasien#' => @$resultHeader['nama_pasien'],
                '#identitas_norekammedik#' => @$resultHeader['no_rekam_medik'],
                '#identitas_tanggallahir#' => date('d-m-Y', strtotime($resultHeader['tanggal_lahir'])),
                '#identitas_jeniskelamin#' => @$resultHeader['jenis_kelamin'],
                '#identitas_umur#' => @$resultHeader['umur'],
                '#identitas_ruangan#' => @$resultHeader['ruangan_nama'],
                '#identitas_kelas#' => @$resultHeader['kelas_pelayanan'],
                '#identitas_dokterdpjp#' => @$resultHeader['dokter_admisi'],
                '#identitas_penjamin#' => @$resultHeader['penjamin_nama'],
                '#waktu_tiba#' => date('d-m-Y H:i:s', strtotime($mAsesmen->waktu_tiba)),
                '#asal_masuk#' => $mAsesmen->asal_masuk,
                '#tanggal_asesmen#' => date('d-m-Y H:i:s', strtotime($mAsesmen->tgl_asesmen)),
                '#asesmen_diambil_dari#' => $diambil_dari,
                '#masuk_dengan#' => $masuk_dengan,
                '#obat_darirumah#' => $resultHeader['obat_darirumah'] ? 'Tidak Ada' : 'Ada (Lihat Formulir Rekonsiliasi Obat Farmasi)',
                '#hasil_pemeriksaan#' => @$resultHeader['hasil_pemeriksaan'] ? "" : "Tidak Ada",
                '#table_hasil_pemeriksaan#' => @$resultHeader['hasil_pemeriksaan'] ? $this->renderPartial('table_hasil_pemeriksaan', ['data_hasilpemeriksaan' => $data_hasilpemeriksaan]) : '',
                // '#riwayat_keluhanutama#' => @$data_riwayatkesehatan['keluhan_utama'],
                // '#riwayat_diagnosamasuk#' => @$data_riwayatkesehatan['diagnosa_nama'],
                // '#riwayat_kehamilan_g#' => @$data_riwayatkesehatan['r_kehamilan_g'],
                // '#riwayat_kehamilan_p#' => @$data_riwayatkesehatan['r_kehamilan_p'],
                // '#riwayat_kehamilan_a#' => @$data_riwayatkesehatan['r_kehamilan_a'],
                // '#riwayat_hpht#' => $data_riwayatkesehatan['hpht'] ? date('d-m-Y H:i:s', strtotime($data_riwayatkesehatan['hpht'])) : '',
                // '#riwayat_keteranganhaid#' => @$data_riwayatkesehatan['ket_haid'],
                // '#riwayat_ketergantungan#' => @$list_asmenketergantungan,
                // '#riwayat_penyakitkeluarga#' => @$list_asmenpenyakitkel,
                // '#riwayat_kesehatansekarang#' => @$data_riwayatkesehatan['r_kes_sekarang'],
                // '#riwayat_pernahdirawat#' => @$data_riwayatkesehatan['ket_pernahdirawat'],
                // '#riwayat_tanggaldirawat#' => $data_riwayatkesehatan['tgl_dirawat'] ? date('d-m-Y H:i:s', strtotime($data_riwayatkesehatan['tgl_dirawat'])) : '',
                // '#riwayat_alasandirawat#' => @$data_riwayatkesehatan['alasan_dirawat'],
                // '#riwayat_pernahtindakan#' => @$data_riwayatkesehatan['ket_pernahtindakan'],
                // '#riwayat_tanggaltindakan#' => $data_riwayatkesehatan['tgl_tindakan'] ? date('d-m-Y H:i:s', strtotime($data_riwayatkesehatan['tgl_tindakan'])) : '',
                // '#riwayat_jenistindakan#' => @$data_riwayatkesehatan['golonganoperasi_nama'],
                // '#riwayat_ket_alergi#' => @$data_riwayatkesehatan['ket_pernahalergi'],
                // '#riwayat_namaalergi#' => @$data_riwayatkesehatan['nama_alergi'],
                // '#riwayat_ket_transfusi#' => @$data_riwayatkesehatan['ket_transfusi'],
                // '#riwayat_reaksi#' => @$data_riwayatkesehatan['reaksi'],
                '#content_riwayat_kesehatan#' => $this->renderPartial('riwayat_kesehatan', [
                    'data_riwayatkesehatan' => $data_riwayatkesehatan,
                    'list_asmenketergantungan' => $list_asmenketergantungan,
                    'list_asmenpenyakitkel' => $list_asmenpenyakitkel,
                ]),
                '#content_skrining_gizi#' => $this->renderPartial('skrining_gizi', [
                    'data_skrininggizi' => $data_skrininggizi,
                ]),
                '#tgl_cetak#' => date('d-m-Y H:i:s'),
                '#nama_user#' => $request->get('nama_user', 'Nama User')
            ];
            $print->Output();
        } catch (Exception $e) {
            var_dump($e->getMessage());
            die();
        }
    }

    public function actionSimpanSkriningGizi()
    {
        try {
            $params = Yii::$app->request;
            $pendaftaran_id = $params->post('pendaftaran_id', 0);
            $isNew = false;
            $model = SkriningGizi::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
            if (is_null($model)) {
                $model = new SkriningGizi;
                $isNew = true;
            }
            $is_sementara = $params->post('submit-sementara', 0);
            $model->attributes = $params->post('SkriningGiziForm', []);
            $model->pasienadmisi_id = $params->post('pasienadmisi_id', 0);
            $model->pendaftaran_id = $pendaftaran_id;

            if (isset($model->skor)) {
                if ($model->skor >= 2) {
                    $model->status_asesmen = DocoConstants::STATUSSKRININGGIZI_BELUM;
                } else {
                    $model->status_asesmen = DocoConstants::STATUSSKRININGGIZI_TIDAK;
                }
            }
            if ($is_sementara == 1) {
                $model->is_active = false;
            } else {
                $model->is_active = true;
            }
            if (!$model->validate()) {
                throw new \yii\db\Exception('Gagal Validasi Skrining Gizi', $model->getErrors(), 500);
            }
            if ($isNew) {
                if (!$model->save()) {
                    throw new \yii\db\Exception('Gagal Simpan Skrining Gizi', $model->getErrors(), 500);
                }
            } else {
                if (!$model->update()) {
                    throw new \yii\db\Exception('Gagal ubah Skrining Gizi', $model->getErrors(), 500);
                }
            }

            return [
                'message' => 'Proses Berhasil!',
                'text' => 'Data Skrining Berhasil Disimpan ',
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'text' => 'Gagal Validasi Data',
                'errorInfo' => $e->errorInfo
            ];
        } catch (\yii\base\ErrorException $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'data' => $e->getMessage(),
                'message' => $e->getMessage(),
                'text' => 'Kesalahan Internal',
                'errorInfo' => $e->getName()
            ];
        } catch (\yii\base\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'text' => 'Kesalahan Internal',
                'errorInfo' => $e->getName()
            ];
        }
    }

    public function actionAllDokterList()
    {
        $page = Yii::$app->request->get('page', 1);
        $query = Pegawai::find()
            ->select([
                'pegawai_id as id',
                'nama_pegawai as text'
            ]);

        $query = $query->where(['kelompokpegawai_id' => '1'])
            ->orderBy(['nama_pegawai' => SORT_ASC]);

        $term = Yii::$app->request->get('term');
        if (!empty($term)) {
            $query = $query->andWhere([
                'like',
                'LOWER(nama_pegawai)',
                strtolower($term)
            ]);
        }

        $is_active = Yii::$app->request->get('is_active');
        if(!empty($is_active)){
            $query = $query->andWhere(['=', 'is_active', true]);
        }

        return $query
            ->limit(11)
            ->offset(($page - 1) * 10)
            ->asArray()
            ->all();
    }


    /**
     * summary
     *
     * @return void
     * @author Aris Munandar
     */

    public function actionFetchListTindakan()
    {
        $get = Yii::$app->request->get();
        $pasien_id = Yii::$app->request->get('pasien_id');
        $startDate = empty($get['startDate']) ? date('Y-m-d H:i:s', strtotime('1970-01-01 00:00:00')) : DateTime::createFromFormat('d/m/Y', $get['startDate'])->format('Y-m-d') . ' 00:00:00';
        $endDate = empty($get['endDate']) ? date('Y-m-d H:i:s') : DateTime::createFromFormat('d/m/Y', $get['endDate'])->format('Y-m-d') . ' 23:59:59';
        $jenis = empty($get['jenis']) ? new \yii\db\Expression('') : $get['jenis'];
        $instruksi = empty($get['instruksi']) ? new \yii\db\Expression('') : $get['instruksi'];
        $type = ArrayHelper::getValue($get, 'type');
        $pendaftaran_id = $type == 'ranapLaporanTerapi' ? new \yii\db\Expression('NULL') : ArrayHelper::getValue($get, 'type');
        $kelompoktindakan_id = "'".DocoConstants::VAR_KEL_KRCS . ',' . DocoConstants::KELOMPOK_MAKANAN."'";
        $limit = ArrayHelper::getValue($get, 'length');
        $offset = ArrayHelper::getValue($get, 'start');

        $data = (new FGetinstruksi([
                'extParam' =>  $pasien_id . ",'" . $startDate . "','" . $endDate . "','" . $jenis . "','" . $instruksi . "'," . $pendaftaran_id . "," . $kelompoktindakan_id . "," . $limit . "," . $offset
            ]))->find()
            ->select([
                'INITCAP(tipe_instruksi) as jenis',
                'INITCAP(grouping_tipe) as grouping_tipe',
                'tanggal_terapi as tgl_tindakan',
                'instruksi_id',
                'instruksi',
                "dokter as nama_pegawai",
                'pendaftaran_id',
                'no_pendaftaran',
                'ruangan_pertindakan as ruangan_penunjang_nama',
                'tindakaninstruksi_nama',
                'pasienkirimkeunitlain_id',
                'instalasi_id AS instalasi_penunjang_id',
                'instalasi_nama as instalasi_penunjang_nama',
                'is_bayar',
                'tindakan_deleted as deleted',
                'instruksitindakan_id',
                'status_implementasi',
                'is_pulang',
                'noresep',
                'qty',
                'satuankecil_nama',
                'is_telah_implementasi',
                'status_bmhp_id',
                'status_bmhp_nama',
                'alasan_batal'
            ]);

        switch (Yii::$app->request->get('type', 'ranap')) {
            case 'ranap':
                $data = $data->where([
                        'pasienadmisi_id' => Yii::$app->request->get('pasienadmisi_id')
                    ])
                    ->andWhere([
                        'IS NOT', 'instruksi', null
                    ]);
                break;
            case 'ranapLaporanTerapi':
                break;
            default:
                $data = $data->where([
                        'IS NOT', 'kelompoktindakan_id', null
                    ])
                    ->orderBy([
                        'tgl_tindakan' => SORT_DESC
                    ]);
                break;
        }

        if(in_array(Yii::$app->request->get('type', 'ranap'), ['ranap', 'ranapLaporanTerapi'])){
            $data = $data->groupBy([
                'tipe_instruksi',
                'grouping_tipe',
                'tanggal_terapi',
                'instruksi_id',
                'instruksi',
                'dokter',
                'pendaftaran_id',
                'no_pendaftaran',
                'ruangan_pertindakan',
                'tindakaninstruksi_nama',
                'pasienkirimkeunitlain_id',
                'instalasi_id',
                'instalasi_nama',
                'is_bayar',
                'tindakan_deleted',
                'instruksitindakan_id',
                'status_implementasi',
                'is_pulang',
                'noresep',
                'qty',
                'satuankecil_nama',
                'tgl_instruksi',
                'is_telah_implementasi',
                'status_bmhp_id',
                'status_bmhp_nama',
                'alasan_batal'
            ])
            ->orderBy([
                'tgl_tindakan' => SORT_DESC
            ]);
        }
        $listData = $data->asArray()->all();
        $totalData = count($listData);
        $result = [];
        $arr = [];
        foreach($listData as $v) {
            $listTipeGroups = ['reseptur', 'rehab medik'];
            $isMustBeGroup = in_array(strtolower($v['grouping_tipe']), $listTipeGroups);
            if ($isMustBeGroup) {
                $isRehabMedik = strtolower($v['grouping_tipe']) == 'rehab medik';
                $isResep = strtolower($v['grouping_tipe']) == 'reseptur';
                $uniqueKey = "noresep";
                if ($isRehabMedik) {
                    $uniqueKey = "instruksi_id";
                }
                if(!isset($result[$v[$uniqueKey]])) {
                    $result[$v[$uniqueKey]]["grouping_tipe"] = $v["grouping_tipe"];
                    $result[$v[$uniqueKey]]["tgl_tindakan"] = $v["tgl_tindakan"];
                    $result[$v[$uniqueKey]]["nama_pegawai"] = $v["nama_pegawai"];
                    $result[$v[$uniqueKey]]["pendaftaran_id"] = $v["pendaftaran_id"];
                    $result[$v[$uniqueKey]]["no_pendaftaran"] = $v["no_pendaftaran"];
                    $result[$v[$uniqueKey]]["ruangan_penunjang_nama"] = $v["ruangan_penunjang_nama"];
                    $result[$v[$uniqueKey]]["pasienkirimkeunitlain_id"] = $v["pasienkirimkeunitlain_id"];
                    $result[$v[$uniqueKey]]["instalasi_penunjang_id"] = $v["instalasi_penunjang_id"];
                    $result[$v[$uniqueKey]]["instalasi_penunjang_nama"] = $v["instalasi_penunjang_nama"];
                    $result[$v[$uniqueKey]]["is_bayar"] = $v["is_bayar"];
                    $result[$v[$uniqueKey]]["deleted"] = $v["deleted"];
                    $result[$v[$uniqueKey]]["status_implementasi"] = $v["status_implementasi"];
                    $result[$v[$uniqueKey]]["is_pulang"] = $v["is_pulang"];
                    $result[$v[$uniqueKey]]["noresep"] = $v["noresep"];
                    $result[$v[$uniqueKey]]["jenis"][] = $v["jenis"];
                    $result[$v[$uniqueKey]]["instruksi"][] = $v["instruksi"];
                    $result[$v[$uniqueKey]]["tindakaninstruksi_nama"][] = $v["tindakaninstruksi_nama"];
                    $result[$v[$uniqueKey]]["instruksitindakan_id"][] = $v["instruksitindakan_id"];
                    $result[$v[$uniqueKey]]["qty"][] = $v["qty"];
                    $result[$v[$uniqueKey]]["satuankecil_nama"][] = $v["satuankecil_nama"];
                    $result[$v[$uniqueKey]]["is_telah_implementasi"][] = $v["is_telah_implementasi"];
                } else {
                    $result[$v[$uniqueKey]]["grouping_tipe"] = $v["grouping_tipe"];
                    $result[$v[$uniqueKey]]["tgl_tindakan"] = $v["tgl_tindakan"];
                    $result[$v[$uniqueKey]]["nama_pegawai"] = $v["nama_pegawai"];
                    $result[$v[$uniqueKey]]["pendaftaran_id"] = $v["pendaftaran_id"];
                    $result[$v[$uniqueKey]]["no_pendaftaran"] = $v["no_pendaftaran"];
                    $result[$v[$uniqueKey]]["ruangan_penunjang_nama"] = $v["ruangan_penunjang_nama"];
                    $result[$v[$uniqueKey]]["pasienkirimkeunitlain_id"] = $v["pasienkirimkeunitlain_id"];
                    $result[$v[$uniqueKey]]["instalasi_penunjang_id"] = $v["instalasi_penunjang_id"];
                    $result[$v[$uniqueKey]]["instalasi_penunjang_nama"] = $v["instalasi_penunjang_nama"];
                    $result[$v[$uniqueKey]]["is_bayar"] = $v["is_bayar"];
                    $result[$v[$uniqueKey]]["deleted"] = $v["deleted"];
                    $result[$v[$uniqueKey]]["status_implementasi"] = $v["status_implementasi"];
                    $result[$v[$uniqueKey]]["is_pulang"] = $v["is_pulang"];
                    $result[$v[$uniqueKey]]["jenis"][] = $v["jenis"];
                    $result[$v[$uniqueKey]]["instruksi"][] = $v["instruksi"];
                    $result[$v[$uniqueKey]]["tindakaninstruksi_nama"][] = $v["tindakaninstruksi_nama"];
                    $result[$v[$uniqueKey]]["instruksitindakan_id"][] = $v["instruksitindakan_id"];
                    $result[$v[$uniqueKey]]["qty"][] = $v["qty"];
                    $result[$v[$uniqueKey]]["satuankecil_nama"][] = $v["satuankecil_nama"];
                    $result[$v[$uniqueKey]]["is_telah_implementasi"][] = $v["is_telah_implementasi"];
                }
            }else {
                $arr[] = $v;
            }
        }
        $data_merge = array_merge($arr,array_values($result));
        usort($data_merge, function($a, $b){
             return $a['tgl_tindakan'] <= $b['tgl_tindakan'];
         });
        $statusImplementasiFarmasi = Yii::$app->docoPlugin->getExtension('apotek:reseptur') == 'Extensions\reseptur\ResepturBypassApproval' ? DocoConstants::RESEPTUR_SUDAH_DIPROSES : DocoConstants::RESEPTUR_BELUM_DIPROSES;
        return [
            'data' => $data_merge,
            'status_implementasi_farmasi' => $statusImplementasiFarmasi,
            'load_more' => $totalData == $limit ? true : false
        ];
    }

    public function actionIcare() { // untuk hak akses button icare
        return $this->getUrlIcare();
    }

    /**
     * get data to periksa cppt
     */
    public function actionGetBundleDataAsesmenNyeriToPeriksaCppt()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $pasienadmisi_id = $request->get('pasienadmisi_id');

        return [
            'info_cppt' => $this->getDataCpptInstruksiPulang($pendaftaran_id, $pasienadmisi_id),
        ];
    }
}
