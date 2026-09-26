<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use Integrasi\Service\Sirs\Models\TindakanPelayanan;

class CronAkomodasiSementara extends \Integrasi\Contracts\DocoImplement
{
   public function execute()
   {
      ini_set('memory_limit', '256M');
      set_time_limit (60);
      $connection = Yii::$app->db;
      $transaction = $connection->beginTransaction();
      $admisiId = $this->admisiId;
      $endDate = $this->endDate;
      $userId = $this->user_id;
      $listAkomodasi = [];
      try {
         if(!empty($admisiId)) {
            $result = $this->getDataAkomodasi($admisiId, $endDate);
            if(!empty($result)) {
               foreach ($result as $key => $value) {
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
                     'qty_tindakan' => isset($value['qty_tindakan']) ? (int) $value['qty_tindakan'] : 1,
                     'tarif_tindakan' => ArrayHelper::getValue($value, 'tarif_tindakan', 0),
                     'tarifcyto_tindakan' => ArrayHelper::getValue($value, 'tarifcyto_tindakan', 0),
                     'cyto_tindakan' => ArrayHelper::getValue($value, 'cyto_tindakan', false),
                     'discount_tindakan' => ArrayHelper::getValue($value, 'discount_tindakan', 0),
                     'additional_data' => ArrayHelper::getValue($value, 'additional_data'),
                     'created_date' => date('Y-m-d H:i:s'),
                     'is_deleted' => false,
                     'created_by' => $userId,
                     'is_active' => true,
                  ];
               }
            }
            TindakanPelayanan::batchInsert($listAkomodasi);
            $transaction->commit();
         }
      } catch (\yii\db\Exception $e) {
         $transaction->rollBack();
         Yii::error([$e]);
         return $e->getMessage();
      } catch (\Exception $e) {
         $transaction->rollBack();
         Yii::error([$e]);
         return $e->getMessage();
      }

      return json_encode([
         'service' => 'Sirs-CronAkomodasiSementara',
         'payload' => $this->attributes,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
   }

   private function getDataAkomodasi($admisiId, $endDate)
   {
      $client = $this->setUrl();
      try {
			$response = $client->get('api/get-akomodasi-sementara', [
            'query' => [
               'admisiId' => $admisiId,
               'endDate' => $endDate,
            ]
         ]);
         $response = json_decode($response->getBody(), true);
         $response = isset($response['response']) ? $response['response'] : [];
         return isset($response['tindakan_akomodasi']) ? $response['tindakan_akomodasi'] : [];
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			if($e->hasResponse()) {
				$response = $e->getResponse();
				return $response->getBody();
			}
		}
   }

   private function setUrl()
	{
		$header = [
			'Authorization' => $this->token,
			'user-agent' => 'cli',
			'X-Owner' => $this->xOwner,
		];
		$client =  new Client([
			'base_uri' => "http://localhost:8858/ranap/v1/",
			'headers' => $header
		]);

		return $client;
	}
}