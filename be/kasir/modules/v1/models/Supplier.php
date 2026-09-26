<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "supplier_m".
 *
 * @property int $supplier_id
 * @property int $pbf_id
 * @property string $supplier_kode
 * @property string $supplier_nama
 * @property string $supplier_namalain
 * @property string $supplier_alamat
 * @property int $propinsi_id
 * @property int $kabupaten_id
 * @property string $no_tlp
 * @property string $email
 * @property string $no_fax
 * @property int $no_npwp
 * @property string $no_rekening
 * @property string $nama_pemilikrek
 * @property int $bank_id
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
class Supplier extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'supplier_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pbf_id', 'propinsi_id', 'kabupaten_id', 'no_npwp', 'bank_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pbf_id', 'propinsi_id', 'kabupaten_id', 'no_npwp', 'bank_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['supplier_kode', 'supplier_nama', 'supplier_alamat'], 'required'],
            [['supplier_alamat', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['supplier_kode'], 'string', 'max' => 25],
            [['supplier_nama', 'supplier_namalain', 'email'], 'string', 'max' => 100],
            [['no_tlp'], 'string', 'max' => 30],
            [['no_fax'], 'string', 'max' => 50],
            [['no_rekening', 'nama_pemilikrek'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
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
