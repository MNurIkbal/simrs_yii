<?php

/**
 * @author 
 * ? Budi (budi@sirs.co.id)
 * ! Powered by Sirs
 */

namespace Extensions\kasir;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;

class CetakKwitansiMhbg extends \Doco\processes\CetakKwitansiProcess
{
	protected $dokTercetak = 'kwitansi-mhbg';
    protected $dokPath = 'kwitansi-mhbg';

    protected function processFlow()
  	{
    	$this->populateData();
        $this->getBuktiMasuk();
        $this->getHeader();
        $this->cetak();
  	}
}