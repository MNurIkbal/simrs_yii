<?php

namespace app\modules\v1\models;

use Yii;

class StockScrapView extends \Doco\components\DocoActiveRecord
{
    const BMHP = 'pemakaian_obat';
    const ADJM = 'adj_masuk';
    const ADJK = 'adj_keluar';
    const MUSNAH = 'pemusnahan_obat';
    const SO = 'stokopname_obat';
    const BMHP_BARANG = 'pemakaian_barang';
    const ADJM_BARANG = 'adj_masuk_barang';
    const ADJK_BARANG = 'adj_keluar_barang';
    const MUSNAH_BARANG = 'pemusnahan_barang';
    const SO_BARANG = 'stokopname_barang';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'int_stockscrap_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }
}
