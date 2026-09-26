<?php

/**
 * ? @author : Budi (budi@sirs.co.id)
 * ? Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanAnalisaPoNonMedis;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class ExportExcelAction extends Action
{
   public function run()
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $payload = DocoDatatableHelper::advancedFilterParam();
      $doc_name = $this->setDocName($payload);
      $path = Yii::getAlias("@download") . $doc_name;
      $url = $this->controller->_endpoint . 'export-excel';
      try {
         $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => $url,
            'method' => 'GET',
            'payload' => [
                'save_to' => $path,
                'query' => $payload
            ]
        ]);
         return DocoHelpers::response($path);
      } catch (RequestException $e) {
         throw new \yii\web\NotFoundHttpException();
         return $e;
      } catch (\Exception $e) {
         throw new \yii\web\NotFoundHttpException();
         return $e;
      }
   }

   private function setDocName($params)
   {
      if (isset($params['tgl_po'])) {
         $exp = explode(' - ', $params['tgl_po']);
         $tgl_awal = $exp[0];
         $tgl_akhir = $exp[1];
         $tgl_po = "-" . date('d-M-Y', strtotime($tgl_awal)) . " - " . date('dMY', strtotime($tgl_akhir));
      } else {
         $tgl_po = "-" . date('d-M-Y');
      }
      return "/laporan-analisa-po-non-medis" . $tgl_po . ".xlsx";
   }
}
