<?php

namespace Doco\penjaminasuransi\models;

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-19 14:54:49
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-10-08 17:11:49
 */

class KlaimInacbgForm extends \yii\base\Model
{
    public $kunjungan_id;
    public $instalasi_kode;
    public $pasien_id;
    public $los;
    public $dokter_kode;
    public $berat_lahir;
    public $tgl_masuk;
    public $tgl_keluar;
    public $tarif;
    public $total_tarifrs;
    public $prosedur_bedah;
    public $prosedur_nonbedah;
    public $konsultasi;
    public $tenaga_ahli;
    public $keperawatan;
    public $penunjang;
    public $radiologi;
    public $laboratorium;
    public $pelayanan_darah;
    public $rehabilitasi;
    public $kamar_akomodasi;
    public $rawat_intensif;
    public $obat;
    public $alkes;
    public $bmhp;
    public $sewa_alat;
    public $diagnosa_primer;
    public $diagnosa_sekunder;
    public $adl_subacute;
    public $adl_cronic;
    public $jenis_kelasrawat;
    public $umur;
    public $carapulang_id;
    public $no_sep;
    public $no_kartu;
    public $jeniskelamin;
    public $tgl_lahir;
    public $nama_pasien;
    public $no_rekam_medik;
    public $nama_dokter;
    public $obat_kemoterapi;
    public $obat_kronis;
    public $tarif_poli_eks;
    public $kelas_bpjs;
    public $dokter_id;
    public $klaim_penjamin;
    public $total_episode;
    public $identitas_id;
    public $identitas_value;
    public $nokartuasuransi;
    public $no_klaimcovid;
    public $is_komplikasi;
    public $status_covid;
    public $is_pemulasaranjenazah;
    public $is_kantongjenazah;
    public $is_petijenazah;
    public $is_plastikerat;
    public $is_desinfektanjenazah;
    public $is_transport;
    public $is_desinfektanmobil;
    public $no_pendaftaran;
    public $instalasi_id;
    public $pasien_tb;
    public $rujukanrs;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'klaiminacbg_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kunjungan_id'], 'required'],
            [['total_tarifrs', 'prosedur_bedah', 'prosedur_nonbedah', 'konsultasi', 'tenaga_ahli', 'keperawatan', 'penunjang', 'radiologi', 'laboratorium', 'pelayanan_darah', 'rehabilitasi', 'kamar_akomodasi', 'rawat_intensif', 'obat', 'alkes', 'bmhp', 'sewa_alat', 'total_episode'], 'number'],
            [['diagnosa_primer', 'diagnosa_sekunder'], 'string'],
            [['adl_subacute', 'adl_cronic', 'jenis_kelasrawat', 'umur', 'carapulang_id', 'tarif'], 'string', 'max' => 100],
            [['no_sep'], 'string', 'max' => 255],
            [['pasien_id', 'total_tarifrs','instalasi_id','prosedur_bedah', 'prosedur_nonbedah', 'konsultasi', 'tenaga_ahli', 'keperawatan', 'penunjang', 'radiologi', 'laboratorium', 'pelayanan_darah', 'rehabilitasi', 'kamar_akomodasi', 'rawat_intensif', 'obat', 'alkes', 'bmhp', 'sewa_alat', 'diagnosa_primer', 'diagnosa_sekunder', 'adl_subacute', 'adl_cronic', 'jenis_kelasrawat', 'umur', 'carapulang_id', 'tarif', 'no_sep', 'tgl_masuk', 'tgl_keluar', 'instalasi_kode', 'dokter_kode', 'los', 'no_kartu', 'nama_pasien','no_rekam_medik', 'jeniskelamin', 'tgl_lahir', 'prosedur_bedah', 'prosedur_nonbedah', 'konsultasi', 'tenaga_ahli', 'keperawatan', 'penunjang', 'radiologi', 'laboratorium', 'pelayanan_darah', 'rehabilitasi', 'kamar_akomodasi', 'rawat_intensif', 'obat', 'alkes', 'bmhp', 'sewa_alat', 'nama_dokter', 'obat_kemoterapi', 'obat_kronis', 'tarif_poli_eks', 'kelas_bpjs', 'dokter_id', 'berat_lahir', 'klaim_penjamin', 'identitas_id', 'identitas_value', 'nokartuasuransi', 'no_klaimcovid', 'is_komplikasi','is_pemulasaranjenazah', 'is_kantongjenazah', 'is_petijenazah', 'is_plastikerat', 'is_desinfektanjenazah', 'is_transport', 'is_desinfektanmobil', 'status_covid', 'no_pendaftaran'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'klaimgroup_id' => 'Klaimgroup ID',
            'kunjungan_id' => 'Kunjungan ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'instalasi_kode' => 'Instalasi ID',
            'pasien_id' => 'Pasien ID',
            'tgl_masuk' => 'Tgl Masuk',
            'tgl_keluar' => 'Tgl Keluar',
            'los' => 'Los',
            'adl_subacute' => 'Adl Subacute',
            'adl_cronic' => 'Adl Cronic',
            'dokter_kode' => 'Dokterdpjp ID',
            'total_tarifrs' => 'Total Tarifrs',
            'no_sep' => 'No Sep',
            'jenis_kelasrawat' => 'Jenis Kelasrawat',
            'umur' => 'Umur',
            'berat_lahir' => 'Berat Lahir',
            'carapulang_id' => 'Carapulang ID',
            'tarif' => 'Tarif',
            'prosedur_bedah' => 'Prosedur Bedah',
            'prosedur_nonbedah' => 'Prosedur Nonbedah',
            'konsultasi' => 'Konsultasi',
            'tenaga_ahli' => 'Tenaga Ahli',
            'keperawatan' => 'Keperawatan',
            'penunjang' => 'Penunjang',
            'radiologi' => 'Radiologi',
            'laboratorium' => 'Laboratorium',
            'pelayanan_darah' => 'Pelayanan Darah',
            'rehabilitasi' => 'Rehabilitasi',
            'kamar_akomodasi' => 'Kamar Akomodasi',
            'rawat_intensif' => 'Rawat Intensif',
            'obat' => 'Obat',
            'alkes' => 'Alkes',
            'bmhp' => 'Bmhp',
            'sewa_alat' => 'Sewa Alat',
            'diagnosa_primer' => 'Diagnosa Primer',
            'diagnosa_sekunder' => 'Diagnosa Sekunder',
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
            'instalasi_id' => "Instalasi ID",
            'pasien_tb' => "Pasien TB",
            'rujukanrs' => "Rujukan RS"
        ];
    }
}