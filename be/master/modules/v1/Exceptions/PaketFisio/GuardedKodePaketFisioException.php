<?php

namespace app\modules\v1\Exceptions\PaketFisio;

class GuardedKodePaketFisioException extends BaseCurrentException
{
    public function __construct()
    {
        parent::__construct("kode paket tidak bisa diubah", 1);
    }
}
