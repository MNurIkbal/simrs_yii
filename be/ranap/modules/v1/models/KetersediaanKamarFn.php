<?php

namespace app\modules\v1\models;

use Yii;

class KetersediaanKamarFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "fgetketersediaankamar";
    }

    public static function attributSchema()
    {
        return [
            'int4' => [
                'kamartempattidur_id',
                'kamarruangan_id',
                'ruangan_id',
                'kelaspelayanan_id',
                'kettempattidur_id',
            ],
            'varchar' => [
                'ruangan_nama',
                'kamarruangan_nokamar',
                'kamarruangan_jenis',
                'no_tempattidur',
                'kelaspelayanan_nama',
                'status_isi',
                'kode_warna',
                'kettempattidur_warna'
            ],
            'float8'  => [
                'harga_tariftindakan',
                'total_isi',
                'total_kosong'
            ]
        ];
    }
}
?>