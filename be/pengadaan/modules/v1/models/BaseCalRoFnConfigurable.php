<?php

namespace app\modules\v1\models;

use Yii;

class BaseCalRoFnConfigurable extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "new_basecalrofn";
    }

    public static function getData(
        $jenis_obat = null, 
        $pemakaian_ruangan, 
        $bmhp, 
        $mutasi,
        $is_array = true
    ) {
        $model = new BaseCalRoFnConfigurable(
            ['extParam' => [$pemakaian_ruangan, $bmhp, $mutasi]]
        );

        if($is_array) {
            $query = $model::find()
            ->where(['in', 'jenisobatalkes_id', $jenis_obat])
            ->asArray()->all();

            return $query;
        } else {
            return $model::find();
        }
        
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
