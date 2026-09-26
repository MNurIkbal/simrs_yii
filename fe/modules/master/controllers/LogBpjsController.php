<?php

namespace Doco\master\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\Html;
use app\components\DocoController;
use app\components\DHtml;
use app\components\DocoDatatableHelper;
use yii\helpers\ArrayHelper;

class LogBpjsController extends DocoController
{
   protected $_title = "Log BPJS";
   protected $_module = '/master/log-bpjs';
   protected $_restMaster;
   
   public function init()
   {
      parent::init();
      $this->_restMaster = Yii::$app->docoRest->master;
   }

   public function actionIndex()
   {
      $title = empty(DHtml::getTitleMenu()) ? $this->_title : DHtml::getTitleMenu();
      
      return $this->render('index', get_defined_vars());
   }

   public function actionGetData()
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $request = Yii::$app->request;
      $payload = DocoDatatableHelper::advancedFilterParam();
      $response = $this->guzzleExec($this->_restMaster, [
         'url' => "log-bpjs/index",
         'payload' => [
            'query' => $payload,
         ]
      ]);
      foreach ($response['data'] as $key => $value) {
         $primaryKey = ArrayHelper::getValue($value, 'logbpjs_id');
         $response['data'][$key]['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
            'class' => 'btn btn-sm btn-success', 'data-source'=>"/master/log-bpjs/detail-response?id=".$primaryKey,'onclick'=> 'docoHelper.detail(this)']);
         $response['data'][$key]['created_date'] = date('d M Y H:i:s', strtotime($value['created_date']));
      }
      $response['recordsTotal'] = $response['_meta']['totalCount'];
      $response['recordsFiltered'] = $response['_meta']['totalCount'];
      return $response;
   }

   public function actionDetailResponse($id)
   {
      $request = $this->_restMaster->request('GET', 'log-bpjs/get-data-detail?id='.$id);
      $response = json_decode($request->getBody(), true);
      $attributes = $response['response'];
      if(!empty($attributes) && is_array($attributes)) {
         $requestData = !empty($attributes['request']) ? json_decode($attributes['request'], true) : '';
         $responseData = !empty($attributes['response']) ? json_decode($attributes['response'], true) : '';
         $syncRespon = [
            'request' =>  $requestData,
            'response' => $responseData,
         ];
         $results = json_encode($syncRespon, JSON_PRETTY_PRINT);
      }
      else {
         $results = '<center><h3><strong>Tidak Ada Response</strong><h3></center>';
      }

      return $this->renderAjax('_detail', get_defined_vars());
   }
}