<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\InfoPurchaseReqDetailView;
use app\modules\v1\models\InfoPurchaseReqBarangDetailView;
use app\modules\v1\models\InfoPurchaseReqGabungDetailView;
use Doco\components\DocoRestActiveFilter;

class GetListDetailAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $type = $request->get('type');

            $model = new InfoPurchaseReqGabungDetailView;
            $query = $model->find();
            $query->andWhere(['purchasereq_id' => $id]);
            if(strtoupper($type) == 'OBAT'){
                $query->andWhere(['tipe'=>'OBAT']);
            }elseif(strtoupper($type) == 'BARANG'){
                $query->andWhere(['tipe'=>'BARANG']);
            }
            
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
