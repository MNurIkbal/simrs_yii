<?php

/**
 * ? @author : Budi (budi@sirs.co.id)
 * ? Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanAnalisaPoNonMedis;

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
      $request = Yii::$app->request;
      if (isset($request->get()['is_prcyto'])) {
            $payload['advanced-filter']['po_cito'] = $request->get('is_prcyto');
      }
      if (isset($request->get()['is_admin'])) {
            $payload['advanced-filter']['po_admin'] = $request->get('is_admin');
      }
      $endPoint = $this->controller->_endpoint;
      $response = $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
         'url' => $endPoint . 'get-data',
         'payload' => [
            'query' => $payload,
         ]
      ]);
      $response['recordsTotal'] = $response['_meta']['totalCount'];
      $response['recordsFiltered'] = $response['_meta']['totalCount'];
      return $response;
   }
}
