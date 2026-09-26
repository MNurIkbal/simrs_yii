<?php

namespace Doco\radiologi\actions\LapRekapRadiologi;

use Yii;
use yii\base\Action;
use app\components\Traits\ControllerHelperTrait;

class FiltersAction extends Action
{
   use ControllerHelperTrait;
   public function run()
   {
      $endPoint = $this->controller->_endpoint;
      return $this->guzzleExec(Yii::$app->docoRest->radiologi, [
         'url' => $endPoint . 'filters',
         'payload' => [
            'query' => Yii::$app->request->get()
         ],
         'returnResponse' => true
      ]);
   }
}
