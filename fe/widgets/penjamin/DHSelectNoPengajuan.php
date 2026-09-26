<?php

namespace app\widgets\penjamin;

use app\widgets\DHBaseHtmlWidget;
use app\components\DocoHelpers;
use Yii;
use yii\helpers\ArrayHelper;

class DHSelectNoPengajuan extends DHBaseHtmlWidget
{
    public $id;
    public $name = null;

    public function init()
    {
        parent::init();
        if (!$this->id) $this->id = 'select_widget_no_pengajuan';
    }

    public function run()
    {
        // Data-Element : 
        // data-start
        // data-end
        $id = $this->id;
        $name = $this->name;
        return $this->render('DHSelectNoPengajuan/index', get_defined_vars());
    }
}
