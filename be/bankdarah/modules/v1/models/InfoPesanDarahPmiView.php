<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopesandarahpmi_v".
 *
 * @property int $pesandarahpmi_id
 * @property string $no_pesandarahpmi
 * @property string $tgl_pesandarahpmi
 * @property int $ruanganpemesan_id
 * @property int $supplier_id
 * @property string $supplier_nama
 * @property string $supplier_alamat
 * @property string $no_tlp
 * @property double $total_harga
 * @property int $total_kantongdarah
 * @property string $additional_data
 * @property bool $is_active
 * @property bool $is_deleted
 * @property int $pesandarahpmidetail_id
 * @property int $jenisdarah_id
 * @property string $jenisdarah_nama
 * @property int $golongandarah_id
 * @property string $golongandarah_nama
 * @property int $rhesus
 * @property string $tgl_mintakirim
 * @property string $wkt_mintakirim
 * @property int $qty_pesan
 * @property int $qty_diterima
 * @property int $qty_sisa
 * @property double $harga_satuan
 * @property double $sub_total
 */
class InfoPesanDarahPmiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopesandarahpmi_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pesandarahpmi_id', 'ruanganpemesan_id', 'supplier_id', 'total_kantongdarah', 'pesandarahpmidetail_id', 'jenisdarah_id', 'golongandarah_id', 'rhesus', 'qty_pesan', 'qty_diterima', 'qty_sisa'], 'default', 'value' => null],
            [['pesandarahpmi_id', 'ruanganpemesan_id', 'supplier_id', 'total_kantongdarah', 'pesandarahpmidetail_id', 'jenisdarah_id', 'golongandarah_id', 'rhesus', 'qty_pesan', 'qty_diterima', 'qty_sisa'], 'integer'],
            [['tgl_pesandarahpmi', 'tgl_mintakirim', 'wkt_mintakirim'], 'safe'],
            [['supplier_alamat', 'additional_data', 'golongandarah_nama'], 'string'],
            [['total_harga', 'harga_satuan', 'sub_total'], 'number'],
            [['is_active', 'is_deleted'], 'boolean'],
            [['no_pesandarahpmi', 'jenisdarah_nama'], 'string', 'max' => 255],
            [['supplier_nama'], 'string', 'max' => 100],
            [['no_tlp'], 'string', 'max' => 30],
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
            'supplier_id' => 'Supplier ID',
            'supplier_nama' => 'Supplier Nama',
            'supplier_alamat' => 'Supplier Alamat',
            'no_tlp' => 'No Tlp',
            'total_harga' => 'Total Harga',
            'total_kantongdarah' => 'Total Kantongdarah',
            'additional_data' => 'Additional Data',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'pesandarahpmidetail_id' => 'Pesandarahpmidetail ID',
            'jenisdarah_id' => 'Jenisdarah ID',
            'jenisdarah_nama' => 'Jenisdarah Nama',
            'golongandarah_id' => 'Golongandarah ID',
            'golongandarah_nama' => 'Golongandarah Nama',
            'rhesus' => 'Rhesus',
            'tgl_mintakirim' => 'Tgl Mintakirim',
            'wkt_mintakirim' => 'Wkt Mintakirim',
            'qty_pesan' => 'Qty Pesan',
            'qty_diterima' => 'Qty Diterima',
            'qty_sisa' => 'Qty Sisa',
            'harga_satuan' => 'Harga Satuan',
            'sub_total' => 'Sub Total',
        ];
    }
}
