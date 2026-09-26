<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;
use Integrasi\Service\Sirs\BussinesLogic\TagihanHelper;

class DataBelumBayar extends \Integrasi\Contracts\DocoImplement
{
    
    public function execute()
    {
        $cacheFiles = Yii::$app->cacheFiles;
		
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Menyiapkan data.',
				 'progress' => 10
			 ]),
        ]);
        $attributes = $this->getDataAttibutes($this->filter);
		
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Menyiapkan data.',
				 'progress' => 30
			 ]),
        ]);
		$tmpData = [];
		$persentageAdmin = 30; // persentasi awal ketika get admin
		$persenLength = 40; // panjang persentasi selama get admin
		$dataLength = count($attributes);
		$tmpVar = $persenLength/$dataLength;

		foreach($attributes as $key => $val){
			$percentage = ($key+1)*$tmpVar;
			$percentage += $persentageAdmin;
			if($persentageAdmin > 70){
				return json_encode([
					$dataLength, $percentage, $key+1, $tmpVar, $persentageAdmin
				]);
			}
			Yii::$app->redis->executeCommand('PUBLISH', [
				'channel' => 'export-excel:'.$this->unique_str,
				'message' => json_encode([
					 'status' => 'finish', 
					 'messageProcess' => 'Menghitung biaya admin.',
					 'progress' => round($percentage)
				 ]),
			]);
			$pendaftaranId = isset($val['pendaftaran_id']) ? $val['pendaftaran_id'] : null;
			$primary = DocoHelpers::encrypt($pendaftaranId);
			$penjaminId = isset($val['penjamin_id']) ? $val['penjamin_id'] : null;
			$kelasPelayananId = isset($val['kelaspelayanan_id']) ? $val['kelaspelayanan_id'] : null;
			$totalTagihan = !empty($val['total_tagihan']) ? $val['total_tagihan'] : 0;
			$pasienAdmisiId = isset($val['pasienadmisi_id']) ? $val['pasienadmisi_id'] : null;
			$sisaTagihan = !empty($val['sisa_tagihan']) ? $val['sisa_tagihan'] : 0;
			$uangMuka = !empty($val['uang_muka']) ? $val['uang_muka'] : 0;

			$admin =  TagihanHelper::getBiayaAdmin($pendaftaranId, $penjaminId, $kelasPelayananId, $totalTagihan, $pasienAdmisiId);

			$totalTagihan += $admin;
			$sisaTagihan = ($sisaTagihan + $admin) - $uangMuka;
			$attributes[$key]['total_tagihan'] = $totalTagihan;
			$attributes[$key]['sisa_tagihan'] = $sisaTagihan;
		}

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

    private function getDataAttibutes($params)
    {
        $client = $this->setUrl();
        try {
			$response = $client->get((isset($this->params['getDataUrl']) ? $this->params['getDataUrl'] : ''), [
				'query' => $params,
			]);
            $response = json_decode($response->getBody(), true);
			$response = !empty($response['response']) ? $response['response'] : [];
			return $response;
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
			'base_uri' => isset($this->params['base_uri']) ? $this->params['base_uri'] : '',
			'headers' => $header
		]);

		return $client;
	}
}