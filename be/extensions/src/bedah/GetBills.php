<?php

/**
 * @author : Budi
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Extensions\bedah;

use Yii;
use Doco\exceptions\ValidationException;

class GetBills extends \Doco\processes\GetBillsProcess
{
    protected function processFlow() 
  	{
    	return $this->getData();
  	}
}
