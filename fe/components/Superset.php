<?php

namespace app\components;

use Yii;
use yii\base\Component;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class Superset extends Component
{
    public $enabled = true;
    public $domain = '';
    public $address;
    public $username;
    public $password;
    public $timeout = 30;
    public $connect_timeout = 30;

    protected $client;
    protected $token = null;
    protected $mappings = [];
    protected $localMappings = [];

    public function init()
    {
        if ($this->enabled) {
            $this->client = new Client([
                'base_uri' => $this->address,
                'timeout' => $this->timeout,
                'connect_timeout' => $this->connect_timeout
            ]);
            if (Yii::$app->cache->exists('superset-authentication-token')) {
                $this->token = Yii::$app->cache->get('superset-authentication-token');
            }
            $this->initScript();
            $this->initMapping();
        }
    }

    public function dashboard($key, $width = '100%', $backend = '/site/superset-guest-token')
    {
        if (!$this->enabled) {
            return '<!-- dashboard superset is disabled. -->';
        }
        if ($mapping = ArrayHelper::getValue($this->mappings, $key)) {
            $uid = DocoHelpers::generateRandomString(10);
            $elid = sprintf('superset-dashboard-container-%s-%s', $mapping['mapping_identity'], $uid);
            $identity = ArrayHelper::getValue($this->localMappings, $key, $mapping['mapping_identity']);
            $address = $this->domain ?: $this->address;
            $js = "
                supersetEmbeddedSdk.embedDashboard({
                    id: '{$identity}',
                    supersetDomain: '{$address}',
                    mountPoint: document.getElementById('{$elid}'),
                    fetchGuestToken: () => \$.get('{$backend}', {key: '{$key}'}),
                    dashboardUiConfig: { hideTitle: true, hideTab: true, hideChartControls: true },
                }).then(function(result) {
                    window.supersetInstances.push({elid: '{$elid}', result: result, width: '{$width}'});
                });
            ";
            Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY);
            return Html::tag('div', '', ['id' => $elid, 'class' => 'superset-dashboard-container']);
        }
        return sprintf('<!-- superset mapping for key %s not found. -->', $key);
    }

    public function requestGuestToken($key, $rls = [])
    {
        is_null($this->token) and $this->authenticate();

        $mapping = ArrayHelper::getValue($this->mappings, $key);

        $response = $this->client->post('/api/v1/security/guest_token', [
            RequestOptions::JSON => [
                'resources' => [
                    [
                        'id' => ArrayHelper::getValue($this->localMappings, $key, $mapping['mapping_identity']),
                        'type' => 'dashboard'
                    ]
                ],
                'rls' => $rls,
                'user' => [
                    'username' => 'sirs-user-' . Yii::$app->docoVars->user('uid'),
                    'first_name' => Yii::$app->docoVars->user('nama_pegawai'),
                    'last_name' => 'Sirs'
                ]
            ],
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $this->token)
            ]
        ]);
        $body = json_decode($response->getBody(), true);
        return $body['token'];
    }

    public function authenticate()
    {
        $this->token = Yii::$app->cache->getOrSet('superset-authentication-token', function() {
            $response = $this->client->post('/api/v1/security/login', [
                RequestOptions::JSON => [
                    "username" => $this->username,
                    "password" => $this->password,
                    "provider" => "db",
                    "refresh" => false
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            return $body['access_token'];
        }, 1000);
    }

    private function initScript()
    {
        Yii::$app->getView()->registerCss(
            "
            .superset-dashboard-container > iframe {
                border: none;
                width: 100%;
            }
            "
        );
        Yii::$app->getView()->registerJs(
            "
            window.supersetInstances = [];

            setInterval(async () => {
                window.supersetInstances.forEach(function(item, index) {
                    item.result.getScrollSize().then(function(data) {
                        $('iframe', '#' + item.elid).css({
                            height: data.height,
                            width: item.width
                        });
                    });
                });
            }, 1000);
            ",
            \yii\web\View::POS_HEAD
        );
        Yii::$app->getView()->registerJsFile('@web/js/superset.js', [
            'depends' => [
                \yii\web\JqueryAsset::className()
            ]
        ]);
    }

    private function initMapping()
    {
        $this->mappings = Yii::$app->cache->getOrSet('superset-dashboard-mapping', function() {
            $response = Yii::$app->docoRest->master->get('allow/get-superset-mapping');
            $body = json_decode($response->getBody(), true);
            return ArrayHelper::index(isset($body['response']) ? $body['response'] : [], 'mapping_key');
        }, 1800);

        $path = Yii::getAlias('@app') . '/config/superset.php';
        if (is_file($path) && is_readable($path)) {
            $this->localMappings = include($path);
        }

    }
}
