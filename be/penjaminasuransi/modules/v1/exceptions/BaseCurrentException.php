<?php

namespace app\modules\v1\exceptions;

use Exception;

class BaseCurrentException extends Exception
{
    public function __construct($message, $code = 1)
    {
        parent::__construct($message, $code);
    }
}
