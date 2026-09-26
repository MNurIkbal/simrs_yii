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
use app\modules\v1\models\Lookup;
use Doco\components\DocoRestActiveFilter;

class GetStatusPoAction extends Action {
    public function run() {
        try {
            $model = new Lookup;
            $status_po = $model::find()->select(['lookup_id', 'lookup_name'])->where([
                'lookup_type' => 'status_penerimaan_po'
            ])->asArray()->all();

            return $status_po;
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
