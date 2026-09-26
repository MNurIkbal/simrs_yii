<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\CaraBayar;
use Integrasi\Service\Sirs\Models\Lookup;
use yii\helpers\ArrayHelper;
use Integrasi\Service\Sirs\Models\InfoResepView;
use GuzzleHttp\Client;
use Integrasi\Components\DocoRestActiveFilter;

class ExecuteSerahkanObatReseptur extends \Integrasi\Contracts\DocoImplement
{
    public function execute() {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage;
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;
        $row = $footer = [];
        $no = 1;

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang memproses Serahkan Obat',
                'progress' => 80,
                'update_result' => NULL
            ]),
        ]);

        for($i = 0; $i < $totalPerPage; $i++) {
            $data = $cacheFiles->get($this->unique_str . '-' . $i);

            if(!empty($data)) {
                foreach ($data as $key => $value) {
                    $row[] = $value;
                }
            }

            $cacheFiles->delete($this->unique_str . '-' . $i);
        }

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mendapatkan status',
                'progress' => 85,
                'update_result' => NULL
            ])
        ]);

        $hasilUpdateResep = $this->prosesUpdateResep($filter);

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses update status resep berhasil',
                'progress' => 100,
                'update_result' => $hasilUpdateResep
            ]),
        ]);

        return json_encode([
            'service' => 'Sirs-ExportLaporanTarifPenunjang',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'update_result' => $hasilUpdateResep
        ]);
    }

    private function prosesUpdateResep($request) {
        $client = $this->setUrl();

        $data = $this->dataUpdateResep($request)->asArray()->all();

        $dataResponse = [];
        foreach ($data as $key => $value) {
            $nomor = $value['nomor'];
            $ruangan_id = $value['ruangan_id'];
            $instalasiasal_id = $value['instalasi_reseptur_id'];

            try {
                $dataResponse[] = [
                    'nomor_resep' => $nomor,
                    'is_success' => 1,
                    'message' =>'Resep Nomor ' .  $nomor . ' Berhasil di Proses',
                    'status_update' => $this->parsingResponse($nomor, $this->apiUpdateResep($client, $value))
                ];
            } catch(\Exception $e) {
                $dataResponse[] = [
                    'nomor_resep' => $nomor,
                    'is_success' => 0,
                    'message' => 'Resep Nomor ' .  $nomor . ' Gagal di Proses',
                    'status_update' => $e->getMessage()
                ];
            }
        }

        return $dataResponse;
    }

    private function dataUpdateResep($request) {
    	$nomorResep = $request[0]['nomor'];

    	$arr_noresep = explode(',', $nomorResep);

    	$date = date('Y-m-d');
    	$model = new InfoResepView;
    	$query = $model::find()
    	    ->where(['no_resep' => $arr_noresep])
    	    ->orWhere(['no_reseptur' => $arr_noresep]);
    	
    	return DocoRestActiveFilter::advancedFilter($model, $query, $request[0]);
    }

    private function apiUpdateResep($client, $value) {
        $nomor = $value['nomor'];
        $ruangan_id = $value['ruangan_id'];
        $instalasiasal_id = $value['instalasi_reseptur_id'];

        $response = $this->guzzleExec($client, [
            'url' => 'inf-reseptur/serahkan-obat',
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'nomor' => $nomor,
                    'ruangan_id' => $ruangan_id,
                    'instalasiasal_id' => $instalasiasal_id
                ]
            ]
        ]);

        return $response;
    }

    private function parsingResponse($nomor, $value) {
        $response['msg'] = $value['message'];
        $response['error_code'] = $value['meta']['code'];
        return $response;
    }
    
    private function setUrl() {
    	$header = [
    		'Authorization' => $this->token,
    		'user-agent' => 'cli',
    		'X-Owner' => $this->xOwner
    	];

    	$client = new Client([
    		'base_uri' => 'http://localhost:8858/apotek/v1/',
    		'headers' => $header
    	]);

    	return $client;
    }
}