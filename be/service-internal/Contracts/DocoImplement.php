<?php

namespace Integrasi\Contracts;

use GuzzleHttp\Client;
use Integrasi\Components\DocoHelpers;

class DocoImplement implements \Integrasi\Contracts\Task\Task
{

    public $docoRest;
    protected $attributes;
    protected $signature;

    public function __construct(array $data, array $config)
    {
        $this->attributes = $data;
        $this->signature = isset($config['params']['signature']) ? $config['params']['signature'] : null;

        $url = isset($config['params']['connector']) ? $config['params']['connector'] : null;
        $service = isset($config['params']['service']) ? $config['params']['service'] : null;

        $header = [
          'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $this->docoRest = new Client([
                'base_uri' => $url,
                'headers' => $header
        ]);
    }

    public function execute() 
    {
        return true;
    }

    /**
     * [__get Set request menjadi object]
     */
    public function __get($property)
    {
      $attr = $this->attributes;
      if (property_exists($this, $property)) {
        return $this->$property;
      } else {
        $value = isset($attr[$property]) ? $attr[$property] : '';
        return $this->$property = $value;
      }
    }

    public function __set($name, $value)
    {

    }

    public function guzzleExec($guzzleClass, $optionGuzzle, $payloadData = [], $withStatusCode = false)
    {
        return (new DocoHelpers)->guzzleExec($guzzleClass, $optionGuzzle, $payloadData, $withStatusCode);
    }
}