<?php

namespace Integrasi\Components\Services;

use Yii;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class IntegrationBaseService
{

    public $keyConfig;

    public $baseUrl;

    protected $guzzle;

    protected $tokenIdentity;

    protected $baseConfig;

    public function __construct()
    {
        $params = Yii::$app->params['iniFile'];
        $this->baseConfig = isset($params[$this->keyConfig]) ? $params[$this->keyConfig] : [];
        $this->baseUrl = $this->getAttribute('url');
        $this->guzzle = new Client([
            'base_uri' => $this->baseUrl,
            'verify' => false,
            'headers' => [
                'user-agent' => 'cli',
            ]
        ]);
    }

    protected function getAttribute($attr)
    {
        return isset($this->baseConfig[$attr]) ? $this->baseConfig[$attr] : '';
    }

    public function post($url, $payload = [], $options = [])
    {
        $request = $this->guzzle->post($url,$payload, $options);
        return $this->getResponse($request);
    }

    private function getResponse(ResponseInterface $response = null)
    {
        $response = json_decode($response->getBody(), true);
        return $response;
    }

}