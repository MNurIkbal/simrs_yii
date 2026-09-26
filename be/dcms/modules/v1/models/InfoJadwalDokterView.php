<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infojadwaldokter_v".
 *
 * @property int $jadwaldokter_id
 * @property int $ruangan_id
 * @property int $instalasi_id
 * @property int $pegawai_id
 * @property string $ruangan_nama
 * @property string $nama_pegawai
 * @property string $jadwaldokter_hari
 * @property string $Waktu
 * @property int $kuota
 */
class InfoJadwalDokterView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infojadwaldokter_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jadwaldokter_id', 'ruangan_id', 'instalasi_id', 'pegawai_id', 'kuota'], 'default', 'value' => null],
            [['jadwaldokter_id', 'ruangan_id', 'instalasi_id', 'pegawai_id', 'kuota'], 'integer'],
            [['Waktu'], 'string'],
            [['ruangan_nama', 'nama_pegawai'], 'string', 'max' => 50],
            [['jadwaldokter_hari'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
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
            'jadwaldokter_hari' => 'Jadwaldokter Hari',
            'Waktu' => 'Waktu',
            'kuota' => 'Kuota',
        ];
    }
}
