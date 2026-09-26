<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\modules\pengadaan\models\RecommendationOrderForm;

class RecommendationOrderAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $model = new RecommendationOrderForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $narkotika_id = $this->controller->getNarkotikaId();

        if ($request->post()) {
            $is_consignment = $request->post('is_consignment');
            $model->scenario = $is_consignment == 'false' ? RecommendationOrderForm::SCENARIO_NON_CGN : RecommendationOrderForm::SCENARIO_CGN;
            $model->is_consignment = $is_consignment;
            $model->days_of_inventory = $request->post('doi', 0);
            $model->jenisobatalkes_id = $request->post('jenisobatalkes_id', []);
            if ($model->validate()) {
                $action = $is_consignment == 'false' ? '/cal-rekomendation' : '/ro-consignment';
                return $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
                    'url' => 'purchase-requisition'.$action,
                    'method' => 'get',
                    'payload' => [
                        'query' => [
                            'doi' => $model->days_of_inventory,
                            'jenis_obat' => $model->jenisobatalkes_id,
                            'is_consignment' => $model->is_consignment
                        ]
                    ],
                    'returnResponse' => true
                ]);
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'RecommendationOrderForm');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ], 422);
            }
        }

        return $this->controller->renderPartial('recommendation-order', get_defined_vars());
    }
}