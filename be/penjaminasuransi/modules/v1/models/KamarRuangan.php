<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kamarruangan_m".
 *
 * @property int $kamarruangan_id
 * @property int $ruangan_id
 * @property int $kelaspelayanan_id
 * @property string $kamarruangan_nokamar
 * @property int $kamarruangan_jenis lookup_type='jenis_kamar'
 * @property string $kamarruangan_deskripsi
 * @property string $kamarruangan_image
 * @property string $keterangan_kamar lookup_type='keterangan_kamar'
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
 * @property BookingkamarT[] $bookingkamarTs
 * @property KelaspelayananM $kelaspelayanan
 * @property RuanganM $ruangan
 * @property MasukkamarT[] $masukkamarTs
 * @property PasienadmisiT[] $pasienadmisiTs
 * @property PindahkamarT[] $pindahkamarTs
 * @property RencanaoperasiT[] $rencanaoperasiTs
 */
class KamarRuangan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kamarruangan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'kamarruangan_nokamar', 'kamarruangan_jenis'], 'required'],
            [['ruangan_id', 'kelaspelayanan_id', 'kamarruangan_jenis', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'kelaspelayanan_id', 'kamarruangan_jenis', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['kamarruangan_deskripsi', 'kamarruangan_image', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['keterangan_kamar'], 'string', 'max' => 50],
            [['kamarruangan_nokamar'], 'unique'],
            [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelaspelayananM::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kamarruangan_id' => Yii::t('fe','Kamarruangan ID'),
            'ruangan_id' => Yii::t('fe','Ruangan ID'),
            'kelaspelayanan_id' => Yii::t('fe','Kelaspelayanan ID'),
            'kamarruangan_nokamar' => Yii::t('fe','Kamarruangan Nokamar'),
            'kamarruangan_jenis' => Yii::t('fe','Kamarruangan Jenis'),
            'kamarruangan_deskripsi' => Yii::t('fe','Kamarruangan Deskripsi'),
            'kamarruangan_image' => Yii::t('fe','Kamarruangan Image'),
            'keterangan_kamar' => Yii::t('fe','Keterangan Kamar'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Is Active'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookingkamarTs()
    {
        return $this->hasMany(BookingkamarT::className(), ['kamarruangan_id' => 'kamarruangan_id']);
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
    public function getRuangan()
    {
        return $this->hasOne(RuanganM::className(), ['ruangan_id' => 'ruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasukkamarTs()
    {
        return $this->hasMany(MasukkamarT::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienadmisiTs()
    {
        return $this->hasMany(PasienadmisiT::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPindahkamarTs()
    {
        return $this->hasMany(PindahkamarT::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanaoperasiTs()
    {
        return $this->hasMany(RencanaoperasiT::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    }
}
