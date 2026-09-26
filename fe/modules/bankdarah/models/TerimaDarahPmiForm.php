<?php

namespace app\modules\bankdarah\models;

use Yii;

/**
 * This is the model class for table "terimadarahpmi_t".
 *
 * @property int $terimadarahpmi_id
 * @property int $pesandarahpmi_id
 * @property int $ruangan_id
 * @property int $penerima_id pegawai
 * @property string $tgl_terimadarahpmi
 * @property string $no_terimadarahpmi
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
class TerimaDarahPmiForm extends \yii\base\Model
{
     public $terimadarahpmi_id;
     public $pesandarahpmi_id;
     public $ruangan_id;
     public $penerima_id;
     public $tgl_terimadarahpmi;
     public $no_terimadarahpmi;
     public $total_harga;
     public $total_kantongdarah;
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
     public $no_pesandarahpmi;
     public $no_tlp;
     public $nama_pmi;
     public $supplier_alamat;
     public $list_data;
     
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'terimadarahpmi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pesandarahpmi_id', 'ruangan_id', 'penerima_id'], 'required'],
            [['pesandarahpmi_id', 'ruangan_id', 'penerima_id', 'total_kantongdarah', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pesandarahpmi_id', 'ruangan_id', 'penerima_id', 'total_kantongdarah', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['no_pesandarahpmi', 'no_tlp', 'nama_pmi', 'supplier_alamat', 'tgl_terimadarahpmi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['total_harga'], 'number'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_terimadarahpmi'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'terimadarahpmi_id' => 'Terimadarahpmi ID',
            'pesandarahpmi_id' => 'Pesandarahpmi ID',
            'ruangan_id' => 'Ruangan ID',
            'penerima_id' => 'Petugas Penerima',
            'tgl_terimadarahpmi' => 'Tgl Terimadarahpmi',
            'no_terimadarahpmi' => 'No Terimadarahpmi',
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
