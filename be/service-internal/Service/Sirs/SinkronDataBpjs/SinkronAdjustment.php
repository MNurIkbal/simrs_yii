<?php 

namespace Integrasi\Service\Sirs\SinkronDataBpjs;
use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoConstants;
use Integrasi\Service\Sirs\Models\SyKunjunganAdjusmentHeader;
use Integrasi\Service\Sirs\Models\SyKunjunganAdjusmentDetail;
use Integrasi\Service\Sirs\Models\Cron;

class SinkronAdjustment extends \Integrasi\Contracts\DocoImplement
{

   protected $groupBpjs = DocoConstants::GROUP_BPJS;
   protected $instalasiRanap = DocoConstants::INST_ID_RI;
   protected $kunjungan = [];
   protected $kunjunganRanap = [];
   protected $tmpNoDaftar = [];
   protected $hapusNoPendaftaran = [];

   /**
     * @todo Function get data kunjungan
     * @return array
     * @author Budi <budi@sirs.co.id>
     */
   public function execute()
   {
      sleep(2);
      date_default_timezone_set('Asia/Jakarta');
      $tgl_pendaftaran = $this->tgl_pendaftaran;
      $jam_pendaftaran = $this->jam_pendaftaran;

      $this->insertAdjustmentHeader($tgl_pendaftaran, $jam_pendaftaran);
      $this->insertAdjustmentDetail($tgl_pendaftaran, $jam_pendaftaran);

      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:'.$this->unique_str,
         'message' => json_encode([
            'status' => 'finish', 
            'messageProcess' => 'Berhasil menyiapkan data Adjustment.',
            'progress' => 100
         ]),
      ]);

      return json_encode([
         'service' => 'Sirs-SinkronAdjustment',
         'payload' => $this->attributes,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
   }

   protected function insertAdjustmentHeader($tgl_pendaftaran, $jam_pendaftaran)
   {
      $tmpAdjH = [];
      $data = $this->getDataAdjustmentHeader($tgl_pendaftaran, $jam_pendaftaran);
      if(!empty($data)) {
         foreach ($data as $key => $value) {
            $noAdjHeader = (isset($value['no_pendaftaran'])) ? $value['no_pendaftaran'] : ArrayHelper::getValue($value, 'NO_PENDAFTARAN');
            if ($noAdjHeader) {
               if (!isset($tmpAdjH[$noAdjHeader])) {
                  $tmpAdjH[$noAdjHeader] = $noAdjHeader;
               }
               $item = [
                  'no_pendaftaran' => $noAdjHeader,
                  'no_buktiadjust' => array_key_exists('no_buktiadjust', $value) ? $value['no_buktiadjust'] : ArrayHelper::getValue($value, 'NO_BUKTIADJUST'),
                  'tgl_adjust' => array_key_exists('tgl_adjust', $value) ? $value['tgl_adjust'] : ArrayHelper::getValue($value, 'TGL_ADJUST'),
                  'no_buktitrans' => array_key_exists('no_buktitrans', $value) ? $value['no_buktitrans'] : ArrayHelper::getValue($value, 'NO_BUKTITRANS'),
                  'total' => (float) array_key_exists('total', $value) ? $value['total'] : ArrayHelper::getValue($value, 'TOTAL', 0),
                  'selisih' => (float) array_key_exists('selisih', $value) ? $value['selisih'] : ArrayHelper::getValue($value, 'SELISIH', 0),
                  'sisa' => (float) array_key_exists('sisa', $value) ? $value['sisa'] : ArrayHelper::getValue($value, 'SISA', 0)
               ];

               $adjHeader[] = $item;
            }
         }
      }

      if (!empty($tmpAdjH)) {
         $tmpAdjHDouble = array();
         foreach ($tmpAdjH as $key) {
            $tmpAdjHDouble[] = $key;
         }
         $tmpAdjHDouble = implode("','", $tmpAdjHDouble);
         $sql = "delete from sy_adjusment where no_pendaftaran IN ('$tmpAdjHDouble')";
         $hapusAdjH = Yii::$app->db->createCommand($sql)->execute();
      }

      if (!empty($adjHeader)) {
         $syncAdjHeader = SyKunjunganAdjusmentHeader::batchInsert($adjHeader);
      }

      /* ganti proses agar cron tetap berjalan */
      $this->updateCron(DocoConstants::VAR_CRON_ADJH);
   }

   protected function insertAdjustmentDetail($tgl_pendaftaran, $jam_pendaftaran)
   {
      $tmpAdjD = [];
      $data = $this->getDataAdjustmentDetail($tgl_pendaftaran, $jam_pendaftaran);
      if(!empty($data)) {
         foreach ($data as $key => $value) {
            $noAdjDetail = (isset($value['no_pendaftaran'])) ? $value['no_pendaftaran'] : ArrayHelper::getValue($value, 'NO_PENDAFTARAN');
               $modelAdjHeader = SyKunjunganAdjusmentHeader::find()->where(['no_pendaftaran' => $noAdjDetail])->one();
               if ($modelAdjHeader != '') {
                  if (!isset($tmpAdjD[$noAdjDetail])) {
                     $tmpAdjD[$noAdjDetail] = $noAdjDetail;
                  }
                  $item = [
                     'adjusment_id' => $modelAdjHeader->adjusment_id,
                     'no_pendaftaran' => $noAdjDetail,
                     'no_buktiadjust' => array_key_exists('no_buktiadjust', $value) ? $value['no_buktiadjust'] : ArrayHelper::getValue($value, 'NO_BUKTIADJUST'),
                     'kode_bagian' => array_key_exists('kode_bagian', $value) ? $value['kode_bagian'] : ArrayHelper::getValue($value, 'KODE_BAGIAN'),
                     'kode_layanan' => array_key_exists('kode_layanan', $value) ? $value['kode_layanan'] : ArrayHelper::getValue($value, 'KODE_LAYANAN'),
                     'kode_adjust' => array_key_exists('kode_adjust', $value) ? $value['kode_adjust'] : ArrayHelper::getValue($value, 'KODE_ADJUST'),
                     'kode_nota' => array_key_exists('kode_nota', $value) ? $value['kode_nota'] : ArrayHelper::getValue($value, 'KODE_NOTA'),
                     'total_rs' => (float) array_key_exists('total_rs', $value) ? $value['total_rs'] : ArrayHelper::getValue($value, 'TOTAL_RS'),
                     'total_hakrs' => (float) array_key_exists('total_hakrs', $value) ? $value['total_hakrs'] : ArrayHelper::getValue($value, 'TOTAL_HAKRS'),
                     'total_dokter' => (float) array_key_exists('total_dokter', $value) ? $value['total_dokter'] : ArrayHelper::getValue($value, 'TOTAL_DOKTER'),
                     'total_hakdokter' => (float) array_key_exists('total_hakdokter', $value) ? $value['total_hakdokter'] : ArrayHelper::getValue($value, 'TOTAL_HAKDOKTER'),
                     'status_bayar' => array_key_exists('stbayar', $value) ? $value['stbayar'] : ArrayHelper::getValue($value, 'STBAYAR'),
                     'status_batal' => array_key_exists('stbatal', $value) ? $value['stbatal'] : ArrayHelper::getValue($value, 'STBATAL')
                  ];

                  $adjDetail[] = $item;
               }
         }
      }

      if (!empty($tmpAdjD)) {
         $tmpAdjDDouble = array();
         foreach ($tmpAdjD as $key) {
            $tmpAdjDDouble[] = $key;
         }
         $tmpAdjDDouble = implode("','", $tmpAdjDDouble);
         $sql = "delete from sy_adjusmentdetail where no_pendaftaran IN ('$tmpAdjDDouble')";
         $hapusAdjD = Yii::$app->db->createCommand($sql)->execute();
      }

      if (!empty($adjDetail)) {
         $syncAdjDetail = SyKunjunganAdjusmentDetail::batchInsert($adjDetail);
      }

      /* ganti proses agar cron tetap berjalan */
      $this->updateCron(DocoConstants::VAR_CRON_ADJD);
   }

   protected function getDataAdjustmentHeader($tgl_pendaftaran, $jam_pendaftaran)
   {
      return [];
   }

   protected function getDataAdjustmentDetail($tgl_pendaftaran, $jam_pendaftaran)
   {
      return [];
   }

   protected function updateCron($cronId)
   {
      $modelCron = Cron::find()->where(['cron_id' => $cronId])->one();
      if(!empty($modelCron)) {
         $modelCron->cron_tgl_mulai = date('Y-m-d H:i:s', strtotime('NOW'));
         $modelCron->cron_tgl_akhir = date('Y-m-d H:i:s', strtotime('NOW'));
         $modelCron->save();
      }
   }
}