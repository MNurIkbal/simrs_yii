<?php

namespace app\modules\pengadaan\models;

use Yii;

/**
 * This is the model class for table "validasipobarang_t".
 *
 * @property int $validasipobarang_id
 * @property string $tgl_validasi
 * @property int $ruangan_id
 * @property int $pegawai_id
 * @property int $supplier_id supplier_m
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
class ValidasiPoBarangForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    
    public $validasipobarang_id;
    public $tgl_validasi;
    public $ruangan_id;
    public $pegawai_id;
    public $supplier_id;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $deleted_date;
    public $deleted_by;
    public $is_active;
    
    public static function tableName()
    {
        return 'validasipobarang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_validasi', 'ruangan_id', 'pegawai_id', 'supplier_id'], 'required'],
            [['tgl_validasi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['ruangan_id', 'pegawai_id', 'supplier_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'pegawai_id', 'supplier_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'validasipobarang_id' => 'Validasipobarang ID',
            'tgl_validasi' => 'Tgl Validasi',
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
            'supplier_id' => 'Supplier ID',
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
