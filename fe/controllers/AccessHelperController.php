<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use GuzzleHttp\Exception\RequestException;

class AccessHelperController extends Controller
{
    public function actionIndex()
    {
        dump("TESTING");
        exit;
    }

    public function actionJumpTo()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;

        $jToken = $session->get('token');

        $moduleSlug = $request->get('slug');
        $instalasiId = $request->get('instalationid');
        $roomId = $request->get('roomid');

        $eTargetUri = trim($request->get('target'));
        //$targetUri = base64_decode($eTargetUri);
        //$targetUri = base64_decode(strtr($eTargetUri, '-_,', '+/='));
        $targetUri = hex2bin($eTargetUri);

        $dtp = [
            'home_url' => Url::home(),
            'is_temporary' => 0,
            'module_slug' => $moduleSlug,
            'installation_id' => $instalasiId,
            'room_id' => $roomId,
        ];

        $rest_dcms = Yii::$app->docoRest->dcms;
        try {
            $response = $rest_dcms->post('access-data/set-active-workspace-for-jump', [
                'form_params' => $dtp
            ]);
            $responseBody = json_decode($response->getBody(), true);
            if ($responseBody['metadata']['status'] != 200)
                throw new Exception($responseBody['metadata']['message']);

            $active_workspace = $responseBody['response']['active_workspace'];
            $rModuleId = $responseBody['response']['module_id'];
            $dMenu = $responseBody['response']['menus'];
            $aMenu = $responseBody['response']['menu_access'];

            $session->set('moduleID', $rModuleId);
            $session->set('active_workspace', $active_workspace);

            $generatedMenu = MenuController::generateMenuWithMenuData($dMenu);

            $session->set('menu', $generatedMenu);
            $session->set('akses_menu', $aMenu);

            return $this->redirect([$targetUri]);
        } catch (Exception $e) {
            return 'error ' . $e->getMessage();
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(401, Yii::t("fe", "Tidak Ada Akses"));
        }
    }

    public function actionOpenTo()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;

        $jToken = $session->get('token');

        $moduleSlug = $request->get('slug');
        $instalasiId = $request->get('instalationid');
        $roomId = $request->get('roomid');

        $eTargetUri = trim($request->get('target'));
        //$targetUri = base64_decode($eTargetUri);
        //$targetUri = base64_decode(strtr($eTargetUri, '-_,', '+/='));
        $targetUri = hex2bin($eTargetUri);

        $turiParts = explode('?', $targetUri);
        $caKey = trim($turiParts[0]);
        $caParams = [];
        if (isset($turiParts[1]))
            parse_str($turiParts[1], $caParams);

        $dtp = [
            'home_url' => Url::home(),
            'is_temporary' => 1,
            'module_slug' => $moduleSlug,
            'installation_id' => $instalasiId,
            'room_id' => $roomId,
        ];

        $rest_dcms = Yii::$app->docoRest->dcms;
        try {
            $response = $rest_dcms->post('access-data/set-active-workspace-for-jump', [
                'form_params' => $dtp
            ]);
            $responseBody = json_decode($response->getBody(), true);
            if ($responseBody['metadata']['status'] != 200)
                throw new Exception($responseBody['metadata']['message']);

            $active_workspace = $responseBody['response']['active_workspace'];
            $rModuleId = $responseBody['response']['module_id'];
            $dMenu = $responseBody['response']['menus'];
            $aMenu = $responseBody['response']['menu_access'];

            $generatedMenu = MenuController::generateMenuWithMenuData($dMenu);
            $session->set('moduleID', $rModuleId);
            $session->set('active_workspace', $active_workspace);
            $session->set('menu', $generatedMenu);
            $session->set('akses_menu', $aMenu);

            define('SIRS_SESSION_DEFINED', 1);
            return Yii::$app->runAction($caKey, $caParams);
        } catch (Exception $e) {
            return 'error ' . $e->getMessage();
        }
    }
}
