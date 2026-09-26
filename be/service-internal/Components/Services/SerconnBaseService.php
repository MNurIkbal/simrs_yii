<?php

namespace Integrasi\Components\Services;

use Yii;

class SerconnBaseService
{
    protected $url;
    protected $baseUrl;
    protected $curl;

    /**
     * initialize curl
     *
     * @return object
     * @author Tsani N (tsani@docotel.com)
     **/
    protected function initCurl($url = null, $param = null, $method = 'GET', $option)
    {
        if (is_null($url)) {
            throw new \Exception("Url must be set, Please check your param", 1);
        } else {
            $this->curl = curl_init();
            $headers = [
                'Content-Type:application/json',
            ];
            if (isset($option['is_blocking']) && $option['is_blocking']) {
                array_push($headers, 'X-Behaviour:blocking');
            }
            curl_setopt_array($this->curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POSTFIELDS => $method !== 'GET' ? json_encode($param) : "",
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_HTTPHEADER => $headers,
            ]);
            $this->setUrl($url, $method === 'GET' ? $param : null);
        }
    }

    /**
     * set url
     *
     * @author Tsani N (tsani@docotel.com)
     **/
    protected function setUrl($url, $param)
    {
        $this->baseUrl = Yii::$app->params['serconn']['uri_api'] . '/on';
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

    /**
     * execute API RSPAD
     *
     * @return array
     * @author Tsani N (tsani@docotel.com)
     **/
    protected function executeApi($url, $param = null, $method = 'GET')
    {
        $option = [];
        if (is_array($method)) {
            $option = $method;
        } else {
            $option['method'] = $method;
        }
        $this->initCurl($url, $param, isset($option['method']) ? $option['method'] : 'GET', $option);
        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);
        curl_close($this->curl);
        if (!$err) {
            $result = json_decode($response, true);
            Yii::error([
                "REPSONSE" => $result,
                "URL" => $this->url,
                "PAYLOAD" => json_encode($param)
            ]);
            $data = isset($result['Results']['data']) ? $result['Results']['data'] : (isset($result['Results'][0]) && isset($result['Results'][0]['data']) ? $result['Results'][0]['data'] : []);
            if (isset($option['callbackSuccess']) && is_callable($option['callbackSuccess'])) {
                $response = call_user_func($option['callbackSuccess'], $result);
                if (!empty($response)) {
                    $data = $response;
                }
            }
            return $data;
        } else if (isset($option['catchError']) && $option['catchError']) {
            throw new \Exception($err, 1);
        } else {
            Yii::error([
                "ERROR INTEGRATION" => $err
            ]);
            return [];
        }
    }
}
