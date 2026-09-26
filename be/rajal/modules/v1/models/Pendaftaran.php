<?php

namespace app\modules\v1\models;

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
            [['tgl_pendaftaran', 'tgl_selesaiperiksa', 'tgl_konfirmasi', 'tgl_renkontrol', 'tgl_akandilayani', 'created_date', 'last_modified_date', 'deleted_date', 'status_periksa'], 'safe'],
            [['pasienpulang_id', 'pasienbatalperiksa_id', 'penanggungjawab_id', 'penjamin_id', 'shift_id', 'pasien_id', 'persalinan_id', 'pegawai_id', 'instalasi_id', 'caramasuk_id', 'jeniskasuspenyakit_id', 'pembayaranpelayanan_id', 'kelaspelayanan_id', 'carabayar_id', 'pasienadmisi_id', 'golonganumur_id', 'rujukan_id', 'antrian_id', 'karcis_id', 'ruangan_id', 'asuransipasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['alih_status', 'by_phone', 'kunjungan_rumah', 'nopendaftaran_aktif', 'status_farmasi', 'panggil_antrian', 'is_deleted', 'is_active'], 'boolean'],
            [['keterangan_pendaftaran', 'additional_data'], 'string'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_urutantri'], 'string', 'max' => 6],
            [['transportasi', 'keadaan_masuk', 'status_pasien', 'kunjungan', 'status_masuk', 'status_konfirmasi', 'statusdok_rekammedik'], 'string', 'max' => 50],
            [['umur'], 'string', 'max' => 30],
            // [['antrian_id'], 'exist', 'skipOnError' => true, 'targetClass' => AntrianT::className(), 'targetAttribute' => ['antrian_id' => 'antrian_id']],
            // [['carabayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarabayarM::className(), 'targetAttribute' => ['carabayar_id' => 'carabayar_id']],
            // [['caramasuk_id'], 'exist', 'skipOnError' => true, 'targetClass' => CaramasukM::className(), 'targetAttribute' => ['caramasuk_id' => 'caramasuk_id']],
            // [['golonganumur_id'], 'exist', 'skipOnError' => true, 'targetClass' => GolonganumurM::className(), 'targetAttribute' => ['golonganumur_id' => 'golonganumur_id']],
            // [['instalasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => InstalasiM::className(), 'targetAttribute' => ['instalasi_id' => 'instalasi_id']],
            // [['jeniskasuspenyakit_id'], 'exist', 'skipOnError' => true, 'targetClass' => JeniskasuspenyakitM::className(), 'targetAttribute' => ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']],
            // [['karcis_id'], 'exist', 'skipOnError' => true, 'targetClass' => KarcisM::className(), 'targetAttribute' => ['karcis_id' => 'karcis_id']],
            // [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelaspelayananM::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            // [['kelompokumur_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelompokumurM::className(), 'targetAttribute' => ['kelompokumur_id' => 'kelompokumur_id']],
            // [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pasien::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            // [['pasienadmisi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienadmisiT::className(), 'targetAttribute' => ['pasienadmisi_id' => 'pasienadmisi_id']],
            // [['pasienbatalperiksa_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienbatalperiksaT::className(), 'targetAttribute' => ['pasienbatalperiksa_id' => 'pasienbatalperiksa_id']],
            // [['pasienpulang_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienpulangT::className(), 'targetAttribute' => ['pasienpulang_id' => 'pasienpulang_id']],
            // [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            // [['pembayaranpelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => PembayaranpelayananT::className(), 'targetAttribute' => ['pembayaranpelayanan_id' => 'pembayaranpelayanan_id']],
            // [['peminjamanrm_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeminjamanrmT::className(), 'targetAttribute' => ['peminjamanrm_id' => 'peminjamanrm_id']],
            // [['penanggungjawab_id'], 'exist', 'skipOnError' => true, 'targetClass' => PenanggungjawabM::className(), 'targetAttribute' => ['penanggungjawab_id' => 'penanggungjawab_id']],
            // [['pengirimanrm_id'], 'exist', 'skipOnError' => true, 'targetClass' => PengirimanrmT::className(), 'targetAttribute' => ['pengirimanrm_id' => 'pengirimanrm_id']],
            // [['penjamin_id'], 'exist', 'skipOnError' => true, 'targetClass' => PenjaminM::className(), 'targetAttribute' => ['penjamin_id' => 'penjamin_id']],
            // [['persalinan_id'], 'exist', 'skipOnError' => true, 'targetClass' => PersalinanT::className(), 'targetAttribute' => ['persalinan_id' => 'persalinan_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
            // [['rujukan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RujukanT::className(), 'targetAttribute' => ['rujukan_id' => 'rujukan_id']],
            // [['shift_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShiftM::className(), 'targetAttribute' => ['shift_id' => 'shift_id']],
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
    
    public function getPasien()
    {
        return $this->hasOne(Pasien::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaraBayar()
    {
        return $this->hasOne(Lookup::className(), ['lookup_id' => 'carabayar_id'])->andWhere(['lookup_type' => 'cara_pembayaran']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjamin()
    {
        return $this->hasOne(Penjamin::className(), ['penjamin_id' => 'penjamin_id']);
    }
    
    public function extraFields()
    {
        return [
            'pasien_m' => function($item){
                return $item->pasien;
            }
        ];
    }
}
