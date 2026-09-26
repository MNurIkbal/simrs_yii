<?php

namespace Doco\Libraries\Asuransi\Client\Mcare\Payload;

use Doco\Libraries\Asuransi\Collection;

class DeleteItemRequestPayload extends Collection
{
    public function toArray()
    {
        return [
            "no_claim" => $this->no_claim,
            "item_code" => $this->item_code
        ];
    }
}
