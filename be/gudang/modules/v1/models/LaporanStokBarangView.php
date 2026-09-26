<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanstokbarang_v".
 *
 * @property int $periodestok_id
 * @property string $periodestok_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $barang_id
 * @property string $barang_nama
 * @property int $qty_masuk
 * @property int $qty_keluar
 * @property int $qty_dipesan
 * @property int $qty_tersedia
 * @property int $qty_stok
 * @property string $tglperiodestok_awal
 * @property string $tglperiodestok_akhir
 */
class LaporanStokBarangView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanstokbarang_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['periodestok_id', 'instalasi_id', 'ruangan_id', 'barang_id', 'qty_masuk', 'qty_keluar', 'qty_dipesan', 'qty_tersedia', 'qty_stok'], 'default', 'value' => null],
            [['periodestok_id', 'instalasi_id', 'ruangan_id', 'barang_id', 'qty_masuk', 'qty_keluar', 'qty_dipesan', 'qty_tersedia', 'qty_stok'], 'integer'],
            [['tglperiodestok_awal', 'tglperiodestok_akhir'], 'safe'],
            [['periodestok_nama', 'barang_nama'], 'string', 'max' => 100],
            [['instalasi_nama', 'ruangan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'periodestok_id' => 'Periodestok ID',
            'periodestok_nama' => 'Periodestok Nama',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'barang_id' => 'Barang ID',
            'barang_nama' => 'Barang Nama',
            'qty_masuk' => 'Qty Masuk',
            'qty_keluar' => 'Qty Keluar',
            'qty_dipesan' => 'Qty Dipesan',
            'qty_tersedia' => 'Qty Tersedia',
            'qty_stok' => 'Qty Stok',
            'tglperiodestok_awal' => 'Tglperiodestok Awal',
            'tglperiodestok_akhir' => 'Tglperiodestok Akhir',
        ];
    }
}
