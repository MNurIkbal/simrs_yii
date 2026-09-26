<?php

namespace app\modules\pengadaan\models;

use Yii;
use app\components\DocoHelpers;

class CustomPoManualDetail extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    
    public $item_id;
    public $satuan_id;
    public $qty;
    public $harga;
    public $discount;
    public $discount_rp;
    public $total_harga;
    public $is_disc_nominal;

    public function rules()
    {
        return [
            [['item_id'], 'customValidation'],
        ];
    }

    public function customValidation()
    {
        foreach ($this->item_id as $key => $value) {
            if(empty($value['item_id'])) {
                DocoHelpers::multipleParseError($this, 'Nama Obat/Barang tidak boleh kosong', 'item_id', $key);
            }
            if(empty($value['qty'])) {
                DocoHelpers::multipleParseError($this, 'Qty tidak boleh kosong', 'qty', $key);
            }
            if(empty($value['satuan_id'])) {
                DocoHelpers::multipleParseError($this, 'Satuan tidak boleh kosong', 'satuan_id', $key);
            }
            if(empty($value['harga'])) {
                DocoHelpers::multipleParseError($this, 'Harga tidak boleh kosong', 'harga', $key);
            }
        }
    }
}
