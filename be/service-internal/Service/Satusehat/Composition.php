<?php

namespace Integrasi\Service\Satusehat;

use Yii;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Service\Satusehat\Components\SatuSehatPayload;

class Composition extends \Integrasi\Service\Satusehat\Encounter {

    public function execute()
    {
        $state = $this->state;
        $this->pendaftaranId = $this->attributes['result']['data']['pendaftaran_id']; 
        $this->logType = 'Composition-Diet';

        if ( $this->pendaftaranId ) {
            $payload = (new SatuSehatPayload)->generatePayload([
                            'COMPOSITION-GENERAL-PAYLOAD',
                        ], [
                            'pendaftaran_id' => $this->pendaftaranId
                        ]);
            $resData = (new SatusehatService)->createComposition($payload, false);
            $this->setLogs($this->pendaftaranId, $state, $payload, $resData);
        }

        return json_encode([
            'service' => 'Satusehat-Composition',
            'state' => $state,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}