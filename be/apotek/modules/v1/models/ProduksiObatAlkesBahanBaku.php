<?php

namespace app\modules\v1\models;

/**
 * This is the model class for table "produksiobatalkesbahanbaku_t".
 * @property int $produksiobatalkesbahanbaku_id
 * @property int $produksiobatalkesdetail_id
 * @property int $obatalkes_id
 * @property int $satuankecil_id
 * @property int $qty_obat
 * @property int $pemesananproduksiobat_id
 * @property float $harganetto
 * @property float $harganetto_satuan
 * @property string $created_date
 * @property bool $is_deleted
 * @property bool $is_active
 */
class ProduksiObatAlkesBahanBaku extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'produksiobatalkesbahanbaku_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['produksiobatalkesdetail_id'], 'required'],
            [['produksiobatalkesdetail_id', 'obatalkes_id', 'satuankecil_id', 'qty_obat', 'pemesananproduksiobat_id', 'harganetto', 'harganetto_satuan', 'created_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }
}
