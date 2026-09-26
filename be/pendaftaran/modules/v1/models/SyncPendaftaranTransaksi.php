<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "syncpendaftaran_t".
 *
 * @property int $syncpendaftaran_id
 * @property int $pendaftaran_id
 * @property string $additional_sync
 * @property bool $is_sync
 */
class SyncPendaftaranTransaksi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'syncpendaftaran_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'default', 'value' => null],
            [['pendaftaran_id'], 'integer'],
            [['additional_sync'], 'string'],
            [['is_sync'], 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'syncpendaftaran_id' => 'Syncpendaftaran ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'additional_sync' => 'Additional Sync',
            'is_sync' => 'Is Sync',
        ];
    }
}
