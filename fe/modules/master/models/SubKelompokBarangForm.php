<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "subkelompokbarang_m".
 *
 * @property int $subkelompokbarang_id
 * @property int $kelompokbarang_id
 * @property string $subkelompok_kode
 * @property string $subkelompok_nama
 * @property string $subkelompok_namalain
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
class SubKelompokBarangForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    
    public $subkelompokbarang_id;
    public $kelompokbarang_id;
    public $subkelompok_kode;
    public $subkelompok_nama;
    public $subkelompok_namalain;
    public $is_active;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $deleted_date;
    public $deleted_by;

    public static function tableName()
    {
        return 'subkelompokbarang_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kelompokbarang_id', 'subkelompok_kode', 'subkelompok_nama'], 'required'],
            [['kelompokbarang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kelompokbarang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['subkelompok_kode'], 'string', 'max' => 12],
            [['subkelompok_nama'], 'string', 'max' => 30],
            [['subkelompok_namalain'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'subkelompokbarang_id' => 'Subkelompokbarang ID',
            'kelompokbarang_id' => 'Nama Kelompok',
            'subkelompok_kode' => 'Kode Sub Kelompok Barang',
            'subkelompok_nama' => 'Nama Sub Kelompok Barang',
            'subkelompok_namalain' => 'Nama Lain Sub Kelompok Barang',
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
