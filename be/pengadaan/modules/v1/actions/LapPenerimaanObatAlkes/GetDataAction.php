<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapPenerimaanObatAlkes;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\LaporanPenerimaanObatAlkesView;
use Doco\components\DocoRestActiveFilter;

class GetDataAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;
            $advancedFilter = $request->get('advanced-filter');

            $model = new LaporanPenerimaanObatAlkesView;
            $query = $model->find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            if (is_null($advancedFilter)) {
                $query->andWhere(new \yii\db\Expression('true = false'));
            }
            if (!empty($advancedFilter['tgl_penerimaan'])) {
                $explodeTgl = explode(" - ", $advancedFilter['tgl_penerimaan']);
                if (count($explodeTgl) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explodeTgl[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explodeTgl[1]));
                }
                $query->andWhere(['between', 'tgl_penerimaan', $start, $end]);
            }
            if(!empty($advancedFilter['supplier_id'])) {
                $supplierIds = $advancedFilter['supplier_id'];
                $query->andWhere(['IN', 'supplier_id', $supplierIds]);
                unset($_GET['advanced-filter']['supplier_id']);
            }
            $query->orderBy(['tgl_penerimaan' => SORT_DESC, 'no_penerimaan' => SORT_ASC]);
            
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            $this->controller->logError($e);
            return $this->controller->responseJson(500, $e->getMessage());
        } catch (\Exception $e) {
            $this->controller->logError($e);
            return $this->controller->responseJson(422, $e->getMessage());
        }
    }
}
