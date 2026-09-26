<?php 

namespace app\commands;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\PasienMasukPenunjangT;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\Services\InternalService;
use yii\console\Controller;

class CronRadiologiController extends Controller
{
   public function actionIndex($startDate = null, $endDate = null)
   {
      // Handling sementara dikarenakan ketika di implementasikan di cron tidak berfungsi
      $startDate = date('Y-m-d',strtotime("-3 days"));
      $endDate = date('Y-m-d');
      // Code before improve.
      // $dataPasien = PasienMasukPenunjangT::find()->select([
      //    'pasienmasukpenunjang_t.pasienmasukpenunjang_id',
      //    'pasienmasukpenunjang_t.no_masukpenunjang',
      //    'pasienmasukpenunjang_t.pendaftaran_id',
      //    'tindakanpelayanan_t.daftartindakan_id',
      //    'tindakanpelayanan_t.tindakanpelayanan_id'
      // ])
      // ->join('join', 'tindakanpelayanan_t', 'tindakanpelayanan_t.pasienmasukpenunjang_id=pasienmasukpenunjang_t.pasienmasukpenunjang_id')
      // ->andWhere([
      //    'pasienmasukpenunjang_t.is_deleted' => false,
      //    'tindakanpelayanan_t.instalasi_id' =>DocoConstants::INST_ID_RAD,
      //    'pasienmasukpenunjang_t.status_periksa' => DocoConstants::ST_P_PEN_BLM_PRKS
      // ])->asArray()->all(); 

      $connection = Yii::$app->db;
      $sql = "SELECT
              pasienmasukpenunjang_t.pasienmasukpenunjang_id,
              pasienmasukpenunjang_t.no_masukpenunjang,
              pasienmasukpenunjang_t.pendaftaran_id,
              tindakanpelayanan_t.daftartindakan_id,
              tindakanpelayanan_t.tindakanpelayanan_id
          FROM 
              pasienmasukpenunjang_t
          JOIN tindakanpelayanan_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
          WHERE
              pasienmasukpenunjang_t.is_deleted = FALSE
          AND tindakanpelayanan_t.instalasi_id = :instalasi_id
          AND pasienmasukpenunjang_t.status_periksa <> :batal_periksa
          AND pasienmasukpenunjang_t.status_periksa <> :selesai_periksa
          AND pasienmasukpenunjang_t.is_deleted = FALSE
          AND NOT EXISTS (
            SELECT 1 FROM hasilbridgingradiologi_t WHERE order_no = concat(pasienmasukpenunjang_t.no_masukpenunjang,'-',tindakanpelayanan_t.tindakanpelayanan_id)
          )
      ";

      $params = [
         ':instalasi_id' => DocoConstants::INST_ID_RAD,
         ':batal_periksa' => DocoConstants::ST_P_PEN_BTL,
         ':selesai_periksa' => DocoConstants::ST_P_PEN_SELESAI,
      ];

      if ($startDate && $endDate) {
         $start = date('Y-m-d H:i:s', strtotime($startDate. ' 00:00:00'));
         $end = date('Y-m-d H:i:s', strtotime($endDate. ' 23:59:59'));
         $dateFilter = [
             ':start' => $start,
             ':end' => $end
         ];
         $params = array_merge($params, $dateFilter);
         $sql .= " AND pasienmasukpenunjang_t.tglmasukpenunjang::date BETWEEN :start AND :end";
      }

      $command = $connection->createCommand($sql);
      $command->bindValues($params);
      $dataPasien = $command->queryAll();

      if(!empty($dataPasien)) {
         try {
            foreach ($dataPasien as $key => $value) {
               (new InternalService)->sendTo([
                  'InaBroker' => [
                     'CreateHasilBridging' => [
                        'tindakan_id' => ArrayHelper::getValue($value, 'tindakanpelayanan_id'),
                        'no_masukpenunjang' => ArrayHelper::getValue($value, 'no_masukpenunjang'),
                        'pendaftaran_id' => ArrayHelper::getValue($value, 'pendaftaran_id'),
                        'daftartindakan_id' => ArrayHelper::getValue($value, 'daftartindakan_id'),
                        'pasienpenunjang_id' => ArrayHelper::getValue($value, 'pasienmasukpenunjang_id'),
                     ]
                  ]
              ]);
              usleep(25000);
            }
            Yii::error("Cron berhasil running.");
         } catch (\yii\db\Exception $e) {
            Yii::error($e);
            return $e->getMessage();
         } catch (\Exception $e) {
            Yii::error([
               'file' => $e->getFile(),
               'line' => $e->getLine(),
               'message' => $e->getMessage()
            ]);
            return $e->getMessage();
         }
      }
   }
}


    