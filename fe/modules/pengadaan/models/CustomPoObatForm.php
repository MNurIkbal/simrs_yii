<?php

namespace app\modules\pengadaan\models;

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
        $total_validasi = 0;
        foreach ($this->qty_po as $key => $value) {
            $key = explode('-', $key);
            if (!empty($value['qty'])) {
                // DocoHelpers::multipleParseError($this, 'Qty PO tidak boleh kosong', 'qty_po['.$key[0].$key[1].']', $key[1].$key[0]);
                if (empty($value['id_supplier'])) {
                    DocoHelpers::multipleParseError($this, 'Nama Supplier tidak boleh kosong', 'supplier_id['.$key[0].$key[1].']', $key[1].$key[0]);
                }
                $total_validasi++;
            }
        }
        if ($total_validasi == 0) {
            $this->addError('pegawai_id','Minimal 1 PO Obat / Barang dan tidak boleh kosong');
        }
    }
}
