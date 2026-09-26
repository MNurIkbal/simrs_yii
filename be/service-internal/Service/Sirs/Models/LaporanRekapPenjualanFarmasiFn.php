<?php
namespace Integrasi\Service\Sirs\Models;

class LaporanRekapPenjualanFarmasiFn extends \Doco\components\DocoPostgreFunctionAR
{
    const DATERANGE = 'daterange';
    const NUMBER = 'number';
    const STRING_TYPE = 'string';
    const DATE_FORMAT = 'd M Y';

    public static function functionName()
    {
        return "laporanrekappenjualanfarmasi_fn";
    }

    public static function attributSchema()
    {
        return [
            'varchar' => [
                'kode_obat',
                'nama_obat',
                'satuan_kecil',
                'jenisobatalkes_nama'
            ],
            'float8'  => [
                'baseprice',
                'qty',
                'total'
            ],
            'integer' => [
                'jenisobatalkes_id'
            ]
        ];
    }

    public static function getData($date_start = null,$date_end = null)
    {
        $date_start = !empty($date_start) ? date('Y-m-d',strtotime($date_start)) : date('Y-m-d');
        $date_end = !empty($date_end) ? date('Y-m-d',strtotime($date_end)) : date('Y-m-d');
        return (new LaporanRekapPenjualanFarmasiFn(['extParam'=>[$date_start, $date_end]]))->find();
    }
}
