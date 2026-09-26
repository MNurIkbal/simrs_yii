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
 * @property int $kamarruangan_jmlbed
 * @property string $kamarruangan_nobed
 * @property bool $kamarruangan_status
 * @property string $kamarruangan_image
 * @property string $keterangan_kamar
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
            [['ruangan_id', 'kelaspelayanan_id', 'kamarruangan_jmlbed', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'kelaspelayanan_id', 'kamarruangan_jmlbed', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['kamarruangan_nokamar', 'kamarruangan_jmlbed', 'kamarruangan_nobed'], 'required'],
            [['kamarruangan_status', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['kamarruangan_nobed'], 'string', 'max' => 10],
            [['kamarruangan_image'], 'string', 'max' => 100],
            [['keterangan_kamar'], 'string', 'max' => 50],
            // [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kelaspelayanan::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kamarruangan_id' => 'Kamarruangan ID',
            'ruangan_id' => 'Ruangan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
            'kamarruangan_jmlbed' => 'Kamarruangan Jmlbed',
            'kamarruangan_nobed' => 'Kamarruangan Nobed',
            'kamarruangan_status' => 'Kamarruangan Status',
            'kamarruangan_image' => 'Kamarruangan Image',
            'keterangan_kamar' => 'Keterangan Kamar',
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
    // public function getBookingkamarTs()
    // {
    //     return $this->hasMany(BookingkamarT::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKelaspelayanan()
    // {
    //     return $this->hasOne(KelaspelayananM::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRuangan()
    // {
    //     return $this->hasOne(RuanganM::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getMasukkamarTs()
    // {
    //     return $this->hasMany(MasukkamarT::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPasienadmisiTs()
    // {
    //     return $this->hasMany(PasienadmisiT::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPindahkamarTs()
    // {
    //     return $this->hasMany(PindahkamarT::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRencanaoperasiTs()
    // {
    //     return $this->hasMany(RencanaoperasiT::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    // }

    public function listKamar()
    {
        $query = self::find()
        ->where(['is_active' => 't'])
        ->all();

        return $query;
    }

    public function getKamar($ruangan_id)
    {
        $query = self::find()
            ->where(['ruangan_id' => $ruangan_id, 'is_active' => 't'])
            ->select(['kamarruangan_id', 'kamarruangan_nokamar'])->asArray()->all();

        return $query;
    }
}
