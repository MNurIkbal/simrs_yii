<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "penjualanresep_t".
 *
 * @property int $penjualanresep_id
 * @property int $pasienadmisi_id
 * @property int $pegawai_id
 * @property int $pendaftaran_id
 * @property int $returresep_id
 * @property int $kelaspelayanan_id
 * @property int $penjamin_id
 * @property int $pasien_id
 * @property int $carabayar_id
 * @property int $ruangan_id
 * @property int $reseptur_id
 * @property int $shift_id
 * @property string $tglpenjualan
 * @property string $jenispenjualan
 * @property string $tglresep
 * @property string $noresep
 * @property double $totharganetto
 * @property double $totalhargajual
 * @property double $totaltarifservice
 * @property double $biayaadministrasi
 * @property double $biayakonseling
 * @property double $pembulatanharga
 * @property double $jasadokterresep
 * @property double $discount
 * @property double $subsidiasuransi
 * @property double $subsidipemerintah
 * @property double $subsidirs
 * @property double $iurbiaya
 * @property int $lamapelayanan
 * @property int $penjpasienpegawai_id
 * @property int $penjpasienruangan_id
 * @property int $antrianfarmasi_id
 * @property int $permohonanoa_id
 * @property double $takaranresep
 * @property bool $isresepperawatan
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
 *
 * @property AntrianT $antrianfarmasi
 * @property CarabayarM $carabayar
 * @property KelaspelayananM $kelaspelayanan
 * @property PasienM $pasien
 * @property PasienadmisiT $pasienadmisi
 * @property PegawaiM $pegawai
 * @property PegawaiM $penjpasienpegawai
 * @property PendaftaranT $pendaftaran
 * @property PenjaminM $penjamin
 * @property PermohonanoaT $permohonanoa
 * @property ResepturT $reseptur
 * @property ReturresepT $returresep
 * @property RuanganM $ruangan
 * @property RuanganM $penjpasienruangan
 * @property ShiftM $shift
 * @property ReturresepT[] $returresepTs
 */
class Penjualanresep extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'penjualanresep_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasienadmisi_id', 'pegawai_id', 'pendaftaran_id', 'returresep_id', 'kelaspelayanan_id', 'penjamin_id', 'pasien_id', 'carabayar_id', 'ruangan_id', 'reseptur_id', 'shift_id', 'lamapelayanan', 'penjpasienpegawai_id', 'penjpasienruangan_id', 'antrianfarmasi_id', 'permohonanoa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienadmisi_id', 'pegawai_id', 'pendaftaran_id', 'returresep_id', 'kelaspelayanan_id', 'penjamin_id', 'pasien_id', 'carabayar_id', 'ruangan_id', 'reseptur_id', 'shift_id', 'lamapelayanan', 'penjpasienpegawai_id', 'penjpasienruangan_id', 'antrianfarmasi_id', 'permohonanoa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglpenjualan', 'tglresep', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['jenispenjualan', 'noresep', 'additional_data'], 'string'],
            [['totharganetto', 'totalhargajual', 'totaltarifservice', 'biayaadministrasi', 'biayakonseling', 'pembulatanharga', 'jasadokterresep', 'discount', 'subsidiasuransi', 'subsidipemerintah', 'subsidirs', 'iurbiaya', 'takaranresep'], 'number'],
            [['isresepperawatan', 'is_deleted', 'is_active'], 'boolean'],
            [['antrianfarmasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => AntrianT::className(), 'targetAttribute' => ['antrianfarmasi_id' => 'antrian_id']],
            [['carabayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarabayarM::className(), 'targetAttribute' => ['carabayar_id' => 'carabayar_id']],
            [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelaspelayananM::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienM::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            [['pasienadmisi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienadmisiT::className(), 'targetAttribute' => ['pasienadmisi_id' => 'pasienadmisi_id']],
            [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            [['penjpasienpegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['penjpasienpegawai_id' => 'pegawai_id']],
            [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => PendaftaranT::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            [['penjamin_id'], 'exist', 'skipOnError' => true, 'targetClass' => PenjaminM::className(), 'targetAttribute' => ['penjamin_id' => 'penjamin_id']],
            [['permohonanoa_id'], 'exist', 'skipOnError' => true, 'targetClass' => PermohonanoaT::className(), 'targetAttribute' => ['permohonanoa_id' => 'permohonanoa_id']],
            [['reseptur_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResepturT::className(), 'targetAttribute' => ['reseptur_id' => 'reseptur_id']],
            [['returresep_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReturresepT::className(), 'targetAttribute' => ['returresep_id' => 'returresep_id']],
            [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
            [['penjpasienruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['penjpasienruangan_id' => 'ruangan_id']],
            [['shift_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShiftM::className(), 'targetAttribute' => ['shift_id' => 'shift_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'penjualanresep_id' => 'Penjualanresep ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pegawai_id' => 'Pegawai ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'returresep_id' => 'Returresep ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'penjamin_id' => 'Penjamin ID',
            'pasien_id' => 'Pasien ID',
            'carabayar_id' => 'Carabayar ID',
            'ruangan_id' => 'Ruangan ID',
            'reseptur_id' => 'Reseptur ID',
            'shift_id' => 'Shift ID',
            'tglpenjualan' => 'Tglpenjualan',
            'jenispenjualan' => 'Jenispenjualan',
            'tglresep' => 'Tglresep',
            'noresep' => 'Noresep',
            'totharganetto' => 'Totharganetto',
            'totalhargajual' => 'Totalhargajual',
            'totaltarifservice' => 'Totaltarifservice',
            'biayaadministrasi' => 'Biayaadministrasi',
            'biayakonseling' => 'Biayakonseling',
            'pembulatanharga' => 'Pembulatanharga',
            'jasadokterresep' => 'Jasadokterresep',
            'discount' => 'Discount',
            'subsidiasuransi' => 'Subsidiasuransi',
            'subsidipemerintah' => 'Subsidipemerintah',
            'subsidirs' => 'Subsidirs',
            'iurbiaya' => 'Iurbiaya',
            'lamapelayanan' => 'Lamapelayanan',
            'penjpasienpegawai_id' => 'Penjpasienpegawai ID',
            'penjpasienruangan_id' => 'Penjpasienruangan ID',
            'antrianfarmasi_id' => 'Antrianfarmasi ID',
            'permohonanoa_id' => 'Permohonanoa ID',
            'takaranresep' => 'Takaranresep',
            'isresepperawatan' => 'Isresepperawatan',
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAntrianfarmasi()
    {
        return $this->hasOne(AntrianT::className(), ['antrian_id' => 'antrianfarmasi_id']);
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
    public function getPasienadmisi()
    {
        return $this->hasOne(PasienadmisiT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
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
    public function getPenjpasienpegawai()
    {
        return $this->hasOne(PegawaiM::className(), ['pegawai_id' => 'penjpasienpegawai_id']);
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
    public function getPermohonanoa()
    {
        return $this->hasOne(PermohonanoaT::className(), ['permohonanoa_id' => 'permohonanoa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReseptur()
    {
        return $this->hasOne(ResepturT::className(), ['reseptur_id' => 'reseptur_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturresep()
    {
        return $this->hasOne(ReturresepT::className(), ['returresep_id' => 'returresep_id']);
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
    public function getPenjpasienruangan()
    {
        return $this->hasOne(RuanganM::className(), ['ruangan_id' => 'penjpasienruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShift()
    {
        return $this->hasOne(ShiftM::className(), ['shift_id' => 'shift_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturresepTs()
    {
        return $this->hasMany(ReturresepT::className(), ['penjualanresep_id' => 'penjualanresep_id']);
    }
}
