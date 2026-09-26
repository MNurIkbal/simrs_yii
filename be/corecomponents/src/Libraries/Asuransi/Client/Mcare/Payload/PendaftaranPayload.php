<?php

namespace Doco\Libraries\Asuransi\Client\Mcare\Payload;

use Doco\Libraries\Asuransi\Collection;

class PendaftaranPayload extends Collection
{
    public function toArray()
    {
        return [
            "service" => "5",
            "transaction_id" => $this->transaction_id, // digunakan ketika pasien daftar dari mqare
            "tanggalmasuk" => $this->tanggalmasuk,
            "nokartu" => $this->nokartu,
            "kodebenefit" => $this->kodebenefit,
            "statusrujukan" => isset($this->statusrujukan) ? $this->statusrujukan : "N",
            "asalrujukan" => $this->asalrujukan,
            "cobbpjs" => $this->cobbpjs, // jika pasien COB YA = 1 , tidak = 0
            "nomorsep" => $this->nomorsep, //jika pasien COB wajib di isi nomor
            "keterangan" => $this->keterangan,
            "notransaksiprovider" => $this->notransaksiprovider,
            "inacbgscode" => $this->inacbgscode, //jika pasien COB wajib di isi dan isi kan kode inacbgscode yang di dapat dari INACBGS
            "inacbgsamount" => $this->inacbgsamount //jika pasien COB wajib di isi dan isi kan amount berapa yang di cover oleh bpjs
        ];
    }
}