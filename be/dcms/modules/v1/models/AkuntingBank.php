<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "bank_m".
 *
 * @property int $bank_id 
 * @property int $propinsi_id 
 * @property int $kabupaten_id 
 * @property varchar $nama_bank 
 * @property varchar $cabang 
 * @property int $no_rekening 
 * @property varchar $nama_pemilikrek 
 * @property int $no_tlp 
 * @property int $no_fax 
 * @property varchar $email 
 * @property text $alamat_bank 
 * @property text $additional_data 
 * @property datetime $created_date 
 * @property int $created_by 
 * @property int $modified_count 
 * @property datetime $last_modified_date 
 * @property int $last_modified_by 
 * @property boolean $is_deleted 
 * @property boolean $is_active 
 * @property datetime $deleted_date 
 * @property int $deleted_by 

 */
class AkuntingBank extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bank_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['propinsi_id','kabupaten_id', 'no_tlp', 'no_fax', 'email', 'alamat_bank', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['bank_id', 'propinsi_id','kabupaten_id', 'no_rekening', 'no_fax', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data', 'no_tlp'], 'string'],
            [['bank_id', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nama_bank','cabang','nama_pemilikrek'], 'string', 'max' => 255],
            [['bank_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bank_id' => "ID Bank",
            'propinsi_id' => "ID Provinsi",
            'kabupaten_id' => "ID Kabupaten",
            'nama_bank' => "Nama Bank",
            'cabang' => "Cabang",
            'no_rekening' => "Nomor Rekening",
            'nama_pemilikrek' => "Nama Pemiliki Rekening",
            'no_tlp' => "Nomor Telepon",
            'no_fax' => "Nomor Fax",
            'email' => "Email",
            'alamat_bank' => "Alamat Bank",
            'additional_data' => "Additional Data",
            'created_date' => "Created Date",
            'created_by' => "Created By",
            'modified_count' => "Modified Count",
            'last_modified_date' => "Last Modified Date",
            'last_modified_by' => "Last Modified By",
            'is_deleted' => "Is Deleted",
            'is_active' => "Is Active",
            'deleted_date' => "Deleted Date",
            'deleted_by' => "Deleted By",
        ];
    }
}
