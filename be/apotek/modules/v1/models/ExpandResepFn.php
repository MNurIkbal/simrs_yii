<?php
namespace app\modules\v1\models;

use Yii;

class ExpandResepFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "expandresep_fn";
    }

    public function getData($noResep) {
        return (new ExpandResepFn(['extParam'=>[$noResep]]))->find()->asArray()->all();
    }
}