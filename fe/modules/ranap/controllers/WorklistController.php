<?php

namespace Doco\ranap\controllers;

use app\components\DocoController;
use app\components\Traits\WorklistPatientTrait;
use Yii;

class WorklistController extends DocoController
{
    use WorklistPatientTrait;
    protected $allowAction = [
        'process-sync',
        'download-invoice',
    ];
    /**
     * init method
     * 
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function init()
    {
        parent::init();
        $this->restGeneral = Yii::$app->docoRest->ranap;
        $this->_restKasir =  Yii::$app->docoRest->kasir; 
        $this->modulEndpoint = 'ranap';
    }
}
