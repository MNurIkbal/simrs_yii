<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasienpulangrddanri_v".
 *
 * @property int $pasien_id
 * @property string $jenisidentitas
 * @property string $no_identitas_pasien
 * @property string $namadepan
 * @property string $nama_pasien
 * @property string $nama_bin
 * @property string $jeniskelamin
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $alamat_pasien
 * @property int $rt
 * @property int $rw
 * @property string $agama
 * @property string $golongandarah
 * @property string $photopasien
 * @property string $alamatemail
 * @property string $statusrekammedis
 * @property string $statusperkawinan
 * @property string $no_rekam_medik
 * @property string $tgl_rekam_medik
 * @property int $kelurahan_id
 * @property string $kelurahan_nama
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property string $no_urutantri
 * @property string $transportasi
 * @property string $keadaan_masuk
 * @property string $status_periksa
 * @property string $status_pasien
 * @property string $kunjungan
 * @property bool $alih_status
 * @property bool $by_phone
 * @property bool $kunjungan_rumah
 * @property string $status_masuk
 * @property string $umur
 * @property string $no_asuransi
 * @property string $namapemilik_asuransi
 * @property string $nopokokperusahaan
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $shift_id
 * @property int $penanggungjawab_id
 * @property string $pengantar
 * @property string $hubungankeluarga
 * @property string $penanggungjawab_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property int $rujukan_id
 * @property int $pasienpulang_id
 * @property string $penerima_pasien
 * @property int $lama_rawat
 * @property string $satuan_lamarawat
 * @property string $tglpasienpulang
 * @property int $pasienbatalpulang_id
 * @property int $rujukankeluar_id
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
 * @property int $carakeluar_id
 * @property string $carakeluar
 * @property int $kondisikeluar_id
 * @property string $kondisipulang
 * @property string $tglcetakkartuasuransi
 * @property string $kodefeskestk1
 * @property string $nama_feskestk1
 * @property string $masaberlakukartu
 * @property string $nokartukeluarga
 * @property string $nopassport
 * @property string $status_konfirmasi
 * @property string $tgl_konfirmasi
 * @property bool $is_active
 */
class PasienPulangRdRiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasienpulangrddanri_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id', 'rt', 'rw', 'kelurahan_id', 'pendaftaran_id', 'carabayar_id', 'penjamin_id', 'shift_id', 'penanggungjawab_id', 'ruangan_id', 'instalasi_id', 'jeniskasuspenyakit_id', 'rujukan_id', 'pasienpulang_id', 'lama_rawat', 'pasienbatalpulang_id', 'rujukankeluar_id', 'carakeluar_id', 'kondisikeluar_id'], 'default', 'value' => null],
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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => 'Pasien ID',
            'jenisidentitas' => 'Jenisidentitas',
            'no_identitas_pasien' => 'No Identitas Pasien',
            'namadepan' => 'Namadepan',
            'nama_pasien' => 'Nama Pasien',
            'nama_bin' => 'Nama Bin',
            'jeniskelamin' => 'Jeniskelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'alamat_pasien' => 'Alamat Pasien',
            'rt' => 'Rt',
            'rw' => 'Rw',
            'agama' => 'Agama',
            'golongandarah' => 'Golongandarah',
            'photopasien' => 'Photopasien',
            'alamatemail' => 'Alamatemail',
            'statusrekammedis' => 'Statusrekammedis',
            'statusperkawinan' => 'Statusperkawinan',
            'no_rekam_medik' => 'No Rekam Medik',
            'tgl_rekam_medik' => 'Tgl Rekam Medik',
            'kelurahan_id' => 'Kelurahan ID',
            'kelurahan_nama' => 'Kelurahan Nama',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'no_urutantri' => 'No Urutantri',
            'transportasi' => 'Transportasi',
            'keadaan_masuk' => 'Keadaan Masuk',
            'status_periksa' => 'Status Periksa',
            'status_pasien' => 'Status Pasien',
            'kunjungan' => 'Kunjungan',
            'alih_status' => 'Alih Status',
            'by_phone' => 'By Phone',
            'kunjungan_rumah' => 'Kunjungan Rumah',
            'status_masuk' => 'Status Masuk',
            'umur' => 'Umur',
            'no_asuransi' => 'No Asuransi',
            'namapemilik_asuransi' => 'Namapemilik Asuransi',
            'nopokokperusahaan' => 'Nopokokperusahaan',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'shift_id' => 'Shift ID',
            'penanggungjawab_id' => 'Penanggungjawab ID',
            'pengantar' => 'Pengantar',
            'hubungankeluarga' => 'Hubungankeluarga',
            'penanggungjawab_nama' => 'Penanggungjawab Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'rujukan_id' => 'Rujukan ID',
            'pasienpulang_id' => 'Pasienpulang ID',
            'penerima_pasien' => 'Penerima Pasien',
            'lama_rawat' => 'Lama Rawat',
            'satuan_lamarawat' => 'Satuan Lamarawat',
            'tglpasienpulang' => 'Tglpasienpulang',
            'pasienbatalpulang_id' => 'Pasienbatalpulang ID',
            'rujukankeluar_id' => 'Rujukankeluar ID',
            'rumahsakit_rujukan' => 'Rumahsakit Rujukan',
            'alamat_rsrujukan' => 'Alamat Rsrujukan',
            'telp_fax' => 'Telp Fax',
            'nosuratrujukan' => 'Nosuratrujukan',
            'tgldirujuk' => 'Tgldirujuk',
            'ythdokter' => 'Ythdokter',
            'dirujukkebagian' => 'Dirujukkebagian',
            'alasandirujuk' => 'Alasandirujuk',
            'hasilpemeriksaan_ruj' => 'Hasilpemeriksaan Ruj',
            'diagnosasementara_ruj' => 'Diagnosasementara Ruj',
            'pengobatan_ruj' => 'Pengobatan Ruj',
            'lainlain_ruj' => 'Lainlain Ruj',
            'catatandokterperujuk' => 'Catatandokterperujuk',
            'carakeluar_id' => 'Carakeluar ID',
            'carakeluar' => 'Carakeluar',
            'kondisikeluar_id' => 'Kondisikeluar ID',
            'kondisipulang' => 'Kondisipulang',
            'tglcetakkartuasuransi' => 'Tglcetakkartuasuransi',
            'kodefeskestk1' => 'Kodefeskestk1',
            'nama_feskestk1' => 'Nama Feskestk1',
            'masaberlakukartu' => 'Masaberlakukartu',
            'nokartukeluarga' => 'Nokartukeluarga',
            'nopassport' => 'Nopassport',
            'status_konfirmasi' => 'Status Konfirmasi',
            'tgl_konfirmasi' => 'Tgl Konfirmasi',
            'is_active' => 'Is Active',
        ];
    }
}
