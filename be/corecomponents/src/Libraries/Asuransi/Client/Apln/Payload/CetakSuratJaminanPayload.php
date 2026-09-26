<?php

namespace Doco\Libraries\Asuransi\Client\Apln\Payload;

use Doco\Libraries\Asuransi\Collection;

class CetakSuratJaminanPayload extends Collection
{
    public function toArray()
    {
        return [
            "nosuratjaminan" => $this->nosuratjaminan,
        ];
    }
}   