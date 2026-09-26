<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanrekapunitpelayanan_v".
 *
 * @property int $pendaftaran_id
 * @property string $tgl_pendaftaran
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property string $nama_dokter
 * @property string $status_pasien
 */
class LaporanRekapUnitPelayanan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanrekapunitpelayanan_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'default', 'value' => null],
            [['pendaftaran_id'], 'integer'],
            [['tgl_pendaftaran'], 'safe'],
            [['instalasi_nama', 'ruangan_nama', 'nama_dokter'], 'string', 'max' => 50],
            [['status_pasien'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'nama_dokter' => 'Nama Dokter',
            'status_pasien' => 'Status Pasien',
        ];
    }
}
