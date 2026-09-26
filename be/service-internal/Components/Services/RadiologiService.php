<?php

namespace Integrasi\Components\Services;

use Yii;

class RadiologiService extends SerconnBaseService
{
    /**
     * POST registration UTD to LAB
     *
     * @return Array
     * @author Tsani N (tsani@docotel.com)
     **/
    public function orderRadiologi($payload, $callbackFunc = null, $method = 'POST')
    {
        return $this->executeApi('/ris/order', $payload, [
            'method' => $method,
            'callbackSuccess' => $callbackFunc,
        ]);
    }

     /**
     * POST registration UTD to LAB
     *
     * @return Array
     * @author Tsani N (tsani@docotel.com)
     **/
    public function inputOrderRadiologi($payload, $callbackFunc = null, $method = 'POST')
    {
        return $this->executeApi('/ris/inputorder', $payload, [
            'method' => $method,
            'callbackSuccess' => $callbackFunc,
            'is_blocking' => true,
        ]);
    }

    /**
     * POST registration UTD to LAB
     *
     * @return Array
     * @author Tsani N (tsani@docotel.com)
     **/
    public function openViewerRadiologi($payload, $callbackFunc = null, $method = 'POST')
    {
        return $this->executeApi('/ris/openviewer', $payload, [
            'method' => $method,
            'callbackSuccess' => $callbackFunc,
            'is_blocking' => true,
        ]);
    }

    protected function setUrl($url, $param)
    {
        $this->baseUrl = Yii::$app->params['serconn_ris']['uri_api'] . '/on';
        if (!is_null($param)) {
            $this->url = $this->baseUrl . $url . '?';
            $index = 0;
            foreach ($param as $key => $value) {
                $this->url = $this->url . $key . '=' . $value . (($index + 1 < count($param)) ? "&" : "");
                $index++;
            }
        } else {
            $this->url = $this->baseUrl  . $url;
        }
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
    }
}
