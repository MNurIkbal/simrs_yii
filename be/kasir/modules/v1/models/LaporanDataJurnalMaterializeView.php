<?php

namespace app\modules\v1\models;

use Yii;

class LaporanDataJurnalMaterializeView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporandatajurnal_mv';
    }

    public static function queryReport($start, $end)
    {
        return "
            SELECT * 
            FROM laporandatajurnal_mv 
            WHERE  \"Tanggal Billing\"::date BETWEEN '" . $start . "' AND '" . $end . "' 
            OR \"Tanggal Batal\"::date BETWEEN '" . $start . "' AND '" . $end . "' 
            ORDER BY \"Tanggal Billing\" ASC
        ";
    }
}
