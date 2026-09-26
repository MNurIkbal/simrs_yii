<?php

namespace Integrasi\Service\Sirs\Models;

use Integrasi\Components\ActiveRepositories;

class LaporanPasienFisioterapiRanapV extends ActiveRepositories
{
    public static function tableName()
    {
        return 'lappasienfisioterapiranap_v';
    }
}
