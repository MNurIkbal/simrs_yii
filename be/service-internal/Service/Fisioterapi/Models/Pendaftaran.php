<?php

namespace Integrasi\Service\Fisioterapi\Models;

use Integrasi\Components\ActiveRepositories;

class Pendaftaran extends ActiveRepositories
{
    public static function tableName()
    {
        return 'pendaftaran_t';
    }
}
