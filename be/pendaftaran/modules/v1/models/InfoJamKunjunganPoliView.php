<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infojamkunjunganpoli_v".
 *
 * @property int $jadwalbukapoli_id
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $hari_id
 * @property string $hari
 * @property string $waktu
 * @property string $jam_mulai
 * @property string $jam_tutup
 * @property int $shift_id
 * @property string $shift_nama
 * @property int $kouta_offline
 * @property double $kuota_tersedia_offline
 * @property int $kouta_online
 * @property double $kuota_tersedia_online
 */
class InfoJamKunjunganPoliView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infojamkunjunganpoli_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jadwalbukapoli_id', 'instalasi_id', 'ruangan_id', 'hari_id', 'shift_id', 'kouta_offline', 'kouta_online'], 'default', 'value' => null],
            [['jadwalbukapoli_id', 'instalasi_id', 'ruangan_id', 'hari_id', 'shift_id', 'kouta_offline', 'kouta_online'], 'integer'],
            [['jam_mulai', 'jam_tutup'], 'safe'],
            [['shift_nama'], 'string'],
            [['kuota_tersedia_offline', 'kuota_tersedia_online'], 'number'],
            [['instalasi_nama', 'ruangan_nama', 'waktu'], 'string', 'max' => 50],
            [['hari'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jadwalbukapoli_id' => 'Jadwalbukapoli ID',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'hari_id' => 'Hari ID',
            'hari' => 'Hari',
            'waktu' => 'Waktu',
            'jam_mulai' => 'Jam Mulai',
            'jam_tutup' => 'Jam Tutup',
            'shift_id' => 'Shift ID',
            'shift_nama' => 'Shift Nama',
            'kouta_offline' => 'Kouta Offline',
            'kuota_tersedia_offline' => 'Kuota Tersedia Offline',
            'kouta_online' => 'Kouta Online',
            'kuota_tersedia_online' => 'Kuota Tersedia Online',
        ];
    }
}
