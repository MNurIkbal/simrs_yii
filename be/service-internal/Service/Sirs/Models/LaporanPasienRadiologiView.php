<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

use Integrasi\Components\Repositories\PemeriksaanPasienRadiologiRepositories;

class LaporanPasienRadiologiView extends \Integrasi\Components\ActiveRepositories
{
    public $_repositori = PemeriksaanPasienRadiologiRepositories::class;

    public static function tableName()
    {
        return 'laporanpasienradiologi_v';
    }
}