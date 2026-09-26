<?php

namespace Doco\Libraries\Asuransi;

use Doco\Libraries\Asuransi\Models\KonfigAsuransi;
use Doco\Libraries\Asuransi\Models\Penjamin;

class AsuransiClient
{
    public $providerType;

    public $provider;

    public $kodeProvider;

    public function getProvider()
    {
        if (isset($this->provider[$this->kodeProvider])) {
            return new $this->provider[$this->kodeProvider];
        }
        return false;
    }

    public function setProvider($penjaminId)
    {
        $penjamin = new Penjamin();
        $result = $penjamin->getProviderCode($penjaminId);

        if (empty($result)) {
            return $this;
        }

        /**
         * Set configuration provider
         */
        $result['provider_code'] = ucfirst(strtolower($result['provider_code']));
        $this->kodeProvider = $result['provider_code'];
        AsuransiObject::getInstance()->setAttributes($result);

        return $this->getProvider();
    }

    public function checkConnection($data)
    {
        $this->kodeProvider = $data['provider'];
        $configPayload = [
            'provider_code' => $data['provider'],
            'base_url' => $data['base_url'],
            'auth' => $data['auth'],
        ];
        
        AsuransiObject::getInstance()->setAttributes($configPayload);
        return $this->getProvider();
    }

    public function __call($name, $arguments) {}
}
