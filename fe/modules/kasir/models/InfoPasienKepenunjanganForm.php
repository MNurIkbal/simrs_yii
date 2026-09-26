<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "informasipasienkepenunj_v".
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
 * @property string $tgl_rekam_medik
 * @property integer $pendaftaran_id
 * @property string $tgl_pendaftaran
 * @property string $keadaan_masuk
 * @property string $status_pasien
 * @property boolean $alih_status
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
 * @property integer $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property string $gelarbelakang_nama
 * @property string $no_masukpenunjang
 * @property string $tglmasukpenunjang
 * @property string $no_urutperiksa
 * @property string $kunjungan
 * @property string $status_periksa
 * @property integer $ruangan_id
 * @property string $ruangan_nama
 * @property integer $pasienadmisi_id
 * @property integer $pasienmasukpenunjang_id
 * @property string $gelardepan
 * @property string $nama_pegawai
 * @property integer $pegawai_id
 * @property string $no_rekam_medik
 * @property string $no_pendaftaran
 * @property integer $kelompokumur_id
 * @property integer $golonganumur_id
 * @property integer $pasienkirimkeunitlain_id
 * @property string $tgl_kirimpasien
 * @property string $nopeserta
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
class InfoPasienKepenunjanganForm extends \yii\base\Model
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
    public $tgl_rekam_medik;
    public $pendaftaran_id;
    public $tgl_pendaftaran;
    public $keadaan_masuk;
    public $status_pasien;
    public $alih_status;
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
    public $kelaspelayanan_id;
    public $kelaspelayanan_nama;
    public $gelarbelakang_nama;
    public $no_masukpenunjang;
    public $tglmasukpenunjang;
    public $no_urutperiksa;
    public $kunjungan;
    public $status_periksa;
    public $instalasi_id;
    public $instalasi_nama;
    public $ruangan_id;
    public $ruangan_nama;
    public $pasienadmisi_id;
    public $pasienmasukpenunjang_id;
    public $gelardepan;
    public $nama_pegawai;
    public $pegawai_id;
    public $no_rekam_medik;
    public $no_pendaftaran;
    public $kelompokumur_id;
    public $golonganumur_id;
    public $pasienkirimkeunitlain_id;
    public $tgl_kirimpasien;
    public $nopeserta;
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
        return 'informasipasienkepenunj_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasien_id', 'rt', 'rw', 'pendaftaran_id', 'carabayar_id', 'penjamin_id', 'shift_id', 'kelaspelayanan_id', 'instalasi_id', 'ruangan_id', 'pasienadmisi_id', 'pasienmasukpenunjang_id', 'pegawai_id', 'kelompokumur_id', 'golonganumur_id', 'pasienkirimkeunitlain_id'], 'integer'],
            [['tanggal_lahir', 'tgl_rekam_medik', 'tgl_pendaftaran', 'tglmasukpenunjang', 'tgl_kirimpasien', 'tglcetakkartuasuransi', 'masaberlakukartu', 'tgl_konfirmasi'], 'safe'],
            [['alamat_pasien'], 'string'],
            [['alih_status', 'is_active'], 'boolean'],
            [['jenisidentitas', 'namadepan', 'jeniskelamin', 'agama', 'statusperkawinan', 'no_masukpenunjang', 'no_pendaftaran'], 'string', 'max' => 20],
            [['no_identitas_pasien', 'nama_bin', 'umur'], 'string', 'max' => 30],
            [['nama_pasien', 'keadaan_masuk', 'status_pasien', 'status_masuk', 'no_asuransi', 'namapemilik_asuransi', 'nopokokperusahaan', 'carabayar_nama', 'penjamin_nama', 'kelaspelayanan_nama', 'kunjungan', 'status_periksa', 'instalasi_nama', 'ruangan_nama', 'nama_pegawai', 'nopeserta', 'kodefeskestk1', 'status_konfirmasi'], 'string', 'max' => 50],
            [['tempat_lahir'], 'string', 'max' => 25],
            [['golongandarah'], 'string', 'max' => 2],
            [['photopasien', 'nama_feskestk1', 'nopassport'], 'string', 'max' => 200],
            [['alamatemail', 'nokartukeluarga'], 'string', 'max' => 100],
            [['statusrekammedis', 'gelardepan', 'no_rekam_medik'], 'string', 'max' => 10],
            [['gelarbelakang_nama'], 'string', 'max' => 15],
            [['no_urutperiksa'], 'string', 'max' => 3],
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
            'namadepan' => Yii::t('fe', 'Nama depan'),
            'nama_pasien' => Yii::t('fe', 'Nama pasien'),
            'nama_bin' => Yii::t('fe', 'Nama bin'),
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
            'tgl_rekam_medik' => Yii::t('fe', 'Tanggal rekam medik'),
            'pendaftaran_id' => Yii::t('fe', 'Pendaftaran'),
            'tgl_pendaftaran' => Yii::t('fe', 'Tanggal pendaftaran'),
            'keadaan_masuk' => Yii::t('fe', 'Keadaan masuk'),
            'status_pasien' => Yii::t('fe', 'Status pasien'),
            'alih_status' => Yii::t('fe', 'Alih status'),
            'status_masuk' => Yii::t('fe', 'Status masuk'),
            'umur' => Yii::t('fe', 'Umur'),
            'no_asuransi' => Yii::t('fe', 'No asuransi'),
            'namapemilik_asuransi' => Yii::t('fe', 'Nama pemilik asuransi'),
            'nopokokperusahaan' => Yii::t('fe', 'No pokok perusahaan'),
            'carabayar_id' => Yii::t('fe', 'Cara bayar'),
            'carabayar_nama' => Yii::t('fe', 'Nama cara bayar'),
            'penjamin_id' => Yii::t('fe', 'Penjamin'),
            'penjamin_nama' => Yii::t('fe', 'Nama penjamin'),
            'shift_id' => Yii::t('fe', 'Shift'),
            'kelaspelayanan_id' => Yii::t('fe', 'Kelas pelayanan'),
            'kelaspelayanan_nama' => Yii::t('fe', 'Nama kelas pelayanan'),
            'gelarbelakang_nama' => Yii::t('fe', 'Nama gelar belakang'),
            'no_masukpenunjang' => Yii::t('fe', 'No masuk penunjang'),
            'tglmasukpenunjang' => Yii::t('fe', 'Tanggal masuk penunjang'),
            'no_urutperiksa' => Yii::t('fe', 'No urut periksa'),
            'kunjungan' => Yii::t('fe', 'Kunjungan'),
            'status_periksa' => Yii::t('fe', 'Status periksa'),
            'instalasi_id' => Yii::t('fe', 'Instalasi'),
            'instalasi_nama' => Yii::t('fe', 'Nama instalasi'),
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'ruangan_nama' => Yii::t('fe', 'Nama ruangan'),
            'pasienadmisi_id' => Yii::t('fe', 'Pasien admisi'),
            'pasienmasukpenunjang_id' => Yii::t('fe', 'Pasien masuk penunjang'),
            'gelardepan' => Yii::t('fe', 'Gelar depan'),
            'nama_pegawai' => Yii::t('fe', 'Nama pegawai'),
            'pegawai_id' => Yii::t('fe', 'Pegawai'),
            'no_rekam_medik' => Yii::t('fe', 'No rekam medik'),
            'no_pendaftaran' => Yii::t('fe', 'No pendaftaran'),
            'kelompokumur_id' => Yii::t('fe', 'Kelompok umur'),
            'golonganumur_id' => Yii::t('fe', 'Golongan umur'),
            'pasienkirimkeunitlain_id' => Yii::t('fe', 'Pasien kirim ke unit lain'),
            'tgl_kirimpasien' => Yii::t('fe', 'Tanggal kirim pasien'),
            'nopeserta' => Yii::t('fe', 'No peserta'),
            'tglcetakkartuasuransi' => Yii::t('fe', 'Tgl cetak kartu asuransi'),
            'kodefeskestk1' => Yii::t('fe', 'Kode fasilitas kesehatan tingkat satu'),
            'nama_feskestk1' => Yii::t('fe', 'Nama fasilitas kesehatan tingkat satu'),
            'masaberlakukartu' => Yii::t('fe', 'Masa berlaku kartu'),
            'nokartukeluarga' => Yii::t('fe', 'No kartu keluarga'),
            'nopassport' => Yii::t('fe', 'No passport'),
            'status_konfirmasi' => Yii::t('fe', 'Status konfirmasi'),
            'tgl_konfirmasi' => Yii::t('fe', 'Tanggal konfirmasi'),
            'is_active' => Yii::t('fe', 'Is Active'),
        ];
    }
}
