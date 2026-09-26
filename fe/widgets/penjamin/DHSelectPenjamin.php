<?php

namespace app\widgets\penjamin;

use app\widgets\DHBaseHtmlWidget;
use app\components\DocoHelpers;
use Yii;
use yii\helpers\ArrayHelper;

class DHSelectPenjamin extends DHBaseHtmlWidget
{
    public $id;

    public function init()
    {
        parent::init();
        if (!$this->id) $this->id = 'select_widget_penjamin';
    }

    public function run()
    {
        $id = $this->id;
        return $this->render('DHSelectPenjamin/index', get_defined_vars());
    }
}
