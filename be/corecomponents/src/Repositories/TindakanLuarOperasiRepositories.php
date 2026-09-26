<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\Repositories;

/**
 * @todo  ini dari tabel pemeriksaanpelengkap_t
 */

use app\components\repositories\DocoRepositories;

class TindakanLuarOperasiRepositories extends DocoRepositories
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
            'daftartindakan_id',
            'qty'
        ]);
    }
}
