<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class TestController extends Controller
{
    /**
     * @inheritdoc
     */
    // public function behaviors()
    // {
        // return [
            // 'access' => [
                // 'class' => AccessControl::className(),
                // 'rules' => [
                    // [
                        // 'actions' => ['login', 'error'],
                        // 'allow' => true,
                    // ],
                    // [
                        // 'actions' => ['logout', 'index','notif'],
                        // 'allow' => true,
                        // 'roles' => ['@'],
                    // ],
                // ],
            // ],
            // 'verbs' => [
                // 'class' => VerbFilter::className(),
                // 'actions' => [
                    // 'logout' => ['post', 'get'],
                // ],
            // ],
        // ];
    // }

    /**
     * @inheritdoc
     */
    // public function actions()
    // {
        // return [
            // 'error' => [
                // 'class' => 'yii\web\ErrorAction',
            // ],
            // 'captcha' => [
                // 'class' => 'yii\captcha\CaptchaAction',
                // 'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            // ],
        // ];
    // }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        dump("adsdsd");exit;
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', 'http://localhost/project/simrs-backend/web/master/pendidikan');
        echo $res->getStatusCode();
        // 200
        echo $res->getHeaderLine('content-type');
        // 'application/json; charset=utf8'
        echo $res->getBody();
        // '{"id": 1420053, "name": "guzzle", ...}'

        // Send an asynchronous request.
        // $request = new \GuzzleHttp\Psr7\Request('GET', 'http://httpbin.org');
        // $promise = $client->sendAsync($request)->then(function ($response) {
            // echo 'I completed! ' . $response->getBody();
        // });
        // $promise->wait();
    }
}
