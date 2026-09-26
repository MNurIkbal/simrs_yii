<?php

namespace Doco\laboratorium\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;

class DashboardController extends DocoController
{
    protected $_title = "Dashboard";
    protected $_module = '/laboratorium/dashboard';

    protected $_restMaster;
    protected $_restLab;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restLab = Yii::$app->docoRest->laboratorium;
    }

    public function actionIndex()
    {
        return $this->render('index', get_defined_vars());
    }

    /**
     * This API will update status is_read
     * 
     * @param String $notifikasi_id
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionReadNotif()
    {
        $notifikasi_id = Yii::$app->request->get('notifikasi_id', null);
        if (empty($notifikasi_id)) {
            return $this->responseJson(400, 'ID Notifikasi tidak boleh kosongggggg');
        } else {
            return $this->guzzleExec($this->_restLab, [
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
            $response = $this->_restLab->post('inf-pasien-rujukan-lab/validasi-status-pemeriksaan', [
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