<?php
namespace Extensions\kasir;

use Yii;
use GuzzleHttp\Exception\RequestException;

class PerbandinganHargaFormKramat extends \Doco\processes\PerbandinganHargaFormProcess
{
    protected $dokTercetak = 'index-kramat';
}