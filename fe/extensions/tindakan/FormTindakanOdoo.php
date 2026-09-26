<?php

namespace app\extensions\tindakan;

class FormTindakanOdoo extends \app\components\DocoBaseProcessExtension
{

    protected function processFlow($controller)
    {
        return 'components/tindakan/form-odoo';
    }
}