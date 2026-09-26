<?php

namespace app\modules\v1\Exceptions\PaketFisio;

class PayloadException extends BaseCurrentException
{
    public function __construct()
    {
        parent::__construct("payload tidak sesuai", 1);
    }
}
