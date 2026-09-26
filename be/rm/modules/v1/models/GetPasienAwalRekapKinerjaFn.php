<?php

namespace app\modules\v1\models;

use Yii;

class GetPasienAwalRekapKinerjaFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName() {
        return "f_getpasienawalrekapkinerja";
    }
}