<?php

/**
 * setup config application
 *
 * @author ali.padilah@docotel.com
 * @return session json
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;


class DashboardController extends DocoController
{
    protected $_title = "Dashboard";
    protected $_module = '/antrian/dashboard';
    protected $_restMaster;
    protected $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        return $this->render('index', get_defined_vars());
    }

    public function actionLayarAntrian($layar)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($layar);

        $response = $this->_restMaster->get('layarantrian/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $infoLayarAntrian = $body['response'];

        $title = $infoLayarAntrian['layarantrian_judul'];

        return $this->render('layarantrian', get_defined_vars());
    }

    public function actionReadNotif() {
        $notifikasi_id = Yii::$app->request->get('notifikasi_id', null);
        if (empty($notifikasi_id)) {
            return $this->responseJson(400, 'ID Notifikasi tidak boleh kosong');
        } else {
            return $this->guzzleExec($this->_restApotek, [
                'method' => 'POST',
                'url' => 'allow/read-notif',
                'payload' => [
                    'query' => compact('notifikasi_id')
                ],
                'returnResponse' => true
            ]);
        }
    }

    public function actionClearNotifications() {
        $judulnotifikasi = Yii::$app->request->get('judulnotifikasi', null);
        if (empty($judulnotifikasi)) {
            return $this->responseJson(400, 'Judul Notifikasi tidak boleh kosong');
        } else {
            return $this->guzzleExec($this->_restApotek, [
                'method' => 'GET',
                'url' => 'allow/clear-notifications',
                'payload' => [
                    'query' => compact('judulnotifikasi')
                ],
                'returnResponse' => true
            ]);
        }
    }

    public function actionLoadMoreNotif() {
        $request = Yii::$app->request;
        $last_id = $request->get('last_id', null);
        $limit = $request->get('limit', null);

        $response = $this->_restApotek->get('allow/get-more-notifications?last_id='.$last_id.'&limit='.$limit);
        $body = json_decode($response->getBody(), TRUE);
        $notif = $body['response'];

        return json_encode($notif);
    }
}