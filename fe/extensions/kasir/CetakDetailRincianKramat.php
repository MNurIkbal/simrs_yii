<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\extensions\kasir;

use Yii;
use app\components\DocoHelpers;

class CetakDetailRincianKramat extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
      $title = 'Cetak Detail Rincian Tagihan';
      $request = Yii::$app->request;
      $randString = DocoHelpers::generateRandomString();
      $userIdentity = Yii::$app->session->get('user_identity');
      $nama_pegawai = isset($userIdentity['nama_pegawai']) ? $userIdentity['nama_pegawai'] : null;
      $_GET['nama_pegawai'] = $nama_pegawai;
      $_GET['randString'] = $randString;
      $get = $request->get();
      Yii::$app->session->setFlash($randString, $get);
      return $controller->renderAjax('_modal', get_defined_vars());
	}
}
