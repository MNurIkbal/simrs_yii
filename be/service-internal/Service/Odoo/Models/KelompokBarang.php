<?php

namespace Integrasi\Service\Odoo\Models;

use Yii;

class KelompokBarang extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kelompokbarang_m';
    }
}
