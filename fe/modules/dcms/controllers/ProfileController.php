<?php 

/**
 * @author Randy Vianda Putra
 * @todo User Management
 * @copyright 24 January 2018 aweutist
 */

namespace Doco\dcms\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\dcms\models\ProfileForm;
use yii\helpers\ArrayHelper;

class ProfileController extends DocoController
{
    
    protected $_title = "Profil User";
    protected $_module = '/dcms/profile';
    protected $_restDcms;

    public function init()
    {
        parent::init();
        $this->_restDcms = Yii::$app->docoRest->dcms;
    }

    public function actionIndex()
    {
        // exit($this->);
        $request = Yii::$app->request;
        if ($request->post()) {
        // print_r($request->post());
            $model = new ProfileForm;
            $model->load($request->post());
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            if ($model->validate()) {
                try {
                    $response = $this->_restDcms->post('profile/change-password?id='.Yii::$app->docoVars->user("id"), [
                        'form_params' => $request->post()
                    ]);

                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                } 
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $model = new ProfileForm;
            $request = Yii::$app->request;
            $title = $this->_title;
            return $this->render('index', get_defined_vars());
        }
    }

    public function actionGetEsignStatus() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'status' => 200,
            'message' => 'Sukses',
            'data' => [],
        ];
        try {
            $response = $this->_restDcms->get('profile/get-esign-status?id='.Yii::$app->docoVars->user("id"));
            $body = json_decode($response->getBody(), true);
            switch ($body['response']['data']['status']) {
                case 'ACTIVATION':
                    $result['data'][] = [
                        'label' => 'Aktivasi',
                        'icon' => 'fa fa-envelope-o',
                        'url' => $body['response']['data']['url'][0],
                    ];
                    break;
                case 'REGISTRATION':
                    $result['data'][] = [
                        'label' => 'Ekyc Esign',
                        'icon' => 'fa fa-file',
                        'url' => $body['response']['data']['url'][0],
                    ];
                    break;
                case 'ACTIVE':
                    $result['data'][] = [
                        'label' => 'Ubah MFA',
                        'icon' => 'fa fa-cogs',
                        'url' => $body['response']['data']['url'][0],
                    ];
                    if(isset($body['response']['data']['url'][1])) {
                        $result['data'][] = [
                            'label' => 'Revoke',
                            'icon' => 'fa fa-ban',
                            'url' => $body['response']['data']['url'][1],
                        ];
                    }
                    break;
                case 'REENROLL':
                    $result['data'][] = [
                        'label' => 'Re-Enroll',
                        'icon' => 'fa fa-file',
                        'url' => $body['response']['data']['url'][0],
                    ];
                    break;
                default:
                    break;
            }
        } catch (RequestException $e) {
            $body = json_decode($e->getResponse()->getBody()->getContents(), true);
            $result['status'] = 500;
            $result['message'] = $body['message'];
        } catch (\Exception $e) {
            $result['status'] = 500;
            $result['message'] = $e->getMessage();
        }
        \Yii::$app->response->statusCode = $result['status'];
        return $result;
    }

    public function actionCallbackStatus() {
        $request = Yii::$app->request;
        $get = $request->get();

        if(isset($get['revoke_id'])) {
            $response = $this->_restDcms->get('profile/update-callback?'.http_build_query([
                'id' => Yii::$app->docoVars->user("id"),
                'type' => 'revoke',
                'revoke_id' => $get['revoke_id'],
                'status' => $get['status'],
            ]));
        } else if (isset($get['register_id'])) {
            $response = $this->_restDcms->get('profile/update-callback?'.http_build_query([
                'id' => Yii::$app->docoVars->user("id"),
                'type' => 'register',
                'registration_id' => $get['register_id'],
                'status' => $get['status'],
            ]));
        } else if (isset($get['tilaka_name']) && isset($get['request_id'])) {
            $response = $this->_restDcms->get('profile/update-callback?'.http_build_query([
                'id' => Yii::$app->docoVars->user("id"),
                'type' => 'activation',
                'tilaka_name' => $get['tilaka_name'],
                'registration_id' => $get['request_id'],
            ]));
        } else if (isset($get['issue_id'])) {
            $response = $this->_restDcms->get('profile/update-callback?'.http_build_query([
                'id' => Yii::$app->docoVars->user("id"),
                'type' => 'reenroll',
                'issue_id' => $get['issue_id'],
            ]));
        }
        return $this->redirect(['/dcms/profile']);
    }
}