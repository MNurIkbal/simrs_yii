<?php

namespace Doco\radiologi\actions\LapRekapRadiologi;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\Traits\ControllerHelperTrait;

class GetDataAction extends Action
{
   use ControllerHelperTrait;
   public function run()
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $payload = DocoDatatableHelper::advancedFilterParam();
      $endPoint = $this->controller->_endpoint;
      $response = $this->guzzleExec(Yii::$app->docoRest->radiologi, [
         'url' => $endPoint . 'index',
         'payload' => [
            'query' => $payload,
         ]
      ]);
      $response['recordsTotal'] = isset($response['_meta']['totalCount']) ? $response['_meta']['totalCount'] : 0;
      $response['recordsFiltered'] = isset($response['_meta']['totalCount']) ? $response['_meta']['totalCount'] : 0;
      return $response;
   }
}
