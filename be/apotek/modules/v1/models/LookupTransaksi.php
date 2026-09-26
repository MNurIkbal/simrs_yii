<?php

namespace app\modules\v1\models;

use Yii;

class LookupTransaksi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lookuptransaksi_m';
    }
}
