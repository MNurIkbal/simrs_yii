<?php

namespace Doco\Libraries\Asuransi\Client\Mcare\Payload;

use Doco\Libraries\Asuransi\Collection;

class ItemRequestPayload extends Collection
{
    public function toArray()
    {
        return [
            "no_claim" => $this->no_claim,
            "provider_code" => $this->provider_code,
            "icd" => $this->icd,
            "items" => $this->items_data
        ];
    }
}
