<?php

namespace Doco\Libraries\Asuransi\Client\Mcare\Payload;

use Doco\Libraries\Asuransi\Collection;

class ListItemPayload extends Collection
{
    public function toArray()
    {
        return [
            "no_claim" => $this->no_claim
        ];
    }
}
