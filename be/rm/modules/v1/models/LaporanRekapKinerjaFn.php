<?php

namespace app\modules\v1\models;

use Yii;

class LaporanRekapKinerjaFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName() {
        return "laporanrekapkinerjaprofesional_fn";
    }
}