<?php

namespace app\modules\ranap\components;

class AsesmenDynamicModel extends \yii\base\DynamicModel {

    protected $_labels;

    public function setAttributeLabels($labels){
        $this->_labels = $labels;
    }

    // public function getAttributeLabel($name){
    //     return $this->_labels[$name] ?? $name;
    // }
}