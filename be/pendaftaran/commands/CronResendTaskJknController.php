<?php

namespace app\commands;

use app\modules\v1\controllers\SinkronisasiController as Sinkron;
use Doco\components\DocoActiveController;
use Yii;
use yii\console\Controller;
use Doco\rabbitmq\RabbitBgProcess;

class CronResendTaskJknController extends Controller
{
    protected $keyConfig = 'authentication';

    public function actionIndex()
    {
        Yii::error(json_encode([
            "message" => "CRON MULAI BERJALAN"
        ]));
                
        $params = Yii::$app->params['iniFile'];
        $baseConfig = isset($params[$this->keyConfig]) ? $params[$this->keyConfig] : [];

        $docoRest = Yii::$app->docoRest->dcms;

        $response = $docoRest->post('auth/get-token',[
           'form_params' => [
              'username' => $baseConfig['username'],
              'password' => $baseConfig['password']
           ]
        ]);
        $response = json_decode($response->getBody(), true);
        $token = isset($response['response']['access_token']) ? $response['response']['access_token'] : null;


        $docoRest = Yii::$app->docoRest->pendaftaran;
        $bearer = 'Bearer '.$token;
        $responseCron = $docoRest->post('sync-bpjs/resend-task-antrean-multiple', [
            'headers' => [
                'Authorization' => $bearer,
                'XOwner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ'
            ],
            'form_params' => [
                'is_get_kodebooking_task' => true
            ]
        ]);
        
        $responseCron = json_decode($responseCron->getBody(), true);

        Yii::error(json_encode([
            'return' => $responseCron,
            "message" => "CRON SUDAH BERJALAN"
        ]));
    }
}
