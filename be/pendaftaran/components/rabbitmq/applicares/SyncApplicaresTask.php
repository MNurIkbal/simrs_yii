<?php

namespace app\components\rabbitmq\applicares;

use app\modules\v1\models\KamarTempatTidur;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\models\bpjs\BpjsAplicare;
use Doco\rabbitmq\task\IntegrasiTask;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use mikemadisonweb\rabbitmq\components\ConsumerInterface;

class SyncApplicaresTask extends IntegrasiTask
{
    /**
     * Main Execute 
     * 
     * @author Maulana Muhammad Rizky (maulana.rizky@sirs.co.id)
     * 
     * 23 November 2023
     */

    public $data;

    public function prosesSync()
    {
        $tempatTidur = $this->data;
        if (empty($tempatTidur)) {
            return;
        }

        $maxAttempts = 4;
        $api = new BpjsAplicare();

        foreach ($tempatTidur as $key => $value) {
            $kamarId = ArrayHelper::getValue($value, 'kamarruangan_id');
            $finalResponse = null;

            $finalResponse = $this->_executeWithRetries(function() use ($api, $kamarId) {
                return $api->createOrUpdateAplicare($kamarId, DocoConstants::TYPE_UPDATE_APLICARE);
            }, $maxAttempts);

            $responseCode = $this->_getResponseCode($finalResponse);

            if ($responseCode === 0) {
                $finalResponse = $this->_executeWithRetries(function() use ($api, $kamarId) {
                    return $api->createOrUpdateAplicare($kamarId, DocoConstants::TYPE_CREATE_APLICARE);
                }, $maxAttempts);
            }

            Yii::error(json_encode([
                'service' => 'Sirs-SyncKamarApplicares',
                'response' => $finalResponse,
                'kamarruangan_id' => $kamarId,
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
