<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ambulandetail_v".
 *
 * @property int $ambulan_id
 * @property int $barang_id
 * @property string $barang_nama
 * @property string $barang_merk
 * @property string $no_polisi
 * @property string $is_emergency
 * @property string $keterangan
 * @property int $ambulandetail_id
 * @property string $daftartindakan_nama
 * @property string $biaya_tetap
 * @property string $obatalkes_nama
 * @property int $qty
 * @property string $satuanunit_nama
 */
class AmbulanDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ambulandetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ambulan_id', 'barang_id', 'ambulandetail_id', 'qty'], 'default', 'value' => null],
            [['ambulan_id', 'barang_id', 'ambulandetail_id', 'qty'], 'integer'],
            [['is_emergency', 'keterangan', 'biaya_tetap', 'satuanunit_nama'], 'string'],
            [['barang_nama'], 'string', 'max' => 100],
            [['barang_merk'], 'string', 'max' => 50],
            [['no_polisi'], 'string', 'max' => 20],
            [['daftartindakan_nama'], 'string', 'max' => 200],
            [['obatalkes_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ambulan_id' => 'Ambulan ID',
            'barang_id' => 'Barang ID',
            'barang_nama' => 'Barang Nama',
            'barang_merk' => 'Barang Merk',
            'no_polisi' => 'No Polisi',
            'is_emergency' => 'Is Emergency',
            'keterangan' => 'Keterangan',
            'ambulandetail_id' => 'Ambulandetail ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'biaya_tetap' => 'Biaya Tetap',
            'obatalkes_nama' => 'Obatalkes Nama',
            'qty' => 'Qty',
            'satuanunit_nama' => 'Satuanunit Nama',
        ];
    }
}
