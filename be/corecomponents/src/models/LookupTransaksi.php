<?php

namespace Doco\models;

use Yii;

class LookupTransaksi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lookuptransaksi_m';
    }
}
