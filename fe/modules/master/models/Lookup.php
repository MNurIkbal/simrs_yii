<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "lookup_m".
 *
 * @property int $lookup_id
 * @property string $lookup_type
 * @property string $lookup_name
 * @property string $lookup_value
 * @property int $lookup_urutan
 * @property string $lookup_kode
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
 */
class Lookup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lookup_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lookup_type', 'lookup_name', 'lookup_value', 'lookup_urutan'], 'required'],
            [['lookup_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['lookup_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['lookup_type'], 'string', 'max' => 100],
            [['lookup_name', 'lookup_value'], 'string', 'max' => 200],
            [['lookup_kode'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lookup_id' => Yii::t('fe','Lookup ID'),
            'lookup_type' => Yii::t('fe','Lookup Type'),
            'lookup_name' => Yii::t('fe','Lookup Name'),
            'lookup_value' => Yii::t('fe','Lookup Value'),
            'lookup_urutan' => Yii::t('fe','Lookup Urutan'),
            'lookup_kode' => Yii::t('fe','Lookup Kode'),
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
}
