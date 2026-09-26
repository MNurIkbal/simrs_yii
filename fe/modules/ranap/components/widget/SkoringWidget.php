<?php
// author : Ardi Pratama

namespace app\modules\ranap\components\widget;

use yii\base\Widget;
use yii\helpers\Html;

class SkoringWidget extends Widget
{
    public $model;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
         // Register AssetBundle
        // SkoringWidgetAsset::register($this->getView());
        return $this->render('_skoring', ['product' => $this->model]);
    }
}
?>