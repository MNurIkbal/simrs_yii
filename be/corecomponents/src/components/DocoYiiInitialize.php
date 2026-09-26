<?php

/**
 * @author : Setyabudi Dwisandi Arifin
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\components;

use Yii;
use Doco\components\DocoMessages;

trait DocoYiiInitialize
{
    public function initialize()
    {
        $this->_requestData = Yii::$app->request;
        $this->_db = Yii::$app->db;
        $this->_errorValidation = DocoMessages::KEY_ERR_SYSTEM;
        $this->_error = DocoMessages::KEY_ERR_VALIDATION;
    }
}