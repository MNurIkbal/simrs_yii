<?php

namespace app\components\rabbitmq\resendtask;

use app\modules\v1\models\Cron;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use Doco\rabbitmq\task\IntegrasiTask;

class ResendTaskJknTask extends IntegrasiTask
{
    /**
     * Main Execute 
     * 
     * @author Yafi (yafi.maulana@sirs.co.id)
     * 
     * 08 Desember 2023
     */
    public function prosesSync()
    {
        $type = $this->type_sinkron;
        $kodebooking = $this->kodebooking;

        if (strtolower($type) == "resend-all") {
            $result = self::resendTaskJknMultiple($kodebooking);
        } else if (strtolower($type) == "resend-single") {
            $result = self::resendTaskJkn($kodebooking);
        }

    }

    protected function resendTaskJknMultiple()
    {
        $result = null;
        if (is_array($this->kodebooking)) {
            foreach ($this->kodebooking as $key => $value) {
                $response = $this->syncResendTaskJkn($value);
            }
        } else { 
            $response = $this->syncResendTaskJkn($this->kodebooking);
        
        } 
        $result   = isset($response['response']['list']) ? $response['response']['list'] : [];

        return $result;
    }

    /**
     * Function untuk resend task jkn ke pendaftaran api
     * 
     * @return JSON 
     */
    public function syncResendTaskJkn($kodebooking)
    {
        $params = Yii::$app->params;
        $urlBackend = isset($params['url_backend']) ? $params['url_backend'] : 'http://web:8858/';
        $guzzle = new Client([
          'base_uri' => $urlBackend . 'pendaftaran/v1/',
          'verify' => false,
          'headers' => [
            'user-agent' => 'cli',
            'Authorization' => $this->token,
            'X-Owner' =>  $this->xOwner,
          ]
        ]);
        
        $result = $guzzle->post('sync-bpjs/resend-task-antrean', [
            'form_params' => [
                'kodebooking' => $kodebooking
            ]
        ]);
        
        $result = json_decode($result->getBody(), true);
        Yii::error(json_encode($result));
    }

    protected function publishMessage($progressBar, $message, $state = 'finish')
    {
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'sync-eklaim:' . $this->unique_str,
            'message' => json_encode([
                'status' => $state,
                'messageProcess' => $message,
                'hide' => true,
                'progress' => $progressBar,
            ]),
        ]);
    }
}
