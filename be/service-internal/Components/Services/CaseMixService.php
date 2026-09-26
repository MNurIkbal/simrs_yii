<?php

namespace Integrasi\Components\Services;

use Yii;

class CaseMixService extends IntegrationBaseService
{

    /**
     * mencari config pada params yii
     * @var string
     */
    public $keyConfig = 'casemix';

    /**
     * kebutuhan untuk auth casemix
     * @var string
     */
    public $xOwner;

    /**
     * untuk key cache token
     * @var string
     */
    protected $tokenIdentity = 'case-mix-token';

    const EXPIRED_TOKEN = 3600;

    public function login()
    {
        $request = $this->post('dcms/v1/auth/get-token',[
                        'form_params' => [
                            'username' => $this->getAttribute('username'),
                            'password' => $this->getAttribute('password')
                        ]
                    ]);
        $response = isset($request['response']) ? $request['response'] : null;
        $getToken = isset($response['access_token']) ? $response['access_token'] : null;
        return $getToken;
    }

    public function checkToken()
    {
        $token = Yii::$app->cache->get($this->tokenIdentity);
        if (empty($token)) {
            $token = $this->login();
            Yii::$app->cache->set($this->tokenIdentity, $token, self::EXPIRED_TOKEN);
        }
        return $token;
    }

    public function setHeaders()
    {
        return [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->checkToken(),
                'X-Owner' => $this->xOwner
            ]
        ];
    }

    public function sendDataRegis($payload)
    {
        $payload = [
            'form_params' => $payload
        ];
        $request = $this->post('penjaminasuransi/v1/api/set-klaim',array_merge($payload, $this->setHeaders()));
        return $request;
    }
}