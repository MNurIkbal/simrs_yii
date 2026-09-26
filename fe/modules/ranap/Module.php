<?php

namespace Doco\ranap;

use Yii;
use app\assets\PelayananAsset;

class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'Doco\ranap\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        PelayananAsset::register(Yii::$app->view);
        parent::init();
    }
}
