<?php

namespace app\modules\v1\models;

class TotalTarifNaikKelasFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return 'totaltarifnaikkelas_fn';
    }

    public static function primaryKey()
    {
      return ["tariftindakan_id"];
    }
}
