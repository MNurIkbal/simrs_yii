<?php

namespace Doco\radiologi\actions\LapRekapRadiologi;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;

class ShowPopupAction extends Action
{
   public function run()
   {
      $request = Yii::$app->request;
      $tipe = $request->get('tipe', 1);
      $title = ($tipe == 1) ? 'Unduh Excel ' : 'Cetak PDF ';
      $title .= $this->controller->_title;
      $module = $this->controller->_module;
      $randString = DocoHelpers::generateRandomString();
      $payload = DocoDatatableHelper::advancedFilterParam();
      $userIdentity = Yii::$app->session->get('user_identity');
      $nama_pegawai = isset($userIdentity['nama_pegawai']) ? $userIdentity['nama_pegawai'] : null;
      $_GET['nama_pegawai'] = $nama_pegawai;
      $_GET['randString'] = $randString;
      Yii::$app->session->setFlash($randString, $payload);
      return $this->controller->renderAjax('_modal', get_defined_vars());
   }
}
