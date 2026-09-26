<?php

namespace app\modules\mcu\components\traits;

use Yii;
use app\components\DocoHelpers;
use app\modules\mcu\models\PemeriksaanMcuForm;
use app\modules\mcu\models\PemeriksaanGigiForm;
use app\modules\mcu\models\PemeriksaanKardiologiForm;
use app\modules\mcu\models\PemeriksaanThtForm;
use app\modules\mcu\models\PemeriksaanMataForm;
use app\modules\mcu\models\PemeriksaanObsgynForm;
use app\modules\mcu\models\PemeriksaanInternisForm;
use app\modules\mcu\models\PemeriksaanNeurologiForm;
use app\modules\mcu\models\PemeriksaanUrologiForm;
use app\modules\mcu\models\PemeriksaanGiziForm;
use app\components\DocoConstants;

trait PemeriksaanMcuTrait
{
  private function cekPemeriksaan($pendaftaran_id, $ruangan_id)
  {
      $request = Yii::$app->request;
      $response = $this->_restMcu->get('pemeriksaan/get-pemeriksaan-spesialis', [
          'query' => [
              'pendaftaran_id' => $pendaftaran_id,
              'ruangan_id' => $ruangan_id,
          ]
      ]);
      $body = json_decode($response->getBody(),TRUE);
      $response = $body['response'];
      $ruangan = $response['ruangan'];
      $default_tht = isset($ruangan['default_tht']) ? $ruangan['default_tht'] : null;
      $default_mata = isset($ruangan['default_mata']) ? $ruangan['default_mata'] : null;
      $default_gigi = isset($ruangan['default_gigi']) ? $ruangan['default_gigi'] : null;
      $default_obsgyn = isset($ruangan['default_obsgyn']) ? $ruangan['default_obsgyn'] : null;
      $default_internis = isset($ruangan['default_internis']) ? $ruangan['default_internis'] : null;
      $default_kardiologi = isset($ruangan['default_kardiologi']) ? $ruangan['default_kardiologi'] : null;
      $default_neurologi = isset($ruangan['default_neurologi']) ? $ruangan['default_neurologi'] : null;
      $default_urologi = isset($ruangan['default_urologi']) ? $ruangan['default_urologi'] : null;
      $default_gizi = isset($ruangan['default_gizi']) ? $ruangan['default_gizi'] : null;

      switch ($ruangan_id) {
        case ($ruangan_id == $default_tht):
          $model = new PemeriksaanThtForm;
          $path = '__pemeriksaan_tht';
          break;
          
        case ($ruangan_id == $default_mata):
          $model = new PemeriksaanMataForm;
          $path = '__pemeriksaan_mata';
          break;
        
        case ($ruangan_id == $default_kardiologi):
          $model = new PemeriksaanKardiologiForm;
          $path = '__pemeriksaan_kardiologi';
          break;

        case ($ruangan_id == $default_gigi):
          $model = new PemeriksaanGigiForm;
          $path = '__pemeriksaan_gigi';
          break;

        case ($ruangan_id == $default_obsgyn):
          $model = new PemeriksaanObsgynForm;
          $path = '__pemeriksaan_obsgyn';
          break;

        case ($ruangan_id == $default_internis):
          $model = new PemeriksaanInternisForm;
          $path = '__pemeriksaan_internis';
          break;

        case ($ruangan_id == $default_neurologi):
          $model = new PemeriksaanNeurologiForm;
          $path = '__pemeriksaan_neurologi';
          break;

        case ($ruangan_id == $default_urologi):
          $model = new PemeriksaanUrologiForm;
          $path = '__pemeriksaan_urologi';
          break;

        case ($ruangan_id == $default_gizi):
          $model = new PemeriksaanGiziForm;
          $path = '__pemeriksaan_gizi';
          break;

        default:
          $model = [];
          $path = null;
      }
      $response['model'] = $model;
      $response['path'] = $path;
      return $response;
  }

  private function setAttributeData($pendaftaran_id, $ruangan_id)
  {
      $cekExistData = $this->cekPemeriksaan($pendaftaran_id, $ruangan_id);
      if (empty($cekExistData['model'])) {
          return [
            'data' => [],
            'model' => [],
            'path' => '',
          ];
      }
      $data = $cekExistData['data'];
      $dataRuangan = $cekExistData['ruangan'];
      $model = $cekExistData['model'];
      $path = $cekExistData['path'];
      $add = [];
      if($data) {
        $model->attributes = $data;
        $model->pemeriksaanspesialismcu_id = $data['pemeriksaanspesialismcu_id'];
        $model->berat_badan = DocoHelpers::convertPointToComma($data['berat_badan']);
        $model->tinggi_badan = DocoHelpers::convertPointToComma($data['tinggi_badan']);
        $model->suhu = DocoHelpers::convertPointToComma($data['suhu']);
        if(isset($data['additional_pemeriksaan']) && !empty($data['additional_pemeriksaan'])) {
          $add = json_decode($data['additional_pemeriksaan'], true);
          $anjuran = isset($add['anjuran']) ? $add['anjuran'] : '';
          $kesimpulan = isset($add['kesimpulan']) ? $add['kesimpulan'] : '';
          $keluhan = isset($add['keluhan']) ? $add['keluhan'] : '';
          if($ruangan_id == $dataRuangan['default_tht']) {
            $attributes = ($model->attribute_tht) ? $model->attribute_tht : [];
          }
          elseif($ruangan_id == $dataRuangan['default_mata']) {
            $attributes = ($model->attribute_mata) ? $model->attribute_mata : [];
          }
          elseif($ruangan_id == $dataRuangan['default_kardiologi']) {
            $attributes = ($model->attribute_kardiologi) ? $model->attribute_kardiologi : [];
          }
          elseif($ruangan_id == $dataRuangan['default_gigi']) {
            $attributes = ($model->attribute_gigi) ? $model->attribute_gigi : [];
          }
          elseif($ruangan_id == $dataRuangan['default_obsgyn']) {
            $attributes = ($model->attribute_obsgyn) ? $model->attribute_obsgyn : [];
          }
          elseif($ruangan_id == $dataRuangan['default_internis']) {
            $attributes = ($model->attribute_internis) ? $model->attribute_internis : [];
          }
          elseif($ruangan_id == $dataRuangan['default_neurologi']) {
            $attributes = ($model->attribute_neurologi) ? $model->attribute_neurologi : [];
          }
          elseif($ruangan_id == $dataRuangan['default_urologi']) {
            $attributes = ($model->attribute_urologi) ? $model->attribute_urologi : [];
          }
          elseif($ruangan_id == $dataRuangan['default_gizi']) {
            $attributes = ($model->attribute_gizi) ? $model->attribute_gizi : [];
          }else{
            $attributes = [];
          }

          if(!empty($attributes)) {
            $model->kesimpulan = $this->setValue($kesimpulan);
            $model->keluhan = $this->setValue($keluhan);
            $model->anjuran = $this->setValue($anjuran);
            foreach ($attributes as $key => $value) {
              if(isset($add[$value])) {
                $model->$value = $this->setValue($add[$value]);
              }
            }
            if(isset($add['anak_hidup'])) {
              $model->anak_hidup = $this->setValue($add['anak_hidup']);
            }
            if(isset($add['prematur'])) {
              $model->prematur = $this->setValue($add['prematur']);
            }
          }
        }
      }

      return [
        'data' => $model->attributes,
        'model' => $model,
        'path' => $path,
      ];
  }

  public function actionCetakPemeriksaanMcu($id)
  {
      $id = DocoHelpers::decrypt($id);
      $path = Yii::getAlias("@download") . "/cetak-pemeriksaan-mcu-{$id}.pdf";
      $url = "pemeriksaan/cetak-pemeriksaan-mcu?id=".$id;
      $response = $this->_restMcu->get($url, [
          'save_to' => $path,
      ]);
      $body = json_decode($response->getBody(),TRUE);
      return DocoHelpers::previewPdf($path);
  }

  private function setValue($data)
  {
      $result = '';
      if(isset($data)) {
        $result = $data;
      }

      return $result;
  }
}
