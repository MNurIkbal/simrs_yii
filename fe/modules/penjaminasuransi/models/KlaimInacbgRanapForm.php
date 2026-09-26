<?php

namespace Doco\penjaminasuransi\models;

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-10-02 13:49:55
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-10-11 16:29:39
 */

class KlaimInacbgRanapForm extends \yii\base\Model
{
    public $kunjungan_id;
    public $pasienadmisi_id;
    public $is_naikkelas;
    public $is_rawatintensif;
    public $lama_rawatintensif;
    public $lama_rawatkelas;
    public $ventilator;
    public $naik_kelas;
    public $instalasi_id;
    public $pasien_id;
    public $los;
    public $dokterdpjp_id;
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
    public $diagnosa_primer_ina;
    public $diagnosa_sekunder_ina;
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
    public $pasien_tb;
    public $rujukanrs;
    public $klaim_penjamin;
    public $identitas_id;
    public $identitas_value;
    public $no_klaimcovid;
    public $jenis;
    public $isolasi_rs;
    public $rs_darurat;
    public $no_pendaftaran;
    public $kelas_eksekutif;
    public $intubasi;
    public $ekstubasi;
    public $tarif_poli_eks;
    public $sistole;
    public $diastole;
    public $pembayar_selisih_biaya;
    public $is_pasiensitb;
    public $is_coinsidensecovid;
    public $sitb_number;
    public $dializer;
    public $transfusi_darah;
    public $dokter_additional;




    /**
     * {@inheritdoc}
     */

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kunjungan_id'], 'required'],
            [['rawat_intensif', 'lama_rawatintensif','lama_rawatkelas', 'ventilator','dializer','transfusi_darah'], 'number'],
            [['prosedur_bedah', 'prosedur_nonbedah', 'konsultasi', 'tenaga_ahli', 'keperawatan', 'penunjang', 'radiologi', 'laboratorium', 'pelayanan_darah', 'rehabilitasi', 'kamar_akomodasi','obat', 'alkes', 'bmhp', 'sewa_alat'], 'number', 'numberPattern' => '/^\d+(.\d{1,2})?$/'],
            [['diagnosa_primer', 'diagnosa_sekunder', 'diagnosa_primer_ina', 'diagnosa_sekunder_ina', 'pembayar_selisih_biaya'], 'string'],
            [['adl_subacute', 'adl_cronic', 'jenis_kelasrawat', 'umur', 'carapulang_id', 'tarif'], 'string', 'max' => 100],
            [['no_sep'], 'string', 'max' => 255],
            [['is_naikkelas'], 'cekLamaNaik'],
            [['is_rawatintensif'], 'cekLamaIntensif'],
            [['pasien_id', 'total_tarifrs','transfusi_darah','dializer', 'prosedur_bedah', 'prosedur_nonbedah', 'konsultasi', 'tenaga_ahli', 'keperawatan', 'penunjang', 'radiologi', 'laboratorium', 'pelayanan_darah', 'rehabilitasi', 'kamar_akomodasi', 'rawat_intensif', 'obat', 'alkes', 'bmhp', 'sewa_alat', 'diagnosa_primer', 'diagnosa_sekunder', 'adl_subacute', 'adl_cronic', 'jenis_kelasrawat', 'umur', 'carapulang_id', 'tarif', 'no_sep', 'tgl_masuk', 'tgl_keluar', 'instalasi_id', 'dokterdpjp_id', 'los', 'no_kartu', 'nama_pasien','no_rekam_medik', 'jeniskelamin', 'tgl_lahir', 'prosedur_bedah', 'prosedur_nonbedah', 'konsultasi', 'tenaga_ahli', 'keperawatan', 'penunjang', 'radiologi', 'laboratorium', 'pelayanan_darah', 'rehabilitasi', 'kamar_akomodasi', 'rawat_intensif', 'obat', 'alkes', 'bmhp', 'sewa_alat','is_naikkelas', 'is_rawatintensif', 'lama_rawatkelas', 'lama_rawatintensif', 'naik_kelas','pasienadmisi_id','nama_dokter', 'obat_kemoterapi', 'obat_kronis', 'klaim_penjamin', 'identitas_id', 'identitas_value', 'no_klaimcovid','jenis', 'isolasi_rs', 'rs_darurat', 'intubasi', 'ekstubasi','tarif_poli_eks','sistole', 'diastole', 'pembayar_selisih_biaya', 'is_pasiensitb','is_coinsidensecovid', 'sitb_number'], 'safe'],
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
            'instalasi_id' => 'Instalasi ID',
            'pasien_id' => 'Pasien ID',
            'tgl_masuk' => 'Tgl Masuk',
            'tgl_keluar' => 'Tgl Keluar',
            'los' => 'Los',
            'adl_subacute' => 'Adl Subacute',
            'adl_cronic' => 'Adl Cronic',
            'dokterdpjp_id' => 'Dokterdpjp ID',
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
            'diagnosa_primer' => 'Diagnosa Primer UNU Grouper',
            'diagnosa_sekunder' => 'Diagnosa Sekunder UNU Grouper',
            'diagnosa_primer_ina' => 'Diagnosa Primer INA Grouper',
            'diagnosa_sekunder_ina' => 'Diagnosa Sekunder INA Grouper',
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
            'pasien_tb' => 'Pasien TB',
            'rujukanrs' => "Rujukan RS",
            'jenis' => "Jenis Rawat",
            'isolasi_rs' => "Isolasi RS",
            'rs_darurat' => "RS Darurat / Lapangan",
            'kelas_eksekutif' => "Kelas Eksekutif",
            'intubasi' => "Intubasi",
            'ekstubasi' => "Ekstubasi",
            'sistole' => "Sistole",
            'diastole' => "Diastole",
            'pembayar_selisih_biaya' => "Pembayar Selisih Biaya",
            'is_pasiensitb' => "Pasien SITB",
            'is_coinsidensecovid' => "Coinsidense Covid 19",
            'sitb_number' => "Nomor SITB",
            'dializer' => "Penggunaan Dializer",
            'transfusi_darah' => "Transfusi Darah"

        ];
    }
    public function cekLamaNaik()
    {
        if($this->is_naikkelas){
            if($this->lama_rawatkelas > $this->los){
                $this->addError('lama_rawatkelas', 'Lama Rawat Naik Kelas Tidak Boleh Lebih Dari Lama Rawat Inap');
            } else {
                $this->clearErrors('lama_rawatkelas');
            }
        }
    }
    public function cekLamaIntensif()
    {
        if($this->is_rawatintensif){
            if($this->lama_rawatintensif > $this->los){
                $this->addError('lama_rawatintensif', 'Lama Rawat Intensif Tidak Boleh Lebih Dari Lama Rawat Inap');
                return false;
            } else {
                $this->clearErrors('lama_rawatintensif');
            }
        }
    }
}