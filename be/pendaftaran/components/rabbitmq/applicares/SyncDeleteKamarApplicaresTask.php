<?php

namespace app\components\rabbitmq\applicares;

use Doco\models\bpjs\BpjsAplicare;
use Doco\rabbitmq\task\IntegrasiTask;
use Yii;
use yii\helpers\ArrayHelper;

class SyncDeleteKamarApplicaresTask extends IntegrasiTask
{
    public $data;

    public function prosesSync()
    {
        $tempatTidur = $this->data;
        if (empty($tempatTidur)) {
            return;
        }

        $maxAttempts = 4; 
        $api = new BpjsAplicare(); 

        foreach ($tempatTidur as $key => $tempatTidur) {
            $finalResponse = $this->_executeWithRetries(function() use ($api, $tempatTidur) {
                return $api->deleteKamarNonAktifAplicare($tempatTidur);
            }, $maxAttempts);

            Yii::error(json_encode([
                'service' => 'Sirs-SyncDeleteKamarApplicares',
                'response' => $finalResponse,
                'kamarruangan_id' => ArrayHelper::getValue($tempatTidur, 'kamarruangan_id'),
                'timestamp' => date('Y-m-d H:i:s'),
            ]));
        }
    }

    private function _executeWithRetries(\Closure $action, $maxAttempts = 4)
    {
        $attempts = 0;
        $response = null;

        do {
            $attempts++;
            $response = $action(); 
            $code = $this->_getResponseCode($response);

            if ($code != 500) {
                break; 
            }

        } while ($attempts < $maxAttempts);

        return $response;
    }

    private function _getResponseCode($response)
    {
        if (isset($response['metaData']['code'])) {
            return (int) $response['metaData']['code'];
        }
        if (isset($response['metadata']['code'])) {
            return (int) $response['metadata']['code'];
        }
        return null; 
    }
}
