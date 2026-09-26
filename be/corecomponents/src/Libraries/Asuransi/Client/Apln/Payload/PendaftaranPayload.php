<?php

namespace Doco\Libraries\Asuransi\Client\Apln\Payload;

use Doco\Libraries\Asuransi\Collection;

class PendaftaranPayload extends Collection
{
    public function toArray()
    {
        return [
            "tanggalmasuk" => date('Y-m-d', strtotime($this->tanggalmasuk)),
            "nokartu" => $this->nokartu,
            "kodebenefit" => $this->kodebenefit,
            "asalrujukan" => $this->asalrujukan,
            "nomorsep" => $this->nomorsep,
            "keterangan" => $this->keterangan,
            "notransaksiprovider" => $this->notransaksiprovider,
            "inacbgscode" => $this->inacbgscode,
            "inacbgsamount" => $this->inacbgsamount,
        ];
    }
}