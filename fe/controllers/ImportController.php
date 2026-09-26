<?php

namespace app\controllers;

use Yii;
use GuzzleHttp\Client;
use app\components\DocoController;

class ImportController extends DocoController
{
    const TOKEN = 'token_jwt';

    protected $enabled;
    protected $base_uri;
    protected $server_key;

    public function init()
    {
        parent::init();
        $this->enabled = Yii::$app->params->import['enabled'];
        $this->base_uri = Yii::$app->params->import['address'];
        $this->server_key = Yii::$app->params->import['key'];
    }

    protected function getToken($level)
    {
        $cache = Yii::$app->cache;

        $token = $cache->get(self::TOKEN.'_'.$level);
        if($token === false) {
            $client = new Client(['base_uri'=>$this->base_uri]);
            $rest = $client->get('api/auth/createToken',[
                'query' => [
                    'server_key' => $this->server_key,
                    'level' => $level
                ]
            ]);
            $body = json_decode($rest->getBody());

            $token = $body->access_token;
            $cache->set(self::TOKEN.'_'.$level,$body->access_token,$body->expires_in / 60);
        }
        return $token;
    }

    protected function embedApp($src)
    {
        $html = "
        <iframe sandbox='allow-same-origin allow-scripts allow-presentation allow-popups allow-downloads' src=$src style='border:none; height: 600px; width: 100%;'></iframe>
        ";
        return $this->renderContent($html);
    }

    public function actionUser()
    {
        if($this->enabled == false){
            return $this->renderContent("<center><p>This feature is disabled</p></center>");
        }
        $token = $this->getToken('user');
        $src = $this->base_uri."/user?token=".$token;
        return $this->embedApp($src);
    }

    public function actionAdmin()
    {
        if($this->enabled == false){
            return $this->renderContent("<center><p>This feature is disabled</p></center>");
        }
        $token = $this->getToken('admin');
        $src = $this->base_uri."/admin?token=".$token;
        return $this->embedApp($src);
    }
}