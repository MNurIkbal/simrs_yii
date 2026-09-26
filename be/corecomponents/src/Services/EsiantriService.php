<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\Services;

use Yii;
use GuzzleHttp\Client;

class EsiantriService extends IntegrationService {
    public $keyConfig = 'esiantri';
    public $headers;
    protected $tokenIdentity = 'esiantri-token';
    const EXPIRED_TOKEN = 3600;

    public function __construct() {
        parent::__construct();
        $this->keyConfig = 'esiantri';
    }

    public function login() {
        $request = $this->get('auth', [
            'headers' => [
                'x-username' => $this->getUsername(),
                'x-password' => $this->getPassword()
            ]
        ]);
        $response = isset($request['response']) ? $request['response'] : null;
        $token = isset($response['token']) ? $response['token'] : null;
        return $token;
    }

    public function checkToken() {
        $token = Yii::$app->cache->get($this->tokenIdentity);
        if (empty($token)) {
            $token = $this->login();
            Yii::$app->cache->set($this->tokenIdentity, $token, self::EXPIRED_TOKEN);
        }
        return $token;
    }

    public function setHeaders() {
        return [
            'headers' => [
                'x-token' => $this->checkToken(),
                'x-username' => $this->getUsername()
            ]
        ];
    }

    public function getData($url, $payload = []) {
        return $this->get($url, array_merge($payload, $this->setHeaders()));
    }

    public function getUsername() {
        return $this->getAttribute('username');
    }

    public function getPassword() {
        return $this->getAttribute('password');
    }
}