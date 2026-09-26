<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "reseptempdetail_v".
 *
 * @property int $reseptemp_id
 * @property int $reseptemp_nama
 * @property int $dokter_id
 * @property string $nama_pegawai
 * @property int $racikan_id
 * @property string $racikan_nama
 * @property int $rke
 * @property int $obatalkes_id
 * @property string $obatalkes_nama
 * @property int $satuankecil_id
 * @property string $satuanunit_nama
 * @property int $qty
 * @property int $signa_id
 */
class ResepTempDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reseptempdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reseptemp_id', 'reseptemp_nama', 'dokter_id', 'racikan_id', 'rke', 'obatalkes_id', 'satuankecil_id', 'qty', 'signa_id'], 'default', 'value' => null],
            [['reseptemp_id', 'reseptemp_nama', 'dokter_id', 'racikan_id', 'rke', 'obatalkes_id', 'satuankecil_id', 'qty', 'signa_id'], 'integer'],
            [['satuanunit_nama'], 'string'],
            [['nama_pegawai', 'racikan_nama'], 'string', 'max' => 50],
            [['obatalkes_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reseptemp_id' => 'Reseptemp ID',
            'reseptemp_nama' => 'Reseptemp Nama',
            'dokter_id' => 'Dokter ID',
            'nama_pegawai' => 'Nama Pegawai',
            'racikan_id' => 'Racikan ID',
            'racikan_nama' => 'Racikan Nama',
            'rke' => 'Rke',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_nama' => 'Obatalkes Nama',
            'satuankecil_id' => 'Satuankecil ID',
            'satuanunit_nama' => 'Satuanunit Nama',
            'qty' => 'Qty',
            'signa_id' => 'Signa ID',
        ];
    }
}
