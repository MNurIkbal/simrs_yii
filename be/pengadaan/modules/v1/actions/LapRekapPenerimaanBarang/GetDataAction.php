<?php

/**
 * @author : Bambang Hermawan (bambang.hermawan@sirs.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapRekapPenerimaanBarang;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\LapRekapPenerimaanBarangView;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;

class GetDataAction extends Action {

    private function Model(){
        $model = new LapRekapPenerimaanBarangView();
        return $model::find();
    }

    public function run() {
        try {
            $request = Yii::$app->request;
            $model = new LapRekapPenerimaanBarangView();
            $query   = $this->model();

            $startDate = date('Y-m-d 00:00:00');
            $endDate = date('Y-m-d 23:59:00');

            if($advance_filter = $request->get('advanced-filter')){
                if (isset($advance_filter['tgl_penerimaan'])) {
                    $tgl_filter = explode(' - ', $advance_filter['tgl_penerimaan']);

                    $startDate = date('Y-m-d H:i:s', strtotime($tgl_filter[0]));
                    $endDate   = date('Y-m-d H:i:s', strtotime($tgl_filter[1] . ' 23:59:59'));

                    unset($_GET['advanced-filter']['tgl_penerimaan']);
                }

                if (isset($advance_filter['supplier_id']) && $advance_filter['supplier_id'] != 0 ) {
                    $query->andWhere(['in', 'supplier_id', array_filter(explode(',', $advance_filter['supplier_id']))]);
                    unset($_GET['advanced-filter']['supplier_id']);
                }
            };

            $query->andWhere(['between', 'tgl_penerimaan', $startDate, $endDate]);
            $query->orderBy(['tgl_penerimaan' => SORT_DESC]);
            
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