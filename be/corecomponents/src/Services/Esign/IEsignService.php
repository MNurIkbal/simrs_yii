<?php

namespace Doco\Services\Esign;

use Doco\components\DocoConstansId;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Yii;

interface IEsignService
{
	public static function register($options);
    public static function saveDoc($filename, $stream);
    public static function signing($listDokumenSign, $signer);
}
