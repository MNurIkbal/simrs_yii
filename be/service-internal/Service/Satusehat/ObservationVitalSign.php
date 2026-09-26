<?php

namespace Integrasi\Service\Satusehat;

use Yii;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Service\Satusehat\Components\SatuSehatPayload;

class ObservationVitalSign extends \Integrasi\Service\Satusehat\Encounter {

    public function execute()
    {
        $state = $this->state;
        $this->pendaftaranId = $this->attributes['result']['data']['pendaftaran_id']; 
        $vitalPayload = $this->attributes['result']['data']['vital-sign'];

        $resData = [];
        if ( $this->pendaftaranId ) {
            foreach( $vitalPayload as $vitalItem => $vitalValue) {
                if ( $vitalValue != 0 || !empty($vitalValue) ) {
                    $payload = $this->build($vitalItem);
                    $resData[] = array_merge((new SatusehatService)->createObservation($payload, false), [
                        'logType' => 'Observation-VitalSign-'.$vitalItem,
                        'payload' => $payload
                    ]);
                }
            }

            if ( $resData) {
                $this->setLogs($this->pendaftaranId, $state, $payload, $resData);
            }
        }

        return json_encode([
            'service' => 'Satusehat-Observation',
            'state' => $state,
            'timestamp' => date('Y-m-d H:i:s'),
            'resData' => $resData
        ]);
    }

    public function setLogs($pendaftaranId, $state, $payload, $result, $additionalId = null)
    {
        try {
            $userIdentity = $this->user_identity;
            $logData = [];
            foreach( $result as $key => $resItem) {
                $logData[] = [
                    'pendaftaran_id' => $pendaftaranId,
                    'is_sent' => true,
                    'type' => $resItem['logType'],
                    'state' => $state,
                    'id_sync_sercon' => isset($resItem['uid']) ? $resItem['uid'] : null,
                    'created_date' => date('Y-m-d H:i:s'),
                    'created_by' => isset($userIdentity['uid']) ? $userIdentity['uid'] : null,
                    'payload' => json_encode($resItem['payload']),
                    'is_deleted' => false,
                    'is_active' => true,
                    'additional_id' => $additionalId
                ];
            }
        } catch ( \Exception $e) {
            return json_encode([
                'message' => $e->getMessage(),
                'file' => 'setLogs'
            ]);
        }

        $this->saveLogs($logData);
    }

    protected function build( $vitalItem = '')
    {
        return (new SatuSehatPayload)->generatePayload([
            'GENERAL-VITAL-SIGN',
            strtoupper($vitalItem).'-VITAL-SIGN'
        ], [
            'pendaftaran_id' => $this->pendaftaranId
        ]);
    }
}