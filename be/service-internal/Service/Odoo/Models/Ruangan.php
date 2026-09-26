<?php

namespace Integrasi\Service\Odoo\Models;

use Yii;

class Ruangan extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ruangan_m';
    }
}
