<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rekomendasiorderbarang_v".
 *
 * @property int $periodestok_id
 * @property string $periodestok_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $barang_id
 * @property string $barang_nama
 * @property string $barang_kode
 * @property int $nilai_ro
 * @property int $min_order
 * @property int $max_order
 * @property int $on_ro
 * @property int $on_po
 * @property int $sisa_stok
 * @property int $stok
 * @property int $ro_stok
 * @property int $rekomendasi
 */
class RekomendasiOrderBarangView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rekomendasiorderbarang_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['periodestok_id', 'instalasi_id', 'ruangan_id', 'barang_id', 'nilai_ro', 'min_order', 'max_order', 'on_ro', 'on_po', 'sisa_stok', 'stok', 'ro_stok', 'rekomendasi'], 'default', 'value' => null],
            [['periodestok_id', 'instalasi_id', 'ruangan_id', 'barang_id', 'nilai_ro', 'min_order', 'max_order', 'on_ro', 'on_po', 'sisa_stok', 'stok', 'ro_stok', 'rekomendasi'], 'integer'],
            [['periodestok_nama', 'barang_nama'], 'string', 'max' => 100],
            [['instalasi_nama', 'ruangan_nama', 'barang_kode'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
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
            'barang_kode' => 'Barang Kode',
            'nilai_ro' => 'Nilai Ro',
            'min_order' => 'Min Order',
            'max_order' => 'Max Order',
            'on_ro' => 'On Ro',
            'on_po' => 'On Po',
            'sisa_stok' => 'Sisa Stok',
            'stok' => 'Stok',
            'ro_stok' => 'Ro Stok',
            'rekomendasi' => 'Rekomendasi',
        ];
    }
}
