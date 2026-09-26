<?php 

namespace Integrasi\Service\Sirs\PenjaminAsuransi\PenerimaanPembayaran;

use Yii;
use yii\base\View;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoConstants;

class DataDetail extends \Integrasi\Contracts\DocoImplement
{
   public function execute()
   {
      $cacheFiles = Yii::$app->cacheFiles;
      $attributes = $this->getDataAttibutes($this->id);
      $noPembayaran = ArrayHelper::getValue($attributes, 'no_pembayaran');
      $cacheFiles->set($this->unique_str, $attributes);
      $cacheFiles->set('no-pembayaran-'.$this->unique_str, $noPembayaran);
      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'invoice:'.$this->unique_str,
         'message' => json_encode(['unique_process' => $this->unique_str]),
      ]);
      return json_encode([
         'service' => 'Sirs-DataDetail',
         'payload' => $this->attributes,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
   }

   private function getDataAttibutes()
   {
      $db = Yii::$app->db;
      $header = $db->createCommand("
         SELECT * FROM infoterimabayarklaim_v WHERE terimabayarklaim_id = {$this->id}
      ")->queryOne();

      $detail = $db->createCommand("
         SELECT * FROM infoterimabayarklaimdetail_v WHERE terimabayarklaim_id = {$this->id}
      ")->queryAll();

      // $viewPath = '@app/views/detail-penerimaan-klaim/index';
      return [
         'header' => $header,
         'detail' => $detail,
         'no_pembayaran' => ArrayHelper::getValue($header, 'no_terimabayarklaim', '-')
      ];
   }
}