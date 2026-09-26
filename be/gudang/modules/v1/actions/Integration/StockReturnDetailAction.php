<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Integration;

use Yii;
use yii\base\Action;
use app\modules\v1\models\StockReturnView;
use app\modules\v1\models\StockReturnDetailView;

class StockReturnDetailAction extends Action {
    public function run() {
        $uid = Yii::$app->request->get('uid',null);
        if(is_null($uid)){
            throw new \Exception("UID tidak boleh kosong", 1);
        }
        $stockReturnDetail = StockReturnDetailView::find()->andWhere([
            'is_sending' => false
        ])->orderBy([
            'id' => SORT_ASC
        ])->one();

        if (empty($stockReturnDetail)) {
            return [
                'status' => 422,
                'messages' => 'Tidak ada data yang di proses'
            ];
        }

        $stockReturn = StockReturnView::find()->where([
            'sync_id_api' => $stockReturnDetail->picking_id
        ])->one();

        $data = $stockReturnDetail->attributes;

        if($stockReturn->tipe_rekap == StockReturnView::RETUR) {
            $query = Yii::$app->db->createCommand("
                UPDATE returresepdetail_r SET is_sending = true,id_sync_sercon = '{$uid}' WHERE id = {$stockReturnDetail->id}
            ")->execute();
        } else if($stockReturn->tipe_rekap == StockReturnView::BATAL) {
            $query = Yii::$app->db->createCommand("
                UPDATE pembatalanresepdetail_r SET is_sending = true,id_sync_sercon = '{$uid}' WHERE id = {$stockReturnDetail->id}
            ")->execute();
        }

        return $data;
    }
}