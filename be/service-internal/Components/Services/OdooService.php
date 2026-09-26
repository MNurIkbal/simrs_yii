<?php

namespace Integrasi\Components\Services;

class OdooService extends SerconnBaseService
{
    public function sendOdoo($plugin, $payload, $callbackFunc = null, $blocking = true)
    {
        return $this->executeApi($plugin, $payload, [
            'method' => 'POST',
            'callbackSuccess' => $callbackFunc,
            'is_blocking' => $blocking
        ]);
    }

    public function hitOdoo($plugin,$payload,$callbackFunc = null)
    {
    	return $this->executeApi($plugin,$payload,[
    		'method' => 'GET',
            'callbackSuccess' => $callbackFunc,
            'is_blocking' => false
    	]);
    }
}
