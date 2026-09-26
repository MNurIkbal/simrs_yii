<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "cppt_v".
 *
 * @property int $cppt_id
 * @property int $pegawai_id
 */

class NotifikasiCpptFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "notifikasicppt_fn";
    }

    public function getListNotifikasiCppt($pegawai_id) {
        return (new NotifikasiCpptFn(['extParam'=>[$pegawai_id]]))->find()->select([
                    'nama_pasien',
                    'no_pendaftaran',
                    'pendaftaran_id',
                    'tgl_cppt',
                    'ruangan_id',
                    'jenis_pendaftaran',
                    'instalasi_id',
                    'cppt_id'
                ]);
    }

    public function getCountNotifikasiCppt($pegawai_id) {
        return (new NotifikasiCpptFn(['extParam'=>[$pegawai_id]]))->find()->select([
                    new \yii\db\Expression("COALESCE(SUM(CASE WHEN jenis_pendaftaran='RD' THEN 1 ELSE 0 END), 0) as rd"),
                    new \yii\db\Expression("COALESCE(SUM(CASE WHEN jenis_pendaftaran='RI' THEN 1 ELSE 0 END), 0) as ri"),
                    new \yii\db\Expression("COALESCE(SUM(CASE WHEN jenis_pendaftaran='RJ' THEN 1 ELSE 0 END), 0) as rj")
                ])
                ->asArray()
                ->one();
    }
}
