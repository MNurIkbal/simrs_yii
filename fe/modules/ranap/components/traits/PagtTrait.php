<?php

namespace app\modules\ranap\components\traits;

use Yii;
use app\modules\ranap\models\PagtForm;
use app\modules\ranap\models\PagtMonevForm;

trait PagtTrait
{
   public function actionPagt()
   {
      $pendaftaran_id = $this->helper->decrypt(Yii::$app->request->get('id'));
      $data = $this->guzzleExec($this->_restRanap, [
         'url' => 'pagt',
         'payload' => [
               'query' => [
                  'pendaftaran_id' => $this->helper->decrypt(Yii::$app->request->get('id'))
               ]
         ]
      ]);
      $historyData = isset($data['history']) ? $data['history'] : [];
      $model = new PagtForm;
      $model->attributes = $data['pagt'];
      $data['pagt']['pendaftaran_id'] = Yii::$app->request->get('id');
      $modelMonev = new PagtMonevForm;
      $arrayConfig = $this->getConfig('pagt');
      return $this->renderAjax('pagt/__form', compact('model', 'modelMonev', 'data', 'arrayConfig', 'pendaftaran_id', 'historyData'));
   }

   public function actionSavePagt()
   {
      $model = new PagtForm;
      $model->attributes = Yii::$app->request->post();
      if (!$model->validate()) {
         return $this->responseJson(422, 'Silakan cek kembali inputan.', $this->mapErrorForm($model->errors, 'PagtForm'));
      } else {
         $arrayPayload = [];
         foreach ($model->attributes as $fieldName => $valueField) {
            $arrayPayload[$fieldName] = is_null($valueField) ? '' : $valueField;
         }
         $formData = array_merge($arrayPayload, [
            'pendaftaran_id' => $this->helper->decrypt($model->pendaftaran_id),
            'pagt_monev' => Yii::$app->request->post('pagt_monev')
         ]);
         return $this->guzzleExec($this->_restRanap, [
            'url' => 'pagt/save-pagt',
            'method' => 'POST',
            'payload' => [
               'form_params' => [
                  'formdata' => $formData
               ]
            ],
            'returnResponse' => true
         ]);
      }
   }
}
