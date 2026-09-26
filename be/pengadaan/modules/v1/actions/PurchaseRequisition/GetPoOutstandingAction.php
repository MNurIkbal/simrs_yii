<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Citraraya Nusatam
 * Powered by Sirs
 */

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\CalcPoOutstandingObatalkesView;

class GetPoOutstandingAction extends Action
{
    public function run()
    {
        try {
            $connection = Yii::$app->db;
            $request = Yii::$app->request;
            $item_id = $request->get('oid', false);

            $data = CalcPoOutstandingObatalkesView::find()->select(['obatalkes_id', 'qty_outstanding'])->where(['in', 'obatalkes_id', $item_id])->one();

            if (empty($data)) {
                $data = [
                    'obatalkes_id' => $item_id,
                    'qty_po' => 0,
                    'qty_outstanding' => 0
                ];
            }

            return $data;
        } catch (\Exception $e) {
            $this->logError($e);
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
}
