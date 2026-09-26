<?php

/**
 * setup config application
 *
 * @author ali.padilah@docotel.com
 * @return session json
 */

namespace app\components;


use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use app\controllers\MenuController;
use app\components\DebugHelper;
use Exception;


class DocoSetupController extends Controller
{
    protected $cache;
    protected $_restMaster;
    public $identity;
    protected $allowAction = [];
    protected $autoChangeSession = true;

    public function init()
    {
        parent::init();
        
        //shared session check : rinardi@docotel.com
        $session = Yii::$app->session;

        $jToken = $session->get('token');
        if ($jToken !== null && $this->autoChangeSession)
        {
            if (!defined('SIRS_SESSION_DEFINED'))
            {                
                $rest_dcms = Yii::$app->docoRest->dcms;
                $response = $rest_dcms->post('access-data/get-current-workspace',[]);
                $responseBody = json_decode($response->getBody(),true);
                if ($responseBody['metadata']['status'] != 200)
                    throw new Exception($responseBody['metadata']['message']);

                //DebugHelper::dump($responseBody['response']);
                if ($responseBody['response'] != null)
                {
                    $active_workspace = $responseBody['response']['active_workspace'];
                    $rModuleId = $responseBody['response']['module_id'];
                    $dMenu = $responseBody['response']['menus'];
                    $aMenu = $responseBody['response']['menu_access'];

                    $generatedMenu = MenuController::generateMenuWithMenuData($dMenu);
                    $session->set('moduleID',$rModuleId);
                    $session->set('active_workspace', $active_workspace);            
                    $session->set('menu',$generatedMenu);
                    $session->set('akses_menu',$aMenu);                    
                    $session->reloadSpecialCases();

                    Yii::$app->docoRest->resetVars();
                    //DebugHelper::dump($active_workspace);
                }
            }
        }             
        //end of shared session check
    }

    public function beforeAction($action)
    {
        $this->cache = Yii::$app->cache;
        $this->identity = $this->cache->get('app');

        $controller = Yii::$app->controller;
        $modul = '/' . $controller->module->id . '/' . $controller->id;
        $currnetAction = $this->action->id;

        $notUrl = [
            '/dcms/profile',
            '/site/login',
            '/',
            '/dcms/utility',
            '/basic/site',
        ];

        $akses = Yii::$app->session->get('akses_menu');

        if ($this->identity) {
            if ($akses) {
                if (isset($akses[$modul])) {
                    return true;
                } else {
                    $modulCon = Yii::$app->controller->module;
                    if ($modulCon instanceof \yii\web\Application) {
                        return true;
                    } else if (!in_array($controller->id, ['dashboard', 'default'])) {
                        if (in_array($modul, $notUrl)) {
                            return true;
                        }
                        return $this->checkAllow();
                    } else {
                        if (in_array($controller->id, ['dashboard', 'default'])) {
                            return true;
                        }
                        $baseUrl = Yii::$app->docoVars->workspace('url');
                        if ($baseUrl != '/' . $controller->module->id) {
                            return $this->checkAllow();
                        }
                    }
                }
                return true;
            }
            return true;
        } else {
            $this->generateApp();
            $this->generateWs();
            return true;
        }
    }

    /**
     * Runs an action within this controller with the specified action ID and parameters.
     * If the action ID is empty, the method will use [[defaultAction]].
     * @param string $id the ID of the action to be executed.
     * @param array $params the parameters (name-value pairs) to be passed to the action.
     * @return mixed the result of the action.
     * @throws InvalidRouteException if the requested action ID cannot be resolved into an action successfully.
     * @see createAction()
     */
    public function runAction($id, $params = [])
    {
        try {
            return parent::runAction($id,$params);
        } catch (\Exception $e) {
            throw new \app\components\HandlerExceptions(500, $e->getMessage(), $e);
        }
    }

    protected function checkAllow()
    {
        if (in_array($this->action->id, $this->allowAction)) {
            return true;
        } else {
            if (in_array("*", $this->allowAction)) {
                return true;
            }
            return false;
        }
    }

    private function generateApp()
    {
        $this->_restMaster = Yii::$app->docoRest->master;
        $response = $this->_restMaster->get('setup/datars');

        $body = json_decode($response->getBody(), TRUE);
        $attributes = isset($body['response']) ? $body['response'] : null;
        if (empty($attributes)) {
            // return $this->render('view', get_defined_vars());
        } else {
            $this->generateCacheApp($attributes);
            return true;
        }

    }

    private function generateCacheApp($attributes)
    {
        foreach ($attributes as $key => $value) {
            if (is_array($value)) {
                $attributes[$key] = empty(array_values($value)[0]) ? "" : array_values($value)[0];
            }
        }
        $this->cache->set('app',$attributes);
    }

    private function generateWs()
    {
        $this->generateCacheWs();
    }

    private function generateCacheWs()
    {
        if(Yii::$app->session->get('workspace'))
            Yii::$app->cache->set('workspace', Yii::$app->session->get('workspace'));
        if(Yii::$app->session->get('active_workspace'))
            Yii::$app->cache->set('active_workspace', Yii::$app->session->get('active_workspace'));
    }
}