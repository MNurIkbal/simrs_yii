<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\LoginForm;
use app\models\ContactForm;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\controllers\MenuController;
use yii\helpers\Json;

class AllowController extends DocoController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'set-method', 'unfinished-soap', 'antrian'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'notif', 'set-method', 'unfinished-soap', 'antrian', 'error'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post', 'get'],
                ],
            ],
        ];
    }


    /**
     * Set user identity untuk Antrian, akses tanpa login (auto login)
     * @author ali.padilah@docotel.com
     * @return boolean
     */
    public function actionAntrian($url = "antrian/dashboard")
    {
        $conf = @parse_ini_file('../config/env/.env', true);

        $login = [
            'LoginForm' => [
                'username' => empty($conf['kiosk']['username']) ? null : $conf['kiosk']['username'],
                'password' => empty($conf['kiosk']['password']) ? null : $conf['kiosk']['password']
            ]
        ];


        $method = [
            "modulID" => empty($conf['kiosk']['modul_id']) ? 0 : $conf['kiosk']['modul_id'],
            "instalasiIndex" => empty($conf['kiosk']['instalasi_index']) ? 0 : $conf['kiosk']['instalasi_index'],
            "instalasiID" => empty($conf['kiosk']['instalasi_id']) ? 0 : $conf['kiosk']['instalasi_id'],
            "roomIndex" => empty($conf['kiosk']['room_index']) ? 0 : $conf['kiosk']['room_index'],
            "roomID" => empty($conf['kiosk']['room_id']) ? 0 : $conf['kiosk']['room_id']
        ];


        $identity = $this->cekIdentity($method);

        if ($identity == DocoConstants::STS_LOGINANDMETHOD) {
            return $this->redirect([$url]);
        } else {
            $model = new LoginForm();
            if ($model->load($login) && $model->login()) {
                $status_method = $this->setMethod($method['modulID'], $method['instalasiIndex'], $method['instalasiID'], $method['roomIndex'], $method['roomID']);

                $session = Yii::$app->session;
                $active_workspace = $session->get('active_workspace');

                if ($status_method) {
                    return $this->redirect([$url]);
                } else {
                    return $this->goBack();
                }
            } else {
                return $this->goBack();
            }
        }
    }


    /**
     * copy of Set method and workspace
     * @return boolean
     */
    public function setMethod($modulID = 0, $instalasiIdx = 0, $instalasiID = 0, $roomIdx = 0, $roomID = 0)
    {
        $session = Yii::$app->session;
        $workspace = $session->get('workspace');
        $active_workspace = $session->get('active_workspace');



        $active_workspace['modul_id'] = $modulID;
        $active_workspace['url'] = Url::home() . preg_replace('/^\//i', '', $workspace[$modulID]['url']);
        $active_workspace['modul_name'] = $workspace[$modulID]['name'];
        $active_workspace['modul_alias'] = str_replace("Modul ", "", $workspace[$modulID]['name']);
        $active_workspace['modul_icon'] = $workspace[$modulID]['icon'];
        $active_workspace['instalasi_id'] = $instalasiID;
        $active_workspace['instalasi_index'] = (string) $instalasiIdx;
        $active_workspace['instalasi_name'] = $workspace[$modulID]['installation'][$instalasiIdx]['name'];
        $active_workspace['ruangan_id'] = $roomID;
        $active_workspace['ruangan_index'] = (string) $roomIdx;
        $active_workspace['ruangan_name'] = $workspace[$modulID]['installation'][$instalasiIdx]['rooms'][$roomIdx]['name'];

        $active_workspace['ruangan_lain'] = $workspace[$modulID]['installation'][$instalasiIdx]['rooms'];
        $active_workspace['loket'] = null;

        $session->set('moduleID', $modulID);
        $session->set('active_workspace', $active_workspace);


        MenuController::generateMenu($modulID);

        return true;
    }

    /**
     * copy of Set method and workspace
     * @return 1 = not login, 2 = login, 3 = login + set method
     */
    function cekIdentity($method)
    {
        if (Yii::$app->user->isGuest) {
            return DocoConstants::STS_NOTLOGIN;
        } else {
            $session = Yii::$app->session;
            $workspace = $session->get('workspace');
            $active_workspace = $session->get('active_workspace');

            if ($active_workspace['modul_id'] == $method['modulID'] && $active_workspace['instalasi_id'] == $method['instalasiID'] && $active_workspace['ruangan_id'] == $method['roomID']) {
                return DocoConstants::STS_LOGINANDMETHOD;
            } else {
                return DocoConstants::STS_LOGIN;
            }
        }
    }

    /**
     * This function will refresh the notification
     * 
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionUnfinishedSoap()
    {
        $soap = $this->guzzleExec(Yii::$app->docoRest->dcms, [
            'url' => 'allow/unfinished-soap',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
        $rm = $this->guzzleExec(Yii::$app->docoRest->dcms, [
            'url' => 'allow/unfinished-rm',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);

        return [
            'soap' => $soap,
            'rm'   => $rm,
        ];
    }
    
}
