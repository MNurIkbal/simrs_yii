<?php

namespace app\modules\v1\models;

use Yii;

class DokterView extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dokter_v';
    }
}
