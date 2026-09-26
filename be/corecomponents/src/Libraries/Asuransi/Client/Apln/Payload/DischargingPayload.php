<?php

namespace Doco\Libraries\Asuransi\Client\Apln\Payload;

use Doco\Libraries\Asuransi\Collection;

class DischargingPayload extends Collection
{
    public function toArray()
    {
        return [
            "noklaim" => $this->noklaim,
            "tanggalkeluar" => $this->tanggalkeluar,
            "kodepoli" => $this->kodepoli,
            "statusresep" => $this->statusresep ? $this->statusresep : "N",
            "statusrujukan" => $this->statusrujukan ? $this->statusrujukan : "N",
            "rujukanke" => $this->rujukanke,
            "izinsakit" => $this->izinsakit,
            "kodediagnosa" => $this->kodediagnosa,
            "inacbgscode" => $this->inacbgscode,
            "inacbgsamount" => $this->inacbgsamount,
            "daftarbiaya" => $this->daftarbiaya
        ];
    }
}
