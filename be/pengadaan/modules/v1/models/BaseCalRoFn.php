<?php
namespace app\modules\v1\models;

use Yii;

class BaseCalRoFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "basecalrofn";
    }

    public static function attributSchema()
    {
        return [
            'int4' => [
                'obatalkes_id',
                'jenisobatalkes_id',
                'movingcriteria_id'
            ],
            'int8' => [
                'count'
            ],
            'float8' => [
                'max',
                'min',
                'avg',
                'min_resep',
                'last_7',
                'last_14',
                'last_30'
            ],
            'varchar' => [
                'moving_category'
            ]
        ];
    }
}