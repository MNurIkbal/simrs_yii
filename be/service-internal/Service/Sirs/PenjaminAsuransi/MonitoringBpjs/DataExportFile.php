<?php 

namespace Integrasi\Service\Sirs\PenjaminAsuransi\MonitoringBpjs;

use Yii;
use Integrasi\Service\Sirs\Models\PenjaminAsuransi\MonitoringBpjs\InfoMonitoringBpjsView;
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
         'service' => 'Sirs-DataExportExcel',
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

      $model = new InfoMonitoringBpjsView;
      $query = $model::find();
      $start = date('Y-m-d 00:00:00');
      $end = date('Y-m-d 23:59:00');
      if(isset($this->filter['tgl_pendaftaran'])) {
         $explode = explode(" - ", $this->filter['tgl_pendaftaran']);
         if(count($explode) == 2) {
            $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
            $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
         }
      }
      if(!empty($this->filter['status_periksa'])) {
            $status_periksa = $this->filter['status_periksa'];
            $query->andWhere(['status_periksa' => $status_periksa]);
      }

      $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
      $query->orderBy([$columnOrder => ($typeOrder == 'DESC') ? SORT_DESC : SORT_ASC]);
      return $query->asArray()->all();
   }
}