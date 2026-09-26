<?php

namespace app\modules\v1\models;

use Yii;

class SupplierR extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'supplier_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pbf_id', 'propinsi_id', 'kabupaten_id', 'no_npwp', 'bank_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pbf_id', 'propinsi_id', 'kabupaten_id', 'no_npwp', 'bank_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'supplier_id' => 'Supplier ID',
            'pbf_id' => 'Pbf ID',
            'supplier_kode' => 'Supplier Kode',
            'supplier_nama' => 'Supplier Nama',
            'supplier_namalain' => 'Supplier Namalain',
            'supplier_alamat' => 'Supplier Alamat',
            'propinsi_id' => 'Propinsi ID',
            'kabupaten_id' => 'Kabupaten ID',
            'no_tlp' => 'No Tlp',
            'email' => 'Email',
            'no_fax' => 'No Fax',
            'no_npwp' => 'No Npwp',
            'no_rekening' => 'No Rekening',
            'nama_pemilikrek' => 'Nama Pemilikrek',
            'bank_id' => 'Bank ID',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}
?>