<?php

namespace app\modules\v1\actions\LapRekapitulasiPenjualan;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanRekapitulasiPenjualanView;

class IndexAction extends Action {
    public function run() {
        $model = new LaporanRekapitulasiPenjualanView;
        $query = $model::find(true);
        $request = Yii::$app->request;
        $this->controller->dateFilter($query, $request);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
}