<?php

namespace app\modules\v1\models;

use Yii;
class SyncPasienView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sync_pasien';
    }
}
