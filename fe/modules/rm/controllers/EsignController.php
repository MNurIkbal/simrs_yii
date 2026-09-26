<?php

/**
 * setup config application
 *
 * @author ali.padilah@docotel.com
 * @return session json
 */

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\EsignHelpers;


class EsignController extends DocoController
{
    protected $_title = "Esign";
    protected $_module = '/rm/esign';
    protected $_restMaster;
    protected $_restRm;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionCallbackTilaka()
    {
        $get = Yii::$app->request->get();
        $response = $this->_restRm->post('tilaka/execute-sign?',[
            'form_params' => $get,
        ]);
        return $this->redirect('/rm/esign/list-sign');
    }

    public function actionSign()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        $test = $this->_restRm->post('esign/sign', [
            'form_params' => $post,
        ]);
        $response = json_decode($test->getBody(), true);
        $result = $response['response'];
        return $result;
    }


    public function actionListSign()
    {
        $title = $this->_title;
        $this->_restRm->post('esign/check-delayed');

        return $this->render('list_sign', get_defined_vars());
    }

    public function actionGetList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = Yii::$app->request->get();
        $httpQuery = http_build_query($get);

        $test = $this->_restRm->get('esign/get-list?'.$httpQuery);
        $response = json_decode($test->getBody(), true);
        $result = $response['response'];

        return $result;
    }

    public function actionPreview()
    {
        $get = Yii::$app->request->get();
        EsignHelpers::previewEsign($get);
    }
}