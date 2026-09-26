<?php
namespace app\modules\v1\models;

use Yii;
use Doco\components\DocoConstants;

class LaporanRekapitulasiSoapResumeDokterFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "laporanrekapitulasisoapresumedokter_fn";
    }

    public static function getData($date_start = null, $date_end = null, $jenis_laporan = DocoConstants::L_T_SOAP_DOKTER)
    {
        $date_start = !empty($date_start) ? date('Y-m-d',strtotime($date_start)) : date('Y-m-d');
        $date_end = !empty($date_end) ? date('Y-m-d',strtotime($date_end)) : date('Y-m-d');

        return (
            new LaporanRekapitulasiSoapResumeDokterFn([
                'extParam'=>[
                    $date_start,
                    $date_end,
                    $jenis_laporan
                ]
            ])
        )->find()
        ->select([
            'instalasi_id',
            'instalasi_nama',
            'ruangan_id',
            'ruangan_nama',
            'pegawai_id',
            'tgl_pendaftaran_filter as tgl_pendaftaran',
            'nama_dokter',
            'jumlah_pasien',
            'jenis_laporan'
        ]);
    }
}