<?php

/**
 * @Author: Budi
 */

namespace app\modules\laboratorium\components\traits;

use Yii;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DHtml;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;

trait BatalTrait
{
   public function actionBatal()
   {
      $title = DHtml::getTitleMenu();
		$title = !empty($title) ? $title : 'Informasi Pasien Batal Rujukan';
      $response = $this->_restLab->get('inf-pasien-rujukan-lab/get-options');
      $resResponseBody = json_decode($response->getBody(), True);
      $resResponseBody = ArrayHelper::getValue($resResponseBody, 'response', []);
      $getCaraBayar = ArrayHelper::getValue($resResponseBody, 'cara_bayar', []);
      $legendCaraBayar = [];
      if (is_array($getCaraBayar)) {
         foreach ($getCaraBayar as $key => $value) {
            $legendCaraBayar[$key]['carabayar_nama'] = ArrayHelper::getValue($value, 'carabayar_nama', '');
            $legendCaraBayar[$key]['carabayar_kode_warna'] = ArrayHelper::getValue($value, 'carabayar_kode_warna', '');
         }
      }
      
      return $this->renderAjax('components/batal/index', get_defined_vars());
   }

   public function actionGetDataBatal()
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $request = Yii::$app->request;
      $type = $request->get('type', null);
      $payload = DocoDatatableHelper::advancedFilterParam();
      $payload['advanced-filter']['type'] = $type;

      // filter per ruangan lab
      $payload['advanced-filter']['ruanganpenunjang_id'] = Yii::$app->docoVars->workspace("ruangan_id");
      $response = $this->guzzleExec($this->_restLab, [
         'url' => "inf-pasien-rujukan-lab/index",
         'payload' => [
            'query' => $payload,
         ]
      ]);
      foreach ($response['data'] as $key => $value) {
         $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['pasienkirimkeunitlain_id']);
         $pemeriksaan = "";
         if(!empty($value['pemeriksaan_dibatalkan'])) {
            foreach($value['pemeriksaan_dibatalkan'] as $tindakan) {
               $pemeriksaan .= '<p><b>' .$tindakan['pemeriksaanlab_nama'].'</b></p>' ;
            }
        }
        $response['data'][$key]['pemeriksaan'] = $pemeriksaan;
      }
      $response['recordsTotal'] = $response['_meta']['totalCount'];
      $response['recordsFiltered'] = $response['_meta']['totalCount'];
      return $response;
   }
}