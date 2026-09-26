<?php

namespace Doco\Libraries\Asuransi\Client\Mcare\Payload;

use Doco\Libraries\Asuransi\Collection;

class CekEligiblePayload extends Collection
{
    public function toArray()
    {
        return [
            'service' => 1,
            "nokartu" => $this->no_kartu
        ];
    }
}
