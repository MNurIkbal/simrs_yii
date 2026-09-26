<?php

namespace Doco\radiologi\actions\LapRekapRadiologi;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\Traits\ControllerHelperTrait;

class ProcessSyncAction extends Action
{
   use ControllerHelperTrait;
   public function run($randString, $tipe)
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $session = Yii::$app->session->getFlash($randString);
      $session['randString'] = $randString;
      $session['tipe'] = $tipe;
      $endPoint = $this->controller->_endpoint;
      return $this->guzzleExec(Yii::$app->docoRest->radiologi, [
         'url' => $endPoint . 'unduh-file',
         'payload' => [
            'query' => $session
         ],
      ]);
   }
}
