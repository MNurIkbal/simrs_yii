<?php

namespace app\modules\fisioterapi\models;

class BaseFormModel extends \yii\base\Model
{
    public static function getFormName()
    {
        $tempModel = new self();
        return substr(strrchr(get_class($tempModel), "\\"), 1);
    }
}
