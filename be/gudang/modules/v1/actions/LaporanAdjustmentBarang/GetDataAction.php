<?php

namespace app\modules\v1\actions\LaporanAdjustmentBarang;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanAdjustmentBarangView;

class GetDataAction extends Action
{
    public function run()
    {
        try {
            $request = Yii::$app->request;
            $tipe = $request->get('tipe');
            
            $model = new LaporanAdjustmentBarangView;
            $query = $model::find();
            if (count($request->get('advanced-filter')) > 0) {
                $this->controller->dateFilter($query, $request);
            }
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
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
