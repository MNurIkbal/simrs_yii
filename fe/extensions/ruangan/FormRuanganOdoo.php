<?php

namespace app\extensions\ruangan;

class FormRuanganOdoo extends \app\components\DocoBaseProcessExtension
{

    protected function processFlow($controller)
    {
        return 'form-odoo';
    }
}