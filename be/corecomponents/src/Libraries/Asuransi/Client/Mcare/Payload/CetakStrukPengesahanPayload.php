<?php

namespace Doco\Libraries\Asuransi\Client\Mcare\Payload;

use Doco\Libraries\Asuransi\Collection;

class CetakStrukPengesahanPayload extends Collection
{
    public function toArray()
    {
        return [
            "no_klaim" => $this->no_klaim,
        ];
    }
}   