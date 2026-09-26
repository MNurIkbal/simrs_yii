<?php 

namespace Integrasi\Service\Sirs\Igd\LapPasienIgd;

use Integrasi\Components\DocoRestActiveFilter;

use Integrasi\Service\Sirs\Models\LapPasienIgdView;
use Yii;
use GuzzleHttp\Client;
use Integrasi\Components\DocoConstants;

class Pdf extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        ini_set('memory_limit', '-1'); 
        ini_set('max_execution_time', '600');
        ini_set("pcre.backtrack_limit", "500000000");
        
        $cacheFiles = Yii::$app->cacheFiles;
        $attributes = $this->getData();

        $cacheFiles->set($this->unique_str, $attributes);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-pdf:'.$this->unique_str,
            'message' => json_encode(['unique_process' => $this->unique_str]),
        ]);
        return json_encode([
            'service' => 'Sirs-LapPasienIgd',
            'payload' => $attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getData()
    {
        try {
            $data = $this->data()->asArray()->all();
            
            $params =  [
                'model' => $data,
                'filter' => $this->filter,
            ];

            $datatable = $this->renderAttributes($params);

            $result = [
                'attributes' => $datatable,
            ];

			return $result;
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			if($e->hasResponse()) {
				$response = $e->getResponse();
				return $response->getBody();
			}
		}
    }

    private function renderAttributes($params)
    {
        $client = $this->setUrl();
        try {
			$response = $client->post('lap-pasien-igd/render-attributes', [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode($params)
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

    public function setUrl()
	{
		$header = [
			'Authorization' => $this->token,
			'user-agent' => 'cli',
			'X-Owner' => $this->xOwner,
		];
		$client =  new Client([
			'base_uri' => isset($this->params['base_uri']) ? $this->params['base_uri'] : 'http://localhost:8858/igd/v1/',
			'headers' => $header
		]);

		return $client;   
	}

    private function data()
    {
        $request = $this->filter;
        $model = new LapPasienIgdView;
        $query = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if (isset($request['advanced-filter'])) {
            if (isset($request['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $request['advanced-filter']['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($request['advanced-filter']['tgl_pendaftaran']);
            }

            if (isset($request['dokter_id'])) {
                $dokter_id = $request['dokter_id'];
                $query->andWhere('(dokter_id = ' . $dokter_id. '
                    OR dokter_jaga_id = ' . $dokter_id. ')');

                unset($request['advanced-filter']['dokter_id']); // Unset Advanced Filter pegawai id / dokter id
            }

            if (isset($request['advanced-filter']['no_rekam_medik'])) {
                $no_rekam_medik = $request['advanced-filter']['no_rekam_medik'];
                $query->andWhere(['no_rekam_medik' => $no_rekam_medik]);
                unset($request['advanced-filter']['no_rekam_medik']);
            }
            
            if (isset($request['advanced-filter']['no_pendaftaran'])) {
                $no_pendaftaran = $request['advanced-filter']['no_pendaftaran'];
                $query->andWhere(['no_pendaftaran' => $no_pendaftaran]);
                unset($request['advanced-filter']['no_pendaftaran']);
            }

            if (isset($request['advanced-filter']['status_periksa'])) {
                $listStatus = explode(",", $request['advanced-filter']['status_periksa']);
                $query->andWhere(['IN', 'status_periksa', array_filter($listStatus)]);
                unset($request['advanced-filter']['status_periksa']);
            }else{
                $query->andWhere(['<>', 'status_periksa', DocoConstants::STATUS_PERIKSA_BTL_KUNJ]);
            }
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query->orderby('tgl_pendaftaran ASC');

        return $query;
    }
}
