<?php

namespace app\modules\kasir\components\widget;

use yii\base\Widget;
use yii\helpers\Html;

class CloseBillWidget extends Widget
{
   public function init()
   {
      parent::init();
   }

   public function run()
   {
      $this->registerAssets();

      return Html::button(
         '<b><i class="fa fa-key"></i></b> Lock Bill',
         [
            'class' => 'btn btn-info btn-labeled btn-xs data-add btn-toolbar close-bill',
            'data-options' => 'click',
        ]);
   }

   public function registerAssets()
   {
      $this->getView()->registerJs($this->render('index.js'), \yii\web\View::POS_END);
   }
}
?>