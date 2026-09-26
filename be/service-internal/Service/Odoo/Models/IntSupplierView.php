<?php

namespace Integrasi\Service\Odoo\Models;

use Yii;

class IntSupplierView extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'int_supplier_v';
    }
}