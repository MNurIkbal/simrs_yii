<?php

namespace app\modules\v1\models;

/**
 * This is the model class for table "pemesananproduksiobatdetail_t".
 *
 * @property int $pemesananproduksiobatdetail_id
 * @property int $pemesananproduksiobat_id
 * @property int $obatalkes_id
 * @property int $qty
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property string $deleted_date
 * @property int $deleted_by
 * @property bool $is_deleted
 * @property bool $is_active
 */
class PemesananProduksiObatDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pemesananproduksiobatdetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pemesananproduksiobat_id', 'obatalkes_id', 'satuan_id', 'qty_konversi', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['qty'], 'number'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pemesananproduksiobatdetail_id' => 'Pemesanan Produksi Obat Detail ID',
            'pemesananproduksiobat_id' => 'Pemesanan Produksi Obat ID',
            'obatalkes_id' => 'Obat Alkes ID',
            'qty' => 'Jumlah Pesan',
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
