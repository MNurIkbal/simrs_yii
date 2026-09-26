<?php

namespace app\modules\v1\models;

use Yii;

class KontrakSupplierDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'kontraksupplierdetail_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'kontraksupplier_id',
                    'obatalkes_id',
                    'satuankecil_id',
                    'qty_min',
                    'harga'
                ], 'required'
            ],
            [
                [
                    'qty_min'
                ], 'number', 'min' => 1
            ],
            [
                [
                    'kontraksupplier_id',
                    'obatalkes_id',
                    'kode_obat',
                    'nama_obat',
                    'satuankecil_id',
                    'satuankonv1_id',
                    'harga',
                    'qty_min',
                    'pengurang',
                    'penambah',
                    'total_harga',
                    'created_date',
                    'last_modified_date',
                    'deleted_date'
                ], 'safe'
            ],
            [
                [
                    'is_deleted',
                    'is_active'
                ], 'boolean'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kontraksupplier_id' => 'ID Kontrak Supplier',
            'obatalkes_id' => 'ID Obat Alkes',
            'kode_obat' => 'Kode Obat',
            'nama_obat' => 'Nama Obat',
            'satuankecil_id' => 'ID Satuan Kecil',
            'satuankonv1_id' => 'ID Satuan Konversi 1',
            'satuankonv2_id' => 'ID Satuan Konversi 2',
            'harga' => 'Harga',
            'qty_min' => 'Qty Minimum',
            'pengurang' => 'Pengurang',
            'penambah' => 'Penambah',
            'total_harga' => 'Total Harga'
        ];
    }
}