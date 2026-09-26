<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "kelompokbarang_m".
 *
 * @property int $kelompokbarang_id
 * @property string $kelompokbarang_kode
 * @property string $kelompokbarang_nama
 * @property string $kelompokbarang_namalain
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
class KelompokBarangForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */

    public $kelompokbarang_id;
    public $kelompokbarang_kode;
    public $kelompokbarang_nama;
    public $kelompokbarang_namalain;
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
    public $servicegroup_id;
    public $servicecategory_id;

    public static function tableName()
    {
        return 'kelompokbarang_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kelompokbarang_kode', 'kelompokbarang_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['servicecategory_id', 'servicegroup_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelompokbarang_kode'], 'string', 'max' => 12],
            [['kelompokbarang_nama'], 'string', 'max' => 30],
            [['kelompokbarang_namalain'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kelompokbarang_id' => 'Kelompokbarang ID',
            'kelompokbarang_kode' => 'Kode Kelompok Barang',
            'kelompokbarang_nama' => 'Nama Kelompok Barang',
            'kelompokbarang_namalain' => 'Nama Lain Kelompok Barang',
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
            'servicecategory_id' => 'Service Category',
            'servicegroup_id' => 'Service Group'
        ];
    }
}
