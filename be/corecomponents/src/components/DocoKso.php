<?php

/**
* @author ali.padilah@docotel.com
* Integrasi KSO
*/
namespace Doco\components;


use Yii;
use app\modules\v1\models\KonfigSystem;

class DocoKso
{

    static protected $const_id;
    static protected $secret_key;
    static protected $url;

    public function __construct()
    {

    }

    public function sendKlaim($data = null, $request_method = null)
    {
        $modelKonfig = KonfigSystem::find()->one();
        self::$const_id = empty($modelKonfig->kso_consid) ? null : $modelKonfig->kso_consid;
        self::$secret_key = empty($modelKonfig->kso_secretkey) ? null : $modelKonfig->kso_secretkey;
        self::$url = empty($modelKonfig->kso_url) ? null : $modelKonfig->kso_url;

        // dump(self::$url, $data, self::generateHeader(),$request_method);exit;


        return self::curl(self::$url, $data, self::generateHeader(),$request_method);
    }

    protected static function generateHeader($form_url_encoded = false)
    {
        if ($form_url_encoded)
            $conten_type = "Content-Type: application/x-www-form-urlencoded";
        else
            $conten_type = "Content-Type: application/json";

        return [
            "Dconst-Id : " . self::$const_id,
            "Dsignature : " . self::getSignature(),
            $conten_type
        ];
    }

    protected static function getSignature()
    {
        // Computes the signature by hashing the salt with the secret key as the key
        $signature = hash_hmac(
            'sha256', 
            self::$const_id . "&" . self::getTimestamp(), 
            self::$secret_key, 
            true
        );

        // return $signature;
        return base64_encode($signature);
    }

    protected static function getTimestamp()
    {
        // Computes the timestamp
        date_default_timezone_set('UTC');
        return strtotime(date("Y-m-d H"));
    }

    protected static function curl($url, $data, $header, $action = '')
    {
        // dump($url, $data, $header, $action);exit;

        $header = array(
                    // 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    // 'Accept-Encoding: gzip, deflate',
                    // 'Accept-Language: en-US,en;q=0.5',
                    // 'Connection: keep-alive',
                    'Dconst-Id: '.self::$const_id,
                    'Dsignature: '.self::getSignature(),
                    'Content-Type: application/json',
                  );

        // $data = [
        //     'pendapatan' => $data
        // ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $action);

        $content  = curl_exec($ch);

        curl_close($ch);

        return $content;
    }

}