<?php 
namespace Doco\models;

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
            'int4' => ['obatalkes_id', 'satuankecil_id', 'satuanbesar_id', 'qty_tersedia'],
            'varchar' => ['obatalkes_kode', 'obatalkes_namalain', 'obatalkes_nama', 'satuankecil_nama', 'satuanbesar_nama'],
            'float8' => ['hargajual', 'hargaygdipakai']
        ];
    }
}