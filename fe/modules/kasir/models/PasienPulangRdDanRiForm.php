<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "pasienpulangrddanri_v".
 *
 * @property integer $pasien_id
 * @property string $jenisidentitas
 * @property string $no_identitas_pasien
 * @property string $namadepan
 * @property string $nama_pasien
 * @property string $nama_bin
 * @property string $jeniskelamin
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $alamat_pasien
 * @property integer $rt
 * @property integer $rw
 * @property string $agama
 * @property string $golongandarah
 * @property string $photopasien
 * @property string $alamatemail
 * @property string $statusrekammedis
 * @property string $statusperkawinan
 * @property string $no_rekam_medik
 * @property string $tgl_rekam_medik
 * @property integer $kelurahan_id
 * @property string $kelurahan_nama
 * @property integer $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property string $no_urutantri
 * @property string $transportasi
 * @property string $keadaan_masuk
 * @property string $status_periksa
 * @property string $status_pasien
 * @property string $kunjungan
 * @property boolean $alih_status
 * @property boolean $by_phone
 * @property boolean $kunjungan_rumah
 * @property string $status_masuk
 * @property string $umur
 * @property string $no_asuransi
 * @property string $namapemilik_asuransi
 * @property string $nopokokperusahaan
 * @property integer $carabayar_id
 * @property string $carabayar_nama
 * @property integer $penjamin_id
 * @property string $penjamin_nama
 * @property integer $shift_id
 * @property integer $penanggungjawab_id
 * @property string $pengantar
 * @property string $hubungankeluarga
 * @property string $penanggungjawab_nama
 * @property integer $ruangan_id
 * @property string $ruangan_nama
 * @property integer $instalasi_id
 * @property string $instalasi_nama
 * @property integer $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property integer $rujukan_id
 * @property integer $pasienpulang_id
 * @property string $penerima_pasien
 * @property integer $lama_rawat
 * @property string $satuan_lamarawat
 * @property string $tglpasienpulang
 * @property integer $pasienbatalpulang_id
 * @property integer $rujukankeluar_id
 * @property string $rumahsakit_rujukan
 * @property string $alamat_rsrujukan
 * @property string $telp_fax
 * @property string $nosuratrujukan
 * @property string $tgldirujuk
 * @property string $ythdokter
 * @property string $dirujukkebagian
 * @property string $alasandirujuk
 * @property string $hasilpemeriksaan_ruj
 * @property string $diagnosasementara_ruj
 * @property string $pengobatan_ruj
 * @property string $lainlain_ruj
 * @property string $catatandokterperujuk
 * @property integer $carakeluar_id
 * @property string $carakeluar
 * @property integer $kondisikeluar_id
 * @property string $kondisipulang
 * @property string $tglcetakkartuasuransi
 * @property string $kodefeskestk1
 * @property string $nama_feskestk1
 * @property string $masaberlakukartu
 * @property string $nokartukeluarga
 * @property string $nopassport
 * @property string $status_konfirmasi
 * @property string $tgl_konfirmasi
 * @property boolean $is_active
 */
class PasienPulangRdDanRiForm extends \yii\base\Model
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
    public $kelurahan_id;
    public $kelurahan_nama;
    public $pendaftaran_id;
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
    public $shift_id;
    public $penanggungjawab_id;
    public $pengantar;
    public $hubungankeluarga;
    public $penanggungjawab_nama;
    public $ruangan_id;
    public $ruangan_nama;
    public $instalasi_id;
    public $instalasi_nama;
    public $jeniskasuspenyakit_id;
    public $jeniskasuspenyakit_nama;
    public $rujukan_id;
    public $pasienpulang_id;
    public $penerima_pasien;
    public $lama_rawat;
    public $satuan_lamarawat;
    public $tglpasienpulang;
    public $pasienbatalpulang_id;
    public $rujukankeluar_id;
    public $rumahsakit_rujukan;
    public $alamat_rsrujukan;
    public $telp_fax;
    public $nosuratrujukan;
    public $tgldirujuk;
    public $ythdokter;
    public $dirujukkebagian;
    public $alasandirujuk;
    public $hasilpemeriksaan_ruj;
    public $diagnosasementara_ruj;
    public $pengobatan_ruj;
    public $lainlain_ruj;
    public $catatandokterperujuk;
    public $carakeluar_id;
    public $carakeluar;
    public $kondisikeluar_id;
    public $kondisipulang;
    public $tglcetakkartuasuransi;
    public $kodefeskestk1;
    public $nama_feskestk1;
    public $masaberlakukartu;
    public $nokartukeluarga;
    public $nopassport;
    public $status_konfirmasi;
    public $tgl_konfirmasi;
    public $is_active;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pasienpulangrddanri_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasien_id', 'rt', 'rw', 'kelurahan_id', 'pendaftaran_id', 'carabayar_id', 'penjamin_id', 'shift_id', 'penanggungjawab_id', 'ruangan_id', 'instalasi_id', 'jeniskasuspenyakit_id', 'rujukan_id', 'pasienpulang_id', 'lama_rawat', 'pasienbatalpulang_id', 'rujukankeluar_id', 'carakeluar_id', 'kondisikeluar_id'], 'integer'],
            [['tanggal_lahir', 'tgl_rekam_medik', 'tgl_pendaftaran', 'tglpasienpulang', 'tgldirujuk', 'tglcetakkartuasuransi', 'masaberlakukartu', 'tgl_konfirmasi'], 'safe'],
            [['alamat_pasien', 'alamat_rsrujukan', 'alasandirujuk', 'hasilpemeriksaan_ruj', 'diagnosasementara_ruj', 'pengobatan_ruj', 'lainlain_ruj', 'catatandokterperujuk'], 'string'],
            [['alih_status', 'by_phone', 'kunjungan_rumah', 'is_active'], 'boolean'],
            [['jenisidentitas', 'namadepan', 'jeniskelamin', 'agama', 'statusperkawinan', 'no_pendaftaran'], 'string', 'max' => 20],
            [['no_identitas_pasien', 'nama_bin', 'umur', 'dirujukkebagian'], 'string', 'max' => 30],
            [['nama_pasien', 'kelurahan_nama', 'transportasi', 'keadaan_masuk', 'status_periksa', 'status_pasien', 'kunjungan', 'status_masuk', 'no_asuransi', 'namapemilik_asuransi', 'nopokokperusahaan', 'carabayar_nama', 'penjamin_nama', 'pengantar', 'hubungankeluarga', 'penanggungjawab_nama', 'ruangan_nama', 'instalasi_nama', 'satuan_lamarawat', 'rumahsakit_rujukan', 'telp_fax', 'nosuratrujukan', 'kodefeskestk1', 'status_konfirmasi'], 'string', 'max' => 50],
            [['tempat_lahir'], 'string', 'max' => 25],
            [['golongandarah'], 'string', 'max' => 2],
            [['photopasien', 'nama_feskestk1', 'nopassport'], 'string', 'max' => 200],
            [['alamatemail', 'jeniskasuspenyakit_nama', 'penerima_pasien', 'ythdokter', 'carakeluar', 'kondisipulang', 'nokartukeluarga'], 'string', 'max' => 100],
            [['statusrekammedis', 'no_rekam_medik'], 'string', 'max' => 10],
            [['no_urutantri'], 'string', 'max' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => Yii::t('fe', 'Pasien'),
            'jenisidentitas' => Yii::t('fe', 'Jenis identitas'),
            'no_identitas_pasien' => Yii::t('fe', 'No identitas pasien'),
            'namadepan' => Yii::t('fe', 'No Pendaftaran depan'),
            'nama_pasien' => Yii::t('fe', 'No Pendaftaran pasien'),
            'nama_bin' => Yii::t('fe', 'No Pendaftaran bin'),
            'jeniskelamin' => Yii::t('fe', 'Jenis kelamin'),
            'tempat_lahir' => Yii::t('fe', 'Tempat lahir'),
            'tanggal_lahir' => Yii::t('fe', 'Tanggal lahir'),
            'alamat_pasien' => Yii::t('fe', 'Alamat pasien'),
            'rt' => Yii::t('fe', 'Rt'),
            'rw' => Yii::t('fe', 'Rw'),
            'agama' => Yii::t('fe', 'Agama'),
            'golongandarah' => Yii::t('fe', 'Golongan darah'),
            'photopasien' => Yii::t('fe', 'Photo pasien'),
            'alamatemail' => Yii::t('fe', 'Alamat email'),
            'statusrekammedis' => Yii::t('fe', 'Status rekam medis'),
            'statusperkawinan' => Yii::t('fe', 'Status perkawinan'),
            'no_rekam_medik' => Yii::t('fe', 'No rekam medik'),
            'tgl_rekam_medik' => Yii::t('fe', 'Tanggal rekam medik'),
            'kelurahan_id' => Yii::t('fe', 'Kelurahan'),
            'kelurahan_nama' => Yii::t('fe', 'No Pendaftaran kelurahan'),
            'pendaftaran_id' => Yii::t('fe', 'Pendaftaran'),
            'no_pendaftaran' => Yii::t('fe', 'No pendaftaran'),
            'tgl_pendaftaran' => Yii::t('fe', 'Tanggal pendaftaran'),
            'no_urutantri' => Yii::t('fe', 'No urut antri'),
            'transportasi' => Yii::t('fe', 'Transportasi'),
            'keadaan_masuk' => Yii::t('fe', 'Keadaan masuk'),
            'status_periksa' => Yii::t('fe', 'Status periksa'),
            'status_pasien' => Yii::t('fe', 'Status pasien'),
            'kunjungan' => Yii::t('fe', 'Kunjungan'),
            'alih_status' => Yii::t('fe', 'Alih status'),
            'by_phone' => Yii::t('fe', 'By phone'),
            'kunjungan_rumah' => Yii::t('fe', 'Kunjungan rumah'),
            'status_masuk' => Yii::t('fe', 'Status masuk'),
            'umur' => Yii::t('fe', 'Umur'),
            'no_asuransi' => Yii::t('fe', 'No asuransi'),
            'namapemilik_asuransi' => Yii::t('fe', 'No Pendaftaran pemilik asuransi'),
            'nopokokperusahaan' => Yii::t('fe', 'No pokok perusahaan'),
            'carabayar_id' => Yii::t('fe', 'Cara bayar'),
            'carabayar_nama' => Yii::t('fe', 'No Pendaftaran cara bayar'),
            'penjamin_id' => Yii::t('fe', 'Penjamin'),
            'penjamin_nama' => Yii::t('fe', 'No Pendaftaran penjamin'),
            'shift_id' => Yii::t('fe', 'Shift ID'),
            'penanggungjawab_id' => Yii::t('fe', 'Penanggung jawab'),
            'pengantar' => Yii::t('fe', 'Pengantar'),
            'hubungankeluarga' => Yii::t('fe', 'Hubungan keluarga'),
            'penanggungjawab_nama' => Yii::t('fe', 'No Pendaftaran penanggung jawab'),
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'ruangan_nama' => Yii::t('fe', 'No Pendaftaran ruangan'),
            'instalasi_id' => Yii::t('fe', 'Instalasi'),
            'instalasi_nama' => Yii::t('fe', 'No Pendaftaran instalasi'),
            'jeniskasuspenyakit_id' => Yii::t('fe', 'Jenis kasus penyakit'),
            'jeniskasuspenyakit_nama' => Yii::t('fe', 'No Pendaftaran jenis kasus penyakit'),
            'rujukan_id' => Yii::t('fe', 'Rujukan'),
            'pasienpulang_id' => Yii::t('fe', 'Pasien pulang'),
            'penerima_pasien' => Yii::t('fe', 'Penerima pasien'),
            'lama_rawat' => Yii::t('fe', 'Lama rawat'),
            'satuan_lamarawat' => Yii::t('fe', 'Satuan lama rawat'),
            'tglpasienpulang' => Yii::t('fe', 'Tanggal pasien pulang'),
            'pasienbatalpulang_id' => Yii::t('fe', 'Pasien batal pulang'),
            'rujukankeluar_id' => Yii::t('fe', 'Rujukan keluar'),
            'rumahsakit_rujukan' => Yii::t('fe', 'Rujukan rumah sakit'),
            'alamat_rsrujukan' => Yii::t('fe', 'Alamat rs rujukan'),
            'telp_fax' => Yii::t('fe', 'Telp Fax'),
            'nosuratrujukan' => Yii::t('fe', 'No surat rujukan'),
            'tgldirujuk' => Yii::t('fe', 'Tanggal dirujuk'),
            'ythdokter' => Yii::t('fe', 'Yth dokter'),
            'dirujukkebagian' => Yii::t('fe', 'Dirujuk kebagian'),
            'alasandirujuk' => Yii::t('fe', 'Alasan dirujuk'),
            'hasilpemeriksaan_ruj' => Yii::t('fe', 'Hasil pemeriksaan rujukan'),
            'diagnosasementara_ruj' => Yii::t('fe', 'Diagnosa sementara rujukan'),
            'pengobatan_ruj' => Yii::t('fe', 'Pengobatan rujukan'),
            'lainlain_ruj' => Yii::t('fe', 'Rujukan lainnya'),
            'catatandokterperujuk' => Yii::t('fe', 'Catatan dokter perujuk'),
            'carakeluar_id' => Yii::t('fe', 'Cara keluar'),
            'carakeluar' => Yii::t('fe', 'Cara keluar'),
            'kondisikeluar_id' => Yii::t('fe', 'Kondisi keluar'),
            'kondisipulang' => Yii::t('fe', 'Kondisi pulang'),
            'tglcetakkartuasuransi' => Yii::t('fe', 'Tanggal cetak kartu asuransi'),
            'kodefeskestk1' => Yii::t('fe', 'Tanggal pulang feskestk1'),
            'nama_feskestk1' => Yii::t('fe', 'No Pendaftaran feskestk1'),
            'masaberlakukartu' => Yii::t('fe', 'Masa berlaku kartu'),
            'nokartukeluarga' => Yii::t('fe', 'No kartu keluarga'),
            'nopassport' => Yii::t('fe', 'No passport'),
            'status_konfirmasi' => Yii::t('fe', 'Status konfirmasi'),
            'tgl_konfirmasi' => Yii::t('fe', 'Tanggal Konfirmasi'),
            'is_active' => Yii::t('fe', 'Is Active'),
        ];
    }
}
