<?php 

namespace app\components\Services\Igd;

use Yii;
use app\components\Traits\ControllerHelperTrait;

class BaseCurrentService {

    use ControllerHelperTrait;

    protected $_restRanap;

    public function __construct()
    {
        $this->_restRanap = Yii::$app->docoRest->ranap;
    }
}