<?php

namespace app\modules\v1\models;

use app\components\repositories\PemeriksaanPasienRadiologiRepositories;

class PemeriksaanPasienRadiologiView extends \app\components\ActiveRepositories
{
    public $_repositori = PemeriksaanPasienRadiologiRepositories::class;

    public static function tableName()
    {
        return 'infopasienradiologi_v';
    }

    public static function primaryKey()
    {
        return ["no_rekam_medik"];
    }
}
