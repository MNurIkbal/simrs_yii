<?php

namespace Doco\components;

use Doco\models\Lookup;
use Doco\Services\ApiBPJSLZString;
use yii\rest\Serializer;
use Yii;

class DocoSerializerEncrypt extends DocoSerializer
{
    static protected $cons_ids;
    
    static protected $secret_keys;
    
    static protected $user_keys;
    
    static protected $version = 1.0;
    
    static protected $timestamp;

    public function init()
    {
        parent::init();

        $cache = Yii::$app->cache;
        $cache_bpjs = $cache->get(DocoConstants::LOOKUP_BPJS);
        if (!$cache_bpjs) {
            $lookup = Lookup::find()->where(['lookup_type' => DocoConstants::LOOKUP_BPJS])
                ->asArray()
                ->all();
            $cache->set(DocoConstants::LOOKUP_BPJS, $lookup);
            $cache_bpjs = $cache->get(DocoConstants::LOOKUP_BPJS);
        }

        if ($cache_bpjs) {
            foreach ($cache_bpjs as $each) {
                if ($each['lookup_name'] == 'secret_key') {
                    self::$secret_keys = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'cons_id') {
                    self::$cons_ids = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'user_key_vclaim') {
                    self::$user_keys = $each['lookup_value'];
                }
            }
        }
    }

    /**
     * Serializes the given data into a format that can be easily turned into other formats.
     * This method mainly converts the objects of recognized types into array representation.
     * It will not do conversion for unknown object types or non-object data.
     * The default implementation will handle [[Model]] and [[DataProviderInterface]].
     * You may override this method to support more object types.
     * @param mixed $data the data to be serialized.
     * @return mixed the converted data.
     */
    public function serialize($data)
    {
        $keyEncrpyt = self::generateSecretKey();

        /**
         * Bypass With Headers Request
         */
        $isBypass = Yii::$app->request->getHeaders()->get('X-Bypass');
        $xOwner = Yii::$app->request->getHeaders()->get('X-Owner');
        if ($isBypass && $isBypass == $keyEncrpyt || !empty($xOwner)) {
            return parent::serialize($data);
        }

        $is_mobile = Yii::$app->jwt->is_mobile;
        $data = parent::serialize($data);
        $statusCode = $this->response->getStatusCode();
        $keyEncrpyt = self::keyEncrypt();
        
        if (isset($data['status'])) {
            $statusCode = $data['status'];
            unset($data['status']);
        }
        if ($is_mobile) {
            $response["status"] = $statusCode;
            $response["data"] = $data;
            $response["message"] = DocoSerializer::$phrase[$statusCode];
            if ($statusCode == 200) {
                $response["error"] = null;
            } else {
                $response["data"] = null;
                $response["error"] = isset($data['message']) ? $data['message'] : $data;
            }
        } else {
            $response = [
                "metadata" => [
                    "status" => $statusCode,
                    "message" => DocoSerializer::$phrase[$statusCode]
                ],
                "response" => null
            ];
            $response["response"] = (new ApiBPJSLZString)->encryptWithCompress($keyEncrpyt, $data);
        }

        $response['encrypted'] = true;
        return $response;
    }

    private static function generateSecretKey()
    {
        return self::$secret_keys . self::$cons_ids;
    }

    private static function keyEncrypt() {
        return DocoConstansId::actionGetAdditional('key_enkripsi');
    }
}
