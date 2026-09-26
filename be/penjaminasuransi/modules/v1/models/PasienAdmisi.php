<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-10-09 14:46:16
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-10-09 14:46:23
 */
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasienadmisi_t".
 *
 * @property int $pasienadmisi_id
 * @property int $shift_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $pasien_id
 * @property int $caramasuk_id
 * @property int $ruangan_id
 * @property int $pasienpulang_id
 * @property int $bookingkamar_id
 * @property int $pembayaranpelayanan_id
 * @property int $pendaftaran_id
 * @property int $kamarruangan_id
 * @property int $kelaspelayanan_id
 * @property int $pegawai_id
 * @property string $tgl_admisi
 * @property string $tgl_pendaftaran
 * @property string $tgl_pulang
 * @property string $kunjungan
 * @property bool $status_keluar
 * @property bool $rawat_gabung
 * @property string $rencana_pulang
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property int $kamartempattidur_id
 *
 * @property AnamnesaT[] $anamnesaTs
 * @property AnamnesadietT[] $anamnesadietTs
 * @property AsuhankeperawatanT[] $asuhankeperawatanTs
 * @property BayaruangmukaT[] $bayaruangmukaTs
 * @property BookingkamarT[] $bookingkamarTs
 * @property DietpasienT[] $dietpasienTs
 * @property HasilpemeriksaanlabT[] $hasilpemeriksaanlabTs
 * @property HasilpemeriksaanradT[] $hasilpemeriksaanradTs
 * @property HasilpemeriksaanrmT[] $hasilpemeriksaanrmTs
 * @property MasukkamarT[] $masukkamarTs
 * @property BookingkamarT $bookingkamar
 * @property CarabayarM $carabayar
 * @property CaramasukM $caramasuk
 * @property KamarruanganM $kamarruangan
 * @property KelaspelayananM $kelaspelayanan
 * @property PasienM $pasien
 * @property PasienpulangT $pasienpulang
 * @property PegawaiM $pegawai
 * @property PembayaranpelayananT $pembayaranpelayanan
 * @property PendaftaranT $pendaftaran
 * @property PenjaminM $penjamin
 * @property RuanganM $ruangan
 * @property PasienmasukpenunjangT[] $pasienmasukpenunjangTs
 * @property PasienpulangT[] $pasienpulangTs
 * @property PembayaranpelayananT[] $pembayaranpelayananTs
 * @property PendaftaranT[] $pendaftaranTs
 * @property PenjualanresepT[] $penjualanresepTs
 * @property PindahkamarT[] $pindahkamarTs
 * @property RencanaoperasiT[] $rencanaoperasiTs
 * @property ReturresepT[] $returresepTs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 */
class PasienAdmisi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */

    public $jeniskasuspenyakit_id;
    public static function tableName()
    {
        return 'pasienadmisi_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shift_id', 'carabayar_id', 'penjamin_id', 'pasien_id', 'ruangan_id', 'pasienpulang_id', 'bookingkamar_id', 'pembayaranpelayanan_id', 'pendaftaran_id', 'kamarruangan_id', 'kelaspelayanan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamartempattidur_id'], 'default', 'value' => null],
            [['shift_id', 'carabayar_id', 'penjamin_id', 'pasien_id', 'caramasuk_id', 'ruangan_id', 'pasienpulang_id', 'bookingkamar_id', 'pembayaranpelayanan_id', 'pendaftaran_id', 'kamarruangan_id', 'kelaspelayanan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamartempattidur_id'], 'integer'],
            [['carabayar_id', 'penjamin_id', 'pasien_id', 'ruangan_id', 'pendaftaran_id'], 'required'],
            [['caramasuk_id', 'tgl_admisi', 'tgl_pendaftaran', 'tgl_pulang', 'rencana_pulang', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['status_keluar', 'rawat_gabung', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['kunjungan'], 'string', 'max' => 50],
            // [['bookingkamar_id'], 'exist', 'skipOnError' => true, 'targetClass' => BookingkamarT::className(), 'targetAttribute' => ['bookingkamar_id' => 'bookingkamar_id']],
            // [['carabayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => CaraBayar::className(), 'targetAttribute' => ['carabayar_id' => 'carabayar_id']],
            // [['caramasuk_id'], 'exist', 'skipOnError' => true, 'targetClass' => CaraMasuk::className(), 'targetAttribute' => ['caramasuk_id' => 'caramasuk_id']],
            // [['kamarruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KamarruanganM::className(), 'targetAttribute' => ['kamarruangan_id' => 'kamarruangan_id']],
            // [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelasPelayanan::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            // [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pasien::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            // [['pasienpulang_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienpulangT::className(), 'targetAttribute' => ['pasienpulang_id' => 'pasienpulang_id']],
            // [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pegawai::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            // [['pembayaranpelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => PembayaranpelayananT::className(), 'targetAttribute' => ['pembayaranpelayanan_id' => 'pembayaranpelayanan_id']],
            // [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pendaftaran::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            // [['penjamin_id'], 'exist', 'skipOnError' => true, 'targetClass' => Penjamin::className(), 'targetAttribute' => ['penjamin_id' => 'penjamin_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'shift_id' => 'Shift ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'pasien_id' => 'Pasien ID',
            'caramasuk_id' => 'Caramasuk ID',
            'ruangan_id' => 'Ruangan ID',
            'pasienpulang_id' => 'Pasienpulang ID',
            'bookingkamar_id' => 'Bookingkamar ID',
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'kamarruangan_id' => 'Kamarruangan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'pegawai_id' => 'Pegawai ID',
            'tgl_admisi' => 'Tgl Admisi',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'tgl_pulang' => 'Tgl Pulang',
            'kunjungan' => 'Kunjungan',
            'status_keluar' => 'Status Keluar',
            'rawat_gabung' => 'Rawat Gabung',
            'rencana_pulang' => 'Rencana Pulang',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'kamartempattidur_id' => 'Kamartempattidur ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnamnesaTs()
    {
        return $this->hasMany(AnamnesaT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnamnesadietTs()
    {
        return $this->hasMany(AnamnesadietT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsuhankeperawatanTs()
    {
        return $this->hasMany(AsuhankeperawatanT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBayaruangmukaTs()
    {
        return $this->hasMany(BayaruangmukaT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookingkamarTs()
    {
        return $this->hasMany(BookingkamarT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDietpasienTs()
    {
        return $this->hasMany(DietpasienT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanlabTs()
    {
        return $this->hasMany(HasilpemeriksaanlabT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanradTs()
    {
        return $this->hasMany(HasilpemeriksaanradT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanrmTs()
    {
        return $this->hasMany(HasilpemeriksaanrmT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasukkamarTs()
    {
        return $this->hasMany(MasukkamarT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookingkamar()
    {
        return $this->hasOne(BookingkamarT::className(), ['bookingkamar_id' => 'bookingkamar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarabayar()
    {
        return $this->hasOne(CarabayarM::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaramasuk()
    {
        return $this->hasOne(CaramasukM::className(), ['caramasuk_id' => 'caramasuk_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKamarruangan()
    {
        return $this->hasOne(KamarruanganM::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelaspelayanan()
    {
        return $this->hasOne(KelaspelayananM::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasien()
    {
        return $this->hasOne(PasienM::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienpulang()
    {
        return $this->hasOne(PasienpulangT::className(), ['pasienpulang_id' => 'pasienpulang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawai()
    {
        return $this->hasOne(PegawaiM::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembayaranpelayanan()
    {
        return $this->hasOne(PembayaranpelayananT::className(), ['pembayaranpelayanan_id' => 'pembayaranpelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasOne(PendaftaranT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjamin()
    {
        return $this->hasOne(PenjaminM::className(), ['penjamin_id' => 'penjamin_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(RuanganM::className(), ['ruangan_id' => 'ruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienmasukpenunjangTs()
    {
        return $this->hasMany(PasienmasukpenunjangT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienpulangTs()
    {
        return $this->hasMany(PasienpulangT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembayaranpelayananTs()
    {
        return $this->hasMany(PembayaranpelayananT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaranTs()
    {
        return $this->hasMany(PendaftaranT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualanresepTs()
    {
        return $this->hasMany(PenjualanresepT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPindahkamarTs()
    {
        return $this->hasMany(PindahkamarT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanaoperasiTs()
    {
        return $this->hasMany(RencanaoperasiT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturresepTs()
    {
        return $this->hasMany(ReturresepT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanpelayananTs()
    {
        return $this->hasMany(TindakanpelayananT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }
}
