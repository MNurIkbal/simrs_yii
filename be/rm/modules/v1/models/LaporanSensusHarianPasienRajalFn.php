<?php

namespace app\modules\v1\models;

use Yii;

class LaporanSensusHarianPasienRajalFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName() 
    {
        return "f_getlaporansensusharianrajal";
    }

    public static function getData($starDate, $endDate, $jenisLaporan, $countData = false)
    {
        $fetchData = Yii::$app->db->createCommand("select * from f_getlaporansensusharianrajal(:start, :end, :type)")
        ->bindParam(':start', $starDate)
        ->bindParam(':end', $endDate)
        ->bindParam(':type', $jenisLaporan);

        if ($countData) {
            return $fetchData->queryScalar();
        } else {
            return $fetchData->queryAll();
        }
    }
}