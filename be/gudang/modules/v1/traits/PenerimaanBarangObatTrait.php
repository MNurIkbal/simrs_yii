<?php
namespace app\modules\v1\traits;

use app\components\DocoHelpers;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Pajak;
use app\modules\v1\models\Payterm;
use Yii;

trait PenerimaanBarangObatTrait
{
    /**
     * Get Attribute Of Penerimaan Barang / Obat
     *
     * @return JSON
     * @author Tsani Nashrullah <tsani@docotel.com>
     **/
    public function actionGetAttribute()
    {
        $payterm = Payterm::find()->select([
            'payterm_id',
            'payterm_nama',
        ])->where([
            'is_active' => true,
        ])->asArray()->all();

        $pajak = Pajak::find()->select([
            'pajak_id',
            'pajak_label' => 'CONCAT(pajak_name,\' (\', pajak_persen , \'%)\' )',
        ])->where([
            'is_active' => true,
        ])->asArray()->all();

        $sumberPenerimaan = Lookup::find()->select([
            'lookup_id',
            'lookup_name',
        ])->where([
            'lookup_type' => 'sumber_penerimaan',
            'is_active' => true,
            'is_deleted' => false,
        ])->asArray()->all();

        return [
            'payterm' => $payterm,
            'ppn' => $pajak,
            'sumber_penerimaan' => $sumberPenerimaan,
        ];
    }
}
