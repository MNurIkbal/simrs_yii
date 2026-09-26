<?php
namespace app\modules\v1\models;

use Yii;

class LapPelayananIgdFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "new_laporanpelayananigd_fn";
    }

    public static function getData($date_start = null, $date_end = null)
    {
        $date_start = !empty($date_start) ? date('Y-m-d', strtotime($date_start)) : date('Y-m-d');
        $date_end = !empty($date_end) ? date('Y-m-d', strtotime($date_end)) : date('Y-m-d');
        return (new LapPelayananIgdFn(['extParam' => [$date_start, $date_end]]))->find()->asArray()->all();
    }
}