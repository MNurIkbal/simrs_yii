<?php
namespace app\modules\v1\behaviours;

use yii\base\Behavior;

class PasienUbahBehaviour extends Behavior{
    public function getActive(){
        return $this->where(['is_active' => true]);
    }
}