<?php

namespace app\modules\v1\models;

use app\components\ActiveRepositories;

class DaftarPaketFisioDetailV extends ActiveRepositories
{
    public $_repositori = 'app\components\repositories\InfoDaftarPaketFisioDetailViewRepository';

    public static function tableName()
    {
        return 'infodaftarpaketfisiodet_v';
    }
}
