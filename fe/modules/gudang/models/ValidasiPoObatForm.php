<?php

namespace app\modules\gudang\models;

use Yii;

/**
 * This is the model class for table "validasipoobat_t".
 *
 * @property int $validasipoobat_id
 * @property string $tgl_validasai
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
class ValidasiPoObatForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    
    public $validasipoobat_id;
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
        return 'validasipoobat_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_validasi', 'ruangan_id', 'pegawai_id'], 'required'],
            [['validasipoobat_id', 'ruangan_id', 'pegawai_id', 'supplier_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['validasipoobat_id', 'ruangan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_validasi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['validasipoobat_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'validasipoobat_id' => 'Validasipoobat ID',
            'tgl_validasi' => 'Tgl Validasai',
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
