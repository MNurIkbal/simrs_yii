<?php 

namespace app\components\Services;

use Yii;
use app\components\DocoHelpers;
use app\components\Traits\ControllerHelperTrait;

class BaseService 
{
   use ControllerHelperTrait;
   protected $helper;

   public function init()
   {
      $this->helper = new DocoHelpers;
   }
}