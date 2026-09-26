<?php

namespace Integrasi\Service\Odoo\Models;

use Yii;

class IntPegawaiView extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'int_pegawai_v';
    }
}
