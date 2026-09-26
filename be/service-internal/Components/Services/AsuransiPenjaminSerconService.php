<?php

namespace Integrasi\Components\Services;

class AsuransiPenjaminSerconService extends SerconnBaseService
{
    public function sendPenjamin($plugin, $payload, $callbackFunc = null, $blocking = true)
    {
        return $this->executeApi($plugin, $payload, [
            'method' => 'POST',
            'callbackSuccess' => $callbackFunc,
            'is_blocking' => $blocking
        ]);
    }
}
