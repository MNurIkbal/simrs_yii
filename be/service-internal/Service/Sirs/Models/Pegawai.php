<?php

namespace Integrasi\Service\Sirs\Models;

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
