<?php

/**
 * @Author: Anggoro
 * @Date:   2019-04-25
 */

namespace app\modules\v1\models;

use Yii;

class RekapTempatTidurFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName() {
        return "rekaptempattidur_f";
    }

    public static function attributSchema() {
        return [
            'varchar' => ['vtahun', 'ruangan_nama'],
            'int4' => [
                'ruangan_id',
                '01',
                '02',
                '03',
                '04',
                '05',
                '06',
                '07',
                '08',
                '09',
                '10',
                '11',
                '12',
            ],
        ];
    }
}