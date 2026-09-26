<?php

namespace app\modules\v1\models;

use app\components\ActiveRepositories;

class DaftarPaketFisioV extends ActiveRepositories
{
    public $_repositori = 'app\components\repositories\DaftarPaketFisioterapiViewRepository';

    public static function tableName()
    {
        return 'infodaftarpaketfisio_v';
    }
}
