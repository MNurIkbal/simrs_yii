<?php

namespace Doco\Libraries\Asuransi\Client\Mcare\Payload;

use Doco\Libraries\Asuransi\Collection;

class DischargingPayload extends Collection
{
    public function toArray()
    {
        return [
            "service" => 6,
            "noklaim" => $this->noklaim,
            "tanggalkeluar" => $this->tanggalkeluar, //yyyy-mm-dd
            "kodepoli" => $this->kodepoli,
            "statusrujukan" => isset($this->statusrujukan) ? $this->statusrujukan : "N",
            "statusresep" => isset($this->statusresep) ? $this->statusresep : "N",
            "izinsakit" => $this->izinsakit,
            "kodediagnosa" => $this->kodediagnosa, //jika lebih dari 1 diagnosa contohnya "A01,A02,A03"
            "inacbgscode" => $this->inacbgscode,
            "inacbgsamount" => $this->inacbgsamount
        ];
    }
}