<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infokonsulpoli_v".
 *
 * @property int $konsulpoli_id
 * @property int $pendaftaran_id
 * @property string $tgl_pendaftaran
 * @property string $no_pendaftaran
 * @property int $pasien_id
 * @property string $nama_pasien
 * @property string $no_rekam_medik
 * @property int $ruangan_id
 * @property string $ruangan_tujuan
 * @property int $pegawai_id
 * @property string $nama_dokter
 */
class Infokonsulpoli extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infokonsulpoli_v';
    }

    
}
