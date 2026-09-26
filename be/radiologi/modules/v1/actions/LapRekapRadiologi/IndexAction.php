<?php

namespace app\modules\v1\actions\LapRekapRadiologi;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class IndexAction extends Action 
{
   public function run() 
   {
      $request = Yii::$app->request;
      $data = $this->controller->dateFilter($request);
      $query = ArrayHelper::getValue($data, 'query');
      return new ActiveDataProvider([
         'query' => $query
      ]);
   }
}
