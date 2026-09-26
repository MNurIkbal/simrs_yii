<?php

namespace app\modules\gudang\models;

use Yii;
use app\components\DocoHelpers;
/**
 * This is the model class for table "validasipoobatdetail_t".
 *
 * @property int $validasipoobatdetail_id
 * @property int $validasipoobat_id
 * @property int $rekomendasiobatdetail_id
 * @property int $obatalkes_id
 * @property int $nilai_ro
 * @property int $qty_tersedia
 * @property int $ro_stok
 * @property int $rekomendasi
 * @property int $qty_po
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class CustomPoObatForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    
    
    public $qty_po;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty_po'], 'required'],
            // [['validasipoobatdetail_id', 'validasipoobat_id', 'rekomendasiobatdetail_id', 'obatalkes_id', 'nilai_ro', 'qty_tersedia', 'ro_stok', 'rekomendasi', 'qty_po', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            // [['validasipoobatdetail_id', 'validasipoobat_id', 'rekomendasiobatdetail_id', 'nilai_ro', 'qty_tersedia', 'ro_stok', 'rekomendasi', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            // [['additional_data'], 'string'],
            // [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            // [['is_deleted', 'is_active'], 'boolean'],
            // [['validasipoobatdetail_id'], 'unique'],
            [['qty_po'], 'customQty'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            
            'qty_po' => 'Qty Po',
            
        ];
    }

    public function customQty()
    {
        foreach ($this->qty_po as $key => $value) {
            $key = explode('-', $key);
            if(empty($value['qty'])) {
                DocoHelpers::multipleParseError($this, 'Qty PO tidak boleh kosong', 'qty_po['.$key[0].$key[1].']', $key[1].$key[0]);
            }
            if(empty($value['id_supplier'])) {
                DocoHelpers::multipleParseError($this, 'Nama Supplier tidak boleh kosong', 'supplier_id['.$key[0].$key[1].']', $key[1].$key[0]);
            }
        }
    }
}
