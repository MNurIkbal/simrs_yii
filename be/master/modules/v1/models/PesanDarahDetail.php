<?php

namespace app\modules\v1\models;

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
class PesanDarahDetail extends \Doco\components\DocoActiveRecord
{
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
            [['jenisdarah_id'], 'required'],
            [['tgl_mintakirim', 'wkt_mintakirim', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['harga_satuan', 'sub_total'], 'number'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
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
            'tgl_mintakirim' => 'Tgl Mintakirim',
            'wkt_mintakirim' => 'Wkt Mintakirim',
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
