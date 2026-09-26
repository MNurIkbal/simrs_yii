<?php

/**
 * @author : Sulthan Zaidan Fauzi (sulthanzaidan1026@gmail.com
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\Services;

use Yii;
use Doco\Services\BaseService;

class UpdateEklaimService extends BaseService
{
    public function __construct()
    {
        $this->service = Yii::$app->docoRest->penjaminasuransi;
    }

    public function updateTglPulang($payload)
    {
    	$url_api = 'single-sync/update-tgl-pulang';
    	return $this->post($url_api, [
    	    'form_params' => $payload,
    	    'success' => function($data, $statusCode) use ($payload, $url_api) {
    	        \Yii::error(
    	            'Message : API UPDATE TANGGAL PULANG EKLAIM BERHASIL --||--Line : NULL --||--File : UpdateEklaimService.php --||--API URL : '.$url_api.' --||--Method : POST--||--Payload : ' . json_encode($payload) . ' --||-- Response : ' . json_encode($data),
    	            'server-error'
    	        );
                return [
                    'code' => $statusCode,
                    'data' => $data
                ];
    	    },
    	    'failed' => function($data, $statusCode) use ($payload, $url_api) {
    	        \Yii::error(
    	            'Message : API UPDATE TANGGAL PULANG EKLAIM BERHASIL --||--Line : NULL --||--File : UpdateEklaimService.php --||--API URL : '.$url_api.' --||--Method : POST--||--Payload : ' . json_encode($payload) . ' --||-- Response : ' . json_encode($data),
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