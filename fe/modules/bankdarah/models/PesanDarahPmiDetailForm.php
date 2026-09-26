<?php

namespace app\modules\bankdarah\models;

use Yii;

/**
 * This is the model class for table "pesandarahpmidetail_t".
 *
 * @property int $pesandarahpmidetail_id
 * @property int $pesandarahpmi_id
 * @property int $jenisdarah_id
 * @property int $golongandarah_id lookup_type='golongan_darah'
 * @property int $rhesus lookup_type='rhesus'
 * @property string $tgl_mintakirim
 * @property string $wkt_mintakirim
 * @property int $qty_pesan
 * @property int $qty_diterima
 * @property int $qty_sisa
 * @property double $harga_satuan
 * @property double $sub_total
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
class PesanDarahPmiDetailForm extends \yii\base\Model
{
     public $pesandarahpmidetail_id;
     public $pesandarahpmi_id;
     public $jenisdarah_id;
     public $golongandarah_id;
     public $rhesus;
     public $tgl_mintakirim;
     public $wkt_mintakirim;
     public $qty_pesan;
     public $qty_diterima;
     public $qty_sisa;
     public $harga_satuan;
     public $sub_total;
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
        return 'pesandarahpmidetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenisdarah_id', 'golongandarah_id', 'tgl_mintakirim', 'wkt_mintakirim', 'qty_pesan'], 'required'],
            [['pesandarahpmidetail_id', 'pesandarahpmi_id', 'jenisdarah_id', 'golongandarah_id', 'rhesus', 'qty_pesan', 'qty_diterima', 'qty_sisa', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pesandarahpmidetail_id', 'pesandarahpmi_id', 'jenisdarah_id', 'golongandarah_id', 'rhesus', 'qty_pesan', 'qty_diterima', 'qty_sisa', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_mintakirim', 'wkt_mintakirim', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['qty_pesan'], 'number', 'min' => 1],
            [['harga_satuan', 'sub_total'], 'number'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['pesandarahpmidetail_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pesandarahpmidetail_id' => 'Pesandarahpmidetail ID',
            'pesandarahpmi_id' => 'Pesandarahpmi ID',
            'jenisdarah_id' => 'Jenis Darah',
            'golongandarah_id' => 'Golongan Darah',
            'rhesus' => 'Rhesus',
            'tgl_mintakirim' => 'Tgl Mintakirim',
            'wkt_mintakirim' => 'Waktu di Kirim',
            'qty_pesan' => 'Qty Pesan',
            'qty_diterima' => 'Qty Diterima',
            'qty_sisa' => 'Qty Sisa',
            'harga_satuan' => 'Harga Satuan',
            'sub_total' => 'Sub Total',
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
