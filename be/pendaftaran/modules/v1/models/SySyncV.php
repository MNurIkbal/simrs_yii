<?php

namespace app\modules\v1\models;

use Yii;
class SySyncV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sy_sync_v';
    }
}
