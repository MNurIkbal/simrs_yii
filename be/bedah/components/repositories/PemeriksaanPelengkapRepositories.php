<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

class PemeriksaanPelengkapRepositories extends DocoRepositories
{
    /**
     * where pasienmasukpenunjang_id
     * @param  integer $value
     * @return object
     */
    public function findByPenunjangId($value)
    {
        return $this->andWhere([
            'pasienmasukpenunjang_id' => $value
        ]);
    }

    public function selectAttr($select = [])
    {
        $default = [
            'pasienmasukpenunjang_id',
            'inpostoperasi_id',
            'daftartindakan_id',
            'nama_jaringan',
            'qty',
            'is_cyto',
            'ruangan_id',
            'additional_data',
        ];
        $select = !empty($select) ? $select : $default;
        return $this->select($select);
    }
}
