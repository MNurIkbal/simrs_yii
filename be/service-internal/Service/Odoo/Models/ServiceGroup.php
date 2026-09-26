<?php

namespace Integrasi\Service\Odoo\Models;

use Yii;

class ServiceGroup extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'servicegroup_m';
    }
}
