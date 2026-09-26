<?php

namespace Doco\fisioterapi\actions\PemeriksaanRanap;

use yii\base\Action;
use app\components\Traits\ControllerHelperTrait;

class BaseCurrentAction extends Action
{
    protected $_title = 'Pemeriksaan Pasien Rawat Inap';
    
    use ControllerHelperTrait;
}
