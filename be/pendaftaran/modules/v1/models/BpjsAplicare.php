<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\Lookup;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\Services\ApiBPJSLZString;


class BpjsAplicare extends Bpjs
{
    public $user_key;
    static protected $user_keys;
    static protected $version = 1.0;

    public function init()
    {
        parent::init();
        
        $cache = Yii::$app->cache;
        $cache_bpjs = $cache->get(DocoConstants::LOOKUP_BPJS);
        if (!$cache_bpjs) {
            $lookup = Lookup::find()->where(['lookup_type'=>DocoConstants::LOOKUP_BPJS])
                ->asArray()
                ->all();
            $cache->set(DocoConstants::LOOKUP_BPJS, $lookup);
            $cache_bpjs = $cache->get(DocoConstants::LOOKUP_BPJS);
        }

        if ($cache_bpjs) {
            foreach ($cache_bpjs as $each) {
                if ($each['lookup_name'] == 'secret_key') {
                    $this->secret_key = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'cons_id') {
                    $this->cons_id = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'url_aplicare') {
                    $this->url = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'ppkPelayanan') {
                    $this->ppkPelayanan = $each['lookup_value'];
                }
                
                if ($each['lookup_name'] == 'version_jkn') {
                    self::$version = $each['lookup_value'];
                }

                if ($each['lookup_name'] == 'user_key') {
                    $this->user_key = $each['lookup_value'];
                }
            }
        }

       
        self::$cons_ids = $this->cons_id;
        self::$secret_keys = $this->secret_key;
        self::$user_keys = $this->user_key;

    }
    /**
     * Output from CURL
     * @param mixed $data;
     * @return string JSON
     */
    protected static function out($data)
    {
        $outPut = $data && is_string($data) && json_decode($data) ? json_decode($data, true) : [];

        if (!empty($outPut['response']) && self::$version >= 1.1) {
            $response = $outPut['response'];
            $keyEncrypt = self::$cons_ids . self::$secret_keys . self::getTimestamp();
            if(is_string($response)){
                $outPut['response'] = (new ApiBPJSLZString)->decryptWithDecompress($keyEncrypt,$response);
            }else{
                $outPut['response'] = $response;
            }
        }
        return $outPut;

    }

    /**
     * header for API http://dvlp.bpjs-kesehatan.go.id:8081/devwslokalrest/
     * url baru untuk api https://dvlp.bpjs-kesehatan.go.id/vClaim-rest/
     * @param mixed $data;
     * @return HTTP Header
     */
    protected static function getHeader($form_url_encoded = false)
    {
        if ($form_url_encoded)
            $conten_type = "Content-Type: application/x-www-form-urlencoded";
        else
            $conten_type = "Content-Type: application/json";

        return [
            "X-cons-id:" . self::$cons_ids,
            "X-timestamp:" . self::getTimestamp(),
            "X-signature:" . self::getSignature(),
            "user_key:" . self::$user_keys,
            $conten_type
        ];
    }

    public function showConfig()
    {
        return self::getHeader();
    }

    public function referensiKamarAplicare()
    {
        $param = 'ref/kelas';
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, false, self::getHeader(true), 'GET');
        return self::out($get_curl);
    }
}
