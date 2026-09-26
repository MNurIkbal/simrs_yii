<?php

namespace app\components\Services\Rm;

use Yii;
use app\components\Services\BaseService;
use app\components\Traits\ControllerHelperTrait;

class BaseServiceRm extends BaseService
{
    use ControllerHelperTrait;

    protected $restRm;
    
    public function __construct()
    {
        $this->restRm = Yii::$app->docoRest->rm;
    }
}
