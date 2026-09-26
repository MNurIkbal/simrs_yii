<?php

namespace app\widgets\kasir;

use app\widgets\DHBaseHtmlWidget;
use app\components\DocoHelpers;
use Yii;
use yii\helpers\ArrayHelper;

class DHBtnCetakDetailInvoice extends DHBaseHtmlWidget
{
    public $id;
    public $disabled = true;

    public function init()
    {
        parent::init();
        if (!$this->id) $this->id = 'cetak-detail-invoice';
        $this->disabled = $this->disabled ? 'true' : 'false';
    }

    public function run()
    {
        // Data-Element Required : 
        // data-pembayaran-id
        // data-pendaftaran-id
        $id = $this->id;
        $disabled = $this->disabled;
        return $this->render('DHBtnCetakDetailInvoice/index', get_defined_vars());
    }
}
