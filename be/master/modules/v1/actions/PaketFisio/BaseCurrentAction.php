<?php

namespace app\modules\v1\actions\PaketFisio;

use yii\base\Action;

class BaseCurrentAction extends Action
{
    protected function customResponseTindakan($text = '', $statusCode = 200)
    {
        $result = [
            'status' => $statusCode,
            'title' => $text,
            'text' => $text
        ];
        return $result;
    }
}
