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

class StockReturnAction extends Action {
    public function run() {
        $uid = Yii::$app->request->get('uid',null);
        if(is_null($uid)){
            throw new \Exception("UID tidak boleh kosong", 1);
        }
        $stockReturn = StockReturnView::find()->andWhere([
            'is_sending' => false
        ])
        ->orderBy([
            'date_move' => SORT_ASC
        ])
        ->one();

        if (empty($stockReturn)) {
            return [
                'status' => 422,
                'messages' => 'Tidak ada data yang di proses'
            ];
        }

        $data = $stockReturn->attributes;

        if ($stockReturn->tipe_rekap == StockReturnView::RETUR) {
            $query = Yii::$app->db->createCommand("
                UPDATE returresep_r SET is_sending = true,id_sync_sercon = '{$uid}' WHERE id = {$stockReturn->id}
            ")->execute();
            $this->controller->logWarning(['message'=>'Stock Return RETUR update','payload'=>$stockReturn->attributes]);
        } else if ($stockReturn->tipe_rekap == StockReturnView::BATAL) {
            $query = Yii::$app->db->createCommand("
                UPDATE pembatalanresep_r SET is_sending = true,id_sync_sercon = '{$uid}' WHERE id = {$stockReturn->id}
            ")->execute();
            $this->controller->logWarning(['message'=>'Stock Return BATAL update','payload'=>$stockReturn->attributes]);
        }

        return $data;
    }
}