<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "statuskepemilikanrumah_m".
 *
 * @property integer $statuskepemilikanrumah_id
 * @property string $statuskepemilikanrumah_nama
 * @property string $statuskepemilikanrumah_namalain
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
 * @property PegawaiM[] $pegawaiMs
 */
class StatusKepemilikanRumah extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'statuskepemilikanrumah_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['statuskepemilikanrumah_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['statuskepemilikanrumah_nama', 'statuskepemilikanrumah_namalain'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'statuskepemilikanrumah_id' => 'Statuskepemilikanrumah ID',
            'statuskepemilikanrumah_nama' => 'Statuskepemilikanrumah Nama',
            'statuskepemilikanrumah_namalain' => 'Statuskepemilikanrumah Namalain',
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
    public function getPegawaiMs()
    {
        return $this->hasMany(PegawaiM::className(), ['statuskepemilikanrumah_id' => 'statuskepemilikanrumah_id']);
    }
}
