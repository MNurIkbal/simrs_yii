<?php

namespace Doco\components;

use Yii;
use GuzzleHttp\Client;
use yii\base\Component;

class DocoSerconn extends Component
{
    public $uri_api;
    public $curl;
    public $test;
    private $client = null;

    public function __construct()
    {
        $this->client = new Client();
        $this->curl = $this->guzzle();
    }

    public function guzzle($behaviour = 'blocking')
    {
        return self::_guzzle($behaviour);
    }

    protected function _guzzle($behaviour)
    {
        $header = [
            "X-Behaviour" => $behaviour,
            "Content-Type" => 'application/json',
        ];

        return new Client([
            'base_uri' => $this->uri_api,
            'headers' => $header
        ]);
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($name, $value)
    {

    }
}