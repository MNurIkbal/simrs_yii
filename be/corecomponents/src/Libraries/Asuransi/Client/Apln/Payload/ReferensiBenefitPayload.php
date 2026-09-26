<?php

namespace Doco\Libraries\Asuransi\Client\Apln\Payload;

use Doco\Libraries\Asuransi\Collection;

class ReferensiBenefitPayload extends Collection
{
    public function toArray()
    {
        return [
            "nokartu" => $this->no_kartu
        ];
    }
}   