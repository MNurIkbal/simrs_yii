<?php

namespace app\modules\v1\actions\LaporanPenerimaanBarang;

use Yii;
use app\modules\v1\models\LaporanStockMutasiFn;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use yii\base\Action;
use yii\helpers\ArrayHelper;


class GetListDataAction extends Action {
    public function run()
    {
        $getData = Yii::$app->request->get();
        $query = $this->controller->getDataLaporan($getData);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}

?>