<?php

namespace app\modules\v1\models;

use Yii;

class InfGabungInvoiceView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoinvoicegabung_v';
    }

    public static function primaryKey(){
        return ['invoicegabung_id'];
    }
}
