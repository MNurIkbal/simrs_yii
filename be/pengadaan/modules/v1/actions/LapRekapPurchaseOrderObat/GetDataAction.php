<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A product of PT Citra Raya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapRekapPurchaseOrderObat;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanRekapPurchaseOrderObatView;

class GetDataAction extends Action {
    private function Model(){
        $model = new LaporanRekapPurchaseOrderObatView;
        
        return $model::find();
    }

    public function run() {
        try {
            $request = Yii::$app->request;
            $model   = new LaporanRekapPurchaseOrderObatView;
            $query   = $this->model();

            $startDate = date('Y-m-d 00:00:00');
            $endDate   = date('Y-m-d 23:59:00');

            if($advance_filter = $request->get('advanced-filter')){
                if (isset($advance_filter['tgl_po'])) {
                    $tgl_filter = explode(' - ', $advance_filter['tgl_po']);

                    $startDate = date('Y-m-d H:i:s', strtotime($tgl_filter[0]));
                    $endDate   = date('Y-m-d H:i:s', strtotime($tgl_filter[1] . ' 23:59:59'));

                    unset($_GET['advanced-filter']['tgl_po']);
                }

                if (isset($advance_filter['supplier_id']) && $advance_filter['supplier_id'] != 0 ) {
                    $query->andWhere(['in', 'supplier_id', array_filter(explode(',', $advance_filter['supplier_id']))]);
                    unset($_GET['advanced-filter']['supplier_id']);
                }

            };

            $query->andWhere(['between', 'tgl_po', $startDate, $endDate]);
            $query->orderBy(['tgl_penerimaan' => SORT_DESC]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
