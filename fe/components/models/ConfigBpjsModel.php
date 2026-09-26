<?php

namespace app\components\models;

use Yii;
use app\components\ApiBPJSLZString;

class ConfigBpjsModel extends \yii\base\Model
{
    public $consId;
    public $secretKey;
    public $version;
    public $ppkPelayanan;
    public $url;
    public $user_key_vclaim;

    private $timestamp;

    public function rules()
    {
        return [
            [[
                'consId',
                'secretKey',
                'version',
                'ppkPelayanan',
                'url',
                'user_key_vclaim',
            ], 'safe']
        ];
    }

    public function getSignature()
    {
        // Computes the signature by hashing the salt with the secret key as the key
        $signature = hash_hmac(
            'sha256', 
            $this->consId . "&" . $this->getTimestamp(), 
            $this->secretKey, 
            true
        );
        return base64_encode($signature);
    }

    protected function getTimestamp()
    {
        // Computes the timestamp
        date_default_timezone_set('UTC');
        if (empty($this->timestamp)) {
            $this->timestamp = strval(time()-strtotime('1970-01-01 00:00:00'));
        }
        return $this->timestamp;
    }

    /**
     * Output from CURL
     * @param mixed $data;
     * @return string JSON
     */
    public function out($data)
    {
        $outPut = $data && is_string($data) && json_decode($data) ? json_decode($data, true) : [];
        if (!empty($outPut['response']) && $this->version != 1) {
            $response = $outPut['response'];
            $keyEncrypt = $this->consId . $this->secretKey . $this->getTimestamp();
            $outPut['response'] = (new ApiBPJSLZString)->decryptWithDecompress($keyEncrypt,$response);
        }
        return $outPut;
    }

    public function curl($url, $data, $header, $action = '')
    {
        $curl = curl_init($this->url . $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);

        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            if (!empty($action)) {
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $action);
            }
        }

        $res = curl_exec($curl);

        $curl_get_info = curl_getinfo($curl);
        $get_response_time = !empty($curl_get_info['total_time']) ? $curl_get_info['total_time'] : 0;

        if (!$res) {
            $errorArray = [
                'metaData'=>[
                    "code"=>500,
                    "message"=>curl_error($curl) ? : "Problem with bridging",
                ]
            ];
            $res = json_encode($errorArray);
            // die('Error: "'.curl_error($curl).'" - Code: '.curl_errno($curl));
        }
        curl_close($curl);

        return self::out($res);
    }

    /**
     * header for API http://dvlp.bpjs-kesehatan.go.id:8081/devwslokalrest/
     * url baru untuk api https://dvlp.bpjs-kesehatan.go.id/vClaim-rest/
     * @param mixed $data;
     * @return HTTP Header
     */
    public function getHeader($form_url_encoded = false)
    {
        if ($form_url_encoded)
            $conten_type = "Content-Type: application/x-www-form-urlencoded";
        else
            $conten_type = "Content-Type: application/json";

        return [
            "X-cons-id:" . $this->consId,
            "X-timestamp:" . $this->getTimestamp(),
            "X-signature:" . $this->getSignature(),
            "user_key:" . $this->user_key_vclaim,
            $conten_type
        ];
    }
}
