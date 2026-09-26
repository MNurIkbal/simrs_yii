<?php

namespace Doco\models\kasir;

use Yii;

class InfoPasienSudahBayarView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopasiensudahbayar_v';
    }

    public static function primaryKey()
    {
        return ['pembayaranpelayanan_id'];
    }
}
