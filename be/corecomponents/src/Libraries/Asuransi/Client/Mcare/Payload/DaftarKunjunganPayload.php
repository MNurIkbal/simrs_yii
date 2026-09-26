<?php

namespace Doco\Libraries\Asuransi\Client\Mcare\Payload;

use Doco\Libraries\Asuransi\Collection;

class DaftarKunjunganPayload extends Collection
{
    public function toArray()
    {
        return [
            "service" => 14,
            "bulanlayanan" => date('mY', strtotime($this->bulanlayanan)),
        ];
    }
}
