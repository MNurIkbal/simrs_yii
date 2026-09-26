<?php

namespace Doco\fisioterapi\actions\Pemeriksaan;

use yii\base\Action;
use app\components\Traits\ControllerHelperTrait;

class BaseCurrentAction extends Action
{
    protected $_title = 'Pemeriksaan Pasien Rawat Jalan';
    protected $_rest;

    use ControllerHelperTrait;
}
