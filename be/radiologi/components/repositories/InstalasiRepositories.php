<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

class InstalasiRepositories extends DocoRepositories
{
    /**
     * where is_sending
     * @param  [boolean] $value
     * @return object
     */
    public function findByInstalasi($value)
    {
        return $this->andWhere([
            'instalasi_id' => $value
        ]);
    }

    public function selectAttr()
    {
        return $this->select([
            'instalasi_id',
            'instalasi_nama',
            'instalasi_namalainnya',
            'instalasi_singkatan'
        ]);
    }
}
