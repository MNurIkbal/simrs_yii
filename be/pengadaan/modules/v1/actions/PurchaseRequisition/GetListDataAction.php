<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\InfoPurchaseRequisition;
use app\modules\v1\models\InfoPurchaseRequisitionBarang;
use app\modules\v1\models\InfoPurchaseRequisitionGabung;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;

class GetListDataAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;

            $model = new InfoPurchaseRequisitionGabung;

            if(isset($_GET['type']) && $_GET['type'] == "obat") {
                $model = new InfoPurchaseRequisition;
            } else if (isset($_GET['type']) && $_GET['type'] == "barang") {
                $model = new InfoPurchaseRequisitionBarang;
            }
            
            $query = $model::find();
            if(isset($_GET['ruangan_id']) && $_GET['ruangan_id'] == DocoConstants::RUANGAN_PENGADAAN) $query = $model::find()->where(['!=','status_pr','Belum Approved']);

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];

                if (isset($advancedFilter['tgl_pr_awal']) &&
                    isset($advancedFilter['tgl_pr_akhir'])) {
                    $start = $advancedFilter['tgl_pr_awal'];
                    $end = $advancedFilter['tgl_pr_akhir'];
                }

                if (isset($advancedFilter['pr_cyto'])) {
                    $query->andWhere(['is_prcyto' => $advancedFilter['pr_cyto'] == "Cyto" ? true : false]);
                }

                if (isset($advancedFilter['status_pr'])) {
                    $query->andWhere(['status_pr' => $advancedFilter['status_pr']]);
                }
            }

            $query->andWhere(['between', 'tgl_pr', $start, $end]);

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
