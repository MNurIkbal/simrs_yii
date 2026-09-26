<?php 

namespace app\commands;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\TindakanPelayanan;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use app\modules\v1\businessLogic\TindakanAkomodasi;
use Mpdf\Tag\Em;
use yii\console\Controller;
use app\modules\v1\models\PasienAdmisi;

class CronAkomodasiSementaraController extends Controller
{
   public function actionIndex($endDate = null)
   {
      $configAkomodasi = (new DocoConstansId)->actionGetAdditional('konfig_stop_akomodasi');
      $cutOff = "";
      if(!empty($configAkomodasi)) {
         $configAkomodasi = json_decode($configAkomodasi, true);
         $cutOff = isset($configAkomodasi[0]) ? $configAkomodasi[0] : "";
      }
      if(empty($endDate)) {
         $now = strtotime("now");
         $endDate = strtotime(date('Y-m-d '.$cutOff));
         $endDate = date('Y-m-d H:i', $endDate);
      }
      $listAkomodasi = [];

      $dataPasienRanap = PasienAdmisi::find()->select([
         'pasienadmisi_t.pasienadmisi_id',
         'pendaftaran_t.pendaftaran_id',
         'pendaftaran_t.tgl_pendaftaran',
      ])
      ->join('join', 'pendaftaran_t', 'pendaftaran_t.pendaftaran_id=pasienadmisi_t.pendaftaran_id')
      ->andWhere([
         'pendaftaran_t.is_stopakomodasi' => false
      ])
      ->andWhere([
         'pendaftaran_t.is_ditagihkan' => true
      ])
      ->andWhere(['<>', 'pasienadmisi_t.status_ranap', DocoConstants::STATUS_RANAP_BATAL_RAWAT])
      ->orderBy(['pendaftaran_t.tgl_pendaftaran' => 'SORT_DESC'])
      ->asArray()->all(); 
      
      if(!empty($dataPasienRanap)) {
         $transaction = Yii::$app->db->beginTransaction();
         try {
            foreach ($dataPasienRanap as $key => $value) {
               $admisiId = ArrayHelper::getValue($value, 'pasienadmisi_id');
               if(!empty($admisiId)) {
                  $type = DocoConstants::AKOMODASI_MUTASI;
                  $dataAkomodasi = TindakanAkomodasi::execute(
                     $type, 
                     $admisiId,
                     $endDate,
                     true,
                     false,
                     null,
                     true
                  );
                  if(!empty($dataAkomodasi)) {
                     $data = ArrayHelper::getValue($dataAkomodasi, 'tindakan_akomodasi', []);
                     if(!empty($data)) {
                        foreach ($data as $key => $value) {
                           $listAkomodasi[] = [
                              'kelaspelayanan_id' => ArrayHelper::getValue($value, 'kelaspelayanan_id'),
                              'pasien_id' => ArrayHelper::getValue($value, 'pasien_id'),
                              'daftartindakan_id' => ArrayHelper::getValue($value, 'daftartindakan_id'),
                              'tipepaket_id' => ArrayHelper::getValue($value, 'tipepaket_id'),
                              'carabayar_id' => ArrayHelper::getValue($value, 'carabayar_id'),
                              'pendaftaran_id' => ArrayHelper::getValue($value, 'pendaftaran_id'),
                              'pasienadmisi_id' => ArrayHelper::getValue($value, 'pasienadmisi_id'),
                              'jeniskasuspenyakit_id' => ArrayHelper::getValue($value, 'jeniskasuspenyakit_id'),
                              'instalasi_id' => ArrayHelper::getValue($value, 'instalasi_id'),
                              'kamarruangan_id' => ArrayHelper::getValue($value, 'kamarruangan_id'),
                              'kamartempattidur_id' => ArrayHelper::getValue($value, 'kamartempattidur_id'),
                              'ruangan_id' => ArrayHelper::getValue($value, 'ruangan_id'),
                              'penjamin_id' => ArrayHelper::getValue($value, 'penjamin_id'),
                              'tgl_tindakan' => ArrayHelper::getValue($value, 'tgl_tindakan'),
                              'dokterpenanggungjawab_id' => ArrayHelper::getValue($value, 'dokterpenanggungjawab_id'),
                              'tarif_satuan' => ArrayHelper::getValue($value, 'tarif_satuan', 0),
                              'qty_tindakan' => 1,
                              //'qty_tindakan' => isset($value['qty_tindakan']) ? (int) $value['qty_tindakan'] : 1,
                              'tarif_tindakan' => ArrayHelper::getValue($value, 'tarif_tindakan', 0),
                              'tarifcyto_tindakan' => ArrayHelper::getValue($value, 'tarifcyto_tindakan', 0),
                              'cyto_tindakan' => ArrayHelper::getValue($value, 'cyto_tindakan', false),
                              'discount_tindakan' => ArrayHelper::getValue($value, 'discount_tindakan', 0),
                              'additional_data' => ArrayHelper::getValue($value, 'additional_data'),
                              'created_date' => date('Y-m-d H:i:s'),
                              'is_deleted' => false,
                              'created_by' => 1,
                              'is_active' => true,
                           ];
                        }
                     }
                  }else{
                     Yii::error([
                        'msg' => 'nah kalo masuk sini, berarti data akomodasinya kosong',
                        'admisi_id' => isset($admisiId) ? $admisiId : 0,
                    ]);
                  }
               }
            }
            if(!empty($listAkomodasi)) {
               Yii::error([
                  'msg' => 'nah kalo masuk sini, berarti datanya ada siap untuk di insert',
                  'pendaftaran_id' => isset($listAkomodasi[0]['pendaftaran_id']) ? $listAkomodasi[0]['pendaftaran_id'] : 0,
              ]);
               TindakanPelayanan::batchInsert($listAkomodasi);
               $transaction->commit();
            }
         } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            return $e->getMessage();
         } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            return $e->getMessage();
         }
      }
   }
}


    