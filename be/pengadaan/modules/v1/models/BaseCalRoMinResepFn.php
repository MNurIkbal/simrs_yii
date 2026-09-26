<?php
namespace app\modules\v1\models;

use Yii;

class BaseCalRoMinResepFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "basecalro_minresep_fn";
    }
}