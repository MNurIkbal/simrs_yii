<?php

namespace app\components\Services\Laboratorium;

use Yii;
use app\components\Services\BaseService;
use app\components\Traits\ControllerHelperTrait;

class BaseCurrentService extends BaseService
{
    use ControllerHelperTrait;

    protected $restLab;
    
    public function __construct()
    {
        $this->restLab = Yii::$app->docoRest->laboratorium;
    }
}
