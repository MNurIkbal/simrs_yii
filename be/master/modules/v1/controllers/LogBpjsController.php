<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LogBpjs;

class LogBpjsController extends DocoActiveController
{
   public $modelClass = 'app\modules\v1\models\LogBpjs';

   public function verbs()
   {
      $verbs = parent::verbs();
      return $verbs;
   }

   public function actions()
   {
      $actions = parent::actions();
      unset($actions['index']);
      return $actions;
   }

   public function actionIndex()
   {
      $model = new LogBpjs;
      $query = $model::find();
      $start = date('Y-m-d 00:00:00');
      $end = date('Y-m-d 23:59:59');

      if(isset($_GET['advanced-filter'])) {
         if(isset($_GET['advanced-filter']['created_date'])) {
            $explode = explode(" - ", $_GET['advanced-filter']['created_date']);
            if(count($explode) == 2) {
               $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
               $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
            }
            unset($_GET['advanced-filter']['created_date']);
         }
         if(isset($_GET['advanced-filter']['request'])) {
            $request = $_GET['advanced-filter']['request'];
            $query->andWhere(['ILIKE', 'LOWER(request)', strtolower($request)]);
            $query->orWhere(['ILIKE', 'LOWER(response)', strtolower($request)]);
            $query->orWhere(['ILIKE', 'LOWER(url)', strtolower($request)]);
            unset($_GET['advanced-filter']['request']);
         }
      }

      $query->andWhere(['between', 'created_date', $start, $end]);
      $query = DocoRestActiveFilter::advancedFilter($model, $query);
      return new ActiveDataProvider([
         'query' => $query,
      ]);
   }

   public function actionGetDataDetail()
   {
      $request = Yii::$app->request;
      $id = $request->get('id');
      return LogBpjs::findOne($id);
   }
}
