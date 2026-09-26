<?php 

namespace Integrasi\Service\Sirs\Remunerasi;

use Yii;
use Integrasi\Service\Sirs\Models\Remunerasi\RemunPegawaiView;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;

class DataExportExcel extends \Integrasi\Contracts\DocoImplement
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
         'service' => 'Sirs-Renunerasi-DataExportExcel',
         'payload' => $this->attributes,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
   }

   protected function getDataAttibutes()
   {
      $model = new RemunPegawaiView;
      $query = $model::find();
      $bulan = date('m');
      $tahun = date('Y');
      $get = $this->get;
      $filter = $this->filter;
      $ids = ArrayHelper::getValue($get, 'ids', []);
      $periodeRemun = ArrayHelper::getValue($filter, 'periode_remun');
      $namaPegawai = ArrayHelper::getValue($filter, 'nama_pegawai');
      $jabatan = ArrayHelper::getValue($filter, 'jabatan_nama');
      $nik = ArrayHelper::getValue($filter, 'nik');
      
      if(!empty($ids)) {
         return $query->where(['remunpegawai_id' => $ids])->asArray()->all();
      }
      else {
         if(!empty($periodeRemun)) {
            $explode = explode("-", $periodeRemun);
            if(count($explode) == 2) {
               $bulan = ArrayHelper::getValue($explode, 1);
               $tahun = ArrayHelper::getValue($explode, 0);
            }
         }

         $query->andWhere([
            'periode_bulan' => $bulan,
            'periode_tahun' => $tahun,
         ]);

         if(!empty($namaPegawai)) {
            $query->andWhere(['ILIKE', 'LOWER(nama_pegawai)', strtolower($namaPegawai)]);
         }
         if(!empty($jabatan)) {
            $query->andWhere(['ILIKE', 'LOWER(jabatan_nama)', strtolower($jabatan)]);
         }
         if(!empty($nik)) {
            $query->andWhere(['ILIKE', 'nik', strtolower($nik)]);
         }

         return $query->asArray()->all();
      }
   }
}