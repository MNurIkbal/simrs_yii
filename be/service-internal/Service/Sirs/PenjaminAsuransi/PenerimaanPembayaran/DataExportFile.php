<?php 

namespace Integrasi\Service\Sirs\PenjaminAsuransi\PenerimaanPembayaran;

use Yii;
use Integrasi\Service\Sirs\Models\PenjaminAsuransi\PenerimaanPembayaran\InfoPenerimaanPembayaranView;
use yii\helpers\ArrayHelper;

class DataExportFile extends \Integrasi\Contracts\DocoImplement
{
    
   public function execute()
   {
      $cacheFiles = Yii::$app->cacheFiles;
      $attributes = $this->getDataAttibutes();
      $cacheFiles->set($this->unique_str, $attributes);

      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:'.$this->unique_str,
         'message' => json_encode([
            'status' => 'finish', 
            'messageProcess' => 'Berhasil menyiapkan data.',
            'progress' => 70
         ]),
      ]);

      return json_encode([
         'service' => 'Sirs-DataExportFile',
         'payload' => $this->attributes,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
   }

   protected function getDataAttibutes()
   {
      $order = $this->order;
      $columnOrder = $typeOrder = '';
      if(!empty($order)) {
         $order = explode(" ", $order);
         $columnOrder = ArrayHelper::getValue($order, 0);
         $typeOrder = ArrayHelper::getValue($order, 1);
      }

      $model = new InfoPenerimaanPembayaranView;
      $query = $model::find();
      $start = date('Y-m-d 00:00:00');
      $end = date('Y-m-d 23:59:00');
      
      if(isset($this->filter['tgl_terimabayarklaim'])) {
         $explode = explode(" - ", $this->filter['tgl_terimabayarklaim']);
         if(count($explode) == 2) {
            $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
            $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
         }
      }

      if(isset($this->filter['no_terimabayarklaim'])) {
         $query->andWhere(['ILIKE', 'no_terimabayarklaim', $this->filter['no_terimabayarklaim']]);
      }

      if(isset($this->filter['carabayar_id'])) {
         $query->andWhere(['carabayar_id' => $this->filter['carabayar_id']]);
      }

      if(isset($this->filter['penjamin_id'])) {
         $query->andWhere(['penjamin_id' => $this->filter['penjamin_id']]);
      }

      $query->andWhere(['between', 'tgl_terimabayarklaim', $start, $end]);
      $query->orderBy([$columnOrder => ($typeOrder == 'DESC') ? SORT_DESC : SORT_ASC]);
      return $query->asArray()->all();
   }
}