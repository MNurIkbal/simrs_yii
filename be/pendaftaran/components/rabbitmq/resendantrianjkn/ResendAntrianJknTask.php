<?php

namespace app\components\rabbitmq\resendantrianjkn;

use app\modules\v1\models\Cron;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use Doco\rabbitmq\task\IntegrasiTask;

class ResendAntrianJknTask extends IntegrasiTask
{
    /**
     * Main Execute 
     * 
     * @author Yafi (yafi.maulana@sirs.co.id)
     * 
     * 29 Oktober 2024
     */
    public function prosesSync()
    {
        $this->createAntrianJkn();
    }



    /**
     * Function untuk resend Create Antrian JKN ke pendaftaran api
     * 
     * @return JSON 
     */
    private function createAntrianJkn()
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
        $result = $guzzle->post('api/create-antrian-jkn', [
            'form_params' => [
                'payload' => $this->payload,
                'pendaftaran_id' => $this->pendaftaran_id,
                'pendaftaranol_id' => $this->pendaftaranol_id,
                'is_logged' => $this->is_logged,
            ]
        ]);
        
        $result = json_decode($result->getBody(), true);
        Yii::error(json_encode($result));
    }
}
