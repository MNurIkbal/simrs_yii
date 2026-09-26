<?php

namespace Doco\processes;

class ImplementationButtonProcess extends \Doco\components\DocoBaseProcessExtension
{
    protected function processFlow()
    {
        // Default config, set disable button to false
        return false;
    }
}
