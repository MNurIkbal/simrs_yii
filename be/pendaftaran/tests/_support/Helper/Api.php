<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module
{
    public $var;

    public  $error_response = [
        'metadata' => ['status' => \Codeception\Util\HttpCode::OK, 'message' => 'Sukses'],
        'response' => [],
    ];
    public $response = [];

    public function getConfig($key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        } else {
            return $this->config[$key];
        }
    }

    public function getResponse($response = [])
    {
        if(!empty($response))
        {
            $this->response = $response;
            var_dump($this->response);
            return $this->response;
        }
        var_dump($this->response);
        return $this->response;
    }

    public function validateApi($methode, $url)
    {
        $this->validateParamNull($methode);
        $this->validateParamNull($url);

        $this->validateParamString($methode);
        $this->validateParamString($url);

        $this->validateMethodeNotValid($methode);
        $this->validateUrlNotValid($url);

        return $this->getResponse($this->error_response);
    }

    private function validateParamNull($params){
        
        if ($params == null) {
            $this->error_response = [
                'metadata' => ['status' => \Codeception\Util\HttpCode::NOT_FOUND,
                'message' => 'Param tidak boleh kosong'],
                'response' => [],
            ];
            return $this->error_response;
        }
        return $this->error_response;
    }

    private function validateParamString($params){
        
        if (!is_string($params)) {
            $this->error_response = [
                'metadata' => ['status' => \Codeception\Util\HttpCode::NOT_FOUND,
                'message' => 'param tidak boleh kosong',],
                'response' => [],
            ];
            return $this->error_response;
        }
        return $this->error_response;
    }

    private function validateMethodeNotValid($methode){
        $pattern = "/GET|POST|PUT|PATCH|DELETE/i";
        if (!preg_match($pattern, $methode)) {
            $this->error_response = [
                'metadata' => ['status' => \Codeception\Util\HttpCode::NOT_FOUND,
                'message' => 'Methode tidak valid',],
                'response' => [],
            ];
            return $this->error_response;
        }
        return $this->error_response;
    }

    private function validateUrlNotValid($url){
        
        $pattern = "%^((https?://)|(www\.))([a-z0-9-].?)+(:[0-9]+)?(/.*)?$%i";
        if (!preg_match($pattern, $this->getConfig('base_url').''.$url)) {
            $this->error_response = [
                'metadata' => ['status' => \Codeception\Util\HttpCode::NOT_FOUND,
                'message' => 'uri tidak valid',],
                'response' => [],
            ];
            return $this->error_response;
        }
        return $this->error_response;
    }
    
}
