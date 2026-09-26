<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapPemakaianBmhpPasien;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanPemakaianBmhpPasienView;

class IndexAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $model = new LaporanPemakaianBmhpPasienView;
        $query = $model::find(true);
        $model->daterangeFilter($query, $request, 'tgl_transaksi');
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
}

