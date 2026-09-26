<?php

namespace app\modules\v1\models;

use Yii;
use Doco\components\DocoActiveRecord;

class HargaNettoObatView extends DocoActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'harganettoobat_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'transaksi_id', 'harga_netto_transaksi', 'harga_netto_sekarang', 'harga_disarankan'], 'integer'],
            [['tipe', 'obatalkes_nama', 'satuan_transaksi', 'satuan_disarankan'], 'string', 'max' => 200]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'obatalkes_id' => 'Obat Alkes ID',
            'transaksi_id' => 'Transaksi ID',
            'harga_netto_transaksi' => 'Harga Dasar Transaksi',
            'harga_netto_sekarang' => 'Harga Dasar Sekarang',
            'harga_disarankan' => 'Harga Dasar yang Disarankan',
            'tipe' => 'Tipe',
            'obatalkes_nama' => 'Nama Obat Alkes',
            'satuan_transaksi' => 'Satuan Transaksi',
            'satuan_disarankan' => 'Satuan Disarankan'
        ];
    }
}
