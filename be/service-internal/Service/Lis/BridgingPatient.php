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
use Integrasi\Service\Roche\Models\IntegrasiPasienRoche;

class BridgingPatient extends \Integrasi\Contracts\DocoImplement
{
    protected $keyConfig = 'lisintegration';
    
    public function execute()
    {   
        $params = Yii::$app->params['iniFile'];
        $baseConfig = isset($params[$this->keyConfig]) ? $params[$this->keyConfig] : [];
        $regisId = $this->pendaftaran_id;
        $noRekamedik = $this->no_rekamedik;
        $namaPemakai = $this->nama_pemakai;
        if($regisId == '') {
            $regisId = null;
        }
        $integrasi = GenerateDataPatientLabRepositories::getData(null, null, null, $noRekamedik); 
        $payload = [];
        $response = [];
        $insertLog = [];

        if(isset($integrasi[0])) {
            $gender = ArrayHelper::getValue($integrasi[0], 'gender');
            if($gender == 'F') $gender = 'P';
            if($gender == 'M') $gender = 'L';
            $telp = ArrayHelper::getValue($integrasi[0], 'no_telepon_pasien');
            $address = ArrayHelper::getValue($integrasi[0], 'address');
            $payload = [
               "pasien" => [
                    "msh" => [
                        "product" => "SOFTMEDIX LIS",
                        "version" => "ws.001",
                        "user_id" => $baseConfig['username'],
                        "key" => $baseConfig['key']
                    ],
                    "pid" => [
                        'pmrn' => ArrayHelper::getValue($integrasi[0], 'no_rekam_medik'),
                        'pname' => ArrayHelper::getValue($integrasi[0], 'nama_pasien'),
                        'sex' => $gender,
                        'birth_dt' => date('d.m.Y', strtotime(ArrayHelper::getValue($integrasi[0], 'date_of_birth'))),
                        'address' => $address != null ? $address : '-',
                        'no_tlp' => $telp != null ? $telp : '-'
                    ],
                    "obr" => [
                        "order_control" => "UP",
                        "user_id" => $namaPemakai
                    ]
                ]
            ];
        }

        if(! empty($payload)) {
            /**
             * Lakukan pengiriman data
             */
            $response = (new LisService)->updatePatient($payload, function($data) {
                return $data;
            });
            $id_sync_sercon = ArrayHelper::getValue($response, 'ProcessUID');
            
            $insertLog[] = [
                'pasien_id' => ArrayHelper::getValue($integrasi[0], 'pasien_id'),
                'payload' => json_encode($payload),
                'is_sent' => true,
                'is_sending' => true,
                'id_sync_sercon' => $id_sync_sercon,
                'sync_respon' => json_encode([
                    'pendaftaran_id' => $regisId,
                    'response' => $response
                ]),
            ]; 
        }

        if(! empty($insertLog)) {
            IntegrasiPasienRoche::batchInsert($insertLog);
        }

        return json_encode([
            'service' => 'Lis-BridgingPatient',
            'attributes' => $this->attributes,
            'response' => $response,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);  
    }
}