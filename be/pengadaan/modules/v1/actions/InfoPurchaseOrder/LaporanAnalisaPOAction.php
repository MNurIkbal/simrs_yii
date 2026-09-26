<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\LaporanAnalisaPurchaseOrderView;
use Doco\components\DocoRestActiveFilter;

class LaporanAnalisaPOAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;
            $advanced_filter = $request->get('advanced-filter');
            $model = new LaporanAnalisaPurchaseOrderView;
            $query = $model::find();
            $dateKey = ['tgl_po'];
            if(!empty($advanced_filter)) {
                $this->controller->dateFilter($query, $request, $dateKey);
            } else {
                $query->andWhere(new \yii\db\Expression('true = false'));
            }

            if (isset($advancedFilter['status_po_kondisi'])){
                $query->andWhere(['ILIKE', 'status_po_kondisi', $advancedFilter['status_po_kondisi']]);
            }

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
