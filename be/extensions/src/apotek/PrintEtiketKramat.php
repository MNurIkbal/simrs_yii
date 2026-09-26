<?php

/**
 * @author : Bambang Hermawan (bambang.hermawan@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Extensions\apotek;

use Yii;

class PrintEtiketKramat extends \Doco\processes\PrintEtiketProcess
{
    protected $_renderView = 'etiket-kramat';

    protected function processFlow()
    {
        $this->cetakEtiket();
    }
}
