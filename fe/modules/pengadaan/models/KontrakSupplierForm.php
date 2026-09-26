<?php

namespace app\modules\pengadaan\models;

use Yii;

class KontrakSupplierForm extends \yii\base\Model
{
    public $purchasereq_id;
    public $kontraksupplier_id;
    public $kontraksupplier_no;
    public $supplier_id;
    public $payterm_id;
    public $jumlah_hari;
    public $pajak_id;
    public $persen_ppn;
    public $tgl_berlaku;
    public $additional_data;
    public $metode_bayar;
    public $dikirim_ke;
    public $contact_person;
    public $catatan;

    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;


    public static function tableName()
    {
        return 'kontraksupplier_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'kontraksupplier_no',
                    'supplier_id',
                    'payterm_id',
                    // 'metode_bayar'
                    'tgl_berlaku'
                ], 'required'
            ],
            [
                [
                    'kontraksupplier_no'
                ], 'string', 'max' => 35
            ],
            [
                [
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
            'kontraksupplier_no'    => 'No Kontrak Supplier',
            'supplier_id'           => 'Nama Supplier',
            'tgl_berlaku'           => 'Tanggal Berlaku',
            'payterm_id'            => 'Payment Term',
            'pajak_id'              => 'Tarif Pajak',
            'metode_bayar'          => 'Metode Pembayaran',
            'dikirim_ke'            => 'Dikirim Ke',
            'contact_person'        => 'Contact Person',
            'catatan'               => 'Catatan'
        ];
    }
}










