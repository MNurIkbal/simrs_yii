<?php

namespace app\components\actions;

use Yii;
use yii\web\ViewAction;

class ExtensionAction extends ViewAction
{
	public $key = '';
    public function run() {
        return Yii::$app->docoPlugin->execute($this->controller,$this->key);
    }
}