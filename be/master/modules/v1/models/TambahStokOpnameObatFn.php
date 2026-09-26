<?php

namespace app\modules\v1\models;

use Yii;

/**
 *
 * @property int $obatalkes_id
 */
class TambahStokOpnameObatFn extends \Doco\components\DocoPostgreFunctionAR {

    public static function functionName()
    {
        return 'tambahstokopnameobat_fn';
    }

    public static function attributSchema()
    {
        return [
            'varchar' => [
                'obatalkes_kode',
                'obatalkes_nama',
                'jenisobatalkes_nama',
                'servicecategory_nama',
                'servicegroup_nama',
                'satuankeci',
                'satuanbesar',
                'rakobat_nama',
                'laci',
                'uom'
            ],
            'integer'  => [
                'obatalkes_id',
                'satuankecil_id',
                'satuanbesar_id',
                'rakobat_id',
                'laciobat_id',
                'stok_saatini',
                'stok_in',
                'stok_out'
            ],
            'float8' => [
            	'stok_sistem',
            ],
        ];
    }

}