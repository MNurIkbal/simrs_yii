<?php

namespace app\modules\v1\models;

use Yii;

class PurchaseRequisitionBarangDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'purchasereqbrgdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'barang_id',
                    'purchasereqbrg_id',
                    'satuan_id',
                    'qty_input',
                    'qty_konversi',
                    'satuankonversi_id',
                    'catatan',
                    'status',
                    'alasan',
                    'additional_data',
                    'created_date',
                    'is_deleted',
                    'is_active',
                    'doi',
                    'ssmin',
                    'qty_sugesstion',
                    'qty_pr',
                    'qty_saatini',
                    'stok_gudang',
                    'stok_ruanganlain',
                    'last_7',
                    'last_14',
                    'last_30',
                    'qty_outstanding'
                ],
                'safe'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [];
    }

    public function getPurchaseRequisition()
    {
        return $this->hasOne(PurchaseRequisitionBarang::className(),['purchasereqbrg_id'=>'purchasereqbrg_id']);
    }

    public function getHeader()
    {
        return $this->hasOne(PurchaseRequisitionBarang::className(),['purchasereqbrg_id'=>'purchasereqbrg_id']);
    }
}
