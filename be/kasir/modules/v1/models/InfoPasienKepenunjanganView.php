<?php

namespace app\modules\v1\models;

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
class InfoPasienKepenunjanganView extends \Doco\components\DocoActiveRecord
{
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
            'tgl_rekam_medik' => Yii::t('app', 'Tanggal rekam medik'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran'),
            'tgl_pendaftaran' => Yii::t('app', 'Tanggal pendaftaran'),
            'keadaan_masuk' => Yii::t('app', 'Keadaan masuk'),
            'status_pasien' => Yii::t('app', 'Status pasien'),
            'alih_status' => Yii::t('app', 'Alih status'),
            'status_masuk' => Yii::t('app', 'Status masuk'),
            'umur' => Yii::t('app', 'Umur'),
            'no_asuransi' => Yii::t('app', 'No asuransi'),
            'namapemilik_asuransi' => Yii::t('app', 'Nama pemilik asuransi'),
            'nopokokperusahaan' => Yii::t('app', 'No pokok perusahaan'),
            'carabayar_id' => Yii::t('app', 'Cara bayar'),
            'carabayar_nama' => Yii::t('app', 'Nama cara bayar'),
            'penjamin_id' => Yii::t('app', 'Penjamin'),
            'penjamin_nama' => Yii::t('app', 'Nama penjamin'),
            'shift_id' => Yii::t('app', 'Shift'),
            'kelaspelayanan_id' => Yii::t('app', 'Kelas pelayanan'),
            'kelaspelayanan_nama' => Yii::t('app', 'Nama kelas pelayanan'),
            'gelarbelakang_nama' => Yii::t('app', 'Nama gelar belakang'),
            'no_masukpenunjang' => Yii::t('app', 'No masuk penunjang'),
            'tglmasukpenunjang' => Yii::t('app', 'Tanggal Masuk Penunjang'),
            'no_urutperiksa' => Yii::t('app', 'No urut periksa'),
            'kunjungan' => Yii::t('app', 'Kunjungan'),
            'status_periksa' => Yii::t('app', 'Status periksa'),
            'instalasi_id' => Yii::t('app', 'Instalasi'),
            'instalasi_nama' => Yii::t('app', 'Nama instalasi'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'ruangan_nama' => Yii::t('app', 'Nama ruangan'),
            'pasienadmisi_id' => Yii::t('app', 'Pasien admisi'),
            'pasienmasukpenunjang_id' => Yii::t('app', 'Pasien masuk penunjang'),
            'gelardepan' => Yii::t('app', 'Gelar depan'),
            'nama_pegawai' => Yii::t('app', 'Nama pegawai'),
            'pegawai_id' => Yii::t('app', 'Pegawai'),
            'no_rekam_medik' => Yii::t('app', 'No rekam medik'),
            'no_pendaftaran' => Yii::t('app', 'No pendaftaran'),
            'kelompokumur_id' => Yii::t('app', 'Kelompok umur'),
            'golonganumur_id' => Yii::t('app', 'Golongan umur'),
            'pasienkirimkeunitlain_id' => Yii::t('app', 'Pasien kirim ke unit lain'),
            'tgl_kirimpasien' => Yii::t('app', 'Tanggal kirim pasien'),
            'nopeserta' => Yii::t('app', 'No peserta'),
            'tglcetakkartuasuransi' => Yii::t('app', 'Tgl cetak kartu asuransi'),
            'kodefeskestk1' => Yii::t('app', 'Kode fasilitas kesehatan tingkat satu'),
            'nama_feskestk1' => Yii::t('app', 'Nama fasilitas kesehatan tingkat satu'),
            'masaberlakukartu' => Yii::t('app', 'Masa berlaku kartu'),
            'nokartukeluarga' => Yii::t('app', 'No kartu keluarga'),
            'nopassport' => Yii::t('app', 'No passport'),
            'status_konfirmasi' => Yii::t('app', 'Status konfirmasi'),
            'tgl_konfirmasi' => Yii::t('app', 'Tanggal konfirmasi'),
            'is_active' => Yii::t('app', 'Is Active'),
        ];
    }
}
