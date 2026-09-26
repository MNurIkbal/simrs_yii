<?php

namespace app\modules\v1\models;


/**
 * This is the model class for table "produksiobatalkesdetail_t".
 * @property int $produksiobatalkesdetail_id
 * @property int $produksiobatalkes_id
 * @property int $obatalkes_id
 * @property int $satuankecil_id
 * @property int $qty_produksi
 * @property int $pemesananproduksiobatdetail_id
 * @property bool $is_deleted
 * @property string $created_date
 * @property bool $is_active
 */

class ProduksiObatAlkesDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'produksiobatalkesdetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['produksiobatalkes_id'], 'required'],
            [['produksiobatalkes_id', 'obatalkes_id', 'satuankecil_id', 'qty_produksi', 'pemesananproduksiobatdetail_id', 'created_date', 'is_deleted', 'is_active'], 'safe'],
        ];
    }
}
