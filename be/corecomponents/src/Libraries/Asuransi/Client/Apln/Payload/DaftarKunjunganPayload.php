<?php

namespace Doco\Libraries\Asuransi\Client\Apln\Payload;

use Doco\Libraries\Asuransi\Collection;

class DaftarKunjunganPayload extends Collection
{
    public function toArray()
    {
        return [
            "bulanlayanan" => date('mY', strtotime($this->bulanlayanan)),
            "kodeperusahaan" => $this->kodeperusahaan,
            "kodeklien" => $this->kodeklien,
        ];
    }
}