<?php

namespace SirsCore\models;

class InpostOperasiDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inpostoperasidetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'additional_data',
                    'created_by',
                    'created_date',
                    'daftartindakan_id',
                    'deleted_by',
                    'deleted_date',
                    'dokter_id',
                    'golonganoperasi_id',
                    'harga',
                    'inpostoperasi_id',
                    'is_active',
                    'is_cyto',
                    'is_deleted',
                    'is_penyulit',
                    'is_verifikasi',
                    'operasi_id',
                    'last_modified_date',
                    'deleted_date'
                ],
                'safe'
            ]
        ];
    }
}
