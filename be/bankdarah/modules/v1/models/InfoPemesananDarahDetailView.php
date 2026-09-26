<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopemesanandarahdetail_v".
 *
 * @property int $pesandarahdetail_id
 * @property int $pesandarah_id
 * @property int $jenisdarah_id
 * @property string $jenisdarah_nama
 * @property int $lama_penyimpanan
 * @property int $suhu_penyimpanan
 * @property double $harga
 * @property string $tgl_mintakirim
 * @property string $wkt_mintakirim
 * @property int $jumlah
 * @property double $harga_satuan
 * @property double $sub_total
 * @property int $status_pesan
 * @property string $additional_data
 * @property bool $is_deleted
 * @property bool $is_active
 */
class InfoPemesananDarahDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopemesanandarahdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pesandarahdetail_id', 'pesandarah_id', 'jenisdarah_id', 'lama_penyimpanan', 'suhu_penyimpanan', 'jumlah', 'status_pesan'], 'default', 'value' => null],
            [['pesandarahdetail_id', 'pesandarah_id', 'jenisdarah_id', 'lama_penyimpanan', 'suhu_penyimpanan', 'jumlah', 'status_pesan'], 'integer'],
            [['harga', 'harga_satuan', 'sub_total'], 'number'],
            [['tgl_mintakirim', 'wkt_mintakirim'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jenisdarah_nama'], 'string', 'max' => 255],
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
            'jenisdarah_id' => 'Jenisdarah ID',
            'jenisdarah_nama' => 'Jenisdarah Nama',
            'lama_penyimpanan' => 'Lama Penyimpanan',
            'suhu_penyimpanan' => 'Suhu Penyimpanan',
            'harga' => 'Harga',
            'tgl_mintakirim' => 'Tgl Mintakirim',
            'wkt_mintakirim' => 'Wkt Mintakirim',
            'jumlah' => 'Jumlah',
            'harga_satuan' => 'Harga Satuan',
            'sub_total' => 'Sub Total',
            'status_pesan' => 'Status Pesan',
            'additional_data' => 'Additional Data',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
        ];
    }
}
