<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "generik_m".
 *
 * @property integer $generik_id
 * @property string $generik_nama
 * @property string $generik_namalain
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
 * @property ObatalkesM[] $obatalkesMs
 */
class Generik extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'generik_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['generik_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['generik_nama', 'generik_namalain'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'generik_id' => 'Generik ID',
            'generik_nama' => 'Generik Nama',
            'generik_namalain' => 'Generik Namalain',
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
    public function getObatalkesMs()
    {
        return $this->hasMany(ObatAlkes::className(), ['generik_id' => 'generik_id']);
    }
}
