<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\widgets;

use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;

class DocoActiveField extends \yii\bootstrap\ActiveField
{
    // public function staticControl($options = [])
    // {
    //     $this->adjustLabelFor($options);
    //     $this->parts['{input}'] = Html::activeStaticControl($this->model, $this->attribute, $options);
    //     return $this;
    // }

    public function staticDate($options = [])
    {
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = DocoHtml::activeStaticDate($this->model, $this->attribute, $options);
        return $this;
    }
}
