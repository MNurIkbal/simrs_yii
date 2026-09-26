<?php

/**
 * @author : Ardi Pratama (ardi.pratama@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use app\components\DocoHelpers;
use app\modules\v1\models\LaporanPurchaseRequisitionView;
use Doco\components\DocoRestActiveFilter;

class GetdataLaporanPurchaseOrderProcess extends \Doco\components\DocoBaseProcessExtension
{
	protected function processFlow() {
        try {
            $request = Yii::$app->request;
            $model = new LaporanPurchaseRequisitionView;
            $query = $model::find();
            if(count($request->get('advanced-filter')) > 0) {
                $this->controller->dateFilter($query, $request);
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