<?php

namespace app\modules\v1\actions\InfProduksiObat;

use Yii;
use yii\base\Action;
use Doco\components\DocoSpout;
use Doco\components\DocoHelpers;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\InfoProduksiObatAlkesDetailView;

class GetListProduksiAction extends Action {
    public function run()
    {
        $request = Yii::$app->request;
        $request = $request->post();
        
        $model = new InfoProduksiObatAlkesDetailView;
        $query = $model::find();
        $query->where(['produksiobatalkes_id' => $request['id']]);
        $query->orderBy(['obatalkes_nama' => SORT_DESC]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}