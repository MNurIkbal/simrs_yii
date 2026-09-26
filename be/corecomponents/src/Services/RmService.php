<?php

namespace Doco\Services;

use Yii;
use Doco\Services\BaseService;

class RmService extends BaseService {

    public function __construct()
    {
        $this->service = Yii::$app->docoRest->rm;
    }
    /**
     * Function for handle integration of permintaan makan from unit
     * 
     * @param Array payload
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function periksa($payload)
    {
                    // Yii::error('orang 55');
        return $this->post('monitoring-dokumen/update-document-status', [
            'form_params' => $payload,
            'success' => function ($data) use ($payload) {
                return $data;
            },
            'failed' => function ($data) use ($payload) {
                return $data;
            }
        ]);
    }
}
