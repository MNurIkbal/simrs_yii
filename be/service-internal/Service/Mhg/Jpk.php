<?php

namespace Integrasi\Service\Mhg;

use Yii;
use GuzzleHttp\Client;

class Jpk extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
    	$header = [
                    'Authorization' => $this->authHeader,
                    'user-agent' => 'cli',
                    'X-Owner' => $this->authOwner
                ];
        

        $client =  new Client([
                'base_uri' => $this->url,
                'headers' => $header
                ]);
        try {
	        $req = $client->post('api/billing',[
	        	'form_params' => $this->payload
	        ]);
	        $fail = '';
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
        	$fail = $e->getResponse()->getBody()->getContents();
        }
    	return json_encode([
            'service' => 'JPK',
            'payload' => $this->payload,
            'fail' => $fail,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}