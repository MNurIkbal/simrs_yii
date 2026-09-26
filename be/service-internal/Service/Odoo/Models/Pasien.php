<?php

namespace Integrasi\Service\Odoo\Models;

use Yii;

class Pasien extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasien_m';
    }
}
