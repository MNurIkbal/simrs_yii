<?php

namespace Doco\Libraries\Asuransi\Client\Mcare\Payload;

use Doco\Libraries\Asuransi\Collection;

class ReferensiPesertaPayload extends Collection
{
    public function toArray()
    {
        return [
            'nik' => $this->nik,
            'birth_date' => $this->birth_date
        ];
    }
}
