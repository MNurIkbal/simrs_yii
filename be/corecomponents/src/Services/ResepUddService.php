<?php

namespace Doco\Services;

use Yii;
use Doco\Services\BaseService;
use Doco\components\DocoHelpers;

class ResepUddService extends BaseService
{
    public function __construct()
    {
        $this->service = Yii::$app->docoRest->ranap;
    }

    public function saveResepUdd($payload, $header = [])
    {
    	Yii::error(json_encode(['payload' => $payload]));

    	$url_api = 'cppt/simpan-reseptur';
    	return $this->post($url_api, [
    	    'form_params' => $payload,
            'headers' => !empty($header) ? $header : [],
    	    'success' => function($data, $statusCode) use ($payload, $url_api) {
    	        \Yii::error(
    	            'Message : API SIMPAN RESEPTUR UDD BERHASIL --||--Line : NULL --||--File : ResepUddService.php --||--API URL : '.$url_api.' --||--Method : POST--||--Payload : ' . json_encode($payload) . ' --||-- Response : ' . json_encode($data),
    	            'server-error'
    	        );
                return [
                    'code' => $statusCode,
                    'data' => $data
                ];
    	    },
    	    'failed' => function($data, $statusCode) use ($payload, $url_api) {
    	        \Yii::error(
    	            'Message : API SIMPAN RESEPTUR UDD GAGAL --||--Line : NULL --||--File : ResepUddService.php --||--API URL : '.$url_api.' --||--Method : POST--||--Payload : ' . json_encode($payload) . ' --||-- Response : ' . json_encode($data),
    	            'server-error'
    	        );
                return [
                    'code' => $statusCode,
                    'data' => $data
                ];
    	    }
    	]);
    } 
}