<?php

namespace app\modules\v1\Exceptions\PaketFisio;

use Exception;

class UniqueJenisPemeriksaanFisioException extends BaseCurrentException
{
    public function __construct()
    {
        parent::__construct("Nama / Kode Tindakan Harus Bersifat Unik", 1);
    }
}
