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
 * @property double $jumlah_tt
 * @property int $kamaruangan_tipe 0=fixed, 1=fleksibel
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
            [['ruangan_id', 'kelaspelayanan_id', 'kamarruangan_jenis', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamaruangan_tipe'], 'default', 'value' => null],
            [['ruangan_id', 'kelaspelayanan_id', 'kamarruangan_jenis', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamaruangan_tipe'], 'integer'],
            [['kamarruangan_deskripsi', 'kamarruangan_image', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jumlah_tt'], 'number'],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['keterangan_kamar'], 'string', 'max' => 50],
            [['kamarruangan_nokamar'], 'unique'],
            // [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelaspelayananM::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
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
            'kamarruangan_jenis' => 'Kamarruangan Jenis',
            'kamarruangan_deskripsi' => 'Kamarruangan Deskripsi',
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
            'jumlah_tt' => 'Jumlah Tt',
            'kamaruangan_tipe' => 'Kamaruangan Tipe',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getBookingkamarTs()
    // {
    //     return $this->hasMany(BookingkamarT::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getKelaspelayanan()
    // {
    //     return $this->hasOne(KelaspelayananM::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    public function getTempatTidur()
    {
        return $this->hasMany(KamarTempatTidur::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getMasukkamarTs()
    // {
    //     return $this->hasMany(MasukkamarT::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPasienadmisiTs()
    // {
    //     return $this->hasMany(PasienadmisiT::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPindahkamarTs()
    // {
    //     return $this->hasMany(PindahkamarT::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getRencanaoperasiTs()
    // {
    //     return $this->hasMany(RencanaoperasiT::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    // }
}
