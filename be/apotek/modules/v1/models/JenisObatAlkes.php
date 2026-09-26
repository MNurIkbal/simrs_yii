<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jenisobatalkes_m".
 *
 * @property integer $jenisobatalkes_id
 * @property string $jenisobatalkes_kode
 * @property string $jenisobatalkes_nama
 * @property string $jenisobatalkes_namalain
 * @property boolean $jenisobatalkes_farmasi
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
 * @property DiskonobatpenjaminM[] $diskonobatpenjaminMs
 * @property JenisobatalkesrekM[] $jenisobatalkesrekMs
 * @property ObatalkesM[] $obatalkesMs
 * @property ObatalkespenjaminM[] $obatalkespenjaminMs
 * @property SubjenisM[] $subjenisMs
 */
class JenisObatAlkes extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jenisobatalkes_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jenisobatalkes_id'], 'required'],
            [['jenisobatalkes_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jenisobatalkes_kode', 'jenisobatalkes_nama', 'jenisobatalkes_namalain', 'additional_data'], 'string'],
            [['jenisobatalkes_farmasi', 'is_deleted', 'is_active'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jenisobatalkes_id' => 'Jenisobatalkes ID',
            'jenisobatalkes_kode' => 'Jenisobatalkes Kode',
            'jenisobatalkes_nama' => 'Jenisobatalkes Nama',
            'jenisobatalkes_namalain' => 'Jenisobatalkes Namalain',
            'jenisobatalkes_farmasi' => 'Jenisobatalkes Farmasi',
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
    public function getDiskonobatpenjaminMs()
    {
        return $this->hasMany(DiskonObatPenjamin::className(), ['jenisobatalkes_id' => 'jenisobatalkes_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisobatalkesrekMs()
    {
        return $this->hasMany(JenisObatAlkesRek::className(), ['jenisobatalkes_id' => 'jenisobatalkes_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObatalkesMs()
    {
        return $this->hasMany(ObatAlkes::className(), ['jenisobatalkes_id' => 'jenisobatalkes_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObatalkespenjaminMs()
    {
        return $this->hasMany(ObatAlkesPenjamin::className(), ['jenisobatalkes_id' => 'jenisobatalkes_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubjenisMs()
    {
        return $this->hasMany(SubJenis::className(), ['jenisobatalkes_id' => 'jenisobatalkes_id']);
    }
}
