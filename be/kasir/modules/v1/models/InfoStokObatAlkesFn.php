<?php

namespace app\modules\v1\models;

class InfoStokObatAlkesFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return 'infostokobatalkes_fn';
    }

    public static function primaryKey()
    {
      return ["obatalkes_id"];
    }
}
