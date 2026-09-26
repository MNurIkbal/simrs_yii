<?php

namespace app\modules\bankdarah\models;

use Yii;

/**
 * This is the model class for table "pesandarahdetail_t".
 *
 * @property int $pesandarahdetail_id
 * @property int $pesandarah_id
 * @property int $jenisdarah_id
 * @property string $tgl_mintakirim
 * @property string $wkt_mintakirim
 * @property int $jumlah
 * @property double $harga_satuan
 * @property double $sub_total
 * @property int $status_pesan
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
class PesanDarahDetailForm extends \yii\base\Model
{
     public $pesandarahdetail_id;
     public $pesandarah_id;
     public $jenisdarah_id;
     public $tgl_mintakirim;
     public $wkt_mintakirim;
     public $jumlah;
     public $harga_satuan;
     public $sub_total;
     public $status_pesan;
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
    public static function tableName()
    {
        return 'pesandarahdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pesandarah_id', 'jenisdarah_id', 'jumlah', 'status_pesan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pesandarah_id', 'jenisdarah_id', 'jumlah', 'status_pesan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jenisdarah_id', 'wkt_mintakirim'], 'required'],
            [['tgl_mintakirim', 'wkt_mintakirim', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['harga_satuan', 'sub_total'], 'number'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jumlah'], 'number', 'min' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pesandarahdetail_id' => 'Pesandarahdetail ID',
            'pesandarah_id' => 'Pesandarah ID',
            'jenisdarah_id' => 'Jenis Darah',
            'tgl_mintakirim' => 'Tgl Mintakirim',
            'wkt_mintakirim' => 'Waktu di Kirim',
            'jumlah' => 'Jumlah',
            'harga_satuan' => 'Harga Satuan',
            'sub_total' => 'Sub Total',
            'status_pesan' => 'Status Pesan',
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
