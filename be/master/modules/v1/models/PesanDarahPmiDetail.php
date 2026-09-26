<?php

namespace app\modules\v1\models;

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
class PesanDarahPmiDetail extends \Doco\components\DocoActiveRecord
{
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
            [['pesandarahpmidetail_id', 'pesandarahpmi_id'], 'required'],
            [['pesandarahpmidetail_id', 'pesandarahpmi_id', 'jenisdarah_id', 'golongandarah_id', 'rhesus', 'qty_pesan', 'qty_diterima', 'qty_sisa', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pesandarahpmidetail_id', 'pesandarahpmi_id', 'jenisdarah_id', 'golongandarah_id', 'rhesus', 'qty_pesan', 'qty_diterima', 'qty_sisa', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_mintakirim', 'wkt_mintakirim', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
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
            'jenisdarah_id' => 'Jenisdarah ID',
            'golongandarah_id' => 'Golongandarah ID',
            'rhesus' => 'Rhesus',
            'tgl_mintakirim' => 'Tgl Mintakirim',
            'wkt_mintakirim' => 'Wkt Mintakirim',
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
