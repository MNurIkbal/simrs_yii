<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "validasipobarangdetail_t".
 *
 * @property int $validasipobarangdetail_id
 * @property int $validasipobarang_id
 * @property int $rekomendasibarangdetail_id
 * @property int $barang_id
 * @property int $nilai_ro
 * @property int $qty_tersedia
 * @property int $ro_stok
 * @property int $rekomendasi
 * @property int $qty_po
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
class ValidasiPoBarangDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */


    public static function tableName()
    {
        return 'validasipobarangdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['validasipobarang_id', 'rekomendasibarangdetail_id', 'barang_id', 'qty_po'], 'required'],
            [['validasipobarang_id', 'rekomendasibarangdetail_id', 'barang_id', 'nilai_ro', 'qty_tersedia', 'ro_stok', 'rekomendasi', 'qty_po', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['validasipobarang_id', 'rekomendasibarangdetail_id', 'barang_id', 'nilai_ro', 'qty_tersedia', 'ro_stok', 'rekomendasi', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', "qty_retur"], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'validasipobarangdetail_id' => 'Validasipobarangdetail ID',
            'validasipobarang_id' => 'Validasipobarang ID',
            'rekomendasibarangdetail_id' => 'Rekomendasibarangdetail ID',
            'barang_id' => 'Barang ID',
            'nilai_ro' => 'Nilai Ro',
            'qty_tersedia' => 'Qty Tersedia',
            'ro_stok' => 'Ro Stok',
            'rekomendasi' => 'Rekomendasi',
            'qty_po' => 'Qty Po',
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
