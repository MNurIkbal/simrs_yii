<?php

namespace Doco\rm\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoConstants;

class MonitoringDokumenController extends DocoController {
    protected $_title = 'Monitoring Dokumen Rekam Medik';
    protected $_module = 'rm';
    protected $_restRm;
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
        $getLookupList = $this->helper->guzzleExec($this->_restRm, [
            'method' => 'GET',
            'url' => 'monitoring-dokumen'
        ]);
        $rakList = $this->helper->guzzleExec($this->_restRm, [
            'method' => 'GET',
            'url' => 'lokasi-rak-rekam-medik/list-rak'
        ]);

        $subrakList = $this->helper->guzzleExec($this->_restRm, [
            'method' => 'GET',
            'url' => 'sub-rak-rekam-medik/list-subrak-all'
        ]);
        $isRM = Yii::$app->docoVars->workspace('instalasi_id') == DocoConstants::INSTALASI_ID_RM;
        $remindId = DocoConstants::MONITORING_RM_REMIND;

        return $this->render('index', [
            'lookupList' => $getLookupList,
            'rakList' => $rakList,
            'subrakList' => $subrakList,
            'isRM' => $isRM,
            'remindId' => $remindId,
        ]);
    }

    public function actionGetDocumentData()
    {
        return $this->helper->guzzleExec($this->_restRm, [
            'method' => 'GET',
            'url' => 'monitoring-dokumen/get-document-list',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }

    public function actionUpdateDocumentStatus()
    {
        return $this->helper->guzzleExec($this->_restRm, [
            'method' => 'POST',
            'url' => 'monitoring-dokumen/update-document-status',
            'payload' => [
                'form_params' => Yii::$app->request->post(),
                'query' => []
            ],
            'returnResponse' => true
        ]);
    }

    public function actionListSubrak()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $lokasirak = $post['depdrop_parents'][0];

        $SubrakRequest = $this->_restRm->get('sub-rak-rekam-medik/list-subrak?lokasi='.$lokasirak);
        $body = json_decode($SubrakRequest->getBody(),TRUE);
        $ddlSubrak = $body['response'];

        $out = [];
        foreach($ddlSubrak as $key => $value) {
            $out[] = [
                        'id' => $key,
                        'name' => $value
                    ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }
}

