<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "adjusmenobatmasuk_t".
 *
 * @property int $adjusmenobatmasuk_id
 * @property int $adjusmenobat_id
 * @property int $obatalkes_id
 * @property string $tgl_kadaluarsa
 * @property int $qty
 * @property int $satuankecil_id
 * @property double $harga_netto
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
class AdjusmenObatMasuk extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'adjusmenobatmasuk_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'qty', 'satuankecil_id', 'harga_netto', 'tgl_kadaluarsa'], 'required'],
            [['adjusmenobatmasuk_id', 'adjusmenobat_id', 'obatalkes_id', 'qty', 'satuankecil_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['adjusmenobatmasuk_id', 'adjusmenobat_id', 'obatalkes_id', 'qty', 'satuankecil_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['harga_netto_satuan', 'no_batch', 'keterangan', 'qty_konversi', 'satuankonversi_id', 'satuanbesar_id', 'tgl_kadaluarsa', 'created_date',
            'last_modified_date', 'deleted_date'], 'safe'],
            [['harga_netto', 'harga_netto_satuan'], 'number'],
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
            'adjusmenobatmasuk_id' => 'Adjusmenobatmasuk ID',
            'adjusmenobat_id' => 'Adjusmenobat ID',
            'obatalkes_id' => 'Nama Obat Alkes',
            'tgl_kadaluarsa' => 'Tanggal Kadaluarsa',
            'qty' => 'Jumlah Penerimaan',
            'satuankecil_id' => 'Satuan Kecil',
            'harga_netto' => 'Harga Netto',
            'harga_netto_satuan' => 'Harga Netto Satuan',
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
