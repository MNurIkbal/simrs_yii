<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "loket_mp".
 *
 * @property int $loket_id
 * @property int $carabayar_id
 * @property int $status_pasien lookup_type='status_pasien'
 * @property int $konfigantrian_id
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
class LoketMp extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loket_mp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loket_id', 'konfigantrian_id'], 'required'],
            [['loket_id', 'carabayar_id', 'status_pasien', 'konfigantrian_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['loket_id', 'carabayar_id', 'status_pasien', 'konfigantrian_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','loket_id','konfigantrian_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['loket_id', 'konfigantrian_id'], 'unique', 'targetAttribute' => ['loket_id', 'konfigantrian_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'loket_id' => 'Loket ID',
            'carabayar_id' => 'Carabayar ID',
            'status_pasien' => 'Status Pasien',
            'konfigantrian_id' => 'Konfigantrian ID',
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
