<?php

/**
 * @author : Budi (budi@sirs.co.id)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LaporanAnalisaPoNonMedis;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanAnalisaPoNonMedisView;

class GetDataAction extends Action
{
   public function run()
   {
      try {
         $request = Yii::$app->request;
         $model = new LaporanAnalisaPoNonMedisView;
         $query = $model::find();
         if (!empty($request->get('advanced-filter'))) {
            $this->controller->dateFilter($query, $request);
         } else {
            $query->andWhere(new \yii\db\Expression('true = false'));
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
