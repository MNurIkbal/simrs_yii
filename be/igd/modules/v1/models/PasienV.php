<?php

namespace app\modules\v1\models;

use Yii;

class PasienV extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pasien_v';
    }
}
