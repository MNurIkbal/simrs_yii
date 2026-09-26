<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "jenisobatalkes_m".
 *
 * @property integer $jenisobatalkes_id
 * @property string $jenisobatalkes_kode
 * @property string $jenisobatalkes_nama
 * @property string $jenisobatalkes_namalain
 * @property boolean $jenisobatalkes_farmasi
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 *
 * @property DiskonobatpenjaminM[] $diskonobatpenjaminMs
 * @property JenisobatalkesrekM[] $jenisobatalkesrekMs
 * @property ObatalkesM[] $obatalkesMs
 * @property ObatalkespenjaminM[] $obatalkespenjaminMs
 * @property SubjenisM[] $subjenisMs
 */
class JenisObatAlkesForm extends \yii\base\Model
{
    /**
     * @inheritdoc
     */

    public $jenisobatalkes_id;
    public $jenisobatalkes_kode;
    public $jenisobatalkes_nama;
    public $jenisobatalkes_namalain;
    public $jenisobatalkes_farmasi;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $group_jenisobat;
    public $service_group;
    public $service_category;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jenisobatalkes_kode',
                'jenisobatalkes_nama',
                'jenisobatalkes_namalain',
                'group_jenisobat',
                'service_group',
                'service_category'
            ], 'required',
                'message'=>'{attribute} Tidak boleh kosong'],
            [['jenisobatalkes_id',
                'group_jenisobat',
                'service_group',
                'service_category',
                'modified_count',
                'last_modified_by',
                'deleted_by'], 'integer'],
            [['jenisobatalkes_kode', 'jenisobatalkes_nama', 'jenisobatalkes_namalain', 'additional_data'], 'string'],
            [['jenisobatalkes_farmasi', 'is_deleted', 'is_active'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jenisobatalkes_id' => 'Jenisobatalkes ID',
            'jenisobatalkes_kode' => 'Kode Jenis Obat Alkes',
            'jenisobatalkes_nama' => 'Nama Jenis Obat Alkes',
            'jenisobatalkes_namalain' => 'Nama Lain Jenis Obat Alkes',
            'jenisobatalkes_farmasi' => 'Jenisobatalkes Farmasi',
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
            'group_jenisobat' => 'Group',
            'service_group' => 'Service Group',
            'service_category' => 'Service Category',
        ];
    }
}
