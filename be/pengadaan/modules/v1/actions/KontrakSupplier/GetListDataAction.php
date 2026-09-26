<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\KontrakSupplierHeaderView;
use Doco\components\DocoRestActiveFilter;

class GetListDataAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;
            $model = new KontrakSupplierHeaderView;
            $query = $model::find();

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];

                if (isset($advancedFilter['tgl_berlaku_awal']) &&
                    isset($advancedFilter['tgl_berlaku_akhir'])) {
                    $start = $advancedFilter['tgl_berlaku_awal'];
                    $end = $advancedFilter['tgl_berlaku_akhir'];

                    $query->andWhere(['between', 'tgl_berlaku', $start, $end]);
                }

                if (isset($advancedFilter['supplier_nama'])) {
                    $query->andWhere(['ilike', 'supplier_nama', $advancedFilter['supplier_nama']]);
                }

                if (isset($advancedFilter['kontraksupplier_no'])) {
                    $query->andWhere(['ilike', 'kontraksupplier_no', $advancedFilter['kontraksupplier_no']]);
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
}
