<?php

namespace Integrasi\Service\Odoo\Models;

use Yii;

class ServiceCategory extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'servicecategory_m';
    }
}
