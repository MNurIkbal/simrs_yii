<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infostokopnamebarangdetail_v".
 *
 * @property int $stokopnamebarangdetail_id
 * @property int $stokopnamebarang_id
 * @property int $barang_id
 * @property int $ruangan_id
 * @property int $pegmengetahui_id
 * @property int $petugas_id
 * @property string $ruangan_nama
 * @property string $pegawai_mengetahui
 * @property string $pegawai_petugas
 * @property string $nostokopname
 * @property string $barang_nama
 * @property double $volume_fisik
 * @property double $volume_sistem
 * @property double $harganetto
 */
class InfoStokOpnameBarangDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infostokopnamebarangdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stokopnamebarangdetail_id', 'stokopnamebarang_id', 'barang_id', 'ruangan_id', 'pegmengetahui_id', 'petugas_id'], 'default', 'value' => null],
            [['stokopnamebarangdetail_id', 'stokopnamebarang_id', 'barang_id', 'ruangan_id', 'pegmengetahui_id', 'petugas_id'], 'integer'],
            [['nostokopname, noformulir, kondisibarang'], 'string'],
            [['volume_fisik', 'volume_sistem', 'harganetto'], 'number'],
            [['ruangan_nama', 'pegawai_mengetahui', 'pegawai_petugas'], 'string', 'max' => 50],
            [['barang_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stokopnamebarangdetail_id' => 'Stokopnamebarangdetail ID',
            'stokopnamebarang_id' => 'Stokopnamebarang ID',
            'barang_id' => 'Barang ID',
            'ruangan_id' => 'Ruangan ID',
            'pegmengetahui_id' => 'Pegmengetahui ID',
            'petugas_id' => 'Petugas ID',
            'ruangan_nama' => 'Ruangan Nama',
            'pegawai_mengetahui' => 'Pegawai Mengetahui',
            'pegawai_petugas' => 'Pegawai Petugas',
            'nostokopname' => 'Nostokopname',
            'barang_nama' => 'Barang Nama',
            'volume_fisik' => 'Volume Fisik',
            'volume_sistem' => 'Volume Sistem',
            'harganetto' => 'Harganetto',
        ];
    }
}
