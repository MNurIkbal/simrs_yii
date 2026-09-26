<?php

namespace Integrasi\Service\Sirs\PenjaminAsuransi\PenerimaanPembayaran;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Service\Sirs\Models\PenjaminAsuransi\TerimaBayarKlaimDetailR;

class InsertImportKlaim extends \Integrasi\Contracts\DocoImplement
{
   public function execute()
   {
      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'invoice:'.$this->randString,
         'message' => json_encode([
            'status' => 'finish', 
            'messageProcess' => 'Sedang mengconvert data.',
            'progress' => 80
         ]),
      ]);
      
      $cacheFiles = Yii::$app->cacheFiles;
      $result = $cacheFiles->get('detail-klaim-bpjs-'.$this->randString);
      $dataPengajuan = ArrayHelper::getValue($result, 'data', []);
      $tmpPengajuan = [];
      if(!empty($dataPengajuan)) {
         foreach ($dataPengajuan as $value) {
            $pengajuanKlaimId = ArrayHelper::getValue($value, 'pengajuanklaim_id');
            $totalPengajuanBpjs = ArrayHelper::getValue($value, 'diajukan', 0);
            $totalTerbayarBpjs = ArrayHelper::getValue($value, 'disetujui', 0);
            if($totalPengajuanBpjs != 0) {
               $tmpPengajuan[] = [
                  'pengajuanklaim_id' => $pengajuanKlaimId,
                  'no_pengajuanklaim' => ArrayHelper::getValue($value, 'no_pengajuanklaim'),
                  'total_pengajuan' => $totalPengajuanBpjs,
                  'total_terbayar' => $totalTerbayarBpjs,
                  'pembayaran' => ArrayHelper::getValue($value, 'pembayaran', 0),
                  'total_sisapiutang' => ArrayHelper::getValue($value, 'total_sisapiutang', 0),
                  'no_sep' => ArrayHelper::getValue($value, 'no_sep'),
                  'tgl_verifikasi' => ArrayHelper::getValue($value, 'tgl_verifikasi'),
                  'tagihan_rs' => ArrayHelper::getValue($value, 'riil_rs', 0),
                  'unique_str' => $this->randString,
                  'is_alokasi' => false,
                  'created_by' => $this->user_id,
                  'created_date' => date('Y-m-d H:i:s'),
                  'is_deleted' => false,
                  'is_active' => true,
               ];
            }
         }
      }

      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'invoice:'.$this->randString,
         'message' => json_encode([
            'status' => 'finish', 
            'messageProcess' => 'Sedang mengimport data.',
            'progress' => 90
         ]),
      ]);

      if(!empty($tmpPengajuan)) {
         TerimaBayarKlaimDetailR::batchInsert($tmpPengajuan, false);
      }

      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'invoice:'.$this->randString,
         'message' => json_encode([
            'status' => 'finish', 
            'messageProcess' => 'Berhasil mengimport data.',
            'progress' => 100,
            'data_pengajuan' => $dataPengajuan,
            'total_pengajuan' => ArrayHelper::getValue($result, 'total_pengajuan', 0),
            'total_disetujui' => ArrayHelper::getValue($result, 'total_disetujui', 0),
         ]),
      ]);

      return json_encode([
         'service' => 'Sirs-InsertImportKlaim',
         'payload' => $this->randString,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
   }
}