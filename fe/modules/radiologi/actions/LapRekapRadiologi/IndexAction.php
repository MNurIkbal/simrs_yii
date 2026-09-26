<?php

namespace Doco\radiologi\actions\LapRekapRadiologi;

use Yii;
use yii\base\Action;
use app\components\DHtml;

class IndexAction extends Action
{
   public function run()
   {
      $module = $this->controller->_module;
      $titleMenu = DHtml::getTitleMenu();
      $title = !empty($titleMenu) ? $titleMenu : $this->controller->_title;
      return $this->controller->render('index', get_defined_vars());
   }
}
