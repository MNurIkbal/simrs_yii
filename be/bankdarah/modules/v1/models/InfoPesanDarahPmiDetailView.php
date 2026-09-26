<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopesandarahpmidetail_v".
 *
 * @property int $pesandarahpmi_id
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
 * @property string $additional_data
 * @property bool $is_active
 * @property bool $is_deleted
 */
class InfoPesanDarahPmiDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopesandarahpmidetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pesandarahpmi_id', 'pesandarahpmidetail_id', 'jenisdarah_id', 'golongandarah_id', 'rhesus', 'qty_pesan', 'qty_diterima', 'qty_sisa'], 'default', 'value' => null],
            [['pesandarahpmi_id', 'pesandarahpmidetail_id', 'jenisdarah_id', 'golongandarah_id', 'rhesus', 'qty_pesan', 'qty_diterima', 'qty_sisa'], 'integer'],
            [['golongandarah_nama', 'additional_data'], 'string'],
            [['tgl_mintakirim', 'wkt_mintakirim'], 'safe'],
            [['harga_satuan', 'sub_total'], 'number'],
            [['is_active', 'is_deleted'], 'boolean'],
            [['jenisdarah_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pesandarahpmi_id' => 'Pesandarahpmi ID',
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
            'additional_data' => 'Additional Data',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
