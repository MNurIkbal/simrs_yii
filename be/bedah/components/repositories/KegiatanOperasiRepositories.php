<?php

/**
 * @author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

class KegiatanOperasiRepositories extends DocoRepositories
{
    public function selectAttr()
    {
        return $this->select([
            'kegiatanoperasi_id as id',
            'kegiatanoperasi_nama as text',
            'kegiatanoperasi_kode',
            'kegiatanoperasi_namalainnya'
        ]);
    }
}
