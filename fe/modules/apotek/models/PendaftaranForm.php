<?php

namespace Doco\apotek\models;

use Yii;

class PendaftaranForm extends \yii\base\Model
{
    public $pendaftaran_id;
    public $no_pendaftaran;
    public $tgl_pendaftaran;
    public $pasienpulang_id;
    public $pasienbatalperiksa_id;
    public $penanggungjawab_id;
    public $penjamin_id;
    public $shift_id;
    public $pasien_id;
    public $persalinan_id;
    public $pegawai_id;
    public $instalasi_id;
    public $caramasuk_id;
    public $pengirimanrm_id;
    public $peminjamanrm_id;
    public $jeniskasuspenyakit_id;
    public $pembayaranpelayanan_id;
    public $kelaspelayanan_id;
    public $carabayar_id;
    public $pasienadmisi_id;
    public $kelompokumur_id;
    public $golonganumur_id;
    public $rujukan_id;
    public $antrian_id;
    public $karcis_id;
    public $ruangan_id;
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
    public $tgl_selesaiperiksa;
    public $keterangan_pendaftaran;
    public $nopendaftaran_aktif;
    public $status_konfirmasi;
    public $tgl_konfirmasi;
    public $tgl_renkontrol;
    public $status_farmasi;
    public $panggil_antrian;
    public $asuransipasien_id;
    public $tgl_akandilayani;
    public $statusdok_rekammedik;
    public $sep_id;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $tgl_masukperiksa;

    /**
     * @inheritdoc
     */
    /*public static function tableName()
    {
        return 'pendaftaran_t';
    }*/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pegawai_id'], 'required', 'on' => 'set-dokter-mcu'],
            [['no_pendaftaran', 'tgl_pendaftaran', 'status_pasien'], 'required', 'on' => 'default'],
            [['tgl_pendaftaran', 'tgl_selesaiperiksa', 'tgl_konfirmasi', 'tgl_renkontrol', 'tgl_akandilayani', 'created_date', 'last_modified_date', 'deleted_date', 'status_periksa'], 'safe'],
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
            'tgl_masukperiksa' => Yii::t('fe', 'Tanggal masuk periksa'),
            'pendaftaran_id' => Yii::t('fe', 'pendaftaran'),
            'no_pendaftaran' => Yii::t('fe', 'no_pendaftaran'),
            'tgl_pendaftaran' => Yii::t('fe', 'tgl_pendaftaran'),
            'pasienpulang_id' => Yii::t('fe', 'pasienpulang_id'),
            'pasienbatalperiksa_id' => Yii::t('fe', 'pasienbatalperiksa_id'),
            'penanggungjawab_id' => Yii::t('fe', 'penanggungjawab_id'),
            'penjamin_id' => Yii::t('fe', 'penjamin_id'),
            'shift_id' => Yii::t('fe', 'shift_id'),
            'pasien_id' => Yii::t('fe', 'pasien_id'),
            'persalinan_id' => Yii::t('fe', 'persalinan_id'),
            'pegawai_id' => Yii::t('fe', 'Nama Dokter'),
            'instalasi_id' => Yii::t('fe', 'instalasi_id'),
            'caramasuk_id' => Yii::t('fe', 'caramasuk_id'),
            'pengirimanrm_id' => Yii::t('fe', 'pengirimanrm_id'),
            'peminjamanrm_id' => Yii::t('fe', 'peminjamanrm_id'),
            'jeniskasuspenyakit_id' => Yii::t('fe', 'jeniskasuspenyakit_id'),
            'pembayaranpelayanan_id' => Yii::t('fe', 'pembayaranpelayanan_id'),
            'kelaspelayanan_id' => Yii::t('fe', 'kelaspelayanan_id'),
            'carabayar_id' => Yii::t('fe', 'carabayar_id'),
            'pasienadmisi_id' => Yii::t('fe', 'pasienadmisi_id'),
            'kelompokumur_id' => Yii::t('fe', 'kelompokumur_id'),
            'golonganumur_id' => Yii::t('fe', 'golonganumur_id'),
            'rujukan_id' => Yii::t('fe', 'rujukan_id'),
            'antrian_id' => Yii::t('fe', 'antrian_id'),
            'karcis_id' => Yii::t('fe', 'karcis_id'),
            'ruangan_id' => Yii::t('fe', 'ruangan_id'),
            'no_urutantri' => Yii::t('fe', 'no_urutantri'),
            'transportasi' => Yii::t('fe', 'transportasi'),
            'keadaan_masuk' => Yii::t('fe', 'keadaan_masuk'),
            'status_periksa' => Yii::t('fe', 'status_periksa'),
            'status_pasien' => Yii::t('fe', 'status_pasien'),
            'kunjungan' => Yii::t('fe', 'kunjungan'),
            'alih_status' => Yii::t('fe', 'alih_status'),
            'by_phone' => Yii::t('fe', 'by_phone'),
            'kunjungan_rumah' => Yii::t('fe', 'kunjungan_rumah'),
            'status_masuk' => Yii::t('fe', 'status_masuk'),
            'umur' => Yii::t('fe', 'umur'),
            'tgl_selesaiperiksa' => Yii::t('fe', 'tgl_selesaiperiksa'),
            'keterangan_pendaftaran' => Yii::t('fe', 'keterangan_pendaftaran'),
            'nopendaftaran_aktif' => Yii::t('fe', 'nopendaftaran_aktif'),
            'status_konfirmasi' => Yii::t('fe', 'status_konfirmasi'),
            'tgl_konfirmasi' => Yii::t('fe', 'tgl_konfirmasi'),
            'tgl_renkontrol' => Yii::t('fe', 'tgl_renkontrol'),
            'status_farmasi' => Yii::t('fe', 'status_farmasi'),
            'panggil_antrian' => Yii::t('fe', 'panggil_antrian'),
            'asuransipasien_id' => Yii::t('fe', 'asuransipasien_id'),
            'tgl_akandilayani' => Yii::t('fe', 'tgl_akandilayani'),
            'statusdok_rekammedik' => Yii::t('fe', 'statusdok_rekammedik'),
            'sep_id' => Yii::t('fe', 'sep_id'),
            'additional_data' => Yii::t('fe', 'additional_data'),
            'created_date' => Yii::t('fe', 'created_date'),
            'created_by' => Yii::t('fe', 'created_by'),
            'modified_count' => Yii::t('fe', 'modified_count'),
            'last_modified_date' => Yii::t('fe', 'last_modified_date'),
            'last_modified_by' => Yii::t('fe', 'last_modified_by'),
            'is_deleted' => Yii::t('fe', 'is_deleted'),
            'is_active' => Yii::t('fe', 'is_active'),
            'deleted_date' => Yii::t('fe', 'deleted_date'),
            'deleted_by' => Yii::t('fe', 'deleted_by'),
        ];
    }
}