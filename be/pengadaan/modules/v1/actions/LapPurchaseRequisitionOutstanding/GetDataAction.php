<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapPurchaseRequisitionOutstanding;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\LapPurchaseRequisitionOutstandingView;
use app\modules\v1\models\LapPurchaseRequisitionOutstandingBarangView;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;

class GetDataAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;
            $type = $request->get('type');

            if($type == DocoConstants::JENIS_OBAT) {
                $model = new LapPurchaseRequisitionOutstandingView;
                $dateFilter = 'tgl_pr';
            } else {
                $model = new LapPurchaseRequisitionOutstandingBarangView;
                $dateFilter = 'create_date';
            }
            
            $query = $model->find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];

                if (isset($advancedFilter['tgl_pr_awal']) &&
                    isset($advancedFilter['tgl_pr_akhir'])) {
                    $start = $advancedFilter['tgl_pr_awal'];
                    $end = $advancedFilter['tgl_pr_akhir'];
                }
            }

            $query->andWhere(['between', $dateFilter, $start, $end]);
            
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
