<?php

namespace Doco\Libraries\Asuransi\Client\Apln\Payload;

use Doco\Libraries\Asuransi\Collection;

class PembatalanPayload extends Collection
{
    public function toArray()
    {
        return [
            "noklaim" => $this->noklaim,
            "keterangan" => $this->keterangan,
        ];
    }
}