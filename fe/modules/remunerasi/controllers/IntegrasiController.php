<?php 

/**
 * @Author: Budi
 * @Date:   2022-12-19
 */

namespace Doco\remunerasi\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DHtml;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class IntegrasiController extends DocoController 
{
   protected $allowAction = ['*'];
   protected $_title = "Daftar Pegawai Penerima Remunerasi";
   protected $_module = '/remunerasi/integrasi';
   protected $_restRemun;

   public function init()
   {
      parent::init();
      $this->_restRemun = Yii::$app->docoRest->remunerasi;
   }

   public function actionIndex()
   {
      $title = DHtml::getTitleMenu();
		$title = !empty($title) ? $title : $this->_title;
      return $this->render('index', get_defined_vars());
   }

   public function actionGetData()
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $payload = DocoDatatableHelper::advancedFilterParam();
      $response = $this->guzzleExec($this->_restRemun, [
         'url' => "integrasi/index",
         'payload' => [
            'query' => $payload,
         ]
      ]);
      foreach ($response['data'] as $key => $value) {
         $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['remunpegawai_id']);
      }
      $response['recordsTotal'] = $response['_meta']['totalCount'];
      $response['recordsFiltered'] = $response['_meta']['totalCount'];
      return $response;
   }

   public function actionDelete()
   {
      try {
         $request = Yii::$app->request;
         $response = $this->_restRemun->delete('integrasi/delete?', [
            'query' => $request->get()
         ]);
         $body = json_decode($response->getBody(), TRUE);
         return DocoHelpers::response($body);
      } catch (RequestException $e) {
         return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
      } catch (\Exception $e) {
         return DocoHelpers::responseTemplate(500, $e->getMessage());
      }
   }

   public function actionShowPopup()
   {
      $title = 'Detail Remunerasi Pegawai';
      $request = Yii::$app->request;
      $ids = $request->get('ids');
      $advancedFilter = $request->get('advancedFilter', []);
      $idsDecoded = null;
      if(!empty($ids)) {
         $idsDecoded = json_decode($ids);
      }
      $randString = DocoHelpers::generateRandomString();
      $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
      $yiiRestfulParams['advancedFilter'] = $advancedFilter;
      Yii::$app->session->setFlash($randString, $yiiRestfulParams);
      return $this->renderAjax('_modal', get_defined_vars());
   }

   public function actionProcessSync($randString, $ids)
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $session = Yii::$app->session->getFlash($randString);
      $session['randString'] = $randString;
      if(!empty($ids)) {
         $ids = json_decode($ids);
      }
      $session['ids'] = $ids;
      return $this->guzzleExec($this->_restRemun, [
         'url' => "integrasi/export-excel",
         'payload' => [
            'query' => $session
         ],
      ]);
   }

   public function actionDownloadExcel()
   {
      $request = Yii::$app->request;
      $filename = $request->get('fileName', null);
      $fileDownloads = 'Daftar Pegawai Penerima Remunerasi.xlsx';
      $path = Yii::getAlias("@download").'/'.$fileDownloads;
      $response = $this->_restRemun->get('integrasi/download-file-excel', [
         'query' => [
            'filename' => $filename,
         ],
         'save_to' => $path,
      ]);
      $response = json_decode($response->getBody(), true);
      return DocoHelpers::downloadFile($path,true);
   }

   public function actionShowPopupCalc()
   {
      $title = 'Perhitungan Remunerasi Pegawai';
      $request = Yii::$app->request;
      $ids = $request->get('ids');
      $advancedFilter = $request->get('advancedFilter', []);
      $idsDecoded = null;
      if (!empty($ids)) {
         $idsDecoded = json_decode($ids);
      }
      $randString = DocoHelpers::generateRandomString();
      $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
      $yiiRestfulParams['advancedFilter'] = $advancedFilter;
      Yii::$app->session->setFlash($randString, $yiiRestfulParams);
      return $this->renderAjax('_modal_calc', get_defined_vars());
   }

   public function actionProcessSyncCalc($randString, $ids)
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $session = Yii::$app->session->getFlash($randString);
      $session['randString'] = $randString;
      if (!empty($ids)) {
         $ids = json_decode($ids);
      }
      $session['ids'] = $ids;
      return $this->guzzleExec($this->_restRemun, [
         'url' => "integrasi/calc-value",
         'payload' => [
            'query' => $session
         ],
      ]);
   }

   public function actionShowPopupSync()
   {
      $title = 'Sinkronisasi Data';
      $request = Yii::$app->request;
      $ids = $request->get('ids');
      $advancedFilter = $request->get('advancedFilter', []);
      $idsDecoded = null;
      if (!empty($ids)) {
         $idsDecoded = json_decode($ids);
      }
      $randString = DocoHelpers::generateRandomString();
      $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
      $yiiRestfulParams['advancedFilter'] = $advancedFilter;
      Yii::$app->session->setFlash($randString, $yiiRestfulParams);
      return $this->renderAjax('_modal_sync', get_defined_vars());
   }

   public function actionProcessSyncData($randString, $ids, $periode)
   { 
      Yii::$app->response->format = Response::FORMAT_JSON;
      $session = Yii::$app->session->getFlash($randString);
      $session['randString'] = $randString;
      if (!empty($ids)) {
         $ids = json_decode($ids);
      }
      $session['ids'] = $ids;
      $session['periode'] = $periode;
      
      return $this->guzzleExec($this->_restRemun, [
         'url' => "integrasi/sync-data",
         'payload' => [
            'query' => $session
         ],
      ]);
   }
}
