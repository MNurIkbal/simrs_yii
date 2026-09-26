<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "syncsantoyusup_r".
 *
 * @property int $syncsantoyusup_id
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property bool $is_sync 
 * @property string $additional_data
 * @property string $created_date
 * @property string $last_modified_date
 * @property int $count_sync
 * @property string $payload
 * @property string $uid
 */
class SyncsantoyusupR extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'syncsantoyusup_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasien_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id', 'count_sync'], 'integer'],
            [['additional_data', 'is_sync', 'payload', 'uid'], 'string'],
            [['created_date', 'last_modified_date', 'count_sync', 'payload', 'uid'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'syncsantoyusup_id' => 'Syncsantoyusup ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'is_sync' => 'Is Sync',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'last_modified_date' => 'Last Update Date',
            'count_sync' => 'Count Sync',
            'payload' => 'Payload',
        ];
    }
}
