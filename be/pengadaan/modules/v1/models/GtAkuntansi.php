<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "gt_akuntansi_t".
 *
 * @property string $no_referensi
 * @property int $is_send
 * @property string $is_sending
 * @property string $additional_data
 * @property int $created_date
 * @property string $sync_respon
 * @property bool $is_deleted
 * @property bool $is_active
 */
class GtAkuntansi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}    
     */
    public static function tableName()
    {
        return 'gt_akuntansi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['no_referensi'], 'string'],
            [['additional_data', 'sync_respon', 'type_account', 'created_date'], 'safe'],
            [['is_send', 'is_sending', 'is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'no_referensi' => 'No Referensi',
            'is_send' => 'Is Send',
            'is_sending' => 'Is Sending',
            'is_deleted' => 'Deleted',
            'is_active' => 'Active',
        ];
    }
}
