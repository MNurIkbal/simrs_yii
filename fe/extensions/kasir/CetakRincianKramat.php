<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\extensions\kasir;

use Yii;
use app\components\DocoHelpers;

class CetakRincianKramat extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
      $request = Yii::$app->request;
      $id = $request->get('id', null); 
      if(!is_numeric($id)) {
         $id = DocoHelpers::decrypt($id);
      }
      $path = Yii::getAlias("@download") . "/cetak-rincian-{$id}.pdf";
      $userIdentity = Yii::$app->session->get('user_identity');
      $url = "tagihan-pasien/cetak-rincian";
      $restKasir = Yii::$app->docoRest->kasir;
      $response = $restKasir->get($url, [
         'query' => [
               'id' => $id,
               'nama_pegawai' => $userIdentity['nama_pegawai'],
               'instalasi_id' => $request->get('instalasi_id', null),
         ],
         'save_to' => $path
      ]);
      return DocoHelpers::previewPdf($path);
	}
}
