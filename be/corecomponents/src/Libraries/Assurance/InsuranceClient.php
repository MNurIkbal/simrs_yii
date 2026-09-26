<?php

namespace Doco\Libraries\Assurance;

class InsuranceClient
{
    public $providerType;
    
    public $provider;

    public function __construct()
    {
        $this->provider = null;
    }

}