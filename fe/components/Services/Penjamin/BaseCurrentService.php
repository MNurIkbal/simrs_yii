<?php

namespace app\components\Services\Penjamin;

use Yii;
use app\components\Services\BaseService;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\helpers\Html;
use app\components\Traits\ControllerHelperTrait;

class BaseCurrentService extends BaseService
{
    use ControllerHelperTrait;

    protected $restPenjaminAsuransi;
    
    public function __construct()
    {
        $this->restPenjaminAsuransi = Yii::$app->docoRest->penjaminasuransi;
    }
}
