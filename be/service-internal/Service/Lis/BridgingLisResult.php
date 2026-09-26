<?php

namespace Integrasi\Service\Lis;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use yii\db\Query;

use Integrasi\Components\Repositories\GenerateDataPatientLabRepositories;

use Integrasi\Components\Services\LisService;
use Integrasi\Components\Object\LisObject;
use Integrasi\Service\Roche\Models\IntegrasiRoche;
use Integrasi\Service\Roche\Models\BridgingOrderLabRocheView;

use phpDocumentor\Reflection\Types\Array_;
use GuzzleHttp\Client;

class BridgingLisResult extends \Integrasi\Contracts\DocoImplement
{   
    protected $keyConfig = 'lisintegration';

    public function execute()
    {
        $regisId = (int) $this->pendaftaran_id;
        $penunjangId = (int) $this->pasienmasukpenunjang_id;
        $noOrderLab = $this->no_masukpenunjang;
        $responseResult = [];
        $payloadForm = [];
        $insertLog = null;
        
        if(!empty($penunjangId) || !empty($regisId) ) {
            try {
                $params = Yii::$app->params['iniFile'];
                $baseConfig = isset($params[$this->keyConfig]) ? $params[$this->keyConfig] : [];

                $integrasi = GenerateDataPatientLabRepositories::getData($penunjangId, $regisId, null,null);
                $dataPatient = isset($integrasi[0]) ? $integrasi[0] : null;
                $sendData = [];
                $payload = [
                    'no_order_lab' => $dataPatient['order_no'],
                    "user_id" => $baseConfig['username'],
                    "key" => $baseConfig['key'],
                    "version" => isset($baseConfig['version']) ? explode('.',$baseConfig['version'])[1] : ''
                ];
                $response = (new LisService)->resultLis($payload, function ($data) {
                    return $data;
                }, 'POST');
                $resData = ArrayHelper::getValue($response, 'Results.0.data.Data');
                $result = ArrayHelper::getValue($response, 'Results.0.data.0.response');
                $result = json_decode($result, true)['result'];
                $id_sync_sercon = $prosesId = isset($response['ProcessUID']) ? $response['ProcessUID'] : null;
                $resPatient = $result['obx'];
                $resResult = isset($result['obx']['result_test']) ? $result['obx']['result_test'] : [];
                /* 
                * checking send for already result test
                */
                if (!empty($result) && !empty($resResult)) {                    
                    $payloadForm = $this->dataPayload($resResult, $resPatient, $dataPatient);
                    $responseResult = $this->sendResultLab($payloadForm);
                    $insertLog[] = [
                        'pendaftaran_id' => ArrayHelper::getValue($dataPatient, 'pendaftaran_id'),
                        'pasienmasukpenunjang_id' => $penunjangId,
                        'pasienkirimkeunitlain_id' => ArrayHelper::getValue($dataPatient, 'pasienkirimkeunitlain_id'),
                        'payload' => json_encode($payloadForm),
                        'is_sent' => true,
                        'is_sending' => true,
                        'id_sync_sercon' => $id_sync_sercon,
                        'sync_respon' => json_encode($response),
                    ];

                }
            } catch (\Exception $e) {
                $response = [
                    'Message' => $e->getMessage(),
                    'File' => $e->getFile(),
                    'Line' => $e->getLine(),
                ];
            }
            
        }

        if (!empty($insertLog)) {
            IntegrasiRoche::batchInsert($insertLog);
        }

        return json_encode([
            'service' => 'Lis-BridgingLisResult',
            'payload' => $payloadForm,
            'response' => $responseResult,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);      
    }

    private function dataPayload($resultIntegration, $dataIntegration, $dataPatient){
        $resData = [];
        $no = 0;
        foreach ($resultIntegration as $key => $value) {
            $no++;
            $nilaiExplode = explode( ' - ' ,$value['nilai_normal']);
            $referenceRange1 = isset($nilaiExplode[0])? $nilaiExplode[0]:'-';
            $referenceRange2 = isset($nilaiExplode[1])? $nilaiExplode[1]: null;
            $resData[] =[
                'obv_id'=> $dataPatient['order_no'].$no,
                'obv_name'=> isset($value['nama_test']) ? $value['nama_test'] :'-',
                'value'=> isset($value['hasil']) ? $value['hasil'] :'-',
                'unit_text'=> isset($value['unit_text']) ? $value['unit_text'] :'-',
                'ref_range_1'=> isset($$referenceRange1) ? $referenceRange1 :'-',
                'ref_range_2'=> !empty($referenceRange2) ? $referenceRange2 : isset($value['nilai_normal']) ? $value['nilai_normal'] : '-',
                'abnormal_flag'=> isset($value['flag'])? $value['flag'] : '-',
                'observer'=> isset($value['location_name']) ? $value['location_name'] :'-',
                'set_id'=> '-',
                'value_type'=> '-',
                'obv_status'=> '-',
                'obv_time'=> date('Y-m-d H:i:s'),
                'method'=> '-',
           ];
        }

        return  [
            'logid' => '-',
            'ts' => '-',
            'key' => 'patient.result_test',
            'transaction_time'=> isset($dataPatient['order_time']) ? $dataPatient['order_time'] : date('Y-m-d H:i:s'),
            'data' => [
                '_id'=> '-',
                'Ts'=> isset($dataIntegration['lis_sample']) ? $dataIntegration['lis_sample'] : '-',
                'ReqId'=> '-',
                'Key'=> 'patient.result_test',
                'logid' => '-',
                'Data' =>[
                    'logid' => '-',
                    'log'=> isset($dataIntegration['log']) ? $dataPatient['log'] : '-',
                    'patient_id'=> isset($dataPatient['patient_id']) ? $dataPatient['patient_id'] : '-',
                    'patient_name'=> isset($dataPatient['nama_pasien']) ? $dataPatient['nama_pasien'] : '-',
                    'date_of_birth'=> isset($dataPatient['date_of_birth']) ? $dataPatient['date_of_birth'].' 00:00:00': date('Y-m-d'.' 00:00:00'),
                    'gender'=> isset($dataPatient['gender']) ? $dataPatient['gender'] : '-',
                    'address'=> isset($dataPatient['address']) ? $dataPatient['address'] : '-',
                    'patient_class'=> isset($dataPatient['patient_class']) ? $dataPatient['patient_class'] : '-',
                    'case_no'=> isset($dataPatient['case_no']) ? $dataPatient['case_no'] : '-', 
                    'order_ctrl'=> isset($dataPatient['order_ctrl']) ? $dataPatient['order_ctrl'] : '-', 
                    'order_no'=> isset($dataPatient['order_no']) ? $dataPatient['order_no'] : '-',
                    'placer_order_no'=> isset($dataPatient['umur']) ? $dataPatient['umur'] : '-', 
                    'order_status'=> isset($dataPatient['order_status']) ? $dataPatient['order_status'] : '-', 
                    'transaction_time'=> isset($dataPatient['order_time']) ? $dataPatient['order_time'] : date('Y-m-d H:i:s'),
                    'result_time'=> $dataIntegration['acc_date'] ? date('Y-m-d H:i:s',strtotime($dataIntegration['acc_date'])) : date('Y-m-d H:i:s'),
                    'result_status'=> isset($dataPatient['result_status']) ? $dataPatient['result_status'] : '-',
                    'priority'=> isset($dataPatient['priority']) ? $dataPatient['priority'] : '-',
                    'specimen_type'=> isset($dataPatient['specimen_type']) ? $dataPatient['specimen_type'] : '-',
                    'specimen_name'=> isset($dataPatient['specimen_name']) ? $dataPatient['specimen_name'] : '-',
                    'specimen_collection_time'=> isset($dataPatient['specimen_collection_time']) ? $dataPatient['specimen_collection_time'] : date('Y-m-d H:i:s'),
                    'results' => $resData
                ]
            ]
        ];
    }

    private function sendResultLab($payload)
    {
        $client = $this->setUrl();
        $docoRest = Yii::$app->docoRest;
        $docoRest->setToken($this->token);
        $docoRest->setOwner($this->owner);
        try {
            $response = $docoRest->hisRoche->post('api-roche/result',[
                'form_params' => $payload
            ]);
            $response = json_decode($response->getBody(), true);
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
			'base_uri' => "http://localhost:8858/laboratorium/integrator/",
			'headers' => $header
		]);

		return $client;
	}
    
}