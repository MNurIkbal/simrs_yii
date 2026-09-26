<?php

namespace Integrasi\Components\Services;

class LisService extends SerconnBaseService
{
    /*Post*/ 
    public function orderLis($payload, $callbackFunc = null, $method = 'POST')
    {
        return $this->executeApi('/bridginglis/order', $payload, [
            'method' => $method,
            'callbackSuccess' => $callbackFunc,
            'is_blocking' => true
        ]);
    }

    /*Delete*/ 
    public function cancelOrder($payload, $callbackFunc = null, $method = 'POST')
    {
        return $this->executeApi('/bridginglis/cancelorder', $payload, [
            'method' => $method,
            'callbackSuccess' => $callbackFunc,
            'is_blocking' => true
        ]);
    }
    /*Update*/ 
    public function updatePatient($payload, $callbackFunc = null, $method = 'PATCH')
    {
        return $this->executeApi('/bridginglis/updatepatient', $payload, [
            'method' => $method,
            'callbackSuccess' => $callbackFunc,
            'is_blocking' => true
        ]);
    }

    /*result*/ 
    public function resultLis($payload, $callbackFunc = null, $method = 'POST')
    {
        return $this->executeApi('/bridginglis/result', $payload, [
            'method' => $method,
            'callbackSuccess' => $callbackFunc,
            'is_blocking' => true
        ]);
    }
}
