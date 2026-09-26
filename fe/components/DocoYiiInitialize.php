<?php

/**
 * @author : 
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components;

use Yii;

trait DocoYiiInitialize
{
    public function initialize()
    {
        $this->_requestData = Yii::$app->request;
    }
}