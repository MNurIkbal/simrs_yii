<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\Repositories;

use app\modules\v1\models\DokterRujukan;
use app\modules\v1\models\KelompokTindakan;
use Doco\Repositories\LookUpTransaksiRepositories;

class TindakanKelompokRepositories extends LookUpTransaksiRepositories
{
    public static function getKelompokTindakanFisio()
    {
        $keyId = self::getTindakanKelompokFisio();
        $data = KelompokTindakan::find()
            ->where(['kelompoktindakan_id' => $keyId])
            ->asArray()
            ->one();
        return $data;
    }
}
