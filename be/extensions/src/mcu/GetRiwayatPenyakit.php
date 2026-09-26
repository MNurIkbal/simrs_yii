<?php

/**
 * @author : Budi
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Extensions\mcu;

use Yii;
use Doco\exceptions\ValidationException;

class GetRiwayatPenyakit extends \Doco\processes\GetDataMcuProcess
{
    protected function processFlow() 
  	{
    	return $this->getData();
  	}
}