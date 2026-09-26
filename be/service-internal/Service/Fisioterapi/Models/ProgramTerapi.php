<?php

namespace Integrasi\Service\Fisioterapi\Models;

use Integrasi\Components\ActiveRepositories;

class ProgramTerapi extends ActiveRepositories
{
    public static function tableName()
    {
        return 'programterapi_t';
    }
}
