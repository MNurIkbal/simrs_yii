<?php

namespace Integrasi\Service\MobileMhg;

use Yii;
use Integrasi\Service\MobileMhg\Models\DaftarTindakan as Model;
use Integrasi\Service\MobileMhg\Models\TindakanIntegrasi;
use Integrasi\Components\DocoConstansId;
use Integrasi\Components\Services\MasterTindakanMobileMhgService;
use LDAP\Result;

class Tindakan extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $id = $this->id;
        $lastInsert = $this->last_insert;
        $state = $this->state;

        $qTindakan = Model::find(true);

        $listState = true;
        if ($lastInsert) {
            $qTindakan->orderBy(['created_date' => SORT_DESC])->limit(1);
        } else if (!empty($id)) {
            $qTindakan->andWhere([
                'daftartindakan_id' => $id
            ]);
        } else {
            $listState = false;
        }

        $result = [];
        
        if(!empty($listState)){
            $resultCateg = $qTindakan->asArray()->one();
            $payload = [
                'tindakan_id' => $id,
            ];
            
            if(!empty($resultCateg)) {
                $resultCateg['admin_charge'] = ($resultCateg['kelompoktindakan_id'] == (new DocoConstansId)->actionGetId('kelompok_karcis')) ? true : false;
                $daftartindakan_id = !empty($resultCateg['daftartindakan_id']) ? $resultCateg['daftartindakan_id'] : '';
                $daftartindakan_nama = !empty($resultCateg['daftartindakan_nama']) ? $resultCateg['daftartindakan_nama'] : '';
                $daftartindakan_code = !empty($resultCateg['daftartindakan_kode']) ? $resultCateg['daftartindakan_kode'] : '';
                $admin_charge =  $resultCateg['admin_charge'];
                $isActive = !empty($resultCateg['is_active']) ? $resultCateg['is_active'] : false;

                $payload = [
                    'daftartindakan_id' => $daftartindakan_id,
                    'is_active' => $isActive,
                    'daftartindakan_nama' => $daftartindakan_nama,
                    'daftartindakan_code' => $daftartindakan_code,
                    'admin_charge' => $admin_charge
                ];
                
                switch ($state) {
                    case "create":
                        $result = $this->createTindakanMobileMhg($payload);
                        break;
                    case "update":
                        $result = $this->updateTindakanMobileMhg($payload);
                        break;
                    case "delete":
                        $result = $this->deleteTindakanMobileMhg($payload); 
                        break;
                    default:
                        throw new \Exception("State : $state Tidak termasuk diantara create, update dan delete", 1);
                        break;
                }

                $this->setLog($result, $state, $payload);
            }
        }
        return $this->setRespose($payload, $result);
    }

    public function setLog($result, $state, $payload)
    {
        $userIdentity = $this->user_identity;
        $uidSercon = isset($result['uid']) ? $result['uid'] : null;
        $response = isset($result['response']) ? $result['response'] : [];
        $isSent = isset($response['is_error']) ? $response['is_error'] : true;
        $tindakan_id = $payload['daftartindakan_id'];
        
        $logData[] = [
            'tindakan_id' => $tindakan_id,
            'tindakan_kode' => $payload['daftartindakan_code'],
            'status' => $payload['is_active'],
            'admin_charge' => $payload['admin_charge'],
            'sync_response' => isset($response) ? json_encode($response) : null,
            'is_error' => $isSent,
            'state' => $state,
            'created_date' => date('Y-m-d H:i:s'),
            'created_by' => isset($userIdentity['uid']) ? $userIdentity['uid'] : null,
            'id_sync_sercon' => $uidSercon,
            'tindakan_nama' => $payload['daftartindakan_nama'],
        ];

        TindakanIntegrasi::batchInsert($logData);
    }

    public function setRespose($payload, $result)
    {
        return json_encode([
            'service' => 'MobileMhg-Tindakan',
            'payload' => $payload,
            'timestamp' => date('Y-m-d H:i:s'),
            'result' => $result
        ]);
    }

    public function tindakanIntegrasiById($id)
    {
        return TindakanIntegrasi::find()->where(['tindakan_id' => $id])->one();
    }

    private function createTindakanMobileMhg($payload)
    {
        return (new MasterTindakanMobileMhgService)->createTindakanMobileMhg($payload);
    }

    private function deleteTindakanMobileMhg($payload)
    {
        return (new MasterTindakanMobileMhgService)->deleteTindakanMobileMhg($payload);
    }

    private function updateTindakanMobileMhg($payload)
    {
        return (new MasterTindakanMobileMhgService)->updateTindakanMobileMhg($payload);
    }
}