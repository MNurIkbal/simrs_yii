<?php

namespace Doco\Libraries\Asuransi\Client\Mcare\Payload;

use Doco\Libraries\Asuransi\Collection;

class CetakStrukPendaftaranPayload extends Collection
{
    public function toArray()
    {
        return [
            "service" => 10,
            "noklaim" => $this->no_klaim,
        ];
    }
}   