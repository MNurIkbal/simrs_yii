<?php

namespace app\commands;

use Doco\components\DocoActiveController;
use Yii;
use yii\console\Controller;
use Doco\rabbitmq\RabbitBgProcess;

class CronCreateAntrianJknController extends Controller
{
    protected $keyConfig = 'authentication';
    protected $params;
    protected $baseConfig;
    protected $docoRest;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->params = Yii::$app->params['iniFile'];
        $this->baseConfig = isset($this->params[$this->keyConfig]) ? $this->params[$this->keyConfig] : [];
        $this->docoRest = Yii::$app->docoRest;
    }

    public function actionIndex($tanggal = '')
    {
        Yii::error(json_encode([
            "message" => "CRON MULAI BERJALAN"
        ]));
                
        $bearer = 'Bearer '.$this->getToken();

        $responseCron = $this->docoRest->pendaftaran->post('sync-bpjs/resend-create-antrean-jkn', [
            'headers' => [
                'Authorization' => $bearer,
                'XOwner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ'
            ],
            'form_params' => [
                'tanggal' => $tanggal
            ]
        ]);
        
        $responseCron = json_decode($responseCron->getBody(), true);

        Yii::error(json_encode([
            'return' => $responseCron,
            "message" => "CRON SUDAH BERJALAN"
        ]));
    }

    private function getToken()
    {
        $response = $this->docoRest->dcms->post('auth/get-token',[
           'form_params' => [
              'username' => $this->baseConfig['username'],
              'password' => $this->baseConfig['password']
           ]
        ]);
        $response = json_decode($response->getBody(), true);
        return isset($response['response']['access_token']) ? $response['response']['access_token'] : null;
    }
}
