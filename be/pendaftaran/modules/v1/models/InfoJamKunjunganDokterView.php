<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infojamkunjungandokter_v".
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
class InfoJamKunjunganDokterView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infojamkunjungandokter_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jadwaldokter_id', 'ruangan_id', 'instalasi_id', 'pegawai_id', 'hari_jadwalbuka', 'kuota_offline', 'kuota_online', 'kuota_penambahan'], 'default', 'value' => null],
            [['jadwaldokter_id', 'ruangan_id', 'instalasi_id', 'pegawai_id', 'hari_jadwalbuka', 'kuota_offline', 'kuota_online', 'kuota_penambahan'], 'integer'],
            [['waktu'], 'string'],
            [['waktu_mulai', 'waktu_selesai'], 'safe'],
            [['kuota_tersedia_offline', 'kuota_tersedia_online', 'total_kuota'], 'number'],
            [['ruangan_nama', 'nama_pegawai'], 'string', 'max' => 50],
            [['hari'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jadwaldokter_id' => 'Jadwaldokter ID',
            'ruangan_id' => 'Ruangan ID',
            'instalasi_id' => 'Instalasi ID',
            'pegawai_id' => 'Pegawai ID',
            'ruangan_nama' => 'Ruangan Nama',
            'nama_pegawai' => 'Nama Pegawai',
            'hari' => 'Hari',
            'hari_jadwalbuka' => 'Hari Jadwalbuka',
            'waktu' => 'Waktu',
            'waktu_mulai' => 'Waktu Mulai',
            'waktu_selesai' => 'Waktu Selesai',
            'kuota_offline' => 'Kuota Offline',
            'kuota_tersedia_offline' => 'Kuota Tersedia Offline',
            'kuota_online' => 'Kuota Online',
            'kuota_tersedia_online' => 'Kuota Tersedia Online',
            'kuota_penambahan' => 'Kuota Penambahan',
            'total_kuota' => 'Total Kuota',
        ];
    }
}
