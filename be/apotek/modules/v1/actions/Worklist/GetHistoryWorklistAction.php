<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Worklist;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\WorklistView;
use app\modules\v1\models\Pegawai;

class GetHistoryWorklistAction extends Action {
    public function run() {
        $identifier = Yii::$app->request->get('identifier');
        $status_bayar = [];
        $history = [];

        $header = WorklistView::find()
            ->where(['no_reseptur' => $identifier])
            ->orWhere(['no_resep' => $identifier])
            ->asArray()
            ->one();

        if(empty($header)) {
            return [
                'status' => 422,
                'message' => 'Worklist dengan nomor resep / reseptur ' . $identifier . ' tidak ditemukan.'
            ];
        }

        $get_history = !is_null($header['add_penjualaanresep']) ? json_decode($header['add_penjualaanresep'], true) : json_decode($header['add_reseptur'], true);

        $set_history = $this->controller->setWorklistFarmasiLog($get_history, $header);

        return [
            'history' => $set_history['history']
        ];
    }
}
