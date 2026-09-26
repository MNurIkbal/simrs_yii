<?php 
namespace app\modules\v1\models;

use Yii;

class InfoStokObatAlkesFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "infostokobatalkes_fn";
    }
    public static function attributSchema()
    {
        return [
            'int4' => ['obatalkes_id', 'qty_tersedia'],
            'varchar' => ['obatalkes_nama'],
        ];
    }
}