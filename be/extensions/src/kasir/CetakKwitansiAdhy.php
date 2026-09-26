<?php

/**
 * @author : Budi (budi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Extensions\kasir;

use Yii;
use GuzzleHttp\Exception\RequestException;

class CetakKwitansiAdhy extends \Doco\processes\CetakKwitansiProcess
{
    protected $dokTercetak = 'kwitansi-adhy';
	
}