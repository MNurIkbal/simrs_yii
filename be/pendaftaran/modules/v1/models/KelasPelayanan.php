<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelaspelayanan_m".
 *
 * @property integer $kelaspelayanan_id
 * @property integer $jeniskelas_id
 * @property string $kelaspelayanan_nama
 * @property string $kelaspelayanan_namalainnya
 * @property double $persentasirujin
 * @property integer $urutankelas
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
 *
 * @property AsuransipasienM[] $asuransipasienMs
 * @property BookingkamarT[] $bookingkamarTs
 * @property KamarruanganM[] $kamarruanganMs
 * @property JeniskelasM $jeniskelas
 * @property JenisKelas $jeniskelas
 * @property MasukkamarT[] $masukkamarTs
 * @property PasienadmisiT[] $pasienadmisiTs
 * @property PasienkirimkeunitlainT[] $pasienkirimkeunitlainTs
 * @property PasienmasukpenunjangT[] $pasienmasukpenunjangTs
 * @property PendaftaranT[] $pendaftaranTs
 * @property PenjualanresepT[] $penjualanresepTs
 * @property PindahkamarT[] $pindahkamarTs
 * @property TanggunganpenjaminM[] $tanggunganpenjaminMs
 * @property TariftindakanM[] $tariftindakanMs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 * @property TindakanpelayananT[] $tindakanpelayananTs0
 * @property TipepaketM[] $tipepaketMs
 */
class KelasPelayanan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kelaspelayanan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskelas_id', 'kelaspelayanan_nama'], 'required'],
            [['jeniskelas_id', 'urutankelas', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['persentasirujin'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'kelas_rawat_naik_bpjs'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelaspelayanan_nama', 'kelaspelayanan_namalainnya'], 'string', 'max' => 50],
            [['jeniskelas_id'], 'exist', 'skipOnError' => true, 'targetClass' => Jeniskelas::className(), 'targetAttribute' => ['jeniskelas_id' => 'jeniskelas_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'jeniskelas_id' => 'Jeniskelas ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'kelaspelayanan_namalainnya' => 'Kelaspelayanan Namalainnya',
            'persentasirujin' => 'Persentasirujin',
            'urutankelas' => 'Urutankelas',
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
    public function getAsuransipasienMs()
    {
        return $this->hasMany(AsuransipasienM::className(), ['kelastanggunganasuransi_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookingkamarTs()
    {
        return $this->hasMany(BookingkamarT::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKamarruanganMs()
    {
        return $this->hasMany(KamarruanganM::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisKelas()
    {
        return $this->hasOne(Jeniskelas::className(), ['jeniskelas_id' => 'jeniskelas_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasukkamarTs()
    {
        return $this->hasMany(MasukkamarT::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienadmisiTs()
    {
        return $this->hasMany(PasienadmisiT::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienkirimkeunitlainTs()
    {
        return $this->hasMany(PasienkirimkeunitlainT::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienmasukpenunjangTs()
    {
        return $this->hasMany(PasienmasukpenunjangT::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaranTs()
    {
        return $this->hasMany(PendaftaranT::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualanresepTs()
    {
        return $this->hasMany(PenjualanresepT::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPindahkamarTs()
    {
        return $this->hasMany(PindahkamarT::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTanggunganpenjaminMs()
    {
        return $this->hasMany(TanggunganpenjaminM::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTariftindakanMs()
    {
        return $this->hasMany(TariftindakanM::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanpelayananTs()
    {
        return $this->hasMany(TindakanpelayananT::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanpelayananTs0()
    {
        return $this->hasMany(TindakanpelayananT::className(), ['kelastanggungan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipepaketMs()
    {
        return $this->hasMany(TipepaketM::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }
}
