<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Integration;

use Yii;
use yii\base\Action;
use app\modules\v1\models\StockOutView;
use app\modules\v1\models\IntPendaftaranBmhpR;
use app\modules\v1\models\IntPenjualanResepR;

class StockOutAction extends Action {
    public function run() {
        $uid = Yii::$app->request->get('uid',null);
        if(is_null($uid)){
            throw new \Exception("UID tidak boleh kosong", 1);
        }
    	$stockOut = StockOutView::find()->andWhere([
            'is_sending' => false
        ])
        ->orderBy([
            'date_move' => SORT_ASC
        ])
        ->one();

        if (empty($stockOut)) {
            return [
                'status' => 422,
                'messages' => 'Tidak ada data yang di proses'
            ];
        }

        $data = $stockOut->attributes;

        if ($stockOut->tipe_rekap == StockOutView::BMHP) {
            $modelBmhp = IntPendaftaranBmhpR::find()->where(['id'=>$stockOut->id])->one();
            $modelBmhp->is_sending = true;
            $modelBmhp->id_sync_sercon = $uid;
            $modelBmhp->save();
        } else if ($stockOut->tipe_rekap == StockOutView::RESEP || $stockOut->tipe_rekap == StockOutView::RACIKAN_BATAL) {
            $modelResep = IntPenjualanResepR::find()->where(['id'=>$stockOut->id])->one();
            $modelResep->is_sending = true;
            $modelResep->id_sync_sercon = $uid;
            $modelResep->save();
        }

        return $data;
    }
}