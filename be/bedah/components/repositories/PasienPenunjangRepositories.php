<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

class PasienPenunjangRepositories extends DocoRepositories
{
    /**
     * where is_sending
     * @param  [boolean] $value
     * @return object
     */
    public function findById($value)
    {
        return $this->andWhere([
            'pasienmasukpenunjang_id' => $value
        ]);
    }

    public function selectStatusPeriksa()
    {
        return $this->select([
            'status_periksa',
        ]);
    }

    public function selectAttr()
    {
        return $this->select([
            'ruangan_id',
            'instalasi_id',
            'ruangan_nama',
            'ruangan_namalainnya'
        ]);
    }
}
