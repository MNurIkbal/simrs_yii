<?php

namespace app\components;


use Yii;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\components\DocoAccessRule;
use app\components\DocoHelpers;
use app\components\Traits\ControllerHelperTrait;
use yii\web\View;

class DocoController extends DocoSetupController
{
    use ControllerHelperTrait;
    /** @var Class $define helper */
    protected $helper;

    /** @var String $moduleName */
    public $moduleName;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $session = Yii::$app->session;

        return [
            'access' => [
                'class' => DocoAccessRule::className(),
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post', 'get'],
                    'pengambilan-antrian' => ['post', 'get'],
                ],
            ],
        ];
    }

    protected $_options = [
        'confirm' => [
            0 => 'Tidak',
            1 => 'Ya',
        ],
        'status' => [
            1 => 'Aktif',
            0 => 'Tidak aktif',
        ],
        'jenis_pembayaran' => [
            1 => 'Tunai',
            0 => 'Non Tunai',
        ],
    ];

    protected $_status = [
        'Aktif',
        'Tidak Aktif',
    ];

    private function translateOptions()
    {
        // Hard Status
        $this->_status = [
            Yii::t('fe', 'Aktif'),
            Yii::t('fe', 'Tidak aktif'),
        ];

        $tmpOptions = array();

        foreach ($this->_options as $option => $settings) {
            $tmpSettings = $settings;
            foreach ($settings as $key => $val) {
                $tmpSettings[$key] = Yii::t('fe', $val);
            }
            $tmpSettings[''] = Yii::t('fe', 'Pilih');
            $tmpOptions[$option] = $tmpSettings;
        }

        $this->_options = $tmpOptions;
    }

    public function init()
    {
        parent::init();
        $this->translateOptions();
        $this->helper = new DocoHelpers;
        $this->settingCookie();
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $session = Yii::$app->session;

        $url = Yii::$app->getRequest()->getUrl();
        $urlPath = '/' . Yii::$app->request->getPathInfo();
        $url = strtok($url, '?');

        // set lang : ali.padilah@docotel.com
        $cookies = Yii::$app->request->cookies;
        Yii::$app->language = ($cookies->has('lang')) ? $cookies->getValue('lang') : 'ID';

        $notUrl = [
            Url::home() . 'site/login',
            Url::home() . 'allow/antrian',
            '/dcms/utility/flush-cache',
            Url::home() . 'site/read-notification',
            Url::home() . 'site/refresh-notification',
            Url::home() . 'allow/unfinished-soap',
            Url::home() . '/',
            Url::home() . 'site/triage',
            Url::home() . 'site/get-pegawai',
            Url::home() . 'site/save-triage',
            Url::home() . 'rm/esign/callback-tilaka'
        ];        

        if (parent::beforeAction($action))
        {
            if (!empty($session->get('active_workspace')) && !empty($session->get('active_workspace')['modul_alias'])) {
                $this->moduleName = trim(strtolower($session->get('active_workspace')['modul_alias']));
            } else {
                $this->moduleName = null;
            }
            if (!Yii::$app->user->id && !in_array($url, $notUrl) && !$session->has('token')) {
                $controller = Yii::$app->controller;
                if (!empty($controller->module->enableAutoLogin)) {
                    return true;
                }
                $this->redirect(Url::home() . 'site/login');
                return false;
            } else {
                if (empty($session->get('active_workspace')['modul_id']) && ($url != Url::home()) && !in_array($urlPath, $notUrl) && !in_array($url, $notUrl)) {
                    $this->redirect(Url::home());
                } else if (!empty($session->get('active_workspace')['modul_id']) && ($url == Url::home()) && !in_array($urlPath, $notUrl) && !in_array($url, $notUrl)) {
                    $this->redirect(Url::home() . Yii::$app->docoVars->workspace('url'));
                } else {
                    return true;
                }
            }
            return true;
        } else {
            if (!Yii::$app->user->id && !in_array($url, $notUrl) && !$session->has('token')) {
                $this->redirect(Url::home() . 'site/login');
                return false;
            } else {
                throw new \yii\web\HttpException(401, Yii::t("fe", "Tidak Ada Akses"));
            }
        }
        return false;
    }

    /**
     * author: Budi
     * [actionSetCookie set cookie]
     * @return [type] [description]
     */
    private function settingCookie()
    {
        $name = DocoHelpers::encrypt('doco-dev');
        $value = DocoHelpers::encrypt('sirs-dev-' . time());
        $expire = (time() + 3600); // 1 jam

        $cookies = new \yii\web\Cookie([
            'name' => $name,
            'value' => $value,
            'expire' => $expire
        ]);

        if (!Yii::$app->getRequest()->getCookies()->has($name)) {
            Yii::$app->getResponse()->getCookies()->add($cookies);
        }
    }

    /**
     * Method get cache by array
     * 
     * @param Array $keys
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function cacheIn($keys)
    {
        $cache = Yii::$app->cache;
        $result = [];
        foreach ($keys as $key) {
            if ($cacheResult = $cache->get($key)) {
                $result[$key] = $cacheResult;
            } else {
                $result[$key] = [];
            }
        }
        return $result;
    }

    /**
     * This function to dump data
     *
     * @param Array/Object/String $payload
     * @author Tsani Nashrullah
     **/
    public function dd($payload)
    {
        echo '<pre>' . var_export($payload, true) . '</pre>';
        die();
    }

        public function getConfig($directory)
    {
        $arrayDir = explode('.', $directory);
        $fileName = $arrayDir[0];
        unset($arrayDir[0]);
        $configDirectory = dirname(__DIR__) . '/config/files/' . $fileName . '.php';
        $result = null;
        if (file_exists($configDirectory)) {
            array_values($arrayDir);
            $stringKeyArray = implode('.', $arrayDir);
            $arrayFile = include $configDirectory;
            if (empty($stringKeyArray)) {
                $result = $arrayFile;
            } else {
                $result = $this->helper->getKeyByString($stringKeyArray, $arrayFile);
            }
        }
        return $result;
    }
    /**
     * Set PHP Variable to JS
     * 
     * @param String key
     * @param Array val
     * @return Void
     */
    public function registerJsVar($key, $val, $position = View::POS_HEAD){
        $this->registerJs("var $key = ".json_encode($val).";", $position);
    }
}
