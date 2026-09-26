<?php

namespace Doco\Libraries\Asuransi\Client\Apln\Payload;

use Doco\Libraries\Asuransi\Collection;

class CetakStrukPendaftaranPayload extends Collection
{
    public function toArray()
    {
        return [
            "noklaim" => $this->no_klaim,
        ];
    }
}   