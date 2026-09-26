<?php

namespace app\widgets\master;

use app\widgets\DHBaseHtmlWidget;
use app\components\DocoHelpers;
use Yii;
use yii\helpers\ArrayHelper;

class DHSelectBarang extends DHBaseHtmlWidget
{
    public $id;

    public function init()
    {
        parent::init();
        if (!$this->id) $this->id = 'select_widget_barang';
    }

    public function run()
    {
        $id = $this->id;
        return $this->render('DHSelectBarang/index', get_defined_vars());
    }
}
