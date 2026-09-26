<?php

namespace Integrasi\Service\Ris\Models;

use Integrasi\Components\Repositories\PemeriksaanPasienRadiologiRepositories;

class PemeriksaanPasienRadiologiView extends \Integrasi\Components\ActiveRepositories
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
