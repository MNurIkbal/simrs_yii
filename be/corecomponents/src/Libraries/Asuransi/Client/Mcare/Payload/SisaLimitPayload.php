<?php

namespace Doco\Libraries\Asuransi\Client\Mcare\Payload;

use Doco\Libraries\Asuransi\Collection;

class SisaLimitPayload extends Collection
{
    public function toArray()
    {
        return [
            "service" => 4,
            "nokartu" => $this->nokartu,
            "kodebenefit" => $this->kodebenefit
        ];
    }
}
