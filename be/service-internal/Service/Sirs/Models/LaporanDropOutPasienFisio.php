<?php

namespace Integrasi\Service\Sirs\Models;

use Integrasi\Components\ActiveRepositories;

class LaporanDropOutPasienFisio extends ActiveRepositories
{
    public static function tableName()
    {
        return 'lapdropoutpasienfisio_v';
    }
}
