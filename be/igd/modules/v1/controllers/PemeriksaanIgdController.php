<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-10 17:34:40
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

use app\modules\v1\models\Cppt;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\InfoPasienRdV;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PindahKamar;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\InfoTagihanDetailView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\HasilPemeriksaanLab;
use app\modules\v1\models\RiwayatSoapTerra;
use app\modules\v1\models\RiwayatResepTerra;
use app\modules\v1\models\PermintaanMakan;
use Doco\Traits\GiziTrait;
use Doco\Traits\ResumeMedisTrait;
use Doco\Traits\PasienTrait;
use Doco\models\ResumeMedisRi;
use Doco\Services\Cache;
use app\modules\v1\models\AsesmenKeperawatanRD;
use Doco\models\bpjs\Bpjs;
use Doco\models\Pasien;
use Doco\Traits\TerraMedikTrait;
use Doco\Traits\SuratKeteranganTrait;
use Doco\Traits\ICareTrait;
use Doco\Traits\ObservasiEwsTrait;
use Doco\Traits\SbarTrait;

class PemeriksaanIgdController extends DocoActiveController
{
    use GiziTrait;
    use ResumeMedisTrait;
    use TerraMedikTrait;
    use PasienTrait;
    use SuratKeteranganTrait;
    use ICareTrait;
    use ObservasiEwsTrait;
    use SbarTrait;

    public $modelClass = 'app\modules\v1\models\InfoPasienRdV';

    protected $askepModel;

    public function init()
    {
        parent::init();
        $this->diagnosaView = new DiagnosaView;
        $this->hasilPemeriksaanLab = new HasilPemeriksaanLab;
        $this->askepModel = new AsesmenKeperawatanRD;
    }

    /**
     *
     * @see Fungsi get pasien
     * @return array, activeQueryRecords data pasien
     *
     */
    public function actionGetApi()
    {

        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('id');
            $data_pasien = empty($this->getDataPasien($pendaftaran_id)) ?$this->getDataPasienRs($pendaftaran_id):$this->getDataPasien($pendaftaran_id);
            $data_pegawai = $this->getDataPegawai($request->get('pegawai_id'));
            $data_riwayat_pasien = [];
            if(isset($data_pasien['pasien_id'])){
                $data_riwayat_pasien = $this->getRiwayatPasienTerbaru($data_pasien['pasien_id']); //@use PasienTrait Function
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
                        'daftartindakan_m.kelompoktindakan_id' => $constCathlab
                    ])->count();
            }
            $data_pasien['hasCathlab'] = $countCathlabData ? true : false;
            $data_pasien['icare_identifier'] = null;

            $showTtvTab = false;
            $showEwsTab = false;
            $showSbarTab = false;
            $getKonfigSystem = Cache::getKonfigSistem();
            $konfigSystem = ArrayHelper::getValue($getKonfigSystem, 'konfig_monitoring_ttv', null);
            $konfigObservation = ArrayHelper::getValue($getKonfigSystem, 'konfig_observasi_ews', null);
            $konfigSbar = ArrayHelper::getValue($getKonfigSystem, 'konfig_sbar', null);

            if(!is_null($konfigSystem) && !is_array($konfigSystem)) {
                $konfigSystem = json_decode($konfigSystem, true);
            }

            if (isset($konfigSystem['RD']) && $konfigSystem['RD'] == true) {
                $showTtvTab = true;
            }

            if (!is_null($konfigObservation) && !is_array($konfigObservation)) {
                $konfigObservation = json_decode($konfigObservation, true);
            }

            if (isset($konfigObservation['RD']) && $konfigObservation['RD'] == true) {
                $showEwsTab = true;
            }

            if (!is_null($konfigSbar) && !is_array($konfigSbar)) {
                $konfigSbar = json_decode($konfigSbar, true);
            }

            if (isset($konfigSbar['RD']) && $konfigSbar['RD'] == true) {
                $showSbarTab = true;
            }

            $data_pasien['showTtvTab'] = $showTtvTab ? true : false;
            $data_pasien['showEwsTab'] = $showEwsTab ? true : false;
            $data_pasien['showSbarTab'] = $showSbarTab ? true : false;

            // get no kartu BPJS untuk kebutuhan iCare
            $lastDataBpjs = Bpjs::find()->select(['bpjs_id', 'pendaftaran_id', 'nokartuasuransi'])
                ->where(['pendaftaran_id' => $data_pasien['pendaftaran_id']])
                ->orderBy(['bpjs_id' => SORT_DESC])
                ->asArray()->one();
            if(isset($lastDataBpjs['nokartuasuransi']) && !empty($lastDataBpjs['nokartuasuransi'])) {
                $data_pasien['icare_identifier'] = $lastDataBpjs['nokartuasuransi'];
            } else {
                $pasien = Pasien::find()->select(['pasien_id', 'no_rekam_medik', 'no_identitas_pasien', 'nopeserta_bpjs'])
                    ->where(['no_rekam_medik' => $data_pasien['no_rekam_medik']])
                    ->asArray()->one();
                $data_pasien['icare_identifier'] = isset($pasien['nopeserta_bpjs']) ? $pasien['nopeserta_bpjs'] : $pasien['no_identitas_pasien'];
            }
            
            if ($request->get('cppt') && !empty($pendaftaran_id)) {
                $cppt = Cppt::find()
                    ->select([
                        'a_diag_utama'
                    ])
                    ->where([
                        'pendaftaran_id' => $pendaftaran_id
                    ])
                    ->orderBy([
                        'tgl_cppt' => SORT_DESC
                    ])
                    ->one();

                return [
                    'data_pasien' => $data_pasien,
                    'data_pegawai' => $data_pegawai,
                    'data_riwayat_pasien' => $data_riwayat_pasien,
                    'cppt' => $cppt,
                ];
            } else {
                return [
                    'data_pasien' => $data_pasien,
                    'data_pegawai' => $data_pegawai,
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

    /**
     *
     * @see Fungsi get data pasien rawat darurat
     * @var params integer id = primary key pendaftaran_id
     * @return array, activeQueryRecords
     *
     */
    private function getDataPasien($id = null)
    {
        try {
            $model = InfoPasienRdV::find()
                ->select([
                    'infopasienrd_v.*',
                    'infopasienrd_v.status_periksa as status_periksa_nama',
                    'infopasienrd_v.dokter_jaga as nama_pegawai',
                ]);

            if ($id) {
                $model->where([
                    'pendaftaran_id' => $id
                ]);
            }
            return $model->limit(1)->asArray()->one();
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

    private function getDataPasienRs($id = null)
    {
        try {
            $model = (new \yii\db\Query())
            ->from('infopasienrs_v');            

            if ($id) {
                $model->where([
                    'pendaftaran_id' => $id
                ]);
            }
            return $model->limit(1)->one();
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


    private function getDataPegawai($id = null)
    {
        try {
            $model = PegawaiView::find();

            if ($id) {
                $model->where(['pegawai_id' => $id]);
            }

            return $model->one();
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

    /**
     * @controller actionCetakRincianTagihan
     * @attribute #tgl_pendaftaran# => tanggal daftar
     * @attribute #no_rekam_medik# => no rekam medik
     * @attribute #no_pendaftaran# => no pendaftaran
     * @attribute #nama_pasien# => nama pasien
     * @attribute #nama_dok_rj_rd# => dokter
     * @attribute #rua_nama# => ruangan
     * @attribute #kelaspelayanan_nama# => kelas pelayanan
     * @attribute #penjamin_nama# => penjamin
     * @attribute #carabayar_nama# => cara bayar
     * @attribute #status_bayar# => status bayar
     * @attribute #table# => table detail
     **/
    public function actionCetakRincianTagihan()
    {
        $request = Yii::$app->request;
        try {
            // $id = DocoHelpers::decrypt($request->get('id', null));
            $id = $request->get('id', null);
            $header = InfoDataPendaftaran::find()
                ->select([
                    'infodatapendaftaran_v.tgl_pendaftaran',
                    'infodatapendaftaran_v.pendaftaran_id',
                    'infodatapendaftaran_v.no_rekam_medik',
                    'infodatapendaftaran_v.no_pendaftaran',
                    'infodatapendaftaran_v.nama_pasien',
                    'infodatapendaftaran_v.nama_dok_rj_rd',
                    'infodatapendaftaran_v.rua_nama',
                    'infodatapendaftaran_v.kelaspelayanan_nama',
                    'infodatapendaftaran_v.penjamin_nama',
                    'infodatapendaftaran_v.carabayar_nama',
                    'lookup_m.lookup_name AS status_bayar'
                ])
                ->join('JOIN', 'lookup_m', 'lookup_m.lookup_id = infodatapendaftaran_v.status_bayar')
                ->where(['infodatapendaftaran_v.pendaftaran_id' => $id])->asArray()->one();

            $detail = InfoTagihanDetailView::find()
                ->where(['pendaftaran_id' => $id])
                ->orderBy(['tindakan_obat_nama' => SORT_ASC])
                ->all();

            $print = new DocoPrint();
            $print->attributes = [
                '#tgl_pendaftaran#' => date('d/M/Y', strtotime($header['tgl_pendaftaran'])),
                '#no_rekam_medik#' => $header['no_rekam_medik'],
                '#no_pendaftaran#' => $header['no_pendaftaran'],
                '#nama_pasien#' => DocoHelpers::namaPasien($header['nama_pasien']),
                '#nama_dok_rj_rd#' => DocoHelpers::namaPasien($header['nama_dok_rj_rd']),
                '#rua_nama#' => $header['rua_nama'],
                '#kelaspelayanan_nama#' => $header['kelaspelayanan_nama'],
                '#penjamin_nama#' => $header['penjamin_nama'],
                '#carabayar_nama#' => $header['carabayar_nama'],
                '#status_bayar#' => $header['status_bayar'],
                '#table#' => $this->renderPartial('rincian_igd', [
                    'data' => $detail,
                ]),
            ];

            $print->Output();
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
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
     * @controller actionCetakResumeMedisIgd
     * @attribute #no_pendaftaran# => Menamplkan nomor pendaftaran
     * @attribute #nama_pasien# => Menamplkan nama pasien
     * @attribute #no_rekam_medik# => Menamplkan nomor rekam medis
     * @attribute #alamat_pasien# => Menamplkan alamat
     * @attribute #ruangan_nama# => Menamplkan Nama ruangan dan lantai
     * @attribute #umur# => Menamplkan umur
     * @attribute #tgl_pendaftaran# => Menamplkan tanggal masuk / tanggal pendaftaran
     * @attribute #umur# => Menamplkan umur
     * @attribute #jenis_kelamin# => Menamplkan jenis kelamin
     * @attribute #tglpasienpulang# => Menamplkan tanggal keluar
     * @attribute #agama# => Menamplkan Agama
     * @attribute #datatable# => Untuk menampilkan data table
     * @attribute #rujukan# => Menampilkan rujukan
     * @attribute #diagnosa_awal# => Menampilkan diagnosa awal
     * @attribute #riwayat_penyakit_dahulu# => Menampilkan riwayat penyakit
     * @attribute #diagnosa_utama# => Menampilkan diagnosa utama
     * @attribute #diagnosa_sekunder# => Menampilkan diagnosa sekunder
     * @attribute #reaksi_alergi_obat# => Menampilkan reaksi alergi obat
     * @attribute #carapulang_pasien# => Menampilkan cara pulang
     * @attribute #laboratorium# => Menampilkan tabel list pemeriksaan laboratorium
     * @attribute #radiologi# => Menampilkan tabel list pemeriksaan radiologi
     * @attribute #anamnesa# => Menampilkan seluruh data anamnesa
     * @attribute #pemeriksaan_fisik# => Menampilkan seluruh data pemeriksaan fisik
     * @attribute #tindakan# => Menampilkan tabel list tindakan
     * @attribute #konsul_poli# => Menampilkan tabel list konsul poli
     * @attribute #reseptur# => Menampilkan tabel list reseptur
     * @attribute #no_identitas# => Menampilkan No Identitas
     * @attribute #tempat_lahir# => Menampilkan Tempat Lahir
     */
    public function actionCetakResumeMedisIgd()
    {
        $registrationId = Yii::$app->request->get('pendaftaran_id');
        $resumeMedisRecord = ResumeMedisRi::resumeByRegistrationId($registrationId);
        $pfisik = $anamnesa = $riwayatPenyakit = $indikasiPasienDirawat = $dll = $tindakanRs = $konsul = $alergiObat = $obatRs = $obatHome = $kondisiPulang = $instruksi = '';
        // get signature path by dpjp_id
        if (!empty($resumeMedisRecord['patientRecord'])) {
            $signaturePath = Pegawai::signatureEmployee($resumeMedisRecord['patientRecord']['dokter_dpjp_id']);
            // DATE FORMATTING
            //$resumeMedisRecord['patientRecord']['tgl_pendaftaran'] = date('d F Y', strtotime($resumeMedisRecord['patientRecord']['tgl_pendaftaran']));
            //$resumeMedisRecord['patientRecord']['tglpasienpulang'] = !empty($resumeMedisRecord['patientRecord']['tglpasienpulang']) ? date('d F Y', strtotime($resumeMedisRecord['patientRecord']['tglpasienpulang'])) : '';
        } else {
            $signaturePath = '';
        }
        $askepRecord = $resumeMedisRecord['askepRecord'];
        $dll = isset($resumeMedisRecord['lain_lainnya']) && !empty($resumeMedisRecord['lain_lainnya']) ? $resumeMedisRecord['lain_lainnya'] : '-';
        $suhu = isset($askepRecord['suhu']) && !empty($askepRecord['suhu']) ? $askepRecord['suhu'] . ' &deg;C' : '-';
        $nadi = isset($askepRecord['nadi']) && !empty($askepRecord['nadi']) ? $askepRecord['nadi'] . ' x/Menit' : '-';
        $td = isset($askepRecord['td']) && !empty($askepRecord['td']) ? $askepRecord['td'] . ' mmHg' : '-';
        $frekuensi_nafas = isset($askepRecord['rr']) && !empty($askepRecord['rr']) ? $askepRecord['rr'] : '-';
        $tglMasuk = !empty($resumeMedisRecord['tgl_masuk']) ? $this->helper->convertDate($resumeMedisRecord['tgl_masuk'], 'd m Y') : '-';
        $tglKeluar = !empty($resumeMedisRecord['tgl_keluar']) ? $this->helper->convertDate($resumeMedisRecord['tgl_keluar'], 'd m Y') : '-';
        $keluhanUtama = isset($resumeMedisRecord['askepRecord']['keluhan_utama']) && !empty($resumeMedisRecord['askepRecord']['keluhan_utama']) ? $this->generateTextNewLine($resumeMedisRecord['askepRecord']['keluhan_utama'], "\n") : '-';
        $pfisik = isset($resumeMedisRecord['pemeriksaan_fisik']) && !empty($resumeMedisRecord['pemeriksaan_fisik']) ? $resumeMedisRecord['pemeriksaan_fisik'] : '-';
        $kondisiPulang = isset($resumeMedisRecord['patientRecord']['carakeluar']) && !empty($resumeMedisRecord['patientRecord']['carakeluar']) ? $this->generateTextNewLine($resumeMedisRecord['patientRecord']['carakeluar']) : '-';
        $instruksi = isset($resumeMedisRecord['instruction']) && !empty($resumeMedisRecord['instruction']) ? $this->generateTextNewLine($resumeMedisRecord['instruction'], "\n") : '-';
        $riwayatPenyakit = isset($resumeMedisRecord['patientDiseaseHistory']) && !empty($resumeMedisRecord['patientDiseaseHistory']) ? $resumeMedisRecord['patientDiseaseHistory'] : '-';
        $tindakanRs = isset($resumeMedisRecord['prosedur']) && !empty($resumeMedisRecord['prosedur']) ? $this->generateTextNewLine($resumeMedisRecord['prosedur'], "\n") : '';
        $obatHome = isset($resumeMedisRecord['obat_dibawa_pulang']) && !empty($resumeMedisRecord['obat_dibawa_pulang']) ?
            (is_array($resumeMedisRecord['obat_dibawa_pulang']) ? $resumeMedisRecord['obat_dibawa_pulang'] : $this->generateTextNewLine($resumeMedisRecord['obat_dibawa_pulang'], "\n")) : '';
        if (empty($obatHome)) {
            $obatHome = $resumeMedisRecord['medicineOnReturn'];
        }
        $actRecord = isset($resumeMedisRecord['actRecord']) ? $resumeMedisRecord['actRecord'] : [];
        if (empty($tindakanRs)) {
            $tindakanRs = '';
            for ($i = 0; $i < count($actRecord); $i++) {
                if ($actRecord[$i]['tipe'] == 'TINDAKAN') {
                    $tindakanRs .= '<b>Tindakan</b> ' . $actRecord[$i]['tindakan_paket_obat'] . ' Jumlah ' . $actRecord[$i]['qty']  . "<br>";
                }
                if ($actRecord[$i]['tipe'] == 'BMHP') {
                    $tindakanRs .= '&emsp;- <b>Obat</b> ' . $actRecord[$i]['tindakan_paket_obat'] . ' Jumlah ' . $actRecord[$i]['qty']  . "<br>";
                }
            }
            $tindakanRs = !empty($tindakanRs) ? $tindakanRs : '-';
        }
        $tgl_pendaftaran = isset($resumeMedisRecord['patientRecord']['tgl_pendaftaran']) ? $this->helper->convertDate($resumeMedisRecord['patientRecord']['tgl_pendaftaran'], 'd m Y H:i:s') : "-";
        $tglpasienpulang = isset($resumeMedisRecord['patientRecord']['tglpasienpulang']) ? $this->helper->convertDate($resumeMedisRecord['patientRecord']['tglpasienpulang'], 'd m Y H:i:s') : "-";
        $diet = isset($resumeMedisRecord['catatan_diet']) && !empty($resumeMedisRecord['catatan_diet']) ? $this->generateTextNewLine($resumeMedisRecord['catatan_diet'], "\n") : '-';

        $print = new DocoPrint('resume-medis-igd');
        $printAttributes = [];
        // $tanggal_lahir = isset($resumeMedisRecord['patientRecord']['tanggal_lahir']) ?  date('d M Y', strtotime($resumeMedisRecord['patientRecord']['tanggal_lahir'])) : null;

        foreach ($resumeMedisRecord['patientRecord'] as $keyPatient => $value) {
            if ($keyPatient == 'tanggal_lahir') {
                $value = !empty($value) ? date('d M Y', strtotime($value)) : '';
            }
            $printAttributes['#' . $keyPatient . '#'] = $value;
        }
        $ruangan_nama = isset($resumeMedisRecord['patientRecord']['kamar']) ? $resumeMedisRecord['patientRecord']['kamar'] : null;
        $pegawailogin_id   = Yii::$app->jwt->user->pegawai_id;
        $pegawailogin      = Pegawai::findOne($pegawailogin_id);
        $pegawailogin_nama = $pegawailogin->nama_pegawai;
        $tanggal_cetak     = $this->helper->convertDate(date('d M Y'), 'd m Y');
        $waktu_cetak       = date('d-m-Y H:i:s');

        // =========== lab =============
        $lab = $resumeMedisRecord['labOrder']['text'];
        $lab = str_replace("\n", "", $lab);
        $lab = str_replace("<p>&nbsp;</p>", "", $lab);
        $lab = str_replace("<p>", "", $lab);
        $lab = str_replace("</p>", "<br>", $lab);
        $lab = str_replace("<p style=\"margin-left:24px\">", "", $lab);
        $labExplode = explode("<br>", $lab);
        preg_match_all('#<strong>(.+?)</strong>#', $lab, $parts);
        $labIdx = array_unique($parts[0]);
        $labDataArray = [];
        $statusInsert = false;
        if (!empty($labIdx)) {
            foreach ($labIdx as $k => $v) {
                foreach ($labExplode as $key => $value) {
                    if (in_array($value, $labIdx)) {
                        $statusInsert = ($v == $value) ? true : false;
                    } else {
                        if ($statusInsert && !empty($value)) {
                            $labDataArray[$v][] = $value;
                        }
                    }
                }
            }

            $lab = "";
            foreach ($labDataArray as $key => $value) {
                $lab .= '<span style="font-size:12px;font-family:Arial,Helvetica,sans-serif;">' . $key . "</span><br>";
                foreach ($value as $k => $v) {
                    $lab .= '<span style="font-size:12px;font-family:Arial,Helvetica,sans-serif">' . $v . "</span><br>";
                }
            }
            $lab = str_replace("<strong>", "", $lab);
            $lab = str_replace("</strong>", "", $lab);
        }
        $lab = !empty($lab) ? $lab : '-';

        // =========== rad =============
        $rad = $resumeMedisRecord['radOrder']['text'];
        $rad = str_replace("\n", "", $rad);
        $rad = str_replace(array("<p>", "</p>"), array("<span><div style='height:0.5px; font-size:0.5px'>&nbsp;</div>", "</span>"), $rad);
        $rad = str_replace("<strong>", "", $rad);
        $rad = str_replace("</strong>", "", $rad);
        $rad = !empty($rad) ? $rad : '-';

        // penyesuaian form resume medis baru
        $is_igd = $is_alergi = false;
        $kontak_darurat = $edukasi_rencana = $keadaan_umum = $kesadaran = $tindakanProsedur = $cara_keluar_nama = $alergi =
        $keadaan_darurat = $instruksi_kontrol = $instruksi_tanggal = '-';
        $cara_keluar_nama = '';
        $additionalData = !empty($resumeMedisRecord['additional_data']) ? $resumeMedisRecord['additional_data'] : [];

        if (!empty($additionalData)) {
            $additionalData = json_decode($additionalData, true);
            $date_instruksitanggal = isset($additionalData['instruksi_tanggal']) && !empty($additionalData['instruksi_tanggal']) ? str_replace('/', '-', $additionalData['instruksi_tanggal']) : '-';
            $instruksi_kontrol = isset($additionalData['instruksi_kontrol']) && !empty($additionalData['instruksi_kontrol']) ? $additionalData['instruksi_kontrol'] : '-';
            $instruksi_tanggal = isset($additionalData['instruksi_tanggal']) && !empty($additionalData['instruksi_tanggal']) ? $this->helper->convertDate(date('Y-m-d', strtotime($date_instruksitanggal)), 'd m Y') : '-';
            $is_igd = isset($additionalData['is_igd']) && !empty($additionalData['is_igd']) ? $additionalData['is_igd'] : '';
            if ($is_igd == 1) {
                $is_igd = 'IGD, ';
            }
            $kontak_darurat = isset($additionalData['kontak_darurat']) && !empty($additionalData['kontak_darurat']) ? $additionalData['kontak_darurat'] : '-';
            $edukasi_rencana = isset($additionalData['edukasi_rencana']) && !empty($additionalData['edukasi_rencana']) ? $additionalData['edukasi_rencana'] : '-';
            $keadaan_darurat = $is_igd . ' Telepon : ' . $kontak_darurat;
            $cara_keluar = isset($additionalData['cara_keluar']) && !empty($additionalData['cara_keluar']) ? $additionalData['cara_keluar'] : null;
            $keadaan_umum = isset($additionalData['keadaan_umum']) && !empty($additionalData['keadaan_umum']) ? $additionalData['keadaan_umum'] : '-';
            $kesadaran = isset($additionalData['kesadaran']) && !empty($additionalData['kesadaran']) ? $additionalData['kesadaran'] : '-';
            $is_alergi = isset($additionalData['is_alergi']) && !empty($additionalData['is_alergi']) ? $additionalData['is_alergi'] : '-';
            $nama_alergi = isset($additionalData['nama_alergi']) && !empty($additionalData['nama_alergi']) ? $additionalData['nama_alergi'] : '-';
            $alergi = ($is_alergi == 1) ? 'Ya, ' . $nama_alergi : 'Tidak';
            $tindakanProsedur = isset($additionalData['instruksi_tindakanbmhp']) && !empty($additionalData['instruksi_tindakanbmhp']) ? $this->generateTextNewLine($additionalData['instruksi_tindakanbmhp'], "\n") : '-';
            $optionsCaraKeluar = isset($resumeMedisRecord['list_caraKeluar']) ? $resumeMedisRecord['list_caraKeluar'] : [];

            if (!empty($optionsCaraKeluar)) {
                foreach ($optionsCaraKeluar as $key => $value) {
                    if ($cara_keluar == $value['carakeluar_id']) {
                        $cara_keluar_nama = $value['carakeluar_namalain'];
                    }
                }
            }
        }
        $cara_keluar_nama = empty($cara_keluar_nama) ? $kondisiPulang : $cara_keluar_nama;
        $lokasi_rs = Cache::getProfileRs();
        $no_identitas_pasien = isset($resumeMedisRecord['patientRecord']['no_identitas_pasien']) && !empty($resumeMedisRecord['patientRecord']['no_identitas_pasien']) ? $resumeMedisRecord['patientRecord']['no_identitas_pasien'] : '-';
        $additionalPasien = !empty($resumeMedisRecord['patientRecord']['additional_pasien']) ? $resumeMedisRecord['patientRecord']['additional_pasien'] : [];
        if(!empty($additionalPasien)){
            $additionalPasien = json_decode($additionalPasien, true);
            foreach ($additionalPasien as $value) {
                $no_identitas_pasien = isset($value['no_identitas_pasien']) && !empty($value['no_identitas_pasien']) ? $value['no_identitas_pasien'] : '-';
            }
        }
        $tempat_lahir = isset($resumeMedisRecord['patientRecord']['tempat_lahir']) && !empty($resumeMedisRecord['patientRecord']['tempat_lahir']) ? $resumeMedisRecord['patientRecord']['tempat_lahir'] : '-';

        $diagnosa_masuk = isset($resumeMedisRecord['diagAwal']) && isset($resumeMedisRecord['diagAwal']['text']) ? $resumeMedisRecord['diagAwal']['text'] : '-';
        $diagnosa_utama = isset($resumeMedisRecord['cpptRecord']['text']) ? $resumeMedisRecord['cpptRecord']['text'] : (!empty($resumeMedisRecord['cpptRecord']['a_diag_utama']) ? (is_array($resumeMedisRecord['cpptRecord']['a_diag_utama']) && (isset($resumeMedisRecord['cpptRecord']['a_diag_utama']['text']) && !empty($resumeMedisRecord['cpptRecord']['a_diag_utama']['text'])) ? $resumeMedisRecord['cpptRecord']['a_diag_utama']['text'] : json_decode($resumeMedisRecord['cpptRecord']['a_diag_utama'], true)['text']) : '-');
        $diagnosa_sekunder = !empty(strip_tags($resumeMedisRecord['additionalDiagnose'])) ? $resumeMedisRecord['additionalDiagnose'] : '-';
        $print->attributes = array_merge($printAttributes, [
            '#ruangan_nama#' => $ruangan_nama,
            '#nama_dokter#' => $resumeMedisRecord['patientRecord']['dokter_dpjp'],
            '#hari_skr#' => $this->helper->convertDate(date("Y-m-d H:i:s"), 'w'),
            '#tanggal_skr#' => $this->helper->convertDate(date("Y-m-d H:i:s")),
            '#ttd_dokter#' => $signaturePath,
            '#nama_pegawai#' => $pegawailogin_nama,
            '#timestamps#' => $waktu_cetak,
            '#tgl_pendaftaran#' => $tgl_pendaftaran,
            '#tglpasienpulang#' => $tglpasienpulang,
            '#lokasi#' => ucwords(strtolower($lokasi_rs[0]['kota'])) . ', ' . $tanggal_cetak,
            '#no_identitas#' => $no_identitas_pasien,
            '#tempat_lahir#' => $tempat_lahir,
            '#tanggal_cetak#' => $tanggal_cetak,
            '#tgl_masuk#' => $tgl_pendaftaran,
            '#tgl_keluar#' => $tglpasienpulang,
            '#diagnosa_masuk#' => $diagnosa_masuk,
            '#diagnosa_utama#' => $diagnosa_utama,
            '#diagnosa_penyerta#' => $diagnosa_sekunder,
            '#keluhan_utama#' => $keluhanUtama,
            '#riwayat_penyakit_dahulu#' => $riwayatPenyakit,
            '#pemeriksaan_fisik#' => $pfisik,
            '#lab#' => $lab,
            '#rad#' => $rad,
            '#dll#' => $dll,
            '#tindakan#' => $tindakanRs,
            '#tindakan_prosedur#' => $tindakanProsedur,
            '#diet#' => $diet,
            '#alergi#' => $alergi,
            '#keadaan_umum#' => $keadaan_umum,
            '#kesadaran#' => $kesadaran,
            '#td#' => $td,
            '#suhu#' => $suhu,
            '#nadi#' => $nadi,
            '#nafas#' => $frekuensi_nafas,
            '#cara_keluar#' => $cara_keluar_nama,
            '#instruksi_kontrol#' => $instruksi,
            '#instruksi_tanggal#' => $instruksi_tanggal,
            '#keadaan_darurat#' => $keadaan_darurat,
            '#edukasi_rencana#' => $edukasi_rencana,
            '#obat_dibawa_pulang#' => Yii::$app->controller->renderPartial('resume-medis-igd', [
                'obat_dibawa_pulang' => $obatHome
            ]),

        ]);
        return $print->OutputHtml();
    }

    public function actionIcare() { // untuk hak akses button icare
        return $this->getUrlIcare();
    }
}
