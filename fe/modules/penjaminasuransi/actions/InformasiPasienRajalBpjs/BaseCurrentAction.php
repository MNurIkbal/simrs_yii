<?php

namespace Doco\penjaminasuransi\actions\InformasiPasienRajalBpjs;

use Yii;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\penjaminasuransi\actions\BaseCurrentAction as Action;

class BaseCurrentAction extends Action
{
    protected $_title;
    protected $_restPenjamin;
    protected $_module = '/penjaminasuransi/informasi-pasien-rajal-bpjs/';

    protected function beforeRun()
    {
        $this->_title = Yii::t('fe', 'Informasi Pasien Rajal BPJS');
        $this->_restPenjamin = Yii::$app->docoRest->penjaminasuransi;
        return true;
    }
}
