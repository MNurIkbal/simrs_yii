<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "ambulan_m".
 *
 * @property int $ambulan_id
 * @property int $barang_id
 * @property string $no_polisi
 * @property int $km_terakhir
 * @property bool $is_emergency jenis ambulan
 * @property string $keterangan
 * @property int $status lookup_type='status_ambulan'
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
class AmbulanForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    
     public $ambulan_id;
     public $barang_id;
     public $no_polisi;
     public $km_terakhir;
     public $is_emergency;
     public $keterangan;
     public $status_ambulan;
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
     public $daftartindakan_id;
     public $obatalkes_id;
     public $qty;
     public $barang_nama;
     
    public static function tableName()
    {
        return 'ambulan_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['barang_id', 'no_polisi', 'is_emergency'], 'required'],
            [['barang_id', 'km_terakhir', 'status_ambulan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['barang_id', 'km_terakhir', 'status_ambulan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_emergency', 'is_deleted', 'is_active'], 'boolean'],
            [['keterangan', 'additional_data'], 'string'],
            [['no_polisi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['no_polisi'], 'string', 'max' => 20],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ambulan_id' => 'Ambulan ID',
            'barang_id' => 'Nama Barang',
            'no_polisi' => 'No Polisi',
            'km_terakhir' => 'Km Terakhir',
            'is_emergency' => 'Is Emergency',
            'keterangan' => 'Keterangan',
            'status_ambulan' => 'Status',
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
            'daftartindakan_id' => 'Tarif',
            'obatalkes_id' => 'Obat Alkes',
        ];
    }
}
