<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infostokbarangdetail_v".
 *
 * @property int $barang_id
 * @property double $stok_sistem
 * @property string $barang_nama
 * @property string $nobatch
 * @property string $tglkadaluarsa
 * @property double $barang_harganetto
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property int $periodestokbarang_id
 * @property string $tglperiodestok_awal
 * @property string $tglperiodestok_akhir
 * @property int $ruangan_id
 * @property int $instalasi_id
 * @property int $sop_barang_id
 * @property int $sop_sopbarangdetail_id
 */
class InfoStokBarangDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infostokbarangdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['barang_id', 'periodestokbarang_id', 'ruangan_id', 'instalasi_id', 'sop_barang_id', 'sop_sopbarangdetail_id'], 'default', 'value' => null],
            [['barang_id', 'periodestokbarang_id', 'ruangan_id', 'instalasi_id', 'sop_barang_id', 'sop_sopbarangdetail_id'], 'integer'],
            [['stok_sistem', 'barang_harganetto'], 'number'],
            [['tglkadaluarsa', 'tglperiodestok_awal', 'tglperiodestok_akhir'], 'safe'],
            [['barang_nama', 'nobatch'], 'string', 'max' => 100],
            [['instalasi_nama', 'ruangan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'barang_id' => 'Barang ID',
            'stok_sistem' => 'Stok Sistem',
            'barang_nama' => 'Barang Nama',
            'nobatch' => 'Nobatch',
            'tglkadaluarsa' => 'Tglkadaluarsa',
            'barang_harganetto' => 'Barang Harganetto',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'periodestokbarang_id' => 'Periodestokbarang ID',
            'tglperiodestok_awal' => 'Tglperiodestok Awal',
            'tglperiodestok_akhir' => 'Tglperiodestok Akhir',
            'ruangan_id' => 'Ruangan ID',
            'instalasi_id' => 'Instalasi ID',
            'sop_barang_id' => 'Sop Barang ID',
            'sop_sopbarangdetail_id' => 'Sop Sopbarangdetail ID',
        ];
    }
}
