<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "caramasuk_m".
 *
 * @property integer $caramasuk_id
 * @property string $caramasuk_nama
 * @property string $caramasuk_namalainnya
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
 * @property PasienadmisiT[] $pasienadmisiTs
 * @property PendaftaranT[] $pendaftaranTs
 */
class CaraMasuk extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'caramasuk_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['caramasuk_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['caramasuk_nama', 'caramasuk_namalainnya'], 'string', 'max' => 50],
            ['caramasuk_nama', 'unique', 'targetAttribute' => 'caramasuk_nama']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'caramasuk_id' => 'Caramasuk ID',
            'caramasuk_nama' => 'Cara Masuk Nama',
            'caramasuk_namalainnya' => 'Caramasuk Namalainnya',
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
    public function getPasienadmisiTs()
    {
        return $this->hasMany(PasienadmisiT::className(), ['caramasuk_id' => 'caramasuk_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaranTs()
    {
        return $this->hasMany(PendaftaranT::className(), ['caramasuk_id' => 'caramasuk_id']);
    }
}
