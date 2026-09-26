<?php

namespace app\modules\bankdarah\models;

use Yii;

/**
 * This is the model class for table "pesandarahpmi_t".
 *
 * @property int $pesandarahpmi_id
 * @property string $no_pesandarahpmi
 * @property string $tgl_pesandarahpmi
 * @property int $ruanganpemesan_id
 * @property int $supplier_id is_pmi=TRUE
 * @property double $total_harga
 * @property int $total_kantongdarah
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
class PesanDarahPmiForm extends \yii\base\Model
{
     public $pesandarahpmi_id;
     public $no_pesandarahpmi;
     public $tgl_pesandarahpmi;
     public $ruanganpemesan_id;
     public $supplier_id;
     public $total_harga;
     public $total_kantongdarah;
     public $obatalkes_id;
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
     public $alamat;
     public $no_tlp;
     
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pesandarahpmi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['supplier_id'], 'required'],
            [['pesandarahpmi_id', 'ruanganpemesan_id', 'supplier_id', 'total_kantongdarah', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pesandarahpmi_id', 'ruanganpemesan_id', 'supplier_id', 'total_kantongdarah', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_pesandarahpmi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['total_harga'], 'number'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pesandarahpmi'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pesandarahpmi_id' => 'Pesandarahpmi ID',
            'no_pesandarahpmi' => 'No Pesandarahpmi',
            'tgl_pesandarahpmi' => 'Tgl Pesandarahpmi',
            'ruanganpemesan_id' => 'Ruanganpemesan ID',
            'supplier_id' => 'Supplier',
            'total_harga' => 'Total Harga',
            'total_kantongdarah' => 'Total Kantongdarah',
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
