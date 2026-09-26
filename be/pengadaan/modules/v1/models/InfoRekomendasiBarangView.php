<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inforekomendasibarang_v".
 *
 * @property int $rekomendasibarang_id
 * @property string $tgl_rekomendasibarang
 * @property string $no_rekomendasibarang
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $pegawai_id
 * @property string $nama_pegawai
 * @property int $status_po
 * @property string $status
 */
class InfoRekomendasiBarangView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inforekomendasibarang_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rekomendasibarang_id', 'ruangan_id', 'pegawai_id', 'status_po'], 'default', 'value' => null],
            [['rekomendasibarang_id', 'ruangan_id', 'pegawai_id', 'status_po'], 'integer'],
            [['tgl_rekomendasibarang'], 'safe'],
            [['no_rekomendasibarang'], 'string', 'max' => 255],
            [['ruangan_nama', 'nama_pegawai'], 'string', 'max' => 50],
            [['status'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rekomendasibarang_id' => 'Rekomendasibarang ID',
            'tgl_rekomendasibarang' => 'Tgl Rekomendasibarang',
            'no_rekomendasibarang' => 'No Rekomendasibarang',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'status_po' => 'Status Po',
            'status' => 'Status',
        ];
    }
}
