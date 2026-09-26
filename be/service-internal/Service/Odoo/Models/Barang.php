<?php

namespace Integrasi\Service\Odoo\Models;

use Yii;

class Barang extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'barang_m';
    }
}
