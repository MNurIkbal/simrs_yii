<?php

namespace app\modules\v1\models;

use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\components\PelayananHelpers;
use Doco\models\master\CaraKeluar;
use Doco\models\Bedah\VerifikasiBedahR;
use app\modules\v1\models\PemeriksaanFisik;
use app\modules\v1\models\Anamnesa;
use app\modules\v1\models\InfoResepturDetailView;
use app\modules\v1\models\PasienPulang;
use app\modules\v1\models\PermintaanMakan;
use app\modules\v1\models\RiwayatInstruksiTindakanView;
use app\modules\v1\models\Cppt;
use app\modules\v1\models\SoapRj;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\SoapRjView;
use app\modules\v1\models\Reseptur;
use app\modules\v1\models\Infokonsulpoli;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "resumemedisri_t".
 *
 * @property int $resumemedisri_id
 * @property int $registrationId
 * @property string $tgl_masuk
 * @property string $diag_masuk
 * @property string $diag_utama
 * @property string $diag_penyerta
 * @property string $diag_awal
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
 * @property string $lain_lainnya
 * @property string $lain_lainnya
 */
class ResumeMedisRi extends \Doco\components\DocoActiveRecord
{
    /**
     * @var String $registrationId
     * @author Tsani Nashrullah (tsani.nashrullah@gmail.com)
     */
    public $registrationId;
    public $dokter_dpjp_id;

    /**
     * @var Array $encodedFields
     * @author Tsani Nashrullah (tsani.nashrullah@gmail.com)
     */
    public static $encodedFields = ['diag_awal','diag_utama', 'diag_penyerta', 'tindakan', 'order_laboratorium', 'order_radiologi', 'konsul', 'obat_pulang'];
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
            [['diag_awal','diag_masuk', 'diag_utama', 'diag_penyerta', 'prosedur_diag', 'pendaftaran_id', 'kondisipulang_id', 'kontrol_ke', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['keluhan_utama', 'riwayat_penyakit_dahulu', 'pemeriksaan_fisik', 'order_laboratorium', 'order_radiologi', 'lain_lainnya', 'prosedur', 'catatan_diet'], 'default', 'value' => '-'],
            [['pendaftaran_id', 'kondisipulang_id', 'kontrol_ke', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_masuk', 'tgl_kontrol', 'created_date', 'last_modified_date', 'deleted_date', 'keluhan_utama', 'berat_badan', 'nadi', 'tinggi_badan', 'rr', 'td', 'suhu', 'skala', 'alergi_obat', 'obat_diberikan', 'riwayat_penyakit_dahulu', 'metod_asmennyeri', 'tindakan', 'order_laboratorium', 'order_radiologi', 'konsul', 'obat', 'doktor_rawat_bersama', 'obat_dibawa_pulang', 'obat_dibawa_pulang_text', 'indikasi_pasien_dirawat', 'instruksi', 'alergi_makanan', 'alergi_lainnya', 'reaksi_alergi_obat', 'kondisi_pulang', 'diag_awal', 'anamnesa', 'pemeriksaan_fisik', 'prosedur', 'konsultasi', 'lain_lainnya', 'catatan_diet'], 'safe'],
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
            'tgl_masuk' => 'Tgl Masuk',
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
    public static function resumeByRegistrationId($registrationId, $actualRecord = false)
    {
        $patientRecord = Pendaftaran::find()
            ->select(['pasien.nama_pasien', 'pendaftaran_t.no_pendaftaran','pasien.tanggal_lahir', 'pendaftaran_t.pasien_id', 'pendaftaran_t.pendaftaran_id',
                'pasien.no_rekam_medik' ,'pasien.alamat_pasien', 'pendaftaran_t.umur',
                'pendaftaran_t.tgl_pendaftaran', 'pendaftaran_t.pegawai_id as dokter_dpjp_id', 'pendaftaran_t.pasienpulang_id',
                'pegawai.nama_pegawai as dokter_dpjp', 'pasien.tempat_lahir', 'pasien.no_identitas_pasien', 'pendaftaran_t.status_periksa'])
            ->innerJoin('pasien_m pasien', 'pendaftaran_t.pasien_id = pasien.pasien_id')
            ->leftJoin('pegawai_m pegawai', 'pendaftaran_t.pegawai_id = pegawai.pegawai_id')
            ->leftJoin('pasienpulang_t pasienpulang', 'pendaftaran_t.pasienpulang_id = pasienpulang.pasienpulang_id')
            ->andWhere(['pendaftaran_t.pendaftaran_id' => $registrationId])
            ->asArray()
            ->one();

        if (!$actualRecord) {
            $resumeRecord = self::find()
                ->select(['keluhan_utama','tgl_masuk', 'additional_data',  'berat_badan', 'diag_awal', 'diag_utama', 'diag_penyerta', 'nadi', 'tinggi_badan', 'rr', 'td', 'suhu', 'skala', 'alergi_obat', 'obat_diberikan', 'riwayat_penyakit_dahulu', 'metod_asmennyeri', 'tindakan', 'order_laboratorium', 'order_radiologi', 'konsul', 'obat', 'doktor_rawat_bersama', 'obat_dibawa_pulang', 'obat_dibawa_pulang_text', 'indikasi_pasien_dirawat', 'instruksi', 'alergi_makanan', 'alergi_lainnya', 'reaksi_alergi_obat', 'kondisi_pulang', 'anamnesa', 'pemeriksaan_fisik', 'prosedur', 'konsultasi', 'lain_lainnya', 'catatan_diet', 'last_modified_date', 'resumemedisri_id'])
                ->andWhere(['pendaftaran_id' => $registrationId])
                ->one();
        } else {
            $resumeRecord = [];
        }
        
        $resumeClass = new self();
        $resumeClass->registrationId = $registrationId;
        $resumeClass->dokter_dpjp_id = ArrayHelper::getValue($patientRecord, 'dokter_dpjp_id', NULL);
        
        $list_cara_keluar = CaraKeluar::getAllAscending();
        $enable_pulang = (new DocoConstansId)->actionGetAdditional('konfig_edit_form_pelayanan');
        
        // tindakan
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
            $latest_reseptur_dpjp = $resumeClass->resumeData('all-obat');

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
        // return [];
        $result = [];
        if (!empty($this->registrationId)) {
            switch ($type) {
                case 'asmed':
                    $result = PemeriksaanFisik::find()
                        ->select([
                            'beratbadan_kg as berat_badan',
                            'detaknadi as nadi',
                            'tinggibadan_cm as tinggi_badan',
                            'pernapasan as rr',
                            'tekanandarah as td',
                            'suhutubuh as suhu',

                        ])
                        ->orderBy(['created_date' => SORT_DESC])
                        ->andWhere([
                            'pendaftaran_id' => $this->registrationId
                        ])
                        ->asArray()
                        ->one();
                break;
                case 'askep':
                    $result = Anamnesa::find()
                        ->select([
                            'keluhan_utama',
                            'pendaftaran_id',
                            'alergi as alergi_obat',
                            'alergi',
                            'alergi_obat',
                            'alergi_lainnya',
                            'alergi_makanan',
                            'riwayat_penyakit_nama as riwayat_penyakit_dahulu',
                            'diagnosa_rujukan',
                        ])
                        ->orderBy(['created_date' => SORT_DESC])
                        ->andWhere([
                            'pendaftaran_id' => $this->registrationId
                        ])
                        ->asArray()
                        ->one();
                break;
                case 'obat-bawa-pulang':
                    $getLatestNoResep = Reseptur::find()
                        ->select(['noresep'])
                        ->rightJoin('pegawai_m', 'reseptur_t.pegawai_id = pegawai_m.pegawai_id and pegawai_m.kelompokpegawai_id = :kelompokMedis', [':kelompokMedis' => DocoConstants::KELOMPOK_MEDIS])
                        ->orderBy([
                            'reseptur_t.created_date' => SORT_DESC
                        ])
                        ->where([
                            'reseptur_t.pendaftaran_id' => $this->registrationId,
                            'status_reseptur' => [DocoConstants::RESEPTUR_SUDAH_DIPROSES, DocoConstants::RESEPTUR_BELUM_DIPROSES, DocoConstants::RESEPTUR_DISERAHKAN],
                        ]);
                        $getLatestNoResep = $getLatestNoResep->asArray()
                        ->one();
                    if (!$getLatestNoResep) {
                        $result = [];
                    } else {
                        $result = InfoResepturDetailView::find()
                            ->select([
                                'racikan_nama',
                                'rke',
                                'obatalkes_nama',
                                'satuan_kecil',
                                'signa_nama',
                                'signa',
                                'qty_reseptur',
                                'nama_rute',
                                'noresep'
                            ])
                            ->where([
                                'pendaftaran_id' => $this->registrationId,
                                'noresep' => $getLatestNoResep['noresep'],
                            ])
                            ->orderBy([
                                'noresep' => SORT_DESC,
                                'racikan_nama' => SORT_ASC,
                                'rke' => SORT_ASC
                            ])
                            ->asArray();
                            $result = $result->all();
                    }
                    break;
                case 'pulang':
                    $result = PasienPulang::find()
                        ->select([
                            'pasienpulang_t.carakeluar_id',
                            'carakeluar_m.carakeluar_nama',
                            ])
                        ->join('join', 'carakeluar_m', 'carakeluar_m.carakeluar_id=pasienpulang_t.carakeluar_id')
                        ->where(['pendaftaran_id' => $this->registrationId])
                        ->scalar();
                    break;
                case 'tindakan':
                    $result = SoapRjView::find()
                        ->select([
                            'instruksi as tindakan_paket_obat',
                            'qty',
                            'jenis as tipe',
                            'tgl_tindakan',
                            'jenis'
                        ])
                        ->andWhere(['pendaftaran_id' => $this->registrationId])
                        ->andWhere(['grouping_tipe_key' => 'tindakanbmhp'])
                        ->andWhere(['is_telah_implementasi' => TRUE])
                        ->andWhere(['or', ['and', ['<>', 'kelompoktindakan_id', DocoConstants::KT_ADMINISTRATIVE], ['=', 'jenis', 'TINDAKAN']], ['or', ['=', 'jenis', 'BMHP']] ])
                        ->orderBy(['tgl_tindakan' => SORT_ASC,'jenis' => SORT_DESC])
                        ->asArray()
                        ->distinct()
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
                        ->orderBy(['tgl_tindakan' => SORT_ASC,'jenis' => SORT_DESC])
                        ->asArray()
                        ->distinct();
                        $result = $result->all();
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
                case 'diet':
                    $catatanDiet = PermintaanMakan::find()
                        ->select(['catatan_diet'])
                        ->where(['pendaftaran_id' => $this->registrationId])
                        ->orderBy(['tgl_permintaanmakan' => SORT_DESC])
                        ->asArray()
                        ->one();
                    $result = !empty($catatanDiet) ? $catatanDiet['catatan_diet'] : '';
                    break;
                case 'penyerta':
                    $result = SoapRj::find()
                        ->select([
                            'soaprj_t.a_diag_penyerta',
                        ])
                        ->andWhere([
                            'soaprj_t.pendaftaran_id' => $this->registrationId,
                            'soaprj_t.pegawai_id' => $this->dokter_dpjp_id
                        ])
                        ->andWhere([
                            'IS NOT', 'soaprj_t.a_diag_penyerta', null
                        ])
                        ->orderBy(['soaprj_t.tgl_soaprj' => SORT_DESC])
                        ->asArray()
                        ->all();
                    break;
                case 'cppt':
                    $result = SoapRj::find()
                        ->select([
                            'soaprj_t.a_diag_utama',
                            'soaprj_t.a_diag_penyerta',
                            'soaprj_t.pasien_id',
                            'soaprj_t.pendaftaran_id',
                            'soaprj_t.tgl_soaprj',
                            'soaprj_t.subject',
                            'soaprj_t.object',
                        ])
                        ->andWhere([
                            'soaprj_t.pendaftaran_id' => $this->registrationId,
                            'pegawai_m.kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_DOKTER,
                            'soaprj_t.pegawai_id' => $this->dokter_dpjp_id
                        ])
                        ->join('join', 'pegawai_m', 'pegawai_m.pegawai_id=soaprj_t.pegawai_id')
                        ->orderBy(['soaprj_t.soaprj_id' => SORT_DESC])
                        ->asArray()
                        ->one();
                    break;
                case 'latestDiagnoseSoap':
                    $result = SoapRj::find()
                        ->select([
                            'soaprj_t.a_diag_utama'
                        ])
                        ->andWhere([
                            'soaprj_t.pendaftaran_id' => $this->registrationId,
                            'pegawai_m.kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_DOKTER,
                            'soaprj_t.pegawai_id' => $this->dokter_dpjp_id
                        ])
                        ->join('join', 'pegawai_m', 'pegawai_m.pegawai_id=soaprj_t.pegawai_id')
                        ->orderBy(['soaprj_t.soaprj_id' => SORT_ASC])
                        ->asArray();
                    $result = $result->one();
                    break;
                case 'konsul':
                    $result = Infokonsulpoli::find()
                        ->select([
                            'pendaftaran_id',
                            'nama_dokter as dok_mengkonsul',
                            'catatan_dokter_konsul',
                            'jawaban_konsul',
                            'tgl_konsulpoli',
                            'tgl_selesaikonsul'
                        ])
                        ->andWhere([
                            'pendaftaran_id' => $this->registrationId,
                            'status_konsul_id' => DocoConstants::STATUS_KONSUL_DIJAWAB,
                        ]);
                        $result = $result->asArray()
                        ->all();
                    break;
                case 'oldest-diagnosa-utama':
                    $result = SoapRj::find()
                        ->select([
                            'soaprj_t.a_diag_utama'
                        ])
                        ->andWhere([
                            'soaprj_t.pendaftaran_id' => $this->registrationId,
                            'soaprj_t.pegawai_id' => $this->dokter_dpjp_id
                        ])
                        ->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");

                    $result = $result->orderBy(['soaprj_t.soaprj_id' => SORT_ASC])
                        ->asArray()
                        ->one();
                  break;
                case 'latest-diagnosa-utama':
                    $result = SoapRj::find()
                        ->select([
                            'soaprj_t.a_diag_utama'
                        ])
                        ->andWhere([
                            'soaprj_t.pendaftaran_id' => $this->registrationId,
                            'soaprj_t.pegawai_id' => $this->dokter_dpjp_id
                        ])
                        ->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)")
                        ->orderBy(['soaprj_t.soaprj_id' => SORT_DESC])
                        ->asArray()
                        ->one();
                  break;
                case 'latest-subjective':
                    $result = SoapRj::find()
                        ->select([
                            'soaprj_t.subject'
                        ])
                        ->andWhere([
                            'soaprj_t.pendaftaran_id' => $this->registrationId,
                            'soaprj_t.pegawai_id' => $this->dokter_dpjp_id
                        ])
                        ->andWhere(['not', ['soaprj_t.subject' => null]])
                        ->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)")
                        ->orderBy(['soaprj_t.soaprj_id' => SORT_DESC])
                        ->asArray()
                        ->one();
                  break;
                  
                case 'latest-objective':
                    $result = SoapRj::find()
                        ->select([
                            'soaprj_t.object'
                        ])
                        ->andWhere([
                            'soaprj_t.pendaftaran_id' => $this->registrationId,
                            'soaprj_t.pegawai_id' => $this->dokter_dpjp_id
                        ])
                        ->andWhere(['not', ['soaprj_t.object' => null]])
                        ->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)")
                        ->orderBy(['soaprj_t.soaprj_id' => SORT_DESC])
                        ->asArray()
                        ->one();
                  break;
                case 'all-obat':
                    $result = Yii::$app->db->createCommand('
                        SELECT
                            nama_racikan as racikan_nama, rke, om.obatalkes_nama, sm.satuanunit_nama as satuan_kecil, 
                            sm2.signa_nama, signa, qty_reseptur, rt.noresep
                        FROM resepturdetail_t rt2
                        LEFT JOIN (SELECT
                              a.reseptur_id,
                              a.pendaftaran_id,
                              a.created_by,
                              a.noresep
                            FROM reseptur_t a
                            JOIN (SELECT
                                  MAX(b.reseptur_id) AS reseptur_id
                                FROM reseptur_t b
                                where b.pegawai_id = :pegawai_id AND b.status_reseptur <> :batal_reseptur
                                GROUP BY b.pendaftaran_id) max_reseptur ON a.reseptur_id = max_reseptur.reseptur_id) rt ON rt2.reseptur_id = rt.reseptur_id
                        LEFT JOIN obatalkes_m om ON om.obatalkes_id = rt2.obatalkes_id
                        LEFT JOIN satuanunit_m sm ON sm.satuanunit_id = rt2.satuankecil_id
                        LEFT JOIN signaobat_m sm2 ON sm2.signa_id = rt2.signa_id
                        WHERE
                            (rt.pendaftaran_id = :pendaftaran_id)
                            AND ((rt2.is_deleted = false) AND (rt2.deleted_date IS NULL))
                        ORDER BY
                            rt.noresep DESC,
                            racikan_nama,
                            rke,
                            rt.reseptur_id DESC;
                        ')
                        ->bindValue(':pendaftaran_id', $this->registrationId)
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
    // public static function updateResume($registrationId, $type)
    // {
    //     $recordResumeMedis = self::find()
    //         ->andWhere(['pendaftaran_id' => $registrationId])
    //         ->one();
    //     if (!empty($recordResumeMedis)) {
    //         $classResume = new self();
    //         $classResume->registrationId = $registrationId;
    //         switch ($type) {
    //             case 'diagnosaAwal':
    //                 $recordAskep = $classResume->resumeData('diagnosaAwal');
    //                 $recordResumeMedis->diag_awal = isset($recordAskep['diagnosa_masuk']) ? $recordAskep['diagnosa_masuk'] : null;
    //                 break;
    //             case 'asmed':
    //                 $record = $classResume->resumeData('asmed');
    //                 $recordResumeMedis->keluhan_utama = $record['keluhan_utama'];
    //                 $recordResumeMedis->pendaftaran_id = $record['pendaftaran_id'];
    //                 $recordResumeMedis->berat_badan = $record['berat_badan'];
    //                 $recordResumeMedis->nadi = $record['nadi'];
    //                 $recordResumeMedis->tinggi_badan = $record['tinggi_badan'];
    //                 $recordResumeMedis->rr = $record['rr'];
    //                 $recordResumeMedis->td = $record['td'];
    //                 $recordResumeMedis->suhu = $record['suhu'];
    //                 $recordResumeMedis->skala = $record['skala'];
    //                 $recordResumeMedis->alergi_obat = $record['alergi_obat'];
    //                 $recordResumeMedis->alergi_makanan = $record['alergi_makanan'];
    //                 $recordResumeMedis->obat_diberikan = $record['obat_diberikan'];
    //                 $recordResumeMedis->metod_asmennyeri = $record['metod_asmennyeri'];
    //                 $recordResumeMedis->riwayat_penyakit_dahulu = $record['riwayat_penyakit_dahulu'];
    //                 break;
    //             case 'cppt':
    //                 // update diag masuk and diag penyerta
    //                 $recordCppt = $classResume->resumeData('cppt');
    //                 $recordPenyerta = $classResume->resumeData('penyerta');
    //                 $recordResumeMedis->diag_utama = $recordCppt['a_diag_utama'];
    //                 $arrayDiagnose = [];
    //                 foreach ($recordPenyerta as $diagnose) {
    //                     $arrayDiagnose = array_merge( $arrayDiagnose, $diagnose['a_diag_penyerta'] );
    //                 }
    //                 $recordResumeMedis->diag_penyerta = $arrayDiagnose;
    //                 break;
    //             case 'tindakan':
    //                 $record = $classResume->resumeData('tindakan');
    //                 $recordResumeMedis->tindakan = $record;
    //                 break;
    //             case 'laboratorium':
    //                 break;
    //                 $record = $classResume->resumeData('laboratorium');
    //                 $recordResumeMedis->laboratorium = $record;
    //             case 'radiologi':
    //                 $record = $classResume->resumeData('radiologi');
    //                 $recordResumeMedis->radiologi = $record;
    //                 break;
    //             case 'konsul':
    //                 $record = $classResume->resumeData('konsul');
    //                 $recordResumeMedis->konsul = $record;
    //                 break;
    //             case 'obat':
    //                 $record = $classResume->resumeData('obat');
    //                 $recordResumeMedis->obat = $record;
    //                 break;
    //             case 'diet':
    //                 $record = $classResume->resumeData('diet');
    //                 $recordResumeMedis->catatan_diet = $record;
    //                 break;
    //         }
    //         $result = $recordResumeMedis->save();
    //         if (!$result) {
    //             Yii::error([
    //                 "ErrorResumeMedis" => $type,
    //                 "Bucket" => $recordResumeMedis->errors
    //             ]);
    //         }
    //         return $result;
    //     } else {
    //         return false;
    //     }
    // }

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

    private function getOrderPenunjang($id)
    {
        return Yii::$app->db->createCommand("
            SELECT pemeriksaan_nama FROM orderpenunjang_v WHERE pendaftaran_id = {$id}
        ")->queryAll();
    }

    private function getResep($id)
    {
        $statusDiserahkan = DocoConstants::RESEPTUR_DISERAHKAN;
        $group_alkes = DocoConstants::GROUP_JENISOBAT_ALKES;
        return Yii::$app->db->createCommand("
            SELECT iv.obatalkespasien_id,iv.obatalkes_nama,iv.qty_transaksi,iv.satuan_kecil,iv.signa_nama 
            FROM informasiresepdetail_v iv
            LEFT JOIN obatalkes_m om ON om.obatalkes_id = iv.obatalkes_id 
            LEFT JOIN jenisobatalkes_m jenis ON jenis.jenisobatalkes_id = om.jenisobatalkes_id 
            WHERE iv.pendaftaran_id = {$id} 
            AND iv.obatalkespasien_id IS NOT NULL 
            AND iv.penjualanresep_id IS NOT NULL 
            AND iv.status_reseptur_id = {$statusDiserahkan}
            AND jenis.group_jenisobat <> {$group_alkes}
        ")->queryAll();
    }
}
