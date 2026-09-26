<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopemesananbarang_v".
 *
 * @property int $pesanbarang_id
 * @property string $tgl_pesanbarang
 * @property int $instalasipemesan_id
 * @property string $instalasi_pemesan
 * @property int $ruanganpemesan_id
 * @property string $ruangan_pemesan
 * @property int $instalasi_id
 * @property string $instalasi_tujuan
 * @property int $ruangan_id
 * @property string $ruangan_tujuan
 * @property string $no_pemesanan
 * @property string $status_pesan
 */
class InfoStokBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infostokbarang_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'periodestok_id',
                'periodestok_nama',
                'instalasi_id',
                'instalasi_nama',
                'ruangan_id',
                'ruangan_nama',
                'barang_id',
                'barang_nama',
                'qty_masuk',
                'qty_keluar',
                'qty_dipesan',
                'qty_tersedia',
                'qty_stok',
                'tglperiodestok_awal',
                'tglperiodestok_akhir'
            ],'safe']
        ];
    }
}
