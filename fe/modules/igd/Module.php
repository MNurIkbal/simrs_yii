<?php

namespace Doco\igd;

use Yii;
use app\assets\PelayananAsset;

class Module extends \yii\base\Module
{
   
    public $controllerNamespace = 'Doco\igd\controllers';

    public function init()
    {
       PelayananAsset::register(Yii::$app->view);
       parent::init();
    }
}