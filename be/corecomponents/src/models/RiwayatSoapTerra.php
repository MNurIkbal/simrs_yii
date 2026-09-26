<?php
namespace Doco\models;

use Yii;

class RiwayatSoapTerra extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'history.soapterra_v';
    }
}
