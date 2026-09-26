<?php
// author : Ardi Pratama

namespace app\modules\ranap\components\widget;

use yii\web\AssetBundle;

class SkoringWidgetAsset extends AssetBundle
{
    public $js = [
        'js/skoringwidget.js'
    ];

    public $css = [
         // CDN lib
        'css/skoringwidget.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];

    public function init()
    {
        // Tell AssetBundle where the assets files are
        $this->sourcePath = __DIR__ . "/assets";
        parent::init();
    }
}