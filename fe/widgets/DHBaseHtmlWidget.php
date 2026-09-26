<?php

namespace app\widgets;

use yii\web\View;
use yii\base\Widget;

class DHBaseHtmlWidget extends Widget
{
    public function registerJsVar($key, $val)
    {
        $this->registerJs("var $key = " . json_encode($val) . ";", View::POS_HEAD);
    }

    protected function removeNewLine($str)
    {
        $removedNewLine = preg_replace('/\s+/', ' ', trim($str));
        return $removedNewLine;
    }
}
