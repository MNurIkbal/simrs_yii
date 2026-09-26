<?php 

namespace app\components\Services\Kasir;

use Yii;
use app\components\Traits\ControllerHelperTrait;

class BaseCurrentService {

    use ControllerHelperTrait;

    protected $restKasir;

    public function __construct()
    {
        $this->restKasir = Yii::$app->docoRest->kasir;
    }
}