<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard pendaftaran
**/

namespace Doco\radiologi\controllers;

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
    protected $_module = '/radiologi/dashboard';
    protected $_restRad;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restRad = Yii::$app->docoRest->radiologi;
    }

    public function actionIndex()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionReadNotif()
    {
        $notifikasi_id = Yii::$app->request->get('notifikasi_id', null);
        if (empty($notifikasi_id)) {
            return $this->responseJson(400, 'ID Notifikasi tidak boleh kosong');
        } else {
            return $this->guzzleExec($this->_restRad, [
                'method' => 'POST',
                'url' => 'allow/read-notif',
                'payload' => [
                    'query' => compact('notifikasi_id')
                ],
                'returnResponse' => true
            ]);
        }
    }

    public function actionPasienNotifikasi()
    {
        $request = Yii::$app->request;
        $rujukan = $request->post('no_rujukan');
        try {
            $response = $this->_restRad->post('inf-pasien-rujukan-rad/validasi-status-pemeriksaan', [
                'query' => [
                    'no_rujukan' => $rujukan
                ]
            ]);
            $response = json_decode($response->getBody(), true);
            $data = $response['response']; 
            $data['data']['pasienkirimunitlain_dec'] = DocoHelpers::encrypt($data['data']['pasienkirimkeunitlain_id']); 
            return DocoHelpers::response($data, 200);
        } catch (\Exception $th) {
            return DocoHelpers::response($th->getMessage(), 500);
        }
    }
}