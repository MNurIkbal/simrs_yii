<?php

namespace Doco\processes;

use Yii;

class PerbandinganHargaFormProcess extends \Doco\components\DocoBaseProcessExtension
{
    protected $dokTercetak = 'index';

    protected function processFlow()
    {   
        return $this->dokTercetak;
    }
}