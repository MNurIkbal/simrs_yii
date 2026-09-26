<?php

namespace Integrasi\Components\Services;

class RocheService extends SerconnBaseService
{
    /**
     * POST registration UTD to LAB
     *
     * @return Array
     * @author Tsani N (tsani@docotel.com)
     **/
    public function order($payload, $callbackFunc = null, $method = 'POST')
    {
        return $this->executeApi('/lis/order', $payload, [
            'method' => $method,
            'callbackSuccess' => $callbackFunc,
            'is_blocking' => true
        ]);
    }

    public function cancelOrder($payload, $callbackFunc = null, $method = 'DELETE')
    {
        return $this->executeApi('/lis/order', $payload, [
            'method' => $method,
            'callbackSuccess' => $callbackFunc,
            'is_blocking' => true
        ]);
    }

    public function updatePatient($payload, $callbackFunc = null, $method = 'PATCH')
    {
        return $this->executeApi('/lis/patient', $payload, [
            'method' => $method,
            'callbackSuccess' => $callbackFunc,
            'is_blocking' => true
        ]);
    }
}
