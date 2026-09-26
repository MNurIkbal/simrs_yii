<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infostokobatalkes_v".
 *
 * @property int $barang_id
 * @property int $stok_sistem
 * @property string $barang_nama
 * @property string $kelompok_barang
 * @property string $subkelompok_barang
 * @property string $nobatch
 * @property string $tglkadaluarsa
 * @property int $barang_harganetto
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property int $periodestokbarang_id
 * @property string $tglperiodestok_awal
 * @property string $tglperiodestok_akhir
 * @property int $ruangan_id
 * @property int $instalasi_id
 * @property int $sop_barang_id
 * @property int $sop_sopbarangdetail_id
 * @property int $id_stok
 */
class TransaksiFormulirBarangView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infostokbarangdetail_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['barang_id', 'instalasi_id', 'ruangan_id', 'periodestokbarang_id', 'sop_barang_id', 'sop_sopbarangdetail_id', 'id_stok'], 'default', 'value' => null],
            [['barang_id', 'instalasi_id', 'ruangan_id'], 'integer'],
            [['stok_sistem', 'barang_nama', 'kelompok_barang', 'subkelompok_barang', 'nobatch', 'tglkadaluarsa', 'instalasi_nama'], 'string'],
            [['tglperiodestok_awal', 'tglperiodestok_akhir', 'tglkadaluarsa'], 'safe'],
            [['instalasi_nama', 'ruangan_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'barang_id' => 'Barang ID',
            'stok_sistem' => 'Stok Sistem',
            'barang_nama' => 'Nama Barang',
            'kelompok_barang' => 'Kelompok Barang',
            'subkelompok_barang' => 'Sub Kelompok Barang',
            'nobatch' => 'Nomor Batch',
            'tglkadaluarsa' => 'Tanggal Kadaluarsa',
            'barang_harganetto' => 'Harga Barang Netto',
            'instalasi_nama' => 'Nama Instalasi',
            'ruangan_nama' => 'Nama Ruangan',
            'periodestokbarang_id' => 'Periode Stok Barang ID',
            'tglperiodestok_awal' => 'Tanggal Periode Stok Awal',
            'tglperiodestok_akhir' => 'Tanggal Periode Stok Akhir',
            'ruangan_id' => 'Ruangan ID',
            'instalasi_id' => 'Instalasi ID',
            'sop_barang_id' => 'SOP Barang ID',
            'sop_sopbarangdetail_id' => 'SOP Barang Detail ID',
            'id_stok' => 'ID Stok',
        ];
    }
}
