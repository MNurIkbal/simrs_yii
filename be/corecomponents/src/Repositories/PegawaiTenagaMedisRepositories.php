<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\Repositories;

use app\modules\v1\models\DokterRujukan;
use Doco\Repositories\LookUpTransaksiRepositories;

class PegawaiTenagaMedisRepositories extends LookUpTransaksiRepositories
{
    /**
     * @method getTenagaMedis
     * @return Object
     */
    public function getPegawaiTenagaMedis()
    {
        $tenagaMedisId = $this->getTenagaMedisId();
        $dokterPerujuk = DokterRujukan::find()
            ->where(['kelompokpegawai_id' => $tenagaMedisId, 'is_active' => true])
            ->orderBy(['nama_pegawai' => SORT_ASC])
            ->all();
        return $dokterPerujuk;
    }
}

?>