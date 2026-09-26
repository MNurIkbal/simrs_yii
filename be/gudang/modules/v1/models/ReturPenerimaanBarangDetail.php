<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "returpenerimaanbarangdetail_t".
 *
 * @property int $returpenerimaanbarangdetail_id
 * @property int $returpenerimaanbarang_id
 * @property int $penerimaanbarang_id
 * @property int $barang_id
 * @property int $satuanbesar_id
 * @property string $tgl_kadaluarsa
 * @property int $qty_retur
 * @property int $penerimaanbarangdetail_id
 * @property int $qty_input
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
class ReturPenerimaanBarangDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'returpenerimaanbarangdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['returpenerimaanbarangdetail_id', 'returpenerimaanbarang_id', 'barang_id'], 'required'],
            [['returpenerimaanbarangdetail_id', 'returpenerimaanbarang_id', 'penerimaanbarang_id', 'barang_id', 'satuanbesar_id', 'qty_retur', 'penerimaanbarangdetail_id', 'qty_input', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['returpenerimaanbarangdetail_id', 'returpenerimaanbarang_id', 'penerimaanbarang_id', 'barang_id', 'satuanbesar_id', 'qty_retur', 'penerimaanbarangdetail_id', 'qty_input', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_kadaluarsa', 'created_date', 'last_modified_date', 'deleted_date','penerimaansuppbrgdetail_id'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['returpenerimaanbarangdetail_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'returpenerimaanbarangdetail_id' => 'Returpenerimaanbarangdetail ID',
            'returpenerimaanbarang_id' => 'Returpenerimaanbarang ID',
            'penerimaanbarang_id' => 'Penerimaanbarang ID',
            'barang_id' => 'Barang ID',
            'satuanbesar_id' => 'Satuanbesar ID',
            'tgl_kadaluarsa' => 'Tgl Kadaluarsa',
            'qty_retur' => 'Qty Retur',
            'penerimaanbarangdetail_id' => 'Penerimaanbarangdetail ID',
            'qty_input' => 'Qty Input',
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
