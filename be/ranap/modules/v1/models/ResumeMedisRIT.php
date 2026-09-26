<?php

namespace app\modules\v1\models;

use app\modules\v1\models\PermintaanKonsulView;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\components\PelayananHelpers;
use Doco\models\master\CaraKeluar;
use Doco\models\Bedah\VerifikasiBedahR;
use app\modules\v1\models\InfoKunjunganRi;
use app\modules\v1\models\AsesmenMedis;
use app\modules\v1\models\AsesmenAwal;
use app\modules\v1\models\Cppt;
use app\modules\v1\models\Anamnesa;
use app\modules\v1\models\RiwayatInstruksiTindakanView;
use app\modules\v1\models\InfoResepturDetailView;
use Doco\Services\Cache;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "resumemedisri_t".
 *
 * @property int $resumemedisri_id
 * @property int $registrationId
 * @property int $pasienadmisi_id
 * @property string $tgl_masuk
 * @property string $tgl_keluar
 * @property string $diag_masuk
 * @property string $diag_utama
 * @property string $diag_penyerta
 * @property string $a_f_bermakna
 * @property string $prosedur_diag
 * @property string $tatalaksana_obat
 * @property bool $is_rotd
 * @property string $obat_rotd
 * @property int $kondisipulang_id
 * @property string $kondisi_lain
 * @property string $obat_pulang
 * @property int $kontrol_ke
 * @property string $tgl_kontrol
 * @property string $rencana_tindaklanjut
 * @property bool $is_print
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property string $anamnesa
 * @property string $pemeriksaan_fisik
 * @property string $prosedur
 * @property string $konsultasi
 * @property string $obat_rs
 * @property string $lain_lainnya
 */
class ResumeMedisRIT extends \Doco\components\DocoActiveRecord
{
    /**
     * @var String $registrationId
     * @author Tsani Nashrullah (tsani.nashrullah@gmail.com)
     */
    public $registrationId;
    public $pasienadmisiId;
    public $dokter_dpjp_id;

    /**
     * @var Array $encodedFields
     * @author Tsani Nashrullah (tsani.nashrullah@gmail.com)
     */
    public static $encodedFields = ['diag_utama', 'diag_penyerta', 'tindakan', 'order_laboratorium', 'order_radiologi', 'konsul', 'obat_pulang'];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resumemedisri_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['diag_masuk', 'diag_utama', 'diag_penyerta', 'prosedur_diag', 'pendaftaran_id', 'pasienadmisi_id', 'kondisipulang_id', 'kontrol_ke', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'kondisipulang_id', 'kontrol_ke', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_masuk', 'tgl_keluar', 'tgl_kontrol', 'created_date', 'last_modified_date', 'deleted_date', 'keluhan_utama', 'berat_badan', 'nadi', 'tinggi_badan', 'rr', 'td', 'suhu', 'skala', 'alergi_obat', 'obat_diberikan', 'riwayat_penyakit_dahulu', 'metod_asmennyeri', 'tindakan', 'order_laboratorium', 'order_radiologi', 'konsul', 'obat', 'doktor_rawat_bersama', 'obat_dibawa_pulang', 'obat_dibawa_pulang_text', 'indikasi_pasien_dirawat', 'instruksi', 'alergi_makanan', 'alergi_lainnya', 'reaksi_alergi_obat', 'kondisi_pulang', 'diag_awal', 'anamnesa', 'pemeriksaan_fisik', 'prosedur', 'konsultasi', 'obat_rs', 'lain_lainnya', 'catatan_diet'], 'safe'],
            [['a_f_bermakna', 'tatalaksana_obat', 'obat_rotd', 'kondisi_lain', 'rencana_tindaklanjut', 'additional_data'], 'string'],
            [['is_rotd', 'is_print', 'is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'resumemedisri_id' => 'Resumemedisri ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'tgl_masuk' => 'Tgl Masuk',
            'tgl_keluar' => 'Tgl Keluar',
            'diag_masuk' => 'Diag Masuk',
            'diag_utama' => 'Diag Utama',
            'diag_penyerta' => 'Diag Penyerta',
            'a_f_bermakna' => 'A F Bermakna',
            'prosedur_diag' => 'Prosedur Diag',
            'tatalaksana_obat' => 'Tatalaksana Obat',
            'is_rotd' => 'Is Rotd',
            'obat_rotd' => 'Obat Rotd',
            'kondisipulang_id' => 'Kondisipulang ID',
            'kondisi_lain' => 'Kondisi Lain',
            'obat_pulang' => 'Obat Pulang',
            'kontrol_ke' => 'Kontrol Ke',
            'tgl_kontrol' => 'Tgl Kontrol',
            'rencana_tindaklanjut' => 'Rencana Tindaklanjut',
            'is_print' => 'Is Print',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'anamnesa' => 'Anamnesa',
            'pemeriksaan_fisik' => 'Pemeriksaan Fisik',
            'prosedur' => 'Prosedur',
            'konsultasi' => 'Konsultasi',
            'obat_rs' => 'Obat Rumah Sakit',
            'lain_lainnya' => 'Lain - Lain',
            'catatan_diet' => 'Catatan Diet'
        ];
    }

    /**
     * This function will return all of data resume medis
     *
     * @param String $registrationId
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function resumeByRegistrationId($registrationId, $pasienadmisi_id, $actualRecord = false)
    {
        $patientRecord = Pendaftaran::find()
            ->select(['pasien.nama_pasien', 'pendaftaran_t.no_pendaftaran','pasien.tanggal_lahir', 'pendaftaran_t.pasien_id', 'pendaftaran_t.pendaftaran_id', 'pasien.no_rekam_medik' ,'pasien.alamat_pasien', 'pendaftaran_t.umur',
                'pasienadmisi.tgl_admisi as tgl_pendaftaran', 'pasienadmisi.pegawai_id as dokter_dpjp_id', 'pasienadmisi.pasienpulang_id', 'pasienpulang.tglpasienpulang as tgl_pasien_pulang',
                 'pegawai.nama_pegawai as dokter_dpjp','carakeluar.carakeluar_nama', 'carakeluar.carakeluar_id', 'pasien.additional_pasien', 'pasien.tempat_lahir', 'pasien.no_identitas_pasien', 'pendaftaran_t.is_stopakomodasi', 'pasienadmisi.status_ranap as status_periksa'])
            ->innerJoin('pasien_m pasien', 'pendaftaran_t.pasien_id = pasien.pasien_id')
            ->leftJoin('pasienadmisi_t pasienadmisi', 'pasienadmisi.pasienadmisi_id = pendaftaran_t.pasienadmisi_id')
            ->leftJoin('pegawai_m pegawai', 'pasienadmisi.pegawai_id = pegawai.pegawai_id')
            ->leftJoin('pasienpulang_t pasienpulang', 'pasienadmisi.pasienpulang_id = pasienpulang.pasienpulang_id')
            ->leftJoin('carakeluar_m carakeluar', 'carakeluar.carakeluar_id = pasienpulang.carakeluar_id')
            ->andWhere(['pendaftaran_t.pendaftaran_id' => $registrationId])
            ->andWhere(['pendaftaran_t.pasienadmisi_id' => $pasienadmisi_id])
            ->asArray()
            ->one();

        if (!$actualRecord) {
            $resumeRecord = self::find()
                ->select([
                    'tgl_masuk', 'tgl_keluar', 'additional_data', 'keluhan_utama', 'berat_badan', 'diag_awal', 'diag_utama', 'diag_penyerta', 'nadi', 'tinggi_badan', 
                    'rr', 'td', 'suhu', 'skala', 'alergi_obat', 'obat_diberikan', 'riwayat_penyakit_dahulu', 'metod_asmennyeri', 'tindakan', 'order_laboratorium', 
                    'order_radiologi', 'konsul', 'obat', 'doktor_rawat_bersama', 'obat_dibawa_pulang', 'obat_dibawa_pulang_text', 'indikasi_pasien_dirawat', 'instruksi', 
                    'alergi_makanan', 'alergi_lainnya', 'reaksi_alergi_obat', 'kondisi_pulang', 'anamnesa', 'pemeriksaan_fisik', 'prosedur', 'konsultasi', 'obat_rs', 
                    'lain_lainnya', 'catatan_diet', 'diag_masuk', 'resumemedisri_id'
                ])
                ->andWhere(['pasienadmisi_id' => $pasienadmisi_id])
                ->one();
        } else {
            $resumeRecord = [];
        }
        
        $resumeClass = new self();
        $resumeClass->registrationId = $registrationId;
        $resumeClass->pasienadmisiId = $pasienadmisi_id;
        $resumeClass->dokter_dpjp_id = $patientRecord['dokter_dpjp_id'];

        $list_cara_keluar = CaraKeluar::getAllAscending();
        $enable_pulang = (new DocoConstansId)->actionGetAdditional('konfig_edit_form_pelayanan');
        $tindakan = $resumeClass->resumeData('tindakan');
        
        if (!empty($resumeRecord)) {
            return [
                'resume_medis' => $resumeRecord,
                'patient' => $patientRecord,
                'list_cara_keluar' => $list_cara_keluar,
                'enable_pulang' => $enable_pulang,
                'tindakan' => $tindakan,
            ];
        } else {
          $tindakan_bedah = $resumeClass->resumeData('bedah-verifikasi');
          $asmedRecord = $resumeClass->resumeData('asmed');
          $askepRecord = $resumeClass->resumeData('askep');
          $konsul = $resumeClass->resumeData('konsul');
          $all_reseptur = $resumeClass->resumeData('all-obat');
          $latest_reseptur_dpjp = self::getLatestResepDpjp($all_reseptur);
  
          $cppt = [
              'latest_subjective' => ArrayHelper::getValue($resumeClass->resumeData('latest-subjective'), 'subject', NULL),
              'oldest_diagnosa' => ArrayHelper::getValue($resumeClass->resumeData('oldest-diagnosa-utama'), 'a_diag_utama', NULL),
              'latest_diagnosa' => ArrayHelper::getValue($resumeClass->resumeData('latest-diagnosa-utama'), 'a_diag_utama', NULL),
              'latest_objective' => ArrayHelper::getValue($resumeClass->resumeData('latest-objective'), 'object', ''),
              'all_diagnosa_penyerta' => PelayananHelpers::FormatDiagnosaPenyerta($resumeClass->resumeData('penyerta')),
          ];

          return [
              'resume_medis' => $resumeRecord,
              'patient' => $patientRecord,
              'asesmen_keperawatan' => $askepRecord,
              'asesmen_medis' => $asmedRecord,
              'cppt' => $cppt,
              'latest_reseptur_dpjp' => $latest_reseptur_dpjp,
              'tindakan' => $tindakan,
              'tindakan_bedah' => $tindakan_bedah,
              'list_cara_keluar' => $list_cara_keluar,
              'konsul' => $konsul,
              'enable_pulang' => $enable_pulang,
              'all_reseptur' => $all_reseptur,
          ];
        }
    }

    /**
     * This function will return resume source data by type
     *
     * @param String $type
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function resumeData($type)
    {
        $result = [];
        if (!empty($this->registrationId)) {
            switch ($type) {
                case 'asmed':
                    $result = AsesmenMedis::find()
                        ->select([
                            'keluhan_utama',
                            'pendaftaran_id',
                            'berat_badan',
                            'detak_nadi as nadi',
                            'tinggi_badan',
                            'pernapasan as rr',
                            'tekanan_darah as td',
                            'suhu_tubuh as suhu',
                            'skala',
                            'r_alergiobat as alergi_obat',
                            'r_makanan as alergi_makanan',
                            'obat_diberikan',
                            'metod_asmennyeri',
                            'r_penyakitdahulu',
                            'r_penyakitsekarang',
                            'diagnosa_id',
                            'pernapasan',
                            'td_systolic',
                            'td_diastolic',
                        ])
                        ->orderBy(['created_date' => SORT_DESC])
                        ->andWhere([
                            'pendaftaran_id' => $this->registrationId
                        ]);
                        $result = $result->asArray()
                        ->one();
                    
                    $patientDiseaseHistoryArray = !empty($result['r_penyakitdahulu']) ? json_decode($result['r_penyakitdahulu'], true) : [];
                    $patientDiseaseHistory = '';
                    $totalDisease = count($patientDiseaseHistoryArray);
                    foreach ($patientDiseaseHistoryArray as $indexHistory => $history) {
                        if($history['tahun'] != '' || $history['penyakit'] != '' || $history['terapi'] != ''){
                            $patientDiseaseHistory .= $history['tahun'] .' - '. $history['penyakit'] .' - '. $history['terapi'] ."\n";
                        }
                    }
                    $result['riwayat_penyakit_dahulu'] = $patientDiseaseHistory;
                    break;
                case 'cppt':
                    $konfigCpptKosong = ArrayHelper::getValue(Cache::getKonfigSistem(), 'is_hide_cppt_kosong', FALSE);
                    $result = Cppt::find()
                        ->select([
                            'cppt_t.a_diag_utama',
                            'cppt_t.a_diag_penyerta',
                            'cppt_t.pasien_id',
                            'cppt_t.pendaftaran_id',
                        ])
                        ->andWhere([
                            'cppt_t.pendaftaran_id' => $this->registrationId,
                            'pegawai_m.kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_DOKTER,
                            'pegawai_m.pegawai_id' => $this->dokter_dpjp_id
                        ])
                        ->andWhere([
                            'IS NOT', 'cppt_t.a_diag_utama', null
                        ])
                        ->join('join', 'pegawai_m', 'pegawai_m.pegawai_id=cppt_t.pegawai_id')
                        ->orderBy(['cppt_t.tgl_cppt' => SORT_DESC]);
                        
                    if($konfigCpptKosong == TRUE) {
                        $result->andWhere("(subject <> '-'::text OR object <> '-'::text OR planning <> '-'::text OR (a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL OR is_verbal_order = true)");
                    }
                    $result = $result->one();
                    
                    break;
                case 'penyerta':
                    $result = Cppt::find()
                        ->select([
                            'cppt_t.a_diag_penyerta',
                            'cppt_t.pasien_id',
                            'cppt_t.pendaftaran_id',
                        ])
                        ->andWhere([
                            'cppt_t.pendaftaran_id' => $this->registrationId,
                            'pegawai_m.kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_DOKTER,
                            'pegawai_m.pegawai_id' => $this->dokter_dpjp_id
                        ])
                        ->join('join', 'pegawai_m', 'pegawai_m.pegawai_id=cppt_t.pegawai_id')
                        ->andWhere([
                            'IS NOT', 'cppt_t.a_diag_penyerta', null
                        ])
                        ->orderBy(['cppt_t.created_date' => SORT_DESC]);
                        $result=$result->all();
                    break;
                case 'tindakan':
                    $result = RiwayatInstruksiTindakanView::find()
                        ->select([
                            'tindakan',
                            'tindakan_paket_obat',
                            'qty',
                            'tipe'
                        ])
                        ->andWhere(['pendaftaran_id' => $this->registrationId])
                        // ->andWhere(['!=', 'tipe', 'BMHP'])
                        ->andWhere(['status_implementasi' => DocoConstants::IMPLEMENTASI_SUDAH_IMPLEMENTASI])
                        ->andWhere(['tindakan_deleted' => false])
                        ->orderBy(['tipe' => SORT_DESC])
                        ->asArray()
                        ->all();
                    break;
                case 'tindakanpelayanan_t':
                    $result = TindakanPelayanan::find()
                        ->select([
                            'daftartindakan_nama as tindakan_paket_obat',
                            'qty_tindakan as qty',
                            new Expression("'TINDAKAN' as tipe"),
                            'tgl_tindakan',
                            new Expression("'TINDAKAN' as jenis")
                        ])
                        ->leftJoin('daftartindakan_m', 'daftartindakan_m.daftartindakan_id = tindakanpelayanan_t.daftartindakan_id')
                        ->andWhere(['pendaftaran_id' => $this->registrationId])
                        ->andWhere(['pasienmasukpenunjang_id' => null])
                        ->orderBy(['tgl_tindakan' => SORT_ASC,'jenis' => SORT_DESC]);
                        $result=$result->asArray()
                        ->distinct()
                        ->all();
                    break;
                case 'laboratorium':
                    $result = InfoPasienLabDetailView::find()
                        ->select([
                            'pasien_id',
                            'pendaftaran_id',
                            'tgl_tindakan',
                            'jenis',
                            'daftartindakan_nama'
                        ])
                        ->where([
                            'pendaftaran_id' => $this->registrationId
                        ])
                        ->orderBy(['tgl_tindakan' => SORT_ASC])
                        ->asArray()
                        ->all();
                    break;
                case 'radiologi':
                    $result = InfoPasienRadDetailView::find()
                        ->select([
                            'pasien_id',
                            'pendaftaran_id',
                            'tgl_tindakan',
                            'jenis',
                            'daftartindakan_nama'
                        ])
                        ->where([
                            'pendaftaran_id' => $this->registrationId
                        ])
                        ->orderBy(['tgl_tindakan' => SORT_ASC])
                        ->asArray()
                        ->all();
                    break;
                case 'konsul':
                    $result = PermintaanKonsulView::find()
                        ->select([
                            'pendaftaran_id',
                            'dok_konsul as dok_mengkonsul',
                            'ket_konsul as catatan_dokter_konsul',
                            'jawaban_konsul',
                            'waktu_permintaan as tgl_konsulpoli',
                            'waktu_persetujuan as tgl_selesaikonsul',
                            'jenis_konsul',
                        ])
                        ->andWhere([
                            'pendaftaran_id' => $this->registrationId,
                            'status_konsul' => DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU,
                        ])
                        ->andWhere(['not', ['jawaban_konsul' => null]]);
                        $result = $result->asArray()
                        ->all();
                    break;
                case 'obat':
                    $result = InfoResepturDetailView::find()
                        ->select([
                            'racikan_nama',
                            'rke',
                            'obatalkes_nama',
                            'satuan_kecil',
                            'signa_nama',
                            'signa',
                            'qty_reseptur',
                        ])
                        ->where([
                            'pendaftaran_id' => $this->registrationId,
                            // 'is_bayar' => true,
                            'status_reseptur_id' => DocoConstants::RESEPTUR_DISERAHKAN,
                        ])
                        ->orderBy(['racikan_nama' => SORT_ASC])
                        ->asArray()
                        ->all();
                    break;
                case 'obat-bawa-pulang':
                    $result = InfoResepturDetailView::find()
                        ->select([
                            'racikan_nama',
                            'rke',
                            'obatalkes_nama',
                            'satuan_kecil',
                            'signa_nama',
                            'signa',
                            'qty_reseptur',
                            'etiket',
                            'noresep'
                        ])
                        ->where([
                            'pendaftaran_id' => $this->registrationId,
                            // 'is_bayar' => true,
                            // 'status_reseptur_id' => [DocoConstants::RESEPTUR_SUDAH_DIPROSES, DocoConstants::RESEPTUR_BELUM_DIPROSES, DocoConstants::RESEPTUR_DISERAHKAN],
                        ])
                        ->orderBy([
                            'noresep' => SORT_DESC,
                            'racikan_nama' => SORT_ASC,
                            'rke' => SORT_ASC
                        ])
                        ->asArray()
                        ->all();
                    break;
                case 'diagnosaAwal':
                    $result = AsesmenAwal::find()
                        ->select([
                            'diagnosa_masuk',
                            'pendaftaran_id',
                            'r_alergi',
                            'nama_alergi',
                            'additional_data'
                        ])
                        ->where(['pendaftaran_id' => $this->registrationId])
                        ->one();
                    break;
                case 'doctorInpatient':
                    $doctorInpatientConsule = PermintaanKonsulView::find()
                        ->select(['dok_konsul as dokter'])
                        ->andWhere([
                            'pendaftaran_id' => $this->registrationId
                        ])
                        ->andWhere([
                            'jenis_konsul' => DocoConstants::JNS_KNSL_RB,
                            'status_konsul' => DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU
                        ])
                        ->asArray()
                        ->one();
                    if (!empty($doctorInpatientConsule)) {
                        $result = $doctorInpatientConsule['dokter'];
                    } else {
                        $result = '-';
                    }
                    break;
                case 'anamnesa':
                    $result = Anamnesa::find()
                        ->select([
                            'keluhan_utama',
                            'keluhan_tambahan'
                        ])
                        ->andWhere([
                            'pendaftaran_id' => $this->registrationId
                        ])
                        ->asArray()
                        ->one();
                    break;
                case 'diet':
                    $catatanDiet = PermintaanMakan::find()
                        ->select(['catatan_diet'])
                        ->where(['pendaftaran_id' => $this->registrationId])
                        ->orderBy(['tgl_permintaanmakan' => SORT_DESC])
                        ->asArray()
                        ->one();
                    $result = !empty($catatanDiet) ? $catatanDiet['catatan_diet'] : '-';
                    break;
                case 'askep':
                    $result = AsesmenAwal::find()
                        ->select([
                            'r_alergi',
                            'additional_data'
                        ])
                        ->where(['pasienadmisi_id' => $this->pasienadmisiId]);
                      $result = $result->one();
                    break;
                case 'oldest-diagnosa-utama':
                    $result = Cppt::find()
                        ->select([
                            'cppt_t.a_diag_utama'
                        ])
                        ->andWhere([
                            'cppt_t.pendaftaran_id' => $this->registrationId,
                            'cppt_t.pasienadmisi_id' => $this->pasienadmisiId,
                            'cppt_t.pegawai_id' => $this->dokter_dpjp_id
                        ])
                        ->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)")

                        ->orderBy(['cppt_t.created_date' => SORT_ASC]);
                        $result = $result->asArray()
                        ->one();
                  break;
                case 'latest-diagnosa-utama':
                    $result = Cppt::find()
                        ->select([
                            'cppt_t.a_diag_utama'
                        ])
                        ->andWhere([
                            'cppt_t.pendaftaran_id' => $this->registrationId,
                            'cppt_t.pasienadmisi_id' => $this->pasienadmisiId,
                            'cppt_t.pegawai_id' => $this->dokter_dpjp_id
                        ])
                        ->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)")
                        ->orderBy(['cppt_t.created_date' => SORT_DESC]);
                        $result = $result->asArray()
                        ->one();
                  break;
                case 'latest-subjective':
                    $result = Cppt::find()
                        ->select([
                            'cppt_t.subject'
                        ])
                        ->andWhere([
                            'cppt_t.pendaftaran_id' => $this->registrationId,
                            'cppt_t.pasienadmisi_id' => $this->pasienadmisiId,
                            'cppt_t.pegawai_id' => $this->dokter_dpjp_id
                        ])
                        ->andWhere(['not', ['cppt_t.subject' => null]])
                        ->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)")
                        ->orderBy(['cppt_t.created_date' => SORT_DESC]);
                        $result = $result->asArray()
                        ->one();
                  break;
                  
                case 'latest-objective':
                    $result = Cppt::find()
                        ->select([
                            'cppt_t.object'
                        ])
                        ->andWhere([
                            'cppt_t.pendaftaran_id' => $this->registrationId,
                            'cppt_t.pasienadmisi_id' => $this->pasienadmisiId,
                            'cppt_t.pegawai_id' => $this->dokter_dpjp_id
                        ])
                        ->andWhere(['not', ['cppt_t.object' => null]])
                        ->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)")
                        ->orderBy(['cppt_t.created_date' => SORT_DESC]);
                        $result = $result->asArray()
                        ->one();
                  break;
                  
                case 'all-obat':
                    $result = Yii::$app->db->createCommand('
                            SELECT
                                nama_racikan as racikan_nama, rke, om.obatalkes_nama, sm.satuanunit_nama as satuan_kecil, 
                                sm2.signa_nama, signa, COALESCE(qty_medis, qty_reseptur) as qty_transaksi, rt.noresep, rt.reseptur_id
                            FROM reseptur_t rt
                            LEFT JOIN resepturdetail_t rt2 on rt2.reseptur_id = rt.reseptur_id AND rt2.is_deleted IS FALSE
                            LEFT JOIN obatalkes_m om ON om.obatalkes_id = rt2.obatalkes_id
                            LEFT JOIN satuanunit_m sm ON sm.satuanunit_id = rt2.satuankecil_id
                            LEFT JOIN signaobat_m sm2 ON sm2.signa_id = rt2.signa_id
                            WHERE
                                ((rt.pendaftaran_id = :pendaftaran_id) AND (rt.pasienadmisi_id = :pasienadmisi_id))
                                AND (rt.pegawai_id = :pegawai_id)
                                AND (
                                    rt.is_deleted = false 
                                    AND rt.deleted_date IS NULL
                                  )
                                AND rt.status_reseptur <> :batal_reseptur
                            ORDER BY
                                rt.noresep DESC,
                                racikan_nama,
                                rke,
                                rt.reseptur_id DESC;
                        ')
                        ->bindValue(':pendaftaran_id', $this->registrationId)
                        ->bindValue(':pasienadmisi_id', $this->pasienadmisiId)
                        ->bindValue(':pegawai_id', $this->dokter_dpjp_id)
                        ->bindValue(':batal_reseptur', DocoConstants::VAR_B_R);
                        
                      $result = $result->queryAll();
                    break;
                    
                case 'bedah-verifikasi':
                    $result = VerifikasiBedahR::find()
                        ->select([
                            'verifikasibedah_r.id',
                            'verifikasibedah_r.daftartindakan_id',
                            'verifikasibedah_r.daftartindakan_nama',
                        ])
                        ->leftJoin('pasienmasukpenunjang_t pt', 'pt.pasienmasukpenunjang_id = verifikasibedah_r.pasienmasukpenunjang_id')
                        ->leftJoin('ruangan_m rm', 'rm.ruangan_id = pt.ruangan_id')
                        ->innerJoin('inpostoperasi_t it', 'it.pasienmasukpenunjang_id = verifikasibedah_r.pasienmasukpenunjang_id')
                        ->innerJoin('inpostoperasidetail_t it2', 'it2.inpostoperasi_id = it.inpostoperasi_id and it2.is_verifikasi is true')
                        ->where([
                            'pt.pendaftaran_id' => $this->registrationId,
                            'pt.pasienadmisi_id' => $this->pasienadmisiId,
                            'rm.instalasi_id' => DocoConstants::INST_ID_BEDAH,
                        ])
                        ->orderBy(['verifikasibedah_r.id' => SORT_DESC])
                        ->asArray()
                        ->all();
                    break;
                        
                default:
                    break;
            }
        }
        return $result;
    }

    /**
     * This function will update data resume medis by it type
     *
     * @param String $registrationId
     * @param String $type
     * @return Boolean
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function updateResume($registrationId, $type)
    {
        $recordResumeMedis = self::find()
            ->andWhere(['pendaftaran_id' => $registrationId])
            ->one();
        if (empty($recordResumeMedis)) {
            $recordResumeMedis = new self();
            $recordResumeMedis->pendaftaran_id = $registrationId;
        }
        if (!empty($recordResumeMedis)) {
            $classResume = new self();
            $classResume->registrationId = $registrationId;
            switch ($type) {
                case 'diagnosaAwal':
                    $recordAskep = $classResume->resumeData('diagnosaAwal');
                    
                    //dimatikan karena merubah data diag awal setelah asmed
                    // $recordResumeMedis->diag_awal = isset($recordAskep['diagnosa_masuk']) ? $recordAskep['diagnosa_masuk'] : null;
                    break;
                case 'asmed':
                    $record = $classResume->resumeData('asmed');
                    $recordResumeMedis->keluhan_utama = $record['keluhan_utama'];
                    $recordResumeMedis->pendaftaran_id = $record['pendaftaran_id'];
                    $recordResumeMedis->berat_badan = $record['berat_badan'];
                    $recordResumeMedis->nadi = $record['nadi'];
                    $recordResumeMedis->tinggi_badan = $record['tinggi_badan'];
                    $recordResumeMedis->rr = $record['rr'];
                    $recordResumeMedis->td = $record['td'];
                    $recordResumeMedis->suhu = $record['suhu'];
                    $recordResumeMedis->skala = $record['skala'];
                    $recordResumeMedis->alergi_obat = $record['alergi_obat'];
                    $recordResumeMedis->alergi_makanan = $record['alergi_makanan'];
                    $recordResumeMedis->obat_diberikan = $record['obat_diberikan'];
                    $recordResumeMedis->metod_asmennyeri = $record['metod_asmennyeri'];
                    $recordResumeMedis->riwayat_penyakit_dahulu = $record['riwayat_penyakit_dahulu'];
                    $recordResumeMedis->riwayat_penyakit_dahulu = $record['riwayat_penyakit_dahulu'];
                    $recordResumeMedis->diag_masuk = json_decode($record['diagnosa_id'], true);
                    $recordResumeMedis->diag_awal = json_decode($record['diagnosa_id'], true);

                    $additional_data = json_decode($recordResumeMedis->additional_data, true);
                    $additional_data['frekuensi_nafas'] = $record['pernapasan'];
                    $recordResumeMedis->additional_data = json_encode($additional_data);
                    break;
                case 'cppt':
                    // update diag masuk and diag penyerta
                    $recordCppt = $classResume->resumeData('cppt');
                    $recordPenyerta = $classResume->resumeData('penyerta');
                    $recordResumeMedis->diag_utama = isset($recordCppt['a_diag_utama']) ? $recordCppt['a_diag_utama'] : null;
                    $arrayDiagnose = [];
                    foreach ($recordPenyerta as $diagnose) {
                        $arrayDiagnose = array_merge( $arrayDiagnose, $diagnose['a_diag_penyerta'] );
                    }
                    $recordResumeMedis->diag_penyerta = $arrayDiagnose;
                    break;
                case 'tindakan':
                    $record = $classResume->resumeData('tindakan');
                    $recordResumeMedis->tindakan = $record;
                    break;
                case 'laboratorium':
                    break;
                    $record = $classResume->resumeData('laboratorium');
                    $recordResumeMedis->laboratorium = $record;
                case 'radiologi':
                    $record = $classResume->resumeData('radiologi');
                    $recordResumeMedis->radiologi = $record;
                    break;
                case 'konsul':
                    $record = $classResume->resumeData('konsul');
                    $recordResumeMedis->konsul = $record;
                    break;
                case 'obat':
                    $record = $classResume->resumeData('obat');
                    $recordResumeMedis->obat = $record;
                    break;
                case 'diet':
                    $record = $classResume->resumeData('diet');
                    $recordResumeMedis->catatan_diet = $record;
                    break;
            }
            $result = $recordResumeMedis->save();
            if (!$result) {
                Yii::error([
                    "ErrorResumeMedis" => $type,
                    "Bucket" => $recordResumeMedis->errors
                ]);
            }
            return $result;
        } else {
            return false;
        }
    }

    private function recursiveArray( $arr )
    {
        $isArray = false;
        foreach ($arr as $v) {
            if (is_array($v)) $isArray = true;
        }

        if ( $isArray ) {
            return $arr[0];
        }
        return $arr;
    }

    private function getLastCppt($id)
    {
        return Yii::$app->db->createCommand("
            SELECT * FROM soaprs_v WHERE pendaftaran_id = {$id} ORDER BY tgl_soaprj DESC LIMIT 1
        ")->queryOne();
    }

    private function getOrderPenunjang($id)
    {
        return Yii::$app->db->createCommand("
            SELECT pemeriksaan_nama FROM orderpenunjang_v WHERE pendaftaran_id = {$id}
        ")->queryAll();
    }

    private function getResep($id)
    {
        $statusDiserahkan = DocoConstants::RESEPTUR_DISERAHKAN;
        return Yii::$app->db->createCommand("
            SELECT obatalkespasien_id,obatalkes_nama,qty_transaksi,satuan_kecil,signa_nama FROM informasiresepdetail_v WHERE pendaftaran_id = {$id} 
            AND obatalkespasien_id IS NOT NULL AND penjualanresep_id IS NOT NULL AND status_reseptur_id = {$statusDiserahkan}
        ")->queryAll();
    }

    private function getSuggestion($asmed = null,$askep = null)
    {
        $diag_awal = null;

        $diag_awal = ArrayHelper::getValue($asmed,'diagnosa_id');
        if(!is_null($diag_awal)){
            $diag_awal = json_decode($diag_awal,true);
        }

        return [
            'diag_awal' => $diag_awal
        ];
    }
    
    private function getLatestResepDpjp($all_reseptur)
    {
        return ArrayHelper::getValue(array_values(ArrayHelper::index($all_reseptur, null, 'reseptur_id')), '0', []);
    }
    
}
