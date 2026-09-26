<?php

namespace Integrasi\Components\Services;

class WynacomService extends SerconnBaseService
{
    public function sendWynacom($plugin, $payload, $callbackFunc = null, $blocking = true)
    {
        return $this->executeApi($plugin, $payload, [
            'method' => 'POST',
            'callbackSuccess' => $callbackFunc,
            'is_blocking' => $blocking
        ]);
    }
    
    protected function setUrl($url, $param)
    {
        $this->baseUrl = \Yii::$app->params['config-wynacom']['uri_api'];
        $this->url = $this->baseUrl  . $url;
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
    }
}
