<?php 
namespace app\modules\v1\models;

use Yii;

class InvalidBills_fn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "invalidbills_fn";
    }

    public static function attributSchema()
    {
        return [
            'int4' => [
                'pendaftaran_id',
                'status_bayar',
            ],
        ];
    }
}