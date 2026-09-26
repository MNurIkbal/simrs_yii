<?php

namespace Doco\Services;

use Doco\Services\BaseService;
use Yii;
use Doco\components\DocoConstants;

class AkomodasiService extends BaseService
{
    public function __construct()
    {
        $this->service = Yii::$app->docoRest->ranap;
    }
    
    /**
     * Function for handle akomodasi sementara
     * 
     * @param Array payload
     * @return JSON
     * @author : aris munandar (aris.munandar@docotel.co.id)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function getAkomodasiSementara($payload)
    {
        return $this->get('api/get-akomodasi-sementara', [
            'query' => $payload,
            'success' => function ($data) use ($payload) {
                \Yii::error(
                    'Message : API AKOMODASI SEMENTARA SUKSES--||--Line : NULL --||--File : AkomodasiService.php --||--API URL : /api/akomodasi-sementara--||--Method : GET--||--Payload : ' . json_encode($payload),
                    'server-error'
                );
            },
            'failed' => function ($data) use ($payload) {
                \Yii::error(
                    'Message : API AKOMODASI SEMENTARA GAGAL--||--Line : NULL --||--File : AkomodasiService.php --||--API URL : /api/akomodasi-sementara--||--Method : GET--||--Payload : ' . json_encode($payload),
                    'server-error'
                );
            }
        ]);
    }
    
}
