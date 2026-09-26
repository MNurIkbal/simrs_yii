<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Citra Raya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\AccEntry;

use Yii;
use yii\base\Action;

class ExtractConsoleApiAction extends Action {

    public $components = '';
    public $usingDate = false;
    public $paramDate = '';
    private $sync_type;

    public function run()
    {
        $params = Yii::$app->params['iniFile'];
        $baseConfig = isset($params['authentication']) ? $params['authentication'] : [];

        $docoRest = Yii::$app->docoRest->dcms;

        $response = $docoRest->post('auth/get-token',[
           'form_params' => [
              'username' => $baseConfig['username'],
              'password' => $baseConfig['password']
           ]
        ]);
        $response = json_decode($response->getBody(), true);
        $token = isset($response['response']['access_token']) ? $response['response']['access_token'] : null;
        $docoRest = Yii::$app->docoRest->master;
        $bearer = 'Bearer '.$token;
        $action = $this->controller->action->id;
        $responseCron = $docoRest->get('acc-entry/'.$action, [
            'headers' => [
                'Authorization' => $bearer,
                'XOwner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ'
            ],
            'query' => [
                'date' => !empty($this->paramDate) ? $this->paramDate : null
            ]
        ]);
        $responseCron = json_decode($responseCron->getBody(), true);
    }
}