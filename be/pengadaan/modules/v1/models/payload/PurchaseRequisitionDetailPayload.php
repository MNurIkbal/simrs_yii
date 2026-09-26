<?php

namespace app\modules\v1\models\payload;

use Yii;

class PurchaseRequisitionDetailPayload extends \yii\base\Model
{
    public $obatalkes_id;
    public $purchasereq_id;
    public $satuan_id;
    public $qty_input;
    public $qty_konversi;
    public $satuankonversi_id;
    public $catatan;
    public $status;
    public $alasan;
    public $additional_data;
    public $created_date;
    public $is_deleted;
    public $is_active;    
    public $doi;
    public $ssmin;
    public $qty_sugesstion;
    public $qty_pr;
    public $qty_saatini;
    public $stok_gudang;
    public $stok_farmasi;
    public $stok_ruanganlain;
    public $last_7;
    public $last_14;
    public $last_30;
    public $qty_outstanding;
    public $move_category_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'obatalkes_id',
                    'purchasereq_id',
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
                    'stok_farmasi',
                    'stok_ruanganlain',
                    'last_7',
                    'last_14',
                    'last_30',
                    'qty_outstanding',
                    'move_category_id'
                ],
                'safe'
            ]
        ];
    }
}
