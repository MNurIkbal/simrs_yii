<?php

namespace app\modules\v1\models;

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
class PasienPulangRdDanRiView extends \Doco\components\DocoActiveRecord
{
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
            'pasien_id' => Yii::t('app', 'Pasien'),
            'jenisidentitas' => Yii::t('app', 'Jenis identitas'),
            'no_identitas_pasien' => Yii::t('app', 'No identitas pasien'),
            'namadepan' => Yii::t('app', 'Nama depan'),
            'nama_pasien' => Yii::t('app', 'Nama pasien'),
            'nama_bin' => Yii::t('app', 'Nama bin'),
            'jeniskelamin' => Yii::t('app', 'Jenis kelamin'),
            'tempat_lahir' => Yii::t('app', 'Tempat lahir'),
            'tanggal_lahir' => Yii::t('app', 'Tanggal lahir'),
            'alamat_pasien' => Yii::t('app', 'Alamat pasien'),
            'rt' => Yii::t('app', 'Rt'),
            'rw' => Yii::t('app', 'Rw'),
            'agama' => Yii::t('app', 'Agama'),
            'golongandarah' => Yii::t('app', 'Golongan darah'),
            'photopasien' => Yii::t('app', 'Photo pasien'),
            'alamatemail' => Yii::t('app', 'Alamat email'),
            'statusrekammedis' => Yii::t('app', 'Status rekam medis'),
            'statusperkawinan' => Yii::t('app', 'Status perkawinan'),
            'no_rekam_medik' => Yii::t('app', 'No rekam medik'),
            'tgl_rekam_medik' => Yii::t('app', 'Tanggal rekam medik'),
            'kelurahan_id' => Yii::t('app', 'Kelurahan'),
            'kelurahan_nama' => Yii::t('app', 'Nama kelurahan'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran'),
            'no_pendaftaran' => Yii::t('app', 'No pendaftaran'),
            'tgl_pendaftaran' => Yii::t('app', 'Tanggal pendaftaran'),
            'no_urutantri' => Yii::t('app', 'No urut antri'),
            'transportasi' => Yii::t('app', 'Transportasi'),
            'keadaan_masuk' => Yii::t('app', 'Keadaan masuk'),
            'status_periksa' => Yii::t('app', 'Status periksa'),
            'status_pasien' => Yii::t('app', 'Status pasien'),
            'kunjungan' => Yii::t('app', 'Kunjungan'),
            'alih_status' => Yii::t('app', 'Alih status'),
            'by_phone' => Yii::t('app', 'By phone'),
            'kunjungan_rumah' => Yii::t('app', 'Kunjungan rumah'),
            'status_masuk' => Yii::t('app', 'Status masuk'),
            'umur' => Yii::t('app', 'Umur'),
            'no_asuransi' => Yii::t('app', 'No asuransi'),
            'namapemilik_asuransi' => Yii::t('app', 'Nama pemilik asuransi'),
            'nopokokperusahaan' => Yii::t('app', 'No pokok perusahaan'),
            'carabayar_id' => Yii::t('app', 'Cara bayar'),
            'carabayar_nama' => Yii::t('app', 'Nama cara bayar'),
            'penjamin_id' => Yii::t('app', 'Penjamin'),
            'penjamin_nama' => Yii::t('app', 'Nama penjamin'),
            'shift_id' => Yii::t('app', 'Shift ID'),
            'penanggungjawab_id' => Yii::t('app', 'Penanggung jawab'),
            'pengantar' => Yii::t('app', 'Pengantar'),
            'hubungankeluarga' => Yii::t('app', 'Hubungan keluarga'),
            'penanggungjawab_nama' => Yii::t('app', 'Nama penanggung jawab'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'ruangan_nama' => Yii::t('app', 'Nama ruangan'),
            'instalasi_id' => Yii::t('app', 'Instalasi'),
            'instalasi_nama' => Yii::t('app', 'Nama instalasi'),
            'jeniskasuspenyakit_id' => Yii::t('app', 'Jenis kasus penyakit'),
            'jeniskasuspenyakit_nama' => Yii::t('app', 'Nama jenis kasus penyakit'),
            'rujukan_id' => Yii::t('app', 'Rujukan'),
            'pasienpulang_id' => Yii::t('app', 'Pasien pulang'),
            'penerima_pasien' => Yii::t('app', 'Penerima pasien'),
            'lama_rawat' => Yii::t('app', 'Lama rawat'),
            'satuan_lamarawat' => Yii::t('app', 'Satuan lama rawat'),
            'tglpasienpulang' => Yii::t('app', 'Tanggal pasien pulang'),
            'pasienbatalpulang_id' => Yii::t('app', 'Pasien batal pulang'),
            'rujukankeluar_id' => Yii::t('app', 'Rujukan keluar'),
            'rumahsakit_rujukan' => Yii::t('app', 'Rujukan rumah sakit'),
            'alamat_rsrujukan' => Yii::t('app', 'Alamat rs rujukan'),
            'telp_fax' => Yii::t('app', 'Telp Fax'),
            'nosuratrujukan' => Yii::t('app', 'No surat rujukan'),
            'tgldirujuk' => Yii::t('app', 'Tanggal dirujuk'),
            'ythdokter' => Yii::t('app', 'Yth dokter'),
            'dirujukkebagian' => Yii::t('app', 'Dirujuk kebagian'),
            'alasandirujuk' => Yii::t('app', 'Alasan dirujuk'),
            'hasilpemeriksaan_ruj' => Yii::t('app', 'Hasil pemeriksaan rujukan'),
            'diagnosasementara_ruj' => Yii::t('app', 'Diagnosa sementara rujukan'),
            'pengobatan_ruj' => Yii::t('app', 'Pengobatan rujukan'),
            'lainlain_ruj' => Yii::t('app', 'Rujukan lainnya'),
            'catatandokterperujuk' => Yii::t('app', 'Catatan dokter perujuk'),
            'carakeluar_id' => Yii::t('app', 'Cara keluar'),
            'carakeluar' => Yii::t('app', 'Cara keluar'),
            'kondisikeluar_id' => Yii::t('app', 'Kondisi keluar'),
            'kondisipulang' => Yii::t('app', 'Kondisi pulang'),
            'tglcetakkartuasuransi' => Yii::t('app', 'Tanggal cetak kartu asuransi'),
            'kodefeskestk1' => Yii::t('app', 'Kode feskestk1'),
            'nama_feskestk1' => Yii::t('app', 'Nama feskestk1'),
            'masaberlakukartu' => Yii::t('app', 'Masa berlaku kartu'),
            'nokartukeluarga' => Yii::t('app', 'No kartu keluarga'),
            'nopassport' => Yii::t('app', 'No passport'),
            'status_konfirmasi' => Yii::t('app', 'Status konfirmasi'),
            'tgl_konfirmasi' => Yii::t('app', 'Tanggal Konfirmasi'),
            'is_active' => Yii::t('app', 'Is Active'),
        ];
    }
}
