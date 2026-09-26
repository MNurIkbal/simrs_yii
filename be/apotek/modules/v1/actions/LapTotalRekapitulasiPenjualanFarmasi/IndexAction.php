<?php

namespace app\modules\v1\actions\LapTotalRekapitulasiPenjualanFarmasi;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanRekapPenjualanFarmasiFn;

class IndexAction extends Action {
    public function run() {
    	$request = Yii::$app->request;
    	$advanced_filter = $request->get('advanced-filter');
        
        $start = date('Y-m-d 00:00:00');
        $end   = date('Y-m-d 23:59:59');
        
        if(isset($advanced_filter) && isset($advanced_filter['tgl_pelayanan'])) {
            $explode = explode(" - ", $advanced_filter['tgl_pelayanan']);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
            }
        }

        $model = new LaporanRekapPenjualanFarmasiFn;
        $query = $model::getData($start, $end);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
}
