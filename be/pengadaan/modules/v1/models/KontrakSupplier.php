<?php

namespace app\modules\v1\models;

use Yii;

class KontrakSupplier extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */

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
                    'payterm_id'
                ], 'required'
            ],
            [
                [
                    'kontraksupplier_no'
                ], 'string', 'max' => 35
            ],
            [
                [
                    'kontraksupplier_no',
                    'supplier_id',
                    'payterm_id',
                    'pajak_id',
                    'metode_bayar',
                    'tgl_berlaku',
                    'dikirim_ke',
                    'contact_person',
                    'catatan',
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
