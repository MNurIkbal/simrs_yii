<?php
namespace app\modules\v1\models;

use Yii;

class RiwayatResepTerra extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'history.riwayat_resep';
    }
}
