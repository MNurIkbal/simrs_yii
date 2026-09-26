<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "lookupkeperawatan_m".
 *
 * @property integer $lookupkeperawatan_id
 * @property string $lookup_type
 * @property string $lookup_name
 * @property string $lookup_value
 * @property integer $lookup_urutan
 * @property string $lookup_kode
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
 */
class LookupKeperawatan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lookupkeperawatan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lookup_type', 'lookup_name', 'lookup_value', 'lookup_urutan'], 'required'],
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
            'lookupkeperawatan_id' => 'Lookup Keperawatan ID',
            'lookup_type' => 'Lookup Type',
            'lookup_name' => 'Lookup Name',
            'lookup_value' => 'Lookup Value',
            'lookup_urutan' => 'Lookup Urutan',
            'lookup_kode' => 'Lookup Kode',
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
}
