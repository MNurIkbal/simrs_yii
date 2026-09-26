<?php

namespace app\modules\v1\models;


/**
 * This is the model class for table "produksiobatalkes_t".
 * @property int $produksiobatalkes_id
 * @property string $tglproduksiobat
 * @property string $noproduksiobat
 * @property int $pemesananproduksiobat_id
 * @property int $status_produksi
 * @property bool $is_approve
 * @property int $pegawai_approve
 * @property string $tgl_approve
 */
class ProduksiObatAlkes extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'produksiobatalkes_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pemesananproduksiobat_id'], 'required'],
            [['pemesananproduksiobat_id', 'status_produksi', 'pegawai_approve'], 'safe'],
        ];
    }
}
