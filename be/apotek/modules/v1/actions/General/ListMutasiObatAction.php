<?php

namespace app\modules\v1\actions\General;


use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\DetailPemesananObatAlkes;

class ListMutasiObatAction extends Action {

    public function run()
    {
        $pesanobatalkes_id = Yii::$app->request->get('pesanobatalkes_id', null);

        $model = new DetailPemesananObatAlkes;
        $query = $model::find(true);

        $query->where([
            'pesanobatalkes_id' => $pesanobatalkes_id
        ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

}