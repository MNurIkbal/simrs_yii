<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infobayaruangmuka_v".
 *
 * @property int $bayaruangmuka_id
 * @property string $tgl_uangmuka
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property double $jumlah_uangmuka
 * @property string $tgl_pendaftaran
 * @property string $nobuktibayar
 * @property string $nama_pegawai
 */
class InfoBayarUangMukaView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infobayaruangmuka_v';
    }
}
