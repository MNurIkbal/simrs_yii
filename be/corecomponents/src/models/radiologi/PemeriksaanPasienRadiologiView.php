<?php

namespace Doco\models\radiologi;

use Doco\Repositories\PemeriksaanPasienRadiologiRepositories;

class PemeriksaanPasienRadiologiView extends \Doco\components\ActiveRepositories
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
