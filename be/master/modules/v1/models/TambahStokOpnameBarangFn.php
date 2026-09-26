<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "obatalkes_f".
 *
 * @property int $obatalkes_id
 */
class TambahStokOpnameBarangFn extends \Doco\components\DocoPostgreFunctionAR {

    public static function functionName()
    {
        return 'tambahstokopnamebarang_fn';
    }

    public static function attributSchema()
    {
        return [
            'varchar' => [
                'barang_nama',
                'barang_kode',
                'kelompok_barang',
                'subkelompok_barang',
                'kondisi'
            ],
            'integer'  => [
                'barang_id',
            ]
        ];
    }

}