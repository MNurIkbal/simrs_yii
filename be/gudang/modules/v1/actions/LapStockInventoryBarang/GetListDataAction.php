<?php

namespace app\modules\v1\actions\LapStockInventoryBarang;

use Yii;
use app\modules\v1\models\LaporanStockInventoryBarangFn;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use yii\base\Action;


class GetListDataAction extends Action {
    public function run()
    {
        $model = new LaporanStockInventoryBarangFn;
        $date = date('Y-m-d');
        $query = $model::getData($date);

        if(isset($_GET['advanced-filter']))
        {
            if(isset($_GET['advanced-filter']['tanggal_inventory'])){
                $date = date('Y-m-d', strtotime($_GET['advanced-filter']['tanggal_inventory']));    
            }
            $query = $model::getData($date);
        }
        $query = DocoRestActiveFilter::advancedFilter($model,$query);
            
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
}

?>