<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infodatapendaftaran_v".
 *
 * @property int $pendaftaran_id
 * @property int $instalasi_id
 * @property int $ruangan_id
 * @property int $pasien_id
 * @property int $penjamin_id
 * @property int $carabayar_id
 * @property int $kelaspelayanan_id
 * @property int $pasienpulang_id
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $no_mobile_pasien
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property string $kelaspelayanan_nama
 * @property string $tglpasienpulang
 */
class TindakanAdmView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tindakanadm_v';
    }
}
