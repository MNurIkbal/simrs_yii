<?php

namespace Integrasi\Service\Odoo\Models;

use Yii;

class KelompokTindakan extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kelompoktindakan_m';
    }
}
