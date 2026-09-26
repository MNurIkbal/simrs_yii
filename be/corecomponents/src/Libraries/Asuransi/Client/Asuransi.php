<?php

namespace Doco\Libraries\Asuransi\Client;

use Doco\Libraries\Asuransi\AsuransiObject;
use Doco\Libraries\Asuransi\LogBridiging;
use yii\helpers\ArrayHelper;

/**
 * Business logic ini untuk melakukan handle flow dari RSUD Sinjai.
 *
 * @author Maulana Muhammad Rizky
 */
class Asuransi
{
    public $log;

    public $config;

    public $directory;
    public function __construct()
    {
        $this->log = LogBridiging::getInstance();

        $this->config = AsuransiObject::getInstance();

        $this->log->waktumulai = date('Y-m-d H:i:s');

        $this->log->provider = $this->config->provider_id;
 
        $this->directory = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
    }

    /**
     * Sets the log data for the current instance.
     *
     * @param array $data An array containing the log data.
     * @throws Exception If an error occurs while saving the log.
     * @return void
     */
    public function setLogs($data)
    {
        $this->log->payload = ArrayHelper::getValue($data, 'payload', []);
        $this->log->response = ArrayHelper::getValue($data, 'response', []);
        $this->log->rawresponse = ArrayHelper::getValue($data, 'rawresponse', []);
        $this->log->waktuselesai = date('Y-m-d H:i:s');
        $this->log->url = $this->config->base_url . $data['path'];
        $this->log->state = ArrayHelper::getValue($data, 'state');
        $this->log->statuscode = ArrayHelper::getValue($data, 'status');

        $saveLog = LogBridiging::getInstance();
        $saveLog->saveLogBridging();
    }
}