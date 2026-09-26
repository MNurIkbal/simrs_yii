<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "pendaftaran_t".
 *
 * @property integer $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property integer $pasienpulang_id
 * @property integer $pasienbatalperiksa_id
 * @property integer $penanggungjawab_id
 * @property integer $penjamin_id
 * @property integer $shift_id
 * @property integer $pasien_id
 * @property integer $persalinan_id
 * @property integer $pegawai_id
 * @property integer $instalasi_id
 * @property integer $caramasuk_id
 * @property integer $pengirimanrm_id
 * @property integer $peminjamanrm_id
 * @property integer $jeniskasuspenyakit_id
 * @property integer $pembayaranpelayanan_id
 * @property integer $kelaspelayanan_id
 * @property integer $carabayar_id
 * @property integer $pasienadmisi_id
 * @property integer $kelompokumur_id
 * @property integer $golonganumur_id
 * @property integer $rujukan_id
 * @property integer $antrian_id
 * @property integer $karcis_id
 * @property integer $ruangan_id
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
 * @property string $tgl_selesaiperiksa
 * @property string $keterangan_pendaftaran
 * @property boolean $nopendaftaran_aktif
 * @property string $status_konfirmasi
 * @property string $tgl_konfirmasi
 * @property string $tgl_renkontrol
 * @property boolean $status_farmasi
 * @property boolean $panggil_antrian
 * @property integer $asuransipasien_id
 * @property string $tgl_akandilayani
 * @property string $statusdok_rekammedik
 * @property integer $sep_id
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class Pendaftaran extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pendaftaran_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_pendaftaran', 'tgl_pendaftaran', 'status_pasien'], 'required'],
            [['tgl_pendaftaran', 'tgl_selesaiperiksa', 'tgl_konfirmasi', 'tgl_renkontrol', 'tgl_akandilayani', 'created_date', 'last_modified_date', 'deleted_date', 'status_periksa', 'is_stopakomodasi', 'tgl_stopakomodasi'], 'safe'],
            [['pasienpulang_id', 'pasienbatalperiksa_id', 'penanggungjawab_id', 'penjamin_id', 'shift_id', 'pasien_id', 'persalinan_id', 'pegawai_id', 'instalasi_id', 'caramasuk_id', 'jeniskasuspenyakit_id', 'pembayaranpelayanan_id', 'kelaspelayanan_id', 'carabayar_id', 'pasienadmisi_id', 'golonganumur_id', 'rujukan_id', 'antrian_id', 'karcis_id', 'ruangan_id', 'asuransipasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['alih_status', 'by_phone', 'kunjungan_rumah', 'nopendaftaran_aktif', 'status_farmasi', 'panggil_antrian', 'is_deleted', 'is_active'], 'boolean'],
            [['keterangan_pendaftaran', 'additional_data'], 'string'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_urutantri'], 'string', 'max' => 6],
            [['transportasi', 'keadaan_masuk', 'status_pasien', 'kunjungan', 'status_masuk', 'status_konfirmasi', 'statusdok_rekammedik'], 'string', 'max' => 50],
            [['umur'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran'),
            'no_pendaftaran' => Yii::t('app', 'No pendaftaran'),
            'tgl_pendaftaran' => Yii::t('app', 'Tanggal pendaftaran'),
            'pasienpulang_id' => Yii::t('app', 'Pasien pulang'),
            'pasienbatalperiksa_id' => Yii::t('app', 'Pasien batal periksa'),
            'penanggungjawab_id' => Yii::t('app', 'Penanggung jawab'),
            'penjamin_id' => Yii::t('app', 'Penjamin'),
            'shift_id' => Yii::t('app', 'Shift'),
            'pasien_id' => Yii::t('app', 'Pasien'),
            'persalinan_id' => Yii::t('app', 'Persalinan'),
            'pegawai_id' => Yii::t('app', 'Pegawai'),
            'instalasi_id' => Yii::t('app', 'Instalasi'),
            'caramasuk_id' => Yii::t('app', 'Cara masuk'),
            'pengirimanrm_id' => Yii::t('app', 'Pengiriman rm'),
            'peminjamanrm_id' => Yii::t('app', 'Peminjaman rm'),
            'jeniskasuspenyakit_id' => Yii::t('app', 'Jenis kasus penyakit'),
            'pembayaranpelayanan_id' => Yii::t('app', 'Pembayaran pelayanan'),
            'kelaspelayanan_id' => Yii::t('app', 'Kelas pelayanan'),
            'carabayar_id' => Yii::t('app', 'Cara bayar'),
            'pasienadmisi_id' => Yii::t('app', 'Pasien admisi'),
            'kelompokumur_id' => Yii::t('app', 'Kelompok umur'),
            'golonganumur_id' => Yii::t('app', 'Golongan umur'),
            'rujukan_id' => Yii::t('app', 'Rujukan'),
            'antrian_id' => Yii::t('app', 'Antrian'),
            'karcis_id' => Yii::t('app', 'Karcis'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
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
            'tgl_selesaiperiksa' => Yii::t('app', 'Tanggal Selesai periksa'),
            'keterangan_pendaftaran' => Yii::t('app', 'Keterangan pendaftaran'),
            'nopendaftaran_aktif' => Yii::t('app', 'No pendaftaran aktif'),
            'status_konfirmasi' => Yii::t('app', 'Status konfirmasi'),
            'tgl_konfirmasi' => Yii::t('app', 'Tanggal konfirmasi'),
            'tgl_renkontrol' => Yii::t('app', 'Tanggal renkontrol'),
            'status_farmasi' => Yii::t('app', 'Status farmasi'),
            'panggil_antrian' => Yii::t('app', 'Panggil antrian'),
            'asuransipasien_id' => Yii::t('app', 'Asuransi pasien'),
            'tgl_akandilayani' => Yii::t('app', 'Tanggal akan dilayani'),
            'statusdok_rekammedik' => Yii::t('app', 'Status dok rekam medik'),
            'sep_id' => Yii::t('app', 'Sep'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }
}
