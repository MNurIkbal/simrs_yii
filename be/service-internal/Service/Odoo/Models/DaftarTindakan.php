<?php

namespace Integrasi\Service\Odoo\Models;

use Yii;

class DaftarTindakan extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'daftartindakan_m';
    }
}
