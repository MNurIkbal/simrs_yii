<?php

namespace Doco\bedah\controllers;

use app\components\DocoController;
use app\components\Traits\WorklistPatientTrait;
use Yii;

class WorklistController extends DocoController
{
    use WorklistPatientTrait;

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
        $this->restGeneral = Yii::$app->docoRest->bedahsentral;
        $this->modulEndpoint = 'bedah';
    }
}
