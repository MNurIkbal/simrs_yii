<?php

namespace Integrasi\Service\Odoo\Models;

use Yii;

class Supplier extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'supplier_m';
    }
}
