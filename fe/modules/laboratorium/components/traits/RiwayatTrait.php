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

trait RiwayatTrait
{
   public function actionRiwayat()
   {
      $title = DHtml::getTitleMenu();
      $title = !empty($title) ? $title : 'Riwayat Pasien Laboratorium';
      $data = [];
      $legendCaraBayar = [];
      // $cetakPemeriksaan = '/laboratorium/hasil-lab/cetak?id='; //link ori sebelum case mhbg
      $cetakPemeriksaan = '/laboratorium/integrasi-lis-hasil/cetak?id='; 
      try {
         $response = $this->_restLab->get('inf-pasien-rujukan-lab/get-options');
         $body = json_decode($response->getBody(), true);
         $data = isset($body['response']) ? $body['response'] : [];
         $getCaraBayar = ArrayHelper::getValue($data, 'cara_bayar', []);
         if (is_array($getCaraBayar)) {
            foreach ($getCaraBayar as $key => $value) {
                  $legendCaraBayar[$key]['carabayar_nama'] = ArrayHelper::getValue($value, 'carabayar_nama', '');
                  $legendCaraBayar[$key]['carabayar_kode_warna'] = ArrayHelper::getValue($value, 'carabayar_kode_warna', '');
            }
         }
      } catch (RequestException $e) {
         $data = [];
         $legendCaraBayar = [];
      }
      
      return $this->renderAjax('components/riwayat/index', get_defined_vars());
   }

   public function actionGetDataRiwayat()
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $request = Yii::$app->request;
      $type = $request->get('type', null);
      $payload = DocoDatatableHelper::advancedFilterParam();
      $payload['advanced-filter']['type'] = $type;
      $payload['advanced-filter']['tab'] = 'riwayat';
      // filter per ruangan lab
      $payload['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
      $response = $this->guzzleExec($this->_restLab, [
         'url' => "inf-pasien-lab/index",
         'payload' => [
            'query' => $payload,
         ]
      ]);
      if(!empty($response['data'])) {
         foreach ($response['data'] as $key => $value) {
            $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
            $fontStatusBayar = 'black';
            $colorStatusBayar = '#FFFfff';
            if ($value['is_status_bayar'] == true) {
               $colorStatusBayar = '#26A65B';
               $fontStatusBayar = 'white';
            }
            $response['data'][$key]['status_bayar'] = '<span class="badge" style="background: '.$colorStatusBayar.'; color: '.$fontStatusBayar.'; font-weight:bold;">'.$value['status_bayar'].'</span>';
            $pemeriksaan = "";
            if(!empty($value['pemeriksaan'])) {
               foreach($value['pemeriksaan'] as $tindakan) {
                  $pemeriksaan .= '<p><b>' .$tindakan['pemeriksaanlab_nama'].'</b></p>' ;
               }
           }
           $response['data'][$key]['pemeriksaan'] = $pemeriksaan;
         }
      }
      
      $response['recordsTotal'] = $response['_meta']['totalCount'];
      $response['recordsFiltered'] = $response['_meta']['totalCount'];
      return $response;
   }

   private function getAksiRiwayat($data)
   {
      $pendaftaran_id = DocoHelpers::encrypt($data['pendaftaran_id']);
      $return_data = '';
      if(!empty($data['no_antrian'])){
         $return_data .= Html::button(
            '<i class="fa fa-volume-up"></i> '.$data['no_antrian'] ,
            [
               'class' => 'btn btn-turquoise btn-xs-new antrian',
               'data-tooltip' => "tooltip",
               'data-original-title' => Yii::t('fe', 'Panggil antrian'),
               'data-id' => $data['pendaftaran_id'],
               'data-antrian' => $data['no_antrian'],
            ]
         );
      }
      $return_data .= '&nbsp;&nbsp;';
      return $return_data;
   }
}