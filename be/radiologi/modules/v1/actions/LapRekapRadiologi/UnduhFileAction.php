<?php

namespace app\modules\v1\actions\LapRekapRadiologi;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Action;
use Doco\Services\InternalService;

class UnduhFileAction extends Action 
{
   /**
    * @controller actionUnduhFile
    * @attribute #periode# => tanggal daftar
    * @attribute #cetak_oleh# => no rekam medik
    * @attribute #tanggal# => no pendaftaran
    * @attribute #datatable# => table detail
    **/
   public function run() 
   {
      $request = Yii::$app->request;
      $get = $request->get();
      $tipe = ArrayHelper::getValue($get, 'tipe', 1);
      $xOwner = $request->getHeaders()->get('X-Owner');
      $auth = $request->getHeaders()->get('Authorization');
      $randString = ArrayHelper::getValue($get, 'randString');

      if (isset($get['page'])) unset($get['page']);
      if (isset($get['per-page'])) unset($get['per-page']);
      
      $data = $this->controller->actionGetObjectData();
      $endPoint = $this->controller->_endpoint;
      $countData = ($tipe == 1) ? count($data) : ArrayHelper::getValue($data, 'countData', 0);
      $totalPerPage = ceil($countData/20);
      $url = Yii::$app->docoRest->getBaseUri('radiologi');
      $params = [
         'sendToUrl' => $endPoint . 'drop-file',
         'getDataUrl' => $endPoint . 'get-object-data',
         'base_uri' => $url,
      ];
      
      (new InternalService)->sendTo([
         'Sirs' => [
            'LapRekapRadiologi' => [
               'token' => $auth,
               'xOwner' => $xOwner,
               'unique_str' => $randString,
               'filter' => $get,
               'params' => $params
            ]
         ]
      ], true);

      (new InternalService)->sendTo([
         'Sirs' => [ 
            'CetakLapRekapRadiologi' => [
               'token' => $auth,
               'xOwner' => $xOwner,
               'unique_str' => $randString,
               'totalPerPage' => $totalPerPage,
               'countData' => $countData,
               'filter' => $get,
               'title' => 'Laporan Rekapitulasi Pemeriksaan Radiologi',
            ]
         ]
      ], true);

      (new InternalService)->sendTo([
         'Sirs' => [ 
            'UploadLapRekapRadiologi' => [
               'token' => $auth,
               'xOwner' => $xOwner,
               'unique_str' => $randString,
               'totalPerPage' => $totalPerPage,
               'params' => $params,
               'tipe' => $tipe
            ]
         ]
      ], true);

      return [
         'totalPerPage' => $totalPerPage,
         'unique_str' => $randString,
         'countData' => $countData,
      ];
   }
}
