<?php 

namespace app\components\Services;

use Yii;
use app\components\DocoHelpers;
use app\components\Services\Contracts\BpjsConfigInterface;
use app\components\models\ConfigBpjsModel;

class BpjsConfigService implements BpjsConfigInterface
{
    protected $_rest;

    const STRING_CONFIG = 'bpjs_config';
    const CONFIG_SECRET_KEY = 'secret_key';
    const CONFIG_CONS_ID = 'cons_id';
    const CONFIG_URL = 'url';
    const CONFIG_PPK = 'ppkPelayanan';
    const CONFIG_VERSION = 'version';
    const CONFIG_USER_KEY = 'user_key_vclaim';

    public function __construct()
    {
        $this->_rest = Yii::$app->docoRest->pendaftaran;
    }

    /**
     * @return app\components\models\ConfigBpjsModel
     */
    public function getConfig()
    {
        $model = new ConfigBpjsModel;
        $session = Yii::$app->session;
        $getConfig = $session->get(self::STRING_CONFIG);
        if (empty($getConfig)) {
            $respose = $this->_rest->get('allow-bpjs/get-config-bpjs');
            $resposeJson = json_decode($respose->getBody(),true);
            if (!empty($resposeJson['response']) && is_array($resposeJson['response'])) {
                $configBpjs = $resposeJson['response'];
                $getConfig = [];
                foreach ($configBpjs as $key => $value) {
                    if (!isset($value['lookup_name']) && !isset($value['lookup_value'])) continue;

                    switch ($value['lookup_name']) {
                        case self::CONFIG_SECRET_KEY:
                            $getConfig['secretKey'] = $value['lookup_value'];
                            break;
                        case self::CONFIG_CONS_ID:
                            $getConfig['consId'] = $value['lookup_value'];
                            break;
                        case self::CONFIG_URL:
                            $getConfig['url'] = $value['lookup_value'];
                            break;
                        case self::CONFIG_PPK:
                            $getConfig['ppkPelayanan'] = $value['lookup_value'];
                            break;
                        case self::CONFIG_VERSION:
                            $getConfig['version'] = $value['lookup_value'];
                            break;
                        case self::CONFIG_USER_KEY:
                            $getConfig['user_key_vclaim'] = $value['lookup_value'];
                            break;
                        default:
                            # code...
                            break;
                    }

                    $session->set(self::STRING_CONFIG, $getConfig);
                }
            }
        }
        $model->attributes = $getConfig;
        return $model;
    }
}