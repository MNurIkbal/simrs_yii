<?php

namespace Doco\penjaminasuransi\actions;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class BaseCurrentAction extends Action
{
    protected $_title = "Informasi pasien rawat jalan bpjs";
    protected $_module = 'penjaminasuransi/informasi-pasien-rajal-bpjs/';
    protected $_restPenjamin;
    
    protected function beforeRun()
    {
        $this->_restPenjamin = Yii::$app->docoRest->penjaminasuransi;
        return true;
    }
}
