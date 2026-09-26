<?php

namespace Doco\components;

use Yii;
use yii\filters\AccessControl;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use Doco\models\AuditTrail;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoAccessRule;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstansId;
use Doco\components\DocoLookupType;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\GolonganUmur;
use Doco\Traits\ControllerHelperTrait;
use Doco\Services\InternalService;

class DocoActiveController extends ActiveController
{
    use ControllerHelperTrait;

    /**
     * @var string Unique session token taken from jwt.
     */
    public $uniqueToken = '';

    /**
     * Doco\models\User::loginpemakai_id
     * @var integer
     * @var string Unique session token taken from jwt.
     */
    public $loginpemakai_id;

    public $helper;
    public $constans;
    public $lookup_type;
    public $messageBroker;
    private $successProcess = false;
    public $result;

    public $serializer = [
        'class' => '\Doco\components\DocoSerializer',
        'collectionEnvelope' => 'data',
    ];

    public function init()
    {
        parent::init();
        $this->helper = new DocoHelpers;
        $this->constans = new DocoConstansId;
        $this->lookup_type = new DocoLookupType;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className()
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className()
        ];
        $behaviors['rateLimiter']['enableRateLimitHeaders'] = true;
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON; // Force XML Header to Json Response
        $behaviors['contentNegotiator']['formats']['application/xml'] = Response::FORMAT_JSON; // Force XML Header to Json Response
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    public function runAction($id, $params = [])
    {
        $action = $this->createAction($id);
        if ($action === null) {
            throw new \yii\base\InvalidRouteException('Unable to resolve the request: ' . $this->getUniqueId() . '/' . $id);
        }

        Yii::trace('Route to run: ' . $action->getUniqueId(), __METHOD__);

        if (Yii::$app->requestedAction === null) {
            Yii::$app->requestedAction = $action;
        }

        $oldAction = $this->action;
        $this->action = $action;

        $modules = [];
        $runAction = true;

        // call beforeAction on modules
        foreach ($this->getModules() as $module) {
            if ($module->beforeAction($action)) {
                array_unshift($modules, $module);
            } else {
                $runAction = false;
                break;
            }
        }

        $result = null;

        if ($runAction && $this->beforeAction($action)) {
            // run the action
            try {

                //fetch uniqueToken from jwt.
                $jwt = Yii::$app->jwt;
                $this->uniqueToken = $jwt->uniqueToken;
                $this->loginpemakai_id = !empty($jwt->user->loginpemakai_id) 
                                            ? $jwt->user->loginpemakai_id : null;

                $result = $action->runWithParams($params);
            } catch (\yii\base\InvalidParamException $e) {
                $result = false;
            }

            $result = $this->afterAction($action, $result);

            // call afterAction on modules
            foreach ($modules as $module) {
                /* @var $module Module */
                $result = $module->afterAction($action, $result);
            }
        }

        if ($oldAction !== null) {
            $this->action = $oldAction;
        }

        return $result;
    }

    /**
     * Ambil data akses dari cache yg di set waktu login be.
     * @author rbs1518 (rinardi@docotel.com)
     */
    protected function getAccessDataFromCache()
    {
        if ($this->loginpemakai_id == '')
            return null;
        else
        {
            $aCache = Yii::$app->accessCache;
            $data = $aCache->get('access_'.$this->loginpemakai_id);
            if ($data === false)
                throw new Exception('Cache data based on unique token does not exists.');
            return $data;
        }
    }

    public function afterAction($action, $result)
    {
        /**
         * Menambahkan Message Broker
         */
        $this->result = $result;
        $status = is_array($result) && isset($result['status']) ? $result['status'] : \Yii::$app->response->statusCode;
        if (!empty($this->messageBroker) && is_array($this->messageBroker)) {
            $actionId = $action->id;
            if (isset($this->messageBroker[$actionId]['services']) 
                        && is_array($this->messageBroker[$actionId]['services'])) {
                
                if($status < 300 && $status >= 200 ){
                    $this->successProcess = true;
                }
                $this->setMessagesBroker($this->messageBroker[$actionId]['services']);
            }
        }
        if (!$action instanceof \yii\web\ErrorAction) {
            $result = parent::afterAction($action, $result);
            $jwt = Yii::$app->jwt;
            $activity = !empty($jwt->activity) ? json_encode($jwt->activity) : null;
            $action = !empty($jwt->action) ? $jwt->action : null;
        } else {
            $action = 'SECRET';
            $activity = '';
            $result = [
                "metadata" => [
                    "status" => 404,
                    "message" => 'Not Found'
                ],
                "response" => [
                    'message' => 'Service tidak di temukan'
                ]
            ];
        }
        // Sengajat di mattin dulu
        $attributes[] = [
            'service_name' => Yii::$app->params['service'],
            'status' => (int) (isset($result['metadata']['status']) ? $result['metadata']['status'] : 200),
            'detail' => $activity,
            'action' => $action,
            'messages' => json_encode($result),
            'stamp' => date('Y-m-d H:i:s'),
            'loginpemakai_id' => !empty($jwt->user->loginpemakai_id) ? $jwt->user->loginpemakai_id : null,
            'ip_address' => DocoHelpers::getClientIp(),
            'url_referer' => $_SERVER['REQUEST_URI'],
            'browser' => @$_SERVER['HTTP_USER_AGENT'],
            'http_method' => $_SERVER['REQUEST_METHOD'],
        ];

        if (!empty($activity)) {
            AuditTrail::batchInsert($attributes);
        }

        return $result;
    }

    protected function setMessagesBroker(array $services)
    {
        $request = Yii::$app->request;
        $jwt = Yii::$app->jwt;
        
        foreach ($services as $key => $value) {
            $listSending = [];
            if (is_array($value)) {
                $listSending[$key] = [];
                $processing = true;
                foreach ($value as $plugin => $attributes) {
                    $listSending[$key][$plugin] = $attributes;
                    if (!empty($jwt->owner)) {
                        $listSending[$key][$plugin]['owner'] = $request->getHeaders()->get('X-Owner');
                    }

                    if (!empty($jwt->token)) {
                        $listSending[$key][$plugin]['token'] = $request->getHeaders()->get('Authorization');
                        $listSending[$key][$plugin]['user_identity'] = [
                            'username' => $jwt->user->nama_pemakai,
                            'uid' => $jwt->user->loginpemakai_id,
                        ];
                    }
                    $processing = !empty($attributes['successProcess']) ? $this->successProcess : true;
                    
                    if (!empty($attributes['payload']) && is_array($attributes['payload'])) {
                        unset($listSending[$key][$plugin]['payload']);
                        foreach ($attributes['payload'] as $attr => $params) {
                            $payload = $params;
                            if (is_string($attr)) {
                                $payload = $attr;
                            }
                            if ($request->isPost) {
                                $listSending[$key][$plugin][$payload] = ArrayHelper::getValue($request->post(), $params);
                            } else {
                                $listSending[$key][$plugin][$payload] = $request->get($params);
                            }
                        }
                    }

                    if (!empty($attributes['query_params']) && is_array($attributes['query_params'])) {
                        unset($listSending[$key][$plugin]['query_params']);
                        foreach ($attributes['query_params'] as $attr => $params) {
                            $payload = $params;
                            if (is_string($attr)) {
                                $payload = $attr;
                            }

                            if ($valParams = $request->getQueryParam($params)) {
                                $listSending[$key][$plugin][$payload] = $valParams;
                            }
                        }
                    }

                   
                    if (!empty($attributes['result'])) {
                        $listSending[$key][$plugin]['result'] = $this->result;
                    }

                    

                }

                if (!empty($listSending) && $processing) {
                    (new InternalService)->sendTo($listSending);
                }

            }
        }
    }

    /**
     *
     * Fungsi set/get cache untuk data get db
     * @param $cache_name string nama cache diambil dari DocoConstants
     * @param $function mixed fungsi model sampe find atau findBySql aja
     * @param $all boolean pengambilan data all/one default all
     * @param $cache_key string, integer nama key untuk cache, misal ruangan_id, dll
     * @param $expire_time integer, expire time cache dalam satuan detik
     *
     */
    public function getOrSetCache(
        $cache_name,
        $function = null,
        $all = true,
        $cache_key = false,
        $expire_time = 86400
    ) {
        $cache = Yii::$app->cache;
        $data_cache = $cache->get($cache_name);
        if (!$data_cache) {
            $data_cache_temp = $this->getDataCache($data_cache, $function, $cache_key, $all);

            // set default expire 1 hari
            $cache->set($cache_name, $data_cache_temp, $expire_time);
        } else {
            if (($cache_key) && (!isset($data_cache[$cache_key]))) {
                $data_cache_temp = $this->getDataCache($data_cache, $function, $cache_key, $all);

                // set default expire 1 hari
                $cache->set($cache_name, $data_cache_temp, $expire_time);
            }
        }

        if ($cache_key) {
            $data_return = $cache->get($cache_name)[$cache_key];
        } else {
            $data_return = $cache->get($cache_name);
        }

        return $data_return;
    }

    /**
     *
     * Fungsi ambil data
     * @see function getOrSetCache()
     *
     */
    private function getDataCache(
        $data_cache = [],
        $function,
        $cache_key,
        $all
    ) {
        if ($all) {
            $data = $function->asArray()->all();
        } else {
            $data = $function->asArray()->one();
        }

        if ($cache_key) {
            $data_cache[$cache_key] = $data;
        } else {
            $data_cache = $data;
        }

        return $data_cache;
    }

    public function convertAntrian($value = 'PBU1048')
    {
        $huruf = [];
        $arr = str_split($value);
        $i = strlen($value);
        foreach ($arr as $str) {
            if (!is_numeric($str) || $str == 0) {
                $huruf[] = $str;
            } else {
                break;
            }
            $i--;
        }

        $res = array_merge($huruf, [substr($value, ($i * -1))]);

        $hurufConverted = [];
        foreach ($res as $key => $each) {
            if (is_numeric($each)) {
                //edited ketika return blank, supaya tidak merubah function terbilang : ali.padilah@docotel.com
                $hurufConverted[] = trim($this->terbilang($each, true) == " " ? "Kosong" : $this->terbilang($each, true));
            } else {
                $hurufConverted[] = $each;
            }
        }

        return implode(' ', $hurufConverted);
    }

    /**
     * @see Fungsi get data lookup_m
     * @return array, activeQueryRecords
     *
     */
    public function getLookupByType($type = null)
    {
        $result = Lookup::find();

        if ($type) {
            $result->andWhere(['lookup_type' => $type]);
            $result->andWhere(['is_active' => TRUE]);
        }

        return $result;
    }

    public function cekValidasiPassword($password)
    {
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;

        $check = $jwt->katakunci_pemakai;
        $valid = Yii::$app->security->validatePassword($password, $check);

        return $valid; 
    }
}
