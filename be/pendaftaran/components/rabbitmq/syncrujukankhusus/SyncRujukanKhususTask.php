<?php

namespace app\components\rabbitmq\syncrujukankhusus;

use app\modules\v1\models\Cron;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use Doco\rabbitmq\task\IntegrasiTask;
use Doco\models\bpjs\BpjsRujukanKhususT;

class SyncRujukanKhususTask extends IntegrasiTask
{
    /**
     * Main Execute 
     * 
     * @author Yafi (yafi.maulana@sirs.co.id)
     * 
     * 22 April 2025
     */
    public function prosesSync()
    {
        $this->getRujukanKhusus();
    }

    protected function getRujukanKhusus()
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

        $result = $guzzle->post('allow-bpjs/list-rujukan-khusus-with-range', [
            'form_params' => [
                'start_date' => $this->start_date,
                'max_month_backdate' => $this->max_month_backdate,
                'is_sync' => true
            ]
        ]);

        $result = json_decode($result->getBody(), true);

        return $result;
    }
}
