<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 * 
 * Supply data untuk basecalcro_r
 * Jalankan api ini pada 00.01 dini hari.
 */

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\MovingCriteria;
use app\modules\v1\models\SumStokOutView;
use app\modules\v1\models\BaseCalcRecommendationOrder;

class PullBaseCalcROAction extends Action {
    public function run() {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try{
            // Date deducted by 1 because this function runs tommorow.
            $date = date('Y-m-d', strtotime("-1 day"));

            // remove old data with same date
            BaseCalcRecommendationOrder::updateAll([
                'is_deleted'   => true,
                'is_active'    => false,
                'deleted_date' => date('Y-m-d H:i:s'),
                'deleted_by'   => null,
            ], "tanggal = '" . $date . "'");

            $pullingData = SumStokOutView::find()->asArray()->all();
            $getCriteria = MovingCriteria::find()->asArray()->all();

            $data = [];
            foreach ($pullingData as $key => $value) {
                $move_category = null;
                $move_category_id = null;
                // assign category
                foreach ($getCriteria as $criteria) {
                    if($value['count'] >= $criteria['min'] && $value['count'] <= $criteria['max']) {
                        $move_category = $criteria['criteria'];
                        $move_category_id = $criteria['movingcriteria_id'];
                    }
                }

                // casting
                $data[$key] = $value;
                $data[$key]['tanggal'] = $date;
                $data[$key]['move_category'] = $move_category;
                $data[$key]['move_category_id'] = $move_category_id;
            }

            if(count($data) > 0) {
                BaseCalcRecommendationOrder::batchInsert($data);
            }

            $transaction->commit();

            return [
                'status' => 200,
                'message' => 'Data berhasil di pulling.'
            ];
        }
        catch (\yii\db\Exception $e) {
            Yii::error($e->getMessage());
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}
