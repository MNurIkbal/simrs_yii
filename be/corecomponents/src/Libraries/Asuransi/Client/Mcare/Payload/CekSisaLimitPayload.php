<?php

namespace Doco\Libraries\Asuransi\Client\Mcare\Payload;

use Doco\Libraries\Asuransi\Collection;

class CekSisaLimitPayload extends Collection
{
    public function toArray()
    {
        return [
            "email" => $this->payload['email'],
            "password" => $this->payload['password']
        ];
    }
}   