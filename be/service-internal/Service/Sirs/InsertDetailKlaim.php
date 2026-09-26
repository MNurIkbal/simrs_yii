<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoConstansId;
use yii\helpers\ArrayHelper;

class InsertDetailKlaim extends \Integrasi\Contracts\DocoImplement
{
   public function execute()
   {
      $results = $this->result;
      $randString = ArrayHelper::getValue($results, 'randString');
      $idParent = (int) ArrayHelper::getValue($results, 'terimabayarklaim_id');
      $caraBayarId = (int) ArrayHelper::getValue($results, 'carabayar_id');
      $dataPengajuan = ArrayHelper::getValue($results, 'data_pengajuan', []);
      $statusProses = DocoConstants::PROSES_PEMBAYARAN;

      if($caraBayarId == DocoConstants::VAR_ID_CARABAYAR_BPJS) {
         Yii::$app->db->createCommand("
            INSERT INTO terimabayarklaimdetail_t (terimabayarklaim_id, pengajuanklaim_id, no_pengajuanklaim, total_pengajuan, total_terbayar, pembayaran, total_sisapiutang, is_alokasi, 
            no_sep, tgl_verifikasi, tagihan_rs)
            SELECT '$idParent' AS terimabayarklaim_id, pengajuanklaim_id, no_pengajuanklaim, total_pengajuan, total_terbayar, pembayaran, total_sisapiutang, is_alokasi, 
            no_sep, tgl_verifikasi, tagihan_rs
            FROM terimabayarklaimdetail_r
            WHERE unique_str = '$randString'
         ")->execute();

         Yii::$app->db->createCommand("
            DELETE FROM terimabayarklaimdetail_r
            WHERE unique_str = '$randString'
         ")->execute();
      }
      else {
         $pengajuanNonBpjs = [];
         if(!empty($dataPengajuan)) {
            foreach ($dataPengajuan as $key => $value) {
               $pengajuanKlaimId = ArrayHelper::getValue($value, 'pengajuanklaim_id');
               if(!empty($pengajuanKlaimId)) {
                  $pengajuanNonBpjs[] = $pengajuanKlaimId;
               }
            }

            if(!empty($pengajuanNonBpjs)) {
               $pengajuanNonBpjs = "( " . implode(",", $pengajuanNonBpjs) . " )";
               $reCheck = Yii::$app->db->createCommand("
                  SELECT no_pengajuanklaim FROM pengajuanklaim_t
                  WHERE pengajuanklaim_id IN {$pengajuanNonBpjs} AND status_pengajuanklaim = {$statusProses}
               ")->queryAll();
               
               if(empty($reCheck)) {
                  Yii::$app->db->createCommand("
                     UPDATE pengajuanklaim_t SET status_pengajuanklaim = {$statusProses} 
                     WHERE pengajuanklaim_id IN {$pengajuanNonBpjs}
                  ")->execute();
               }
            }
         }
      }

      return json_encode([
         'randString' => $randString,
         'idParent' => $idParent,
         'caraBayarId' => $caraBayarId,
      ]);
   }
}