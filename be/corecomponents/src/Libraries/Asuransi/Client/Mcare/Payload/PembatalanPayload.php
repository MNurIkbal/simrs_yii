<?php

namespace Doco\Libraries\Asuransi\Client\Mcare\Payload;

use Doco\Libraries\Asuransi\Collection;

class PembatalanPayload extends Collection
{
    public function toArray()
    {
        return [
            "service" => 7,
            "noklaim" => $this->noklaim,
            "keterangan" => $this->keterangan
        ];
    }
}
