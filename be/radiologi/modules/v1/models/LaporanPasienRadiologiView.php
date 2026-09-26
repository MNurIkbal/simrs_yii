<?php

namespace app\modules\v1\models;

use app\components\repositories\PemeriksaanPasienRadiologiRepositories;

class LaporanPasienRadiologiView extends \app\components\ActiveRepositories
{
    public $_repositori = PemeriksaanPasienRadiologiRepositories::class;

    public static function tableName()
    {
        return 'laporanpasienradiologi_v';
    }
}
