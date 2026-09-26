<?php

namespace app\modules\v1\models;

use Yii;

class Bpjs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpjs_t';
    }
}
