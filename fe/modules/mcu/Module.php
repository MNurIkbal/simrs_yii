<?php


namespace Doco\mcu;

class Module extends \yii\base\Module
{
   
    public $controllerNamespace = 'Doco\mcu\controllers';

    public function init()
    {
       parent::init();
    }
}