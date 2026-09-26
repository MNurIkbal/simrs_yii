<?php

/**
 * @Author: afil
 * @Date:   2018-01-12 15:25:54
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-19 13:36:58
 * @Description: 
 */

namespace app\modules\rajal\models;

use Yii;

/**
 * This is the model class for table "infokunjunganrj_v".
 */
class InfoKunjunganRajalForm extends \yii\base\Model
{
    public $pasien_id;
    public $jenisidentitas;
    public $no_identitas_pasien;
    public $namadepan;
    public $nama_pasien;
    public $nama_bin;
    public $jeniskelamin;
    public $tempat_lahir;
    public $tanggal_lahir;
    public $alamat_pasien;
    public $rt;
    public $rw;
    public $agama;
    public $golongandarah;
    public $photopasien;
    public $alamatemail;
    public $statusrekammedis;
    public $statusperkawinan;
    public $no_rekam_medik;
    public $tgl_rekam_medik;
    public $propinsi_id;
    public $propinsi_nama;
    public $kabupaten_id;
    public $kabupaten_nama;
    public $kelurahan_id;
    public $kelurahan_nama;
    public $kecamatan_id;
    public $kecamatan_nama;
    public $pendaftaran_id;
    public $pekerjaan_id;
    public $pekerjaan_nama;
    public $no_pendaftaran;
    public $tgl_pendaftaran;
    public $no_urutantri;
    public $transportasi;
    public $keadaan_masuk;
    public $status_periksa;
    public $status_pasien;
    public $kunjungan;
    public $alih_status;
    public $by_phone;
    public $kunjungan_rumah;
    public $status_masuk;
    public $umur;
    public $no_asuransi;
    public $namapemilik_asuransi;
    public $nopokokperusahaan;
    public $carabayar_id;
    public $carabayar_nama;
    public $penjamin_id;
    public $penjamin_nama;
    public $caramasuk_id;
    public $caramasuk_nama;
    public $shift_id;
    public $golonganumur_id;
    public $golonganumur_nama;
    public $no_rujukan;
    public $nama_perujuk;
    public $tanggal_rujukan;
    public $kodediagnosa_rujukan;
    public $asalrujukan_id;
    public $asalrujukan_nama;
    public $penanggungjawab_id;
    public $pengantar;
    public $hubungankeluarga;
    public $penanggungjawab_nama;
    public $ruangan_id;
    public $ruangan_nama;
    public $ruangan_singkatan;
    public $instalasi_id;
    public $instalasi_nama;
    public $jeniskasuspenyakit_id;
    public $jeniskasuspenyakit_nama;
    public $kelaspelayanan_id;
    public $kelaspelayanan_nama;
    public $gelardepan;
    public $nama_pegawai;
    public $gelarbelakang;
    public $status_konfirmasi;
    public $tgl_konfirmasi;
    public $pegawai_id;
    public $tgl_renkontrol;
    public $pembayaranpelayanan_id;
    public $panggil_antrian;
    public $antrian_id;
    public $tgl_antrian;
    public $no_antrian;
    public $panggil_flag;
    public $loket_id;
    public $loket_nama;
    public $loket_fungsi;
    public $loket_singkatan;
    public $loket_nourut;
    public $loket_formatnomor;
    public $loket_maxantrian;
    public $nopeserta;
    public $tglcetakkartuasuransi;
    public $kodefeskestk1;
    public $nama_feskestk1;
    public $masaberlakukartu;
    public $nokartukeluarga;
    public $nopassport;
    public $is_active;
    public $keterangan_pendaftaran;
    public $pengirimanrm_id;
    public $statusdok_rekammedik;
    public $kelompokpegawai_id;
    public $konsulpoli_id;
    public $is_deleted;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infokunjunganrj_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasien_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kelurahan_id', 'kecamatan_id', 'pendaftaran_id', 'pekerjaan_id', 'carabayar_id', 'penjamin_id', 'caramasuk_id', 'shift_id', 'golonganumur_id', 'asalrujukan_id', 'penanggungjawab_id', 'ruangan_id', 'instalasi_id', 'jeniskasuspenyakit_id', 'kelaspelayanan_id', 'pegawai_id', 'pembayaranpelayanan_id', 'antrian_id', 'loket_id', 'loket_nourut', 'loket_maxantrian', 'pengirimanrm_id', 'kelompokpegawai_id', 'konsulpoli_id'], 'default', 'value' => null],
            [['pasien_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kelurahan_id', 'kecamatan_id', 'pendaftaran_id', 'pekerjaan_id', 'carabayar_id', 'penjamin_id', 'caramasuk_id', 'shift_id', 'golonganumur_id', 'asalrujukan_id', 'penanggungjawab_id', 'ruangan_id', 'instalasi_id', 'jeniskasuspenyakit_id', 'kelaspelayanan_id', 'pegawai_id', 'pembayaranpelayanan_id', 'antrian_id', 'loket_id', 'loket_nourut', 'loket_maxantrian', 'pengirimanrm_id', 'kelompokpegawai_id', 'konsulpoli_id'], 'integer'],
            [['tanggal_lahir', 'tgl_rekam_medik', 'tgl_pendaftaran', 'tanggal_rujukan', 'tgl_konfirmasi', 'tgl_renkontrol', 'tgl_antrian', 'tglcetakkartuasuransi', 'masaberlakukartu'], 'safe'],
            [['alamat_pasien', 'loket_fungsi', 'keterangan_pendaftaran'], 'string'],
            [['alih_status', 'by_phone', 'kunjungan_rumah', 'panggil_antrian', 'panggil_flag', 'is_active', 'is_deleted'], 'boolean'],
            [['jenisidentitas', 'namadepan', 'jeniskelamin', 'agama', 'statusperkawinan', 'no_pendaftaran', 'no_rujukan'], 'string', 'max' => 20],
            [['no_identitas_pasien', 'nama_bin', 'umur'], 'string', 'max' => 30],
            [['nama_pasien', 'propinsi_nama', 'kabupaten_nama', 'kelurahan_nama', 'kecamatan_nama', 'pekerjaan_nama', 'transportasi', 'keadaan_masuk', 'status_periksa', 'status_pasien', 'kunjungan', 'status_masuk', 'no_asuransi', 'namapemilik_asuransi', 'nopokokperusahaan', 'carabayar_nama', 'penjamin_nama', 'caramasuk_nama', 'nama_perujuk', 'kodediagnosa_rujukan', 'asalrujukan_nama', 'pengantar', 'hubungankeluarga', 'penanggungjawab_nama', 'ruangan_nama', 'instalasi_nama', 'kelaspelayanan_nama', 'nama_pegawai', 'status_konfirmasi', 'loket_nama', 'nopeserta', 'kodefeskestk1', 'statusdok_rekammedik'], 'string', 'max' => 50],
            [['tempat_lahir', 'golonganumur_nama'], 'string', 'max' => 25],
            [['golongandarah'], 'string', 'max' => 2],
            [['photopasien', 'nama_feskestk1', 'nopassport'], 'string', 'max' => 200],
            [['alamatemail', 'jeniskasuspenyakit_nama', 'nokartukeluarga'], 'string', 'max' => 100],
            [['statusrekammedis', 'no_rekam_medik', 'gelardepan'], 'string', 'max' => 10],
            [['no_urutantri', 'no_antrian'], 'string', 'max' => 6],
            [['ruangan_singkatan'], 'string', 'max' => 3],
            [['gelarbelakang'], 'string', 'max' => 32],
            [['loket_singkatan'], 'string', 'max' => 1],
            [['loket_formatnomor'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => 'Pasien ID',
            'jenisidentitas' => Yii::t('fe', 'Jenis identitas'),
            'no_identitas_pasien' => Yii::t('fe', 'No identitas pasien'),
            'namadepan' => Yii::t('fe', 'Nama depan'),
            'nama_pasien' => Yii::t('fe', 'Nama pasien'),
            'nama_bin' => Yii::t('fe', 'Nama bin'),
            'jeniskelamin' => Yii::t('fe', 'Jenis kelamin'),
            'tempat_lahir' => Yii::t('fe', 'Tempat lahir'),
            'tanggal_lahir' => Yii::t('fe', 'Tanggal lahir'),
            'alamat_pasien' => Yii::t('fe', 'Alamat pasien'),
            'rt' => Yii::t('fe', 'RT'),
            'rw' => Yii::t('fe', 'RW'),
            'agama' => Yii::t('fe', 'Agama'),
            'golongandarah' => Yii::t('fe', 'Golongan darah'),
            'photopasien' => Yii::t('fe', 'Foto pasien'),
            'alamatemail' => Yii::t('fe', 'Alamat email'),
            'statusrekammedis' => Yii::t('fe', 'Status rekam medis'),
            'statusperkawinan' => Yii::t('fe', 'Status perkawinan'),
            'no_rekam_medik' => Yii::t('fe', 'No rekam medik'),
            'tgl_rekam_medik' => Yii::t('fe', 'Tanggal rekam medik'),
            'propinsi_id' => 'Propinsi ID',
            'propinsi_nama' => 'Propinsi Nama',
            'kabupaten_id' => 'Kabupaten ID',
            'kabupaten_nama' => 'Kabupaten Nama',
            'kelurahan_id' => 'Kelurahan ID',
            'kelurahan_nama' => 'Kelurahan Nama',
            'kecamatan_id' => 'Kecamatan ID',
            'kecamatan_nama' => 'Kecamatan Nama',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pekerjaan_id' => 'Pekerjaan ID',
            'pekerjaan_nama' => Yii::t('fe', 'Nama pekerjaan'),
            'no_pendaftaran' => Yii::t('fe', 'No pendaftaran'),
            'tgl_pendaftaran' => Yii::t('fe', 'Tanggal pendaftaran'),
            'no_urutantri' => Yii::t('fe', 'No urut antri'),
            'transportasi' => Yii::t('fe', 'Transportasi'),
            'keadaan_masuk' => Yii::t('fe', 'Keadaan masuk'),
            'status_periksa' => Yii::t('fe', 'Status periksa'),
            'status_pasien' => Yii::t('fe', 'Status pasien'),
            'kunjungan' => Yii::t('fe', 'Kunjungan'),
            'alih_status' => Yii::t('fe', 'Alih status'),
            'by_phone' => 'By Phone',
            'kunjungan_rumah' => Yii::t('fe', 'Kunjungan rumah'),
            'status_masuk' => Yii::t('fe', 'Status masuk'),
            'umur' => Yii::t('fe', 'Umur'),
            'no_asuransi' => Yii::t('fe', 'No asuransi'),
            'namapemilik_asuransi' => Yii::t('fe', 'Nama pemilik asuransi'),
            'nopokokperusahaan' => Yii::t('fe', 'No pokok perusahaan'),
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => Yii::t('fe', 'Nama cara bayar'),
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => Yii::t('fe', 'Nama penjamin'),
            'caramasuk_id' => 'Caramasuk ID',
            'caramasuk_nama' => Yii::t('fe', 'Nama cara masuk'),
            'shift_id' => 'Shift ID',
            'golonganumur_id' => 'Golonganumur ID',
            'golonganumur_nama' => Yii::t('fe', 'Nama golongan umum'),
            'no_rujukan' => 'No Rujukan',
            'nama_perujuk' => Yii::t('fe', 'Nama perujuk'),
            'tanggal_rujukan' => Yii::t('fe', 'Tanggal rujukan'),
            'kodediagnosa_rujukan' => Yii::t('fe', 'Kode diagnosa rujukan'),
            'asalrujukan_id' => 'Asalrujukan ID',
            'asalrujukan_nama' => Yii::t('fe', 'Nama asal rujukan'),
            'penanggungjawab_id' => 'Penanggungjawab ID',
            'pengantar' => Yii::t('fe', 'Pengantar'),
            'hubungankeluarga' => Yii::t('fe', 'Hubungan keluarga'),
            'penanggungjawab_nama' => Yii::t('fe', 'Nama penanggungjawab'),
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => Yii::t('fe', 'Ruangan nama'),
            'ruangan_singkatan' => Yii::t('fe', 'Singkatan ruangan'),
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => Yii::t('fe', 'Nama instalasi'),
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'jeniskasuspenyakit_nama' => Yii::t('fe', 'Nama jenis kasus penyakit'),
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => Yii::t('fe', 'Nama kelas pelayanan'),
            'gelardepan' => Yii::t('fe', 'Gelar depan'),
            'nama_pegawai' => Yii::t('fe', 'Nama pegawai'),
            'gelarbelakang' => Yii::t('fe', 'Gelar belakang'),
            'status_konfirmasi' => Yii::t('fe', 'Status konfirmasi'),
            'tgl_konfirmasi' => Yii::t('fe', 'Tanggal konfirmasi'),
            'pegawai_id' => 'Pegawai ID',
            'tgl_renkontrol' => Yii::t('fe', 'Tanggal renkontrol'),
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'panggil_antrian' => Yii::t('fe', 'Panggil antrian'),
            'antrian_id' => 'Antrian ID',
            'tgl_antrian' => Yii::t('fe', 'Tanggal antrian'),
            'no_antrian' => Yii::t('fe', 'No antrian'),
            'panggil_flag' => 'Panggil Flag',
            'loket_id' => 'Loket ID',
            'loket_nama' => Yii::t('fe', 'Nama loket'),
            'loket_fungsi' => Yii::t('fe', 'Fungsi loket'),
            'loket_singkatan' => Yii::t('fe', 'Singkatan loket'),
            'loket_nourut' => Yii::t('fe', 'No urut loket'),
            'loket_formatnomor' => 'Loket Formatnomor',
            'loket_maxantrian' => 'Loket Maxantrian',
            'nopeserta' => Yii::t('fe', 'No peserta'),
            'tglcetakkartuasuransi' => Yii::t('fe', 'Tanggal cetak kartu asuransi'),
            'kodefeskestk1' => 'Kodefeskestk1',
            'nama_feskestk1' => 'Nama Feskestk1',
            'masaberlakukartu' => Yii::t('fe', 'Masa berlaku kartu'),
            'nokartukeluarga' => Yii::t('fe', 'No kartu keluarga'),
            'nopassport' => Yii::t('fe', 'No paspor'),
            'is_active' => 'Is Active',
            'keterangan_pendaftaran' => Yii::t('fe', 'Keterangan pendaftaran'),
            'pengirimanrm_id' => 'Pengirimanrm ID',
            'statusdok_rekammedik' => 'Statusdok Rekammedik',
            'kelompokpegawai_id' => 'Kelompokpegawai ID',
            'konsulpoli_id' => 'Konsulpoli ID',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
