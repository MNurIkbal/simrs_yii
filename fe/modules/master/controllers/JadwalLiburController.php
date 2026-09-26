<?php

namespace Doco\master\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoController;
use app\assets\CalenderAssets;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoHelpers;
use app\components\DHtml;

use app\modules\master\models\JadwalLiburForm;
use app\modules\master\models\HapusJadwalLiburForm;

class JadwalLiburController extends DocoController 
{
    protected $_title = "Jadwal Libur";
    protected $_module = 'master/jadwal-libur/';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        CalenderAssets::register(Yii::$app->view);
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        $baseTitle = DHtml::getTitleMenu();
        $title = !empty($baseTitle) ? $baseTitle : $this->_title;
        $titleUbah = !empty($baseTitle) ? "Ubah " . $baseTitle : "Ubah " . $this->_title;
        $titleDelete = !empty($baseTitle) ? "Hapus " . $baseTitle : "Hapus " . $this->_title;
        $model = new JadwalLiburForm;
        $modelDelete = new HapusJadwalLiburForm;
        $username = Yii::$app->docoVars->user('nama');

        $modelDelete->username = $username;
        return $this->render('index', compact('title', 'model', 'modelDelete', 'titleDelete', 'titleUbah'));
    }

    public function actionGetData()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $body = $this->helper->guzzleExec($this->_restMaster, [
                'url' => 'jadwal-libur/index',
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'start' => $request->post('start'),
                        'end' => $request->post('end'),
                    ]
                ]
            ]);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCreate() 
    {
        $request = Yii::$app->request;
        $model = new JadwalLiburForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = DHtml::getTitleMenu();
        $title = !empty($title) ? "Tambah " . $title : "Tambah " . $this->_title;

        if($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->helper->guzzleExec($this->_restMaster, [
                    'url' => 'jadwal-libur/create',
                    'method' => 'post',
                    'payload' => [
                        'form_params' => $model->attributes
                    ]
                ]);

                $statusCode = ArrayHelper::getValue($response, 'httpStatusCode', 200);
                return $this->helper->response([
                    'response' => $response
                ], $statusCode);
            }

            return $this->helper->response($model->errors,422,'JadwalLiburForm');
        } else {
            return $this->renderAjax('_form', compact('model','formName', 'title'));
        }
    }

    public function actionUpdate($id) 
    {
        $request = Yii::$app->request;
        $model = new JadwalLiburForm;
        $model->load($request->post());
        if ($model->validate()) {
            $response = $this->helper->guzzleExec($this->_restMaster, [
                'url' => 'jadwal-libur/update',
                'method' => 'POST',
                'payload' => [
                    'query' => [
                        'id' => $id,
                    ],
                    'form_params' => $model->attributes
                ]
            ]);

            $statusCode = ArrayHelper::getValue($response, 'httpStatusCode', 200);
            return $this->helper->response([
                'response' => $response
            ], $statusCode);
        }

        return $this->helper->response($model->errors,422,'JadwalLiburForm');
    }

    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = new HapusJadwalLiburForm();
        $model->load($request->post());
        if ($model->validate()) {
            $response = $this->helper->guzzleExec($this->_restMaster, [
                'url' => 'jadwal-libur/delete',
                'method' => 'delete',
                'payload' => [
                    'query' => [
                        'jadwallibur_id' => $id,
                    ],
                    'form_params' => $model->attributes
                ]
            ]);

            $statusCode = ArrayHelper::getValue($response, 'httpStatusCode', 200);
            return $this->helper->response([
                'response' => $response
            ], $statusCode);
        }

        return $this->helper->response($model->errors,422,'HapusJadwalLiburForm');
    }
}