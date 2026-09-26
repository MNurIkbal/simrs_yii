<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "groupinacbg_m".
 *
 * @property int $groupinacbg_id
 * @property string $groupinacbg_nama
 * @property string $groupinacbg_namalainnya
 * @property string $groupinacbg_kode
 * @property string $catatan
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
class GroupInaCbgForm extends \yii\base\Model
{
    
    public $daftartindakan_ids;
    public $groupinacbg_id;
    public $groupinacbg_kode;
    public $groupinacbg_nama;
    public $groupinacbg_namalainnya;
    public $catatan;
    public $is_active;
    public $is_obat;
    public $daftartindakan_id;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $deleted_date;
    public $deleted_by;

    /**
     * @inheritdoc
     */

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['groupinacbg_nama','groupinacbg_kode', 'groupinacbg_namalainnya'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['catatan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'daftartindakan_ids'], 'safe'],
            [['is_deleted', 'is_active', 'is_obat'], 'boolean'],
            [['groupinacbg_nama', 'groupinacbg_namalainnya'], 'string', 'max' => 255],
            [['groupinacbg_kode'], 'string', 'max' => 100],
            // [['groupinacbg_kode'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'groupinacbg_id' => 'Groupinacbg ID',
            'groupinacbg_nama' => 'Nama Group INA CBG',
            'groupinacbg_namalainnya' => 'Nama Lainnya',
            'groupinacbg_kode' => 'Kode Group INA CBG',
            'catatan' => 'Catatan',
            'is_obat' => 'Kategori',
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
