<?php

namespace Integrasi\Service\Odoo\Models;

use Yii;

class IntServiceCategoryView extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'int_servicecategory_v';
    }
}
