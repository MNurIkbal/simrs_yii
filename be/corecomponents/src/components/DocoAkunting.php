<?php

namespace Doco\components;

use Yii;
use yii\base\Component;
use GuzzleHttp\Client;
use Doco\components\DocoHelpers;

class DocoAkunting
{
	public static function api($method, $url, array $url_param = [])
	{
		$url = "http://akundev.sirs.co.id:9000/api/" . $url;
		$email = 'admin@sirs.com';
		$password = 'dhealt123456';

		// $url_param = http_build_query($url_param);  
		// return $url_param;
		// die;  
		// var_dump($url_param);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						'Accept: application/x.test.v1+json',
						'Content-Type: application/json',
		                'authorization: Basic ' . base64_encode("$email:$password"),                    
		                'cache-control: no-cache',
		                'email: '.$email,
		                'password: '.$password,
		                ));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($url_param));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$result = curl_exec($ch);
		curl_close($ch);	

		return json_decode($result, true);
	}
	
}