<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopasienibubayi_v".
 *
 * @property int $jadwaldokter_id
 * @property int $ruangan_id
 * @property int $instalasi_id
 * @property int $pegawai_id
 * @property string $ruangan_nama
 * @property string $nama_pegawai
 * @property string $hari
 * @property int $hari_jadwalbuka
 * @property string $waktu
 * @property string $waktu_mulai
 * @property string $waktu_selesai
 * @property int $kuota_offline
 * @property double $kuota_tersedia_offline
 * @property int $kuota_online
 * @property double $kuota_tersedia_online
 * @property int $kuota_penambahan
 * @property double $total_kuota
 */
class InfoPasienIbuBayiHeaderView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopasienibubayiheader_v';
    }

    
}
