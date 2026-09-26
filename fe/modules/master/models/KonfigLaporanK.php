<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "konfiglaporan_k".
 *
 * @property int $konfiglaporan_id
 * @property int $ruangan_id
 * @property string $nama_laporan
 * @property string $filter_kebutuhan
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
class KonfigLaporanK extends \yii\db\ActiveRecord
{

    public $konfiglaporan_id;
    public $ruangan_id;
    public $nama_laporan;
    public $filter_kebutuhan;
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

    /**
     * {@inheritdoc}
     */
    public static function tableName () {
        return 'konfiglaporan_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['konfiglaporan_id'], 'required'],
            [['konfiglaporan_id', 'ruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['konfiglaporan_id', 'ruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['filter_kebutuhan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nama_laporan'], 'string', 'max' => 255],
            [['konfiglaporan_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'konfiglaporan_id' => 'Konfiglaporan ID',
            'ruangan_id' => 'Ruangan ID',
            'nama_laporan' => 'Nama Laporan',
            'filter_kebutuhan' => 'Filter Kebutuhan',
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
