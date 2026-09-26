<?php

namespace Doco\Libraries\Asuransi\Client\Mcare\Payload;

use Doco\Libraries\Asuransi\Collection;

class ReferensiBenefitPayload extends Collection
{
    public function toArray()
    {
        return [
            'service' => 2,
            'nokartu' => $this->no_kartu
        ];
    }
}
