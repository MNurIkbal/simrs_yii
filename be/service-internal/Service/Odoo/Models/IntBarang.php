<?php

namespace Integrasi\Service\Odoo\Models;

use Yii;

class IntBarang extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'int_barang';
    }
}
