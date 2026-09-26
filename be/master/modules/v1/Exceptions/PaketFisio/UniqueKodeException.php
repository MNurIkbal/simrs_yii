<?php

namespace app\modules\v1\Exceptions\PaketFisio;

use Exception;

class UniqueKodeException extends BaseCurrentException
{
    public function __construct()
    {
        parent::__construct("Kode Tindakan Harus Bersifat Unik", 1);
    }
}
