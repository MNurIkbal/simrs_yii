<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\Repositories;

use app\modules\v1\models\DokterRujukan;
use app\modules\v1\models\KategoriTindakan;
use Doco\Repositories\LookUpTransaksiRepositories;

class TindakanKategoriRepositories extends LookUpTransaksiRepositories
{
    public static function getKategoriTindakanFisio()
    {
        $keyId = self::getTindakanKategoriFisio();
        $data = KategoriTindakan::find()
            ->where(['kategoritindakan_id' => $keyId])
            ->asArray()
            ->one();
        return $data;
    }
}
