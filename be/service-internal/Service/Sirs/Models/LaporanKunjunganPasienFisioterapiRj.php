<?php

namespace Integrasi\Service\Sirs\Models;

use Integrasi\Components\ActiveRepositories;

class LaporanKunjunganPasienFisioterapiRj extends ActiveRepositories
{
    public static function tableName()
    {
        return 'lapkunjunganpasienfisiorj_v';
    }
}
