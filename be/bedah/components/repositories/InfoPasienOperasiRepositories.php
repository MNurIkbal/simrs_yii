<?php

/**
 * @author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

class InfoPasienOperasiRepositories extends DocoRepositories
{
    /**
     * where is_sending
     * @param  [boolean] $value
     * @return object
     */
    public function findByPenunjangId($value)
    {
        return $this->andWhere([
            'pasienmasukpenunjang_id' => $value
        ]);
    }

    public function selectAttr()
    {
        return $this->select([
            'pasienmasukpenunjang_id',
            'ruangan_id',
            'penjamin_id',
            'kelaspelayanan_id',
            'pendaftaran_id',
            'status as status_operasi'
        ]);
    }
}
