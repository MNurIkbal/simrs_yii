<?php 
namespace app\modules\v1\models;

use Yii;

class TarifTotalRsFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "tariftotalrs_fn";
    }

    public static function attributSchema()
    {
        return [
            'int4' => [
                'ruangan_id',
                'penjamin_id',
                'kelaspelayanan_id',
                'tariftindakan_id',
                'daftartindakan_id',
            ],
            'varchar' => [
                'daftartindakan_nama',
            ],
            'float8'  => [
                'harga_tariftindakan',
            ],
            'bool' => [
                'is_akomodasi'
            ],
            'numeric' => [
                'persencyto_tindakan'
            ]
        ];
    }
}