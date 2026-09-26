<?php

namespace Integrasi\Components\Services;

use Integrasi\Service\Sirs\Models\Lookup;
use phpDocumentor\Reflection\Types\Array_;
use Yii;
use yii\helpers\ArrayHelper;

class AsuransiPenjaminService extends IntegrationBaseService
{

    /**
     * mencari config pada params yii
     * @var string
     */
    // public $keyConfig = 'djamil';

    /**
     * kebutuhan untuk auth casemix
     * @var string
     */
    public $xOwner;

    /**
     * untuk key cache token
     * @var string
     */
    protected $tokenIdentity = 'djamil-token1';

    const EXPIRED_TOKEN = 3600;

    public function login()
    {
        $request = $this->post('sirs/auth',[
                        'form_params' => [
                            'username' => $this->getAttribute("username_djamil"),
                            'password' => $this->getAttribute("password_djamil")
                        ]
                    ]);
        $response = isset($request['response']) ? $request['response'] : null;
        $getToken = isset($request['response']['token']) ? $request['response']['token'] : null;
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

    /**
     * Function untuk mengambil data dari api rsdjamil
     * Function ini bisa digunakan bersama yang membedakan adalah url API nya
     * @param url
     * @param options
     */
    public function getData($url, $options = [])
    {
        $payload = [
            'form_params' => [
                "token" => $this->checkToken(),
                "username" => $this->getAttribute("username_djamil"),
                "from_date" => ArrayHelper::getValue($options, 'tanggal_awal'),
                "to_date" => ArrayHelper::getValue($options, 'tanggal_akhir'),
            ]
        ];
        $request = $this->post($url,array_merge($payload, $this->setHeaders()));
        return $request;
    }


    /**
     * Function untuk trigger data kunjungan dari KASIR PEMBAYARAN
     * @return JSON 
     */
    public function triggerSinkronisasi($instalasi, $no_pendaftaran)
    {
        $tgl_pendaftaran = '2023-04-26';
        $jam_pendaftaran = "00:00:00";
        $penjaminUrl = Yii::$app->docoRest->penjaminasuransi;

        $result = $penjaminUrl->post('single-sync/single-sinkron',[
            'query' => [
                'tgl_pendaftaran' => $tgl_pendaftaran,
                'jam_pendaftaran' => $jam_pendaftaran,
                'instalasi' => $instalasi,
                'no_pendaftaran' => $no_pendaftaran,
                'is_trigger' => true
            ]
        ]);
        
        $result = json_decode($result->getBody(),true);
        return $result;
    }
}