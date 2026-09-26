<?php

namespace app\modules\v1\models;

use Yii;
class SyncPasien extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'syncpasien_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['syncpasien_id'], 'default', 'value' => null],
            [['pasien_id'], 'integer'],
            [['additional_sync'], 'string'],
            [['is_sync'], 'boolean']
        ];
    }
}
