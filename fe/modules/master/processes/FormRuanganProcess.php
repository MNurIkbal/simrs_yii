<?php

namespace app\modules\master\processes;

class FormRuanganProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        return 'form';
    }
}