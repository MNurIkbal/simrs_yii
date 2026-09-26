<?php

namespace app\modules\v1\Exceptions\PaketFisio;

use Exception;

class UniqueNamaException extends BaseCurrentException
{
    public function __construct()
    {
        parent::__construct("Nama Tindakan Harus Bersifat Unik", 1);
    }
}
