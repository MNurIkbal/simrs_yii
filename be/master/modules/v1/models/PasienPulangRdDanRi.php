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
class PasienPulangRdDanRi extends \Doco\components\DocoActiveRecord
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
            'pasien_id' => Yii::t('app', 'Pasien ID'),
            'jenisidentitas' => Yii::t('app', 'Jenisidentitas'),
            'no_identitas_pasien' => Yii::t('app', 'No Identitas Pasien'),
            'namadepan' => Yii::t('app', 'Namadepan'),
            'nama_pasien' => Yii::t('app', 'Nama Pasien'),
            'nama_bin' => Yii::t('app', 'Nama Bin'),
            'jeniskelamin' => Yii::t('app', 'Jeniskelamin'),
            'tempat_lahir' => Yii::t('app', 'Tempat Lahir'),
            'tanggal_lahir' => Yii::t('app', 'Tanggal Lahir'),
            'alamat_pasien' => Yii::t('app', 'Alamat Pasien'),
            'rt' => Yii::t('app', 'Rt'),
            'rw' => Yii::t('app', 'Rw'),
            'agama' => Yii::t('app', 'Agama'),
            'golongandarah' => Yii::t('app', 'Golongandarah'),
            'photopasien' => Yii::t('app', 'Photopasien'),
            'alamatemail' => Yii::t('app', 'Alamatemail'),
            'statusrekammedis' => Yii::t('app', 'Statusrekammedis'),
            'statusperkawinan' => Yii::t('app', 'Statusperkawinan'),
            'no_rekam_medik' => Yii::t('app', 'No Rekam Medik'),
            'tgl_rekam_medik' => Yii::t('app', 'Tgl Rekam Medik'),
            'kelurahan_id' => Yii::t('app', 'Kelurahan ID'),
            'kelurahan_nama' => Yii::t('app', 'Kelurahan Nama'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran ID'),
            'no_pendaftaran' => Yii::t('app', 'No Pendaftaran'),
            'tgl_pendaftaran' => Yii::t('app', 'Tgl Pendaftaran'),
            'no_urutantri' => Yii::t('app', 'No Urutantri'),
            'transportasi' => Yii::t('app', 'Transportasi'),
            'keadaan_masuk' => Yii::t('app', 'Keadaan Masuk'),
            'status_periksa' => Yii::t('app', 'Status Periksa'),
            'status_pasien' => Yii::t('app', 'Status Pasien'),
            'kunjungan' => Yii::t('app', 'Kunjungan'),
            'alih_status' => Yii::t('app', 'Alih Status'),
            'by_phone' => Yii::t('app', 'By Phone'),
            'kunjungan_rumah' => Yii::t('app', 'Kunjungan Rumah'),
            'status_masuk' => Yii::t('app', 'Status Masuk'),
            'umur' => Yii::t('app', 'Umur'),
            'no_asuransi' => Yii::t('app', 'No Asuransi'),
            'namapemilik_asuransi' => Yii::t('app', 'Namapemilik Asuransi'),
            'nopokokperusahaan' => Yii::t('app', 'Nopokokperusahaan'),
            'carabayar_id' => Yii::t('app', 'Carabayar ID'),
            'carabayar_nama' => Yii::t('app', 'Carabayar Nama'),
            'penjamin_id' => Yii::t('app', 'Penjamin ID'),
            'penjamin_nama' => Yii::t('app', 'Penjamin Nama'),
            'shift_id' => Yii::t('app', 'Shift ID'),
            'penanggungjawab_id' => Yii::t('app', 'Penanggungjawab ID'),
            'pengantar' => Yii::t('app', 'Pengantar'),
            'hubungankeluarga' => Yii::t('app', 'Hubungankeluarga'),
            'penanggungjawab_nama' => Yii::t('app', 'Penanggungjawab Nama'),
            'ruangan_id' => Yii::t('app', 'Ruangan ID'),
            'ruangan_nama' => Yii::t('app', 'Ruangan Nama'),
            'instalasi_id' => Yii::t('app', 'Instalasi ID'),
            'instalasi_nama' => Yii::t('app', 'Instalasi Nama'),
            'jeniskasuspenyakit_id' => Yii::t('app', 'Jeniskasuspenyakit ID'),
            'jeniskasuspenyakit_nama' => Yii::t('app', 'Jeniskasuspenyakit Nama'),
            'rujukan_id' => Yii::t('app', 'Rujukan ID'),
            'pasienpulang_id' => Yii::t('app', 'Pasienpulang ID'),
            'penerima_pasien' => Yii::t('app', 'Penerima Pasien'),
            'lama_rawat' => Yii::t('app', 'Lama Rawat'),
            'satuan_lamarawat' => Yii::t('app', 'Satuan Lamarawat'),
            'tglpasienpulang' => Yii::t('app', 'Tglpasienpulang'),
            'pasienbatalpulang_id' => Yii::t('app', 'Pasienbatalpulang ID'),
            'rujukankeluar_id' => Yii::t('app', 'Rujukankeluar ID'),
            'rumahsakit_rujukan' => Yii::t('app', 'Rumahsakit Rujukan'),
            'alamat_rsrujukan' => Yii::t('app', 'Alamat Rsrujukan'),
            'telp_fax' => Yii::t('app', 'Telp Fax'),
            'nosuratrujukan' => Yii::t('app', 'Nosuratrujukan'),
            'tgldirujuk' => Yii::t('app', 'Tgldirujuk'),
            'ythdokter' => Yii::t('app', 'Ythdokter'),
            'dirujukkebagian' => Yii::t('app', 'Dirujukkebagian'),
            'alasandirujuk' => Yii::t('app', 'Alasandirujuk'),
            'hasilpemeriksaan_ruj' => Yii::t('app', 'Hasilpemeriksaan Ruj'),
            'diagnosasementara_ruj' => Yii::t('app', 'Diagnosasementara Ruj'),
            'pengobatan_ruj' => Yii::t('app', 'Pengobatan Ruj'),
            'lainlain_ruj' => Yii::t('app', 'Lainlain Ruj'),
            'catatandokterperujuk' => Yii::t('app', 'Catatandokterperujuk'),
            'carakeluar_id' => Yii::t('app', 'Carakeluar ID'),
            'carakeluar' => Yii::t('app', 'Carakeluar'),
            'kondisikeluar_id' => Yii::t('app', 'Kondisikeluar ID'),
            'kondisipulang' => Yii::t('app', 'Kondisipulang'),
            'tglcetakkartuasuransi' => Yii::t('app', 'Tglcetakkartuasuransi'),
            'kodefeskestk1' => Yii::t('app', 'Kodefeskestk1'),
            'nama_feskestk1' => Yii::t('app', 'Nama Feskestk1'),
            'masaberlakukartu' => Yii::t('app', 'Masaberlakukartu'),
            'nokartukeluarga' => Yii::t('app', 'Nokartukeluarga'),
            'nopassport' => Yii::t('app', 'Nopassport'),
            'status_konfirmasi' => Yii::t('app', 'Status Konfirmasi'),
            'tgl_konfirmasi' => Yii::t('app', 'Tgl Konfirmasi'),
            'is_active' => Yii::t('app', 'Is Active'),
        ];
    }
}
