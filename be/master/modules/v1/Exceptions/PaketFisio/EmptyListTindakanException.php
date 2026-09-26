<?php

namespace app\modules\v1\Exceptions\PaketFisio;

use Exception;

class EmptyListTindakanException extends BaseCurrentException
{
    public function __construct()
    {
        parent::__construct("List Tindakan tidak boleh kosong", 1);
    }
}
