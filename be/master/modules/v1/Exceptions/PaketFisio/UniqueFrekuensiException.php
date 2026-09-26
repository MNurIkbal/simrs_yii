<?php

namespace app\modules\v1\Exceptions\PaketFisio;

use Exception;

class UniqueFrekuensiException extends BaseCurrentException
{
    public function __construct()
    {
        parent::__construct("Frekuensi tidak boleh lebih kecil dari 1 dan tidak boleh lebih besar dari 10.", 1);
    }
}
