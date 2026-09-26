<?php

namespace app\modules\v1\models;

use Yii;

class LaporansensusharianrekapitulasiFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName() {
        return "laporansensusharianri_rekapitulasi_fn";
    }

    public static function attributSchema() {
        return [
            'varchar' => ['vtanggal'],
            'int4' => [
                'pasien_hari_sebelumnya',
                'pasien_masuk',
                'pasien_pindahan',
                'jml_123',
                'keluar_hidup',
                'keluar_dipindahkan',
                'keluar_meninggaljml',
                'keluar_meninggalkur48',
                'keluar_meninggalleb48',
                'jml_567',
                'pasien_akhir',
            ],
        ];
    }
}