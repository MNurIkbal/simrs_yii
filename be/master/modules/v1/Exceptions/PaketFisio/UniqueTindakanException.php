<?php

namespace app\modules\v1\Exceptions\PaketFisio;

class UniqueTindakanException extends BaseCurrentException
{
    public function __construct()
    {
        parent::__construct("nama / kode paket sudah digunakan", 1);
    }
}
