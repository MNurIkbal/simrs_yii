<?php 

namespace app\components\Services\Radiologi;

use Yii;
use app\components\Traits\ControllerHelperTrait;

class BaseCurrentService {

    use ControllerHelperTrait;

    protected $_restRad;

    public function __construct()
    {
        $this->_restRad = Yii::$app->docoRest->radiologi;
    }
}