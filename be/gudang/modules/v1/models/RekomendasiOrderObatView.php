<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rekomendasiorderobat_v".
 *
 * @property int $periodestok_id
 * @property string $periodestok_nama
 * @property string $instalasi_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $obatalkes_id
 * @property string $obatalkes_nama
 * @property string $obatalkes_kode
 * @property int $nilai_ro
 * @property int $qty_tersedia
 * @property int $ro_stok
 * @property int $rekomendasi
 * @property int $min_order
 * @property int $max_order
 */
class RekomendasiOrderObatView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rekomendasiorderobat_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['periodestok_id', 'ruangan_id', 'obatalkes_id', 'nilai_ro', 'qty_tersedia', 'ro_stok', 'rekomendasi', 'min_order', 'max_order'], 'default', 'value' => null],
            [['periodestok_id', 'ruangan_id', 'obatalkes_id', 'nilai_ro', 'qty_tersedia', 'ro_stok', 'rekomendasi', 'min_order', 'max_order'], 'integer'],
            [['obatalkes_kode'], 'string'],
            [['periodestok_nama'], 'string', 'max' => 100],
            [['instalasi_nama', 'ruangan_nama'], 'string', 'max' => 50],
            [['obatalkes_nama'], 'string', 'max' => 255],
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
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_nama' => 'Obatalkes Nama',
            'obatalkes_kode' => 'Obatalkes Kode',
            'nilai_ro' => 'Nilai Ro',
            'qty_tersedia' => 'Qty Tersedia',
            'ro_stok' => 'Ro Stok',
            'rekomendasi' => 'Rekomendasi',
            'min_order' => 'Min Order',
            'max_order' => 'Max Order',
        ];
    }
}
