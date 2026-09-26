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
use app\components\Notifications\AmbulanceNotification;
use app\controllers\MenuController;
use yii\helpers\Json;
use app\components\DebugHelper;
use Exception;
use app\modules\igd\models\TriaseForm;
use app\components\DocoConstants;
use GuzzleHttp\Client;

class SiteController extends DocoController
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
                        'actions' => ['login', 'error', 'set-method', 'antrian', 'triage', 'get-pegawai', 'save-triage'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'notif', 'set-method', 'antrian', 'error', 'read-notification'],
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
     * @inheritdoc
     */
    // public function actions()
    // {
    //     return [
    //         'error' => [
    //             'class' => 'yii\web\ErrorAction',
    //         ],
    //         'captcha' => [
    //             'class' => 'yii\captcha\CaptchaAction',
    //             'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
    //         ],
    //     ];
    // }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        //Dump JWT token to app log.
        $jToken = Yii::$app->session->get('token');
        // Yii::error([
        //     'jwt' => $jToken
        // ]);

        $rest = Yii::$app->docoRest->dcms;
        $userIdentity = Yii::$app->session->get('user_identity');

        if (Yii::$app->session->get('auto_direct')) {
            if (in_array('Pendaftaran Direct', $userIdentity['roles'])) {
                Yii::$app->request->setBodyParams([
                    'moduleID' => 24,
                    'instalasiIndex' => 0,
                    'instalasiID' => 10,
                    'roomIndex' => 4,
                    'roomID' => 5
                ]);
                Yii::$app->session->set('auto_direct',false);
                $this->actionSetMethod();
            }
        }

        if ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
            $response = $rest->get('allow/get-soap', [
                'query' => [
                    'id_pegawai' => $userIdentity['id_pegawai'],
                    'kelompokpegawai_id' => $userIdentity['kelompokpegawai_id']
                ],
            ]);
            $body = json_decode($response->getBody(), true);
        }

        $module = empty(Yii::$app->session->get('workspace')) ? [] : Yii::$app->session->get('workspace');
        $notifications = empty(Yii::$app->session->get('notifications')) ? ['records' => [], 'totalUnread' => 0, 'users' => null] : Yii::$app->session->get('notifications');
        $draftSoap = empty($body['response']['draftSoap']) ? ['rj' => 0, 'rd' => 0, 'ri' => 0] : $body['response']['draftSoap'];
        $draftRm = empty($body['response']['draftRm']) ? ['ri' => 0] : $body['response']['draftRm'];
        $module_disable = empty(Yii::$app->session->get('disable_workspace')) ? [] : Yii::$app->session->get('disable_workspace');
        $kelompok_pegawai = $userIdentity['kelompokpegawai_id'];

        // $active_workspace = Yii::$app->session->get('active_workspace');
        $url = Yii::$app->docoVars->workspace('url');
        if (trim($url) !== '-') {
            $this->redirect(Yii::$app->docoVars->workspace('url'));
        }

        foreach ($module as $key => $value) {
            $name = $value['name'];
            $module[$key]['name'] = explode(" ", $name)[0];
            $module[$key]['name2'] = str_replace($module[$key]['name'] . " ", "", $name);
        }

        foreach ($module_disable as $key => $value) {
            $name = $value['modul_nama'];
            $module_disable[$key]['name'] = explode(" ", $name)[0];
            $module_disable[$key]['name2'] = str_replace($module_disable[$key]['name'] . " ", "", $name);
        }


        $extend_module_count = 46 - (count($module) + count($module_disable));
        return $this->render('index', get_defined_vars());
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $title = 'Login';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        $userId = Yii::$app->user->identity->loginpemakai_id;
        $loginpemakai_id = Yii::$app->docoVars->user("id");
        $rest = Yii::$app->docoRest->master;
        $requests = $rest->get('allow/unset-loket?loginpemakai_id=' . $loginpemakai_id);
        $response = json_decode($requests->getBody(), true);
        $listResponses = $response['response'];
        Yii::$app->cache->set("closing-kasir-{$userId}", []);
        Yii::$app->cache->delete("loket-{$userId}", []);
        Yii::$app->cache->delete("penunjang-loket-{$userId}", []);
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        $request = \Yii::$app->request;
        $isAjax = $request->isAjax ?: false;
        $codeHttp = $exception->statusCode;
        if (
            $exception instanceof \yii\web\NotFoundHttpException
            || $exception instanceof \yii\web\HttpException
        ) {
            $message = $exception->getMessage();
        } else if ($exception instanceof \app\components\HandlerExceptions) {
            $message = $exception->messagesError;
            $codeHttp = $exception->getCode();
        }
        if ($isAjax) {
            return DocoHelpers::response([
                'response' => [
                    'title' => 'Proses Gagal!',
                    'text' => $message,
                ]
            ], $codeHttp);
        }
        return $this->render('error', [
            'exception' => $exception,
            'message' => $message
        ]);
    }

    /**
     * Set method and workspace
     * Modified to enable shared session matching. Move active workspace data handling to backend.
     * 
     * @modify ali.padilah@docotel.com
     * @modify rinardi@docotel.com
     * @return boolean
     */
    public function actionSetMethod()
    {
        $post = Yii::$app->request->post();

        $session = Yii::$app->session;
        $workspace = $session->get('workspace');
        //$active_workspace = $session->get('active_workspace');

        $is_unset = false;
        $unset_all = false;
        if (!empty($post['unset'])) {
            $is_unset = ($post['unset'] == 'home');
            $unset_all = ($post['unset'] == 'true');
        }
        if ($unset_all)
            $is_unset = true;

        $dtp = [
            'home_url' => Url::home(),
            'is_unset' => ($is_unset ? 1 : 0),
            'unset_all' => ($unset_all ? 1 : 0),
            'module_id' => ($is_unset ? 0 : (!empty($post['moduleID']) ? $post['moduleID'] : 0)),
            'instalasi_id' => ($is_unset ? 0 : (!empty($post['instalasiID']) ? $post['instalasiID'] : 0)),
            'instalasi_index' => ($is_unset ? 0 : ($post['instalasiIndex'] ? $post['instalasiIndex'] : 0)),
            'room_id' => ($is_unset ? 0 : ($post['roomID'] ? $post['roomID'] : 0)),
            'room_index' => ($is_unset ? 0 : ($post['roomIndex'] ? $post['roomIndex'] : 0))
        ];

        $loaded_ws = $session->get('active_workspace');
        if (($dtp['module_id'] == 0) && ($loaded_ws !== null))
            $dtp['module_id'] = $loaded_ws['modul_id'];
        if (($dtp['instalasi_id'] == 0) && ($loaded_ws !== null))
            $dtp['instalasi_id'] = $loaded_ws['instalasi_id'];

        //DebugHelper::dump($dtp);

        $rest_dcms = Yii::$app->docoRest->dcms;
        try {
            $response = $rest_dcms->post('access-data/set-active-workspace', [
                'form_params' => $dtp
            ]);

            $responseBody = json_decode($response->getBody(), true);
            if ($responseBody['metadata']['status'] != 200)
                throw new Exception($responseBody['metadata']['message']);
            //DebugHelper::dump($responseBody);

            $active_workspace = $responseBody['response']['active_workspace'];
            $rModuleId = $responseBody['response']['module_id'];
            $dMenu = $responseBody['response']['menus'];
            $aMenu = $responseBody['response']['menu_access'];

            if ($is_unset) {
                $session->set('active_workspace', $active_workspace);
                if ($unset_all) {
                    $session->set('menu', []);
                    $session->set('akses_menu', []);
                }
            } else {
                $active_workspace['url'] = (Url::home() . $active_workspace['url']);

                $session->set('moduleID', $rModuleId);
                $session->set('active_workspace', $active_workspace);

                $ambulanceNotificationCache = Yii::$app->cache->get('ambulanceNotification');
                $laboratoriumNotificationCache = Yii::$app->cache->get('laboratoriumNotification');
                $radiologiNotificationCache = Yii::$app->cache->get('radiologiNotification');
                if (strtolower($active_workspace['modul_alias']) == 'ambulan' || empty($ambulanceNotificationCache)) {
                    $notification = $this->guzzleExec(Yii::$app->docoRest->ambulan, [
                        'url' => 'allow/ambulance-notification'
                    ]);
                    Yii::$app->cache->set('ambulanceNotification', $notification);
                }

                if (strtolower($active_workspace['modul_alias']) == 'laboratorium' || empty($laboratoriumNotificationCache)) {
                    $notification = $this->guzzleExec(Yii::$app->docoRest->laboratorium, [
                        'url' => 'allow/init-bucket-notification'
                    ]);
                }

                if (strtolower($active_workspace['modul_alias']) == 'radiologi' || empty($radiologiNotificationCache)) {
                    $notification = $this->guzzleExec(Yii::$app->docoRest->radiologi, [
                        'url' => 'allow/init-bucket-notification'
                    ]);
                }

                $generatedMenu = MenuController::generateMenuWithMenuData($dMenu);

                $session->set('menu', $generatedMenu);
                $session->set('akses_menu', $aMenu);

                return true;
            }
        } catch (Exception $e) {
            return 'error ' . $e->getMessage();
        }


        /*if (!empty($post['unset'])) {
            $unset_all = $post['unset'];

            if ($unset_all == "false") {
                $roomID = ($post['roomID']) ? $post['roomID'] : 0;
                $roomIdx = ($post['roomIndex']) ? $post['roomIndex'] : 0;
                $instalasiIdx = ($post['instalasiIndex']) ? $post['instalasiIndex'] : 0;

                $active_workspace['ruangan_index'] = $post['roomIndex'];
                $active_workspace['ruangan_id'] = $post['roomID'];
                $active_workspace['ruangan_name'] = $workspace[$active_workspace['modul_id']]['installation'][$instalasiIdx]['rooms'][$roomIdx]['name'];

                DebugHelper::dump($active_workspace);
                $session->set('active_workspace', $active_workspace);
            } else {
                foreach ($active_workspace as $key => $value) {
                    $active_workspace[$key] = "";
                }

                DebugHelper::dump($active_workspace);
                $session->set('active_workspace', $active_workspace);

                $session->set('menu', []);
                $session->set('akses_menu', []);
            }
        } else {
            $modulID = ($post['moduleID']) ? $post['moduleID'] : 0;
            $instalasiID = ($post['instalasiID']) ? $post['instalasiID'] : 0;
            $instalasiIdx = ($post['instalasiIndex']) ? $post['instalasiIndex'] : 0;
            $roomID = ($post['roomID']) ? $post['roomID'] : 0;
            $roomIdx = ($post['roomIndex']) ? $post['roomIndex'] : 0;

            $active_workspace['modul_id'] = $post['moduleID'];
            $active_workspace['url'] = Url::home() . preg_replace('/^\//i', '', $workspace[$modulID]['url']);
            $active_workspace['modul_name'] = $workspace[$modulID]['name'];
            $active_workspace['modul_alias'] = str_replace("Modul ", "", $workspace[$modulID]['name']);
            $active_workspace['modul_icon'] = $workspace[$modulID]['icon'];
            $active_workspace['instalasi_id'] = $post['instalasiID'];
            $active_workspace['instalasi_index'] = (string) $instalasiIdx;
            $active_workspace['instalasi_name'] = $workspace[$modulID]['installation'][$instalasiIdx]['name'];
            $active_workspace['ruangan_id'] = $post['roomID'];
            $active_workspace['ruangan_index'] = (string) $roomIdx;
            $active_workspace['ruangan_name'] = $workspace[$modulID]['installation'][$instalasiIdx]['rooms'][$roomIdx]['name'];

            $active_workspace['ruangan_lain'] = $workspace[$modulID]['installation'][$instalasiIdx]['rooms'];
            $active_workspace['loket'] = null;            

            $session->set('moduleID', $modulID);

            DebugHelper::dump($active_workspace);
            $session->set('active_workspace', $active_workspace);

            $ambulanceNotificationCache = Yii::$app->cache->get('ambulanceNotification');
            $laboratoriumNotificationCache = Yii::$app->cache->get('laboratoriumNotification');
            if (strtolower($active_workspace['modul_alias']) == 'ambulan' || empty($ambulanceNotificationCache)) {
                $notification = $this->guzzleExec(Yii::$app->docoRest->ambulan, [
                    'url' => 'allow/ambulance-notification'
                ]);
                Yii::$app->cache->set('ambulanceNotification', $notification);
            }
            if (strtolower($active_workspace['modul_alias']) == 'laboratorium' || empty($laboratoriumNotificationCache)) {
                $notification = $this->guzzleExec(Yii::$app->docoRest->laboratorium, [
                    'url' => 'allow/init-bucket-notification'
                ]);
            }

            $farmasiNotificationCache = Yii::$app->cache->get('farmasiNotification');
            if (strtolower($active_workspace['modul_alias']) == 'farmasi' || empty($farmasiNotificationCache)) {
                try {
                    $notification = $this->guzzleExec(Yii::$app->docoRest->apotek, [
                        'url' => 'allow/update-notif'
                    ]);
                } catch(\Exception $e) {
                    $notification = ['totalUnread'=>0,'record'=>[]];
                }
            }

            MenuController::generateMenu($post['moduleID']);

            return true;
        }*/
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAntrian()
    {
        $title = 'Login';
        return $this->render('antrian', get_defined_vars());
    }

    public function actionCekUser()
    {
        $model = new LoginForm();
        if (Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $model->username = $post['nama_pemakai'];
            $model->password = $post['katakunci_pemakai'];
            if ($response = $model->userValidasi()) {
                if (isset($response['metadata']['status'])) {
                    if ($response['metadata']['status'] == 422) {
                        $rest = isset($response['response']['data']['password'][0]) ? $response['response']['data']['password'][0] : true;
                        return $rest;
                    }
                }
                return 'sukses';
            } else {
                $err = $model->getErrors();
                return $err['password'][0];
            }
        }
    }

    /**
     * Set language
     * @modify ali.padilah@docotel.com
     * @return boolean
     */
    public function actionSetLanguage()
    {
        $post = Yii::$app->request->post();
        $cookies = Yii::$app->response->cookies;

        $cookies->add(new \yii\web\Cookie([
            'name' => 'lang',
            'value' => empty($post["lang"]) ? 'ID' : $post["lang"],
            'expire' => time() + 86400 * 365,
        ]));

        return true;
    }

    /**
     * Set user identity
     * @modify ali.padilah@docotel.com
     * @return boolean
     */
    public function actionSetIdentity()
    {
        $post = Yii::$app->request->post();
        $cookies = Yii::$app->response->cookies;

        $cookies->add(new \yii\web\Cookie([
            'name' => 'lang',
            'value' => empty($post["lang"]) ? 'ID' : $post["lang"],
            'expire' => time() + 86400 * 365,
        ]));

        return true;
    }

    /**
     * This function will read the notification
     * 
     * @param String $notifikasi_id
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionReadNotification()
    {
        $notifikasi_id = Yii::$app->request->post('notifikasi_id', null);
        if (!empty($notifikasi_id)) {
            return $this->guzzleExec(Yii::$app->docoRest->dcms, [
                'url' => 'allow/read-notification',
                'method' => 'POST',
                'payload' => [
                    'form_params' => compact('notifikasi_id')
                ],
                'returnResponse' => true
            ]);
        } else {
            return $this->responseJson(400, 'Notifikasi tidak boleh kosong');
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
    public function actionRefreshNotification()
    {
        return $this->guzzleExec(Yii::$app->docoRest->dcms, [
            'url' => 'allow/refresh-notification',
            'method' => 'POST',
            'returnResponse' => true
        ]);
    }

    public function actionTriage()
    {
        $model = new TriaseForm;
        $response = $this->guzzleExec(Yii::$app->docoRest->igd, [
            'url' => 'allow/bundle-data-triage'
        ]);
        $data_listgcs = (isset($response['dataGcs']['data-listgcs']) && !empty($response['dataGcs']['data-listgcs'])) ? $response['dataGcs']['data-listgcs'] : [];
        $dataBed = isset($response['dataBed']) ? $response['dataBed'] : [];
        $configData = $this->getConfig('formulir_triase');
        $configRules = $this->getConfig('konfig_formulir_triase');
        return $this->render('triage/index', get_defined_vars());
    }

    public function actionGetPegawai($nip)
    {
        return $this->guzzleExec(Yii::$app->docoRest->igd, [
            'url' => 'allow/get-pegawai',
            'payload' => [
                'query' => compact('nip')
            ],
            'returnResponse' => true
        ]);
    }

    public function actionSaveTriage()
    {
        $payloadData = Yii::$app->request->post('TriaseForm');
        $payloadData['is_doa'] = Yii::$app->request->get('is_doa', false);
        // Check whether pegawai is perawat or dokter
        if ($payloadData['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
            $payloadData['dokter_id'] = $payloadData['pegawai_id'];
        } else {
            $payloadData['perawat_id'] = $payloadData['pegawai_id'];
        }
        return $this->guzzleExec(Yii::$app->docoRest->igd, [
            'url' => 'allow/save-triage',
            'returnResponse' => true,
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'formdata' => $payloadData
                ]
            ]
        ]);
    }

    public function actionSupersetGuestToken($key)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        return Yii::$app->superset->requestGuestToken($key, []);
    }

    public function actionSetDirectMenu() {
        $request = Yii::$app->request;

        $module_id = $request->get('module_id');
        $instalasi_index = $request->get('instalasi_index');
        $instalasi_id = $request->get('instalasi_id');
        $room_index = $request->get('room_index');
        $room_id = $request->get('room_id');

        Yii::$app->request->setBodyParams([
            'moduleID' => $module_id,
            'instalasiIndex' => $instalasi_index,
            'instalasiID' => $instalasi_id,
            'roomIndex' => $room_index,
            'roomID' => $room_id
        ]);
        Yii::$app->session->set('auto_direct',false);

        $this->actionSetMethod();

        $url = Yii::$app->docoVars->workspace('url');
        if (trim($url) !== '-') {
            $this->redirect(Yii::$app->docoVars->workspace('url'));
        }
    }
}
