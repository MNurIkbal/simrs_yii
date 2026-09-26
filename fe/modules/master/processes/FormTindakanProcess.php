<?php

namespace app\modules\master\processes;

class FormTindakanProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        return 'components/tindakan/form';
    }
}