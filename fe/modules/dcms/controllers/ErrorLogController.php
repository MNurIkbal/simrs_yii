<?php

namespace Doco\dcms\controllers;

use Yii;
use app\components\DocoController;


class ErrorLogController extends DocoController
{
    protected $_restDcms;

    public function init()
    {
        parent::init();
        // $dataAkses = Yii::$app->session->get('akses_menu');
        // if (!isset($dataAkses['/dcms/error-log']) || (isset($dataAkses['/dcms/error-log']) && empty($dataAkses['/dcms/error-log']))) {
        //     return $this->redirectWithMessage('/dcms', 'Anda tidak mempunyai akses untuk halaman ini');
        // }
        $this->_restDcms = Yii::$app->docoRest->dcms;
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'listProject' => Yii::$app->params->logView,
            'title' => 'Log Error'
        ]);
    }

    /**
     * return log activity by module
     * 
     * @param String $moduleName
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionLogActivity()
    {
        return $this->helper->guzzleExec($this->_restDcms, [
            'url' => 'error-log/log-activity',
            'payload' => [
                'query' => [
                    'module' => Yii::$app->request->get('module', null)
                ]
            ],
            "returnResponse" => true
        ]);
    }
}
