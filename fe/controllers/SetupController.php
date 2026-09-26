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

class SetupController extends DocoController
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
                        'actions' => ['login', 'error','set-method','antrian'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index','notif','set-method','antrian','error'],
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

    public function actionIndex()
    {

        $requirement = [
            'xml',
            'xmlreader',
            'xmlwriter',


            'mhash',
            'mbstring',
            'curl',
        ];

        $server = [
            'os' => PHP_OS,
            'php_version' => phpversion(),
            'extention_php' => get_loaded_extensions(),
            'extention_status_php' => []
        ];

        foreach ($requirement as $i => $v) {

            if (in_array($v, $server['extention_php'])) {
                $server['extention_status_php'][$v] = true;
            } else {
                $server['extention_status_php'][$v] = false;
            }
        }

        return $this->render('setup', get_defined_vars());

        // dump($server);exit;

    }
}
