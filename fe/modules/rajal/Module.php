<?php

/**
 * @Author: afil
 * @Date:   2018-01-03 14:31:30
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-03 14:31:48
 */

namespace Doco\rajal;

use Yii;
use app\assets\PelayananAsset;

class Module extends \yii\base\Module
{
   
    public $controllerNamespace = 'Doco\rajal\controllers';

    public function init()
    {
       PelayananAsset::register(Yii::$app->view);
       parent::init();
    }
}