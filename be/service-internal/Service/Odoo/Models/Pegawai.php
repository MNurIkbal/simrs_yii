<?php

namespace Integrasi\Service\Odoo\Models;

use Yii;

class Pegawai extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pegawai_m';
    }
}
