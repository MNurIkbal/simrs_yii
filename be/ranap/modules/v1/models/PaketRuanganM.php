<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "paketruangan_mp".
 *
 * @property int $ruangan_id
 * @property int $tipepaket_id
 * @property bool $is_default
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
class PaketRuanganM extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paketruangan_mp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'tipepaket_id'], 'required'],
            [['ruangan_id', 'tipepaket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'tipepaket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_default', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['ruangan_id', 'tipepaket_id'], 'unique', 'targetAttribute' => ['ruangan_id', 'tipepaket_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => 'Ruangan ID',
            'tipepaket_id' => 'Tipepaket ID',
            'is_default' => 'Is Default',
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
