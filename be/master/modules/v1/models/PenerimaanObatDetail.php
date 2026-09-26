<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "penerimaanobatdetail_t".
 *
 * @property int $penerimaanobatdetail_id
 * @property int $penerimaanobat_id
 * @property int $validasipoobatdetail_id jika dari validasipodetail_id
 * @property int $obatalkes_id
 * @property int $qty_po
 * @property int $po_balance
 * @property int $qty_diterima
 * @property int $s_konversiobt_id
 * @property string $tgl_kadaluarsa
 * @property string $no_batch
 * @property double $harga
 * @property double $discount
 * @property double $discount_rp
 * @property double $jumlah
 * @property string $keterangan
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
class PenerimaanObatDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'penerimaanobatdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['penerimaanobat_id', 'obatalkes_id', 'qty_po', 'po_balance', 'qty_diterima', 's_konversiobt_id'], 'required'],
            [['penerimaanobat_id', 'validasipoobatdetail_id', 'obatalkes_id', 'qty_po', 'po_balance', 'qty_diterima', 's_konversiobt_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['penerimaanobat_id', 'validasipoobatdetail_id', 'obatalkes_id', 'qty_po', 'po_balance', 'qty_diterima', 's_konversiobt_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_kadaluarsa', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['harga', 'discount', 'discount_rp', 'jumlah'], 'number'],
            [['keterangan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_batch'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'penerimaanobatdetail_id' => 'Penerimaanobatdetail ID',
            'penerimaanobat_id' => 'Penerimaanobat ID',
            'validasipoobatdetail_id' => 'Validasipoobatdetail ID',
            'obatalkes_id' => 'Obatalkes ID',
            'qty_po' => 'Qty Po',
            'po_balance' => 'Po Balance',
            'qty_diterima' => 'Qty Diterima',
            's_konversiobt_id' => 'S Konversiobt ID',
            'tgl_kadaluarsa' => 'Tgl Kadaluarsa',
            'no_batch' => 'No Batch',
            'harga' => 'Harga',
            'discount' => 'Discount',
            'discount_rp' => 'Discount Rp',
            'jumlah' => 'Jumlah',
            'keterangan' => 'Keterangan',
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
