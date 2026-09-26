<?php

namespace Doco\Libraries\Asuransi\Client\Mcare\Payload;

use Doco\Libraries\Asuransi\Collection;

class CetakSuratJaminanPayload extends Collection
{
    public function toArray()
    {
        return [
            "service" => 12,
            "nosuratjaminan" => $this->nosuratjaminan,
        ];
    }
}
