<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapPemakaianBmhpRuangan;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanPemakaianObatRuanganView;

class IndexAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $model = new LaporanPemakaianObatRuanganView;
        $query = $model::find(true);
        $model->daterangeFilter($query, $request, 'tgl_transaksi');
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
}
