<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inforekomendasiobat_v".
 *
 * @property int $rekomendasiobat_id
 * @property string $tgl_rekomendasiobat
 * @property string $no_rekomendasiobat
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $pegawai_id
 * @property string $nama_pegawai
 * @property int $status_po
 * @property string $status
 */
class InfoRekomendasiObatView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inforekomendasiobat_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rekomendasiobat_id', 'ruangan_id', 'pegawai_id', 'status_po'], 'default', 'value' => null],
            [['rekomendasiobat_id', 'ruangan_id', 'pegawai_id', 'status_po'], 'integer'],
            [['tgl_rekomendasiobat'], 'safe'],
            [['no_rekomendasiobat'], 'string', 'max' => 255],
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
            'rekomendasiobat_id' => 'Rekomendasiobat ID',
            'tgl_rekomendasiobat' => 'Tgl Rekomendasiobat',
            'no_rekomendasiobat' => 'No Rekomendasiobat',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'status_po' => 'Status Po',
            'status' => 'Status',
        ];
    }
}
