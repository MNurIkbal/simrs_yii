<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LaporanAdjustment;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;

class GetDataAction extends Action
{
    public function run()
    {
        try {
            $getData = Yii::$app->request->get();
            $query = $this->controller->getDataLaporan($getData);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
}
