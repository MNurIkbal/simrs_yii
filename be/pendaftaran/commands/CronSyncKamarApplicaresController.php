<?php

namespace app\commands;

use app\modules\v1\models\KamarTempatTidur;
use Doco\components\DocoConstants;
use Doco\models\bpjs\BpjsAplicare;
use Yii;
use yii\console\Controller;

class CronSyncKamarApplicaresController extends Controller
{
    protected $keyConfig = 'authentication';
    public $chunk_size;

    public function options($actionID)
    {
        return ['chunk_size'];
    }
    
    public function optionAliases()
    {
        return ['c' => 'chunk_size'];
    }

    public function actionIndex()
    {
        Yii::error(json_encode([
            "message" => "CRON SYNC KAMAR APPLICARES MULAI BERJALAN"
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
        $responseCron = $docoRest->get('sync-kamar-applicares/sync-kamar', [
            'headers' => [
                'Authorization' => $bearer,
                'XOwner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ'
            ],
            'query' => [
                'chunk' => $this->chunk_size,
            ],

        ]);
        $responseCron = json_decode($responseCron->getBody(), true);

        Yii::error(json_encode([
            'return' => $responseCron,
            "message" => "CRON SYNC KAMAR APPLICARES SUDAH BERJALAN"
        ]));

    }
}
