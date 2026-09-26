<?php

namespace app\modules\v1\Exceptions\PaketFisio;

class GuardedNamaPaketFisioException extends BaseCurrentException
{
    public function __construct()
    {
        parent::__construct("nama paket tidak bisa diubah", 1);
    }
}
