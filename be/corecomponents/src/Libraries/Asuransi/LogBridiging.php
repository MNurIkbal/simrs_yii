<?php

namespace Doco\Libraries\Asuransi;

use Doco\Libraries\Asuransi\Models\IntegrasiVendorAsuransiR;

/**
 * @author: [Maulana Muhammad Rizky]
 * A product of PT. Sirs
 * Powered by Sirs
 */

class LogBridiging {
    
    public $response;

    public $waktumulai;
  
    public $waktuselesai;
  
    public $payload;
  
    public $url;

    public $rawresponse;

    public $statuscode;

    public $state;
    
    public $provider;
  
    private static $instance = null;
  
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new LogBridiging();
        }
        return self::$instance;
    }

    public function saveLogBridging()
    {
        $model = new IntegrasiVendorAsuransiR();
        $model->waktumulai = $this->waktumulai;
        $model->waktuselesai = $this->waktuselesai;
        $model->payload = json_encode($this->payload);
        $model->url = $this->url;
        $model->response = json_encode($this->response);
        $model->raw_response = json_encode($this->rawresponse);
        $model->status_code = isset($this->statuscode) ? $this->statuscode : null;
        $model->state = $this->state;
        $model->provider = $this->provider;
        $model->created_at = date('Y-m-d H:i:s');
        $model->save();
    }
}