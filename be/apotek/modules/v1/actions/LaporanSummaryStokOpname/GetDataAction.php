<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LaporanSummaryStokOpname;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\LaporanSumStokOpnameView;
use Doco\components\DocoRestActiveFilter;

class GetDataAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;

            $model = new LaporanSumStokOpnameView;
            $query = $model->find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];

                if (isset($advancedFilter['tglformulir_awal']) &&
                    isset($advancedFilter['tglformulir_akhir'])) {
                    $start = $advancedFilter['tglformulir_awal'];
                    $end = $advancedFilter['tglformulir_akhir'];
                }
            }

            $query->andWhere(['between', 'tglformulir', $start, $end]);
            $query->orderBy(['tglformulir' => SORT_DESC, 'no_so' => SORT_ASC, 'store' => SORT_ASC]);
            
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }
}
