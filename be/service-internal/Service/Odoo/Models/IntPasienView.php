<?php

namespace Integrasi\Service\Odoo\Models;

use Yii;

class IntPasienView extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'int_pasien_v';
    }
}
