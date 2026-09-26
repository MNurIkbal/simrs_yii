<?php

namespace Doco\Libraries\Asuransi\Client\Apln\Payload;

use Doco\Libraries\Asuransi\Collection;

class AuthPayload extends Collection
{
    public function toArray()
    {
        return [
            "username" => $this->payload['username'],
            "password" => $this->payload['password'],
            "kodeprovider" => $this->payload['kodeprovider']
        ];
    }
}   