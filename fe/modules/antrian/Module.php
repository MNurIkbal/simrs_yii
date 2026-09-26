<?php

namespace Doco\antrian;

class Module extends \yii\base\Module
{
   
    public $controllerNamespace = 'Doco\antrian\controllers';
    public $enableAutoLogin = true;

    public function init()
    {
       parent::init();
    }
}