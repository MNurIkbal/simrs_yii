<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Integrasi\Components\Repositories;

/**
 * @tabel pasienmasukpenunjang_t
 */
class PasienMasukPenunjangTRepositories extends DocoRepositories
{
    /**
     * where pasienmasukpenunjang_id
     * @param  integer $value
     * @return object
     */
    public function findByPenunjangId($id)
    {
        return $this->andWhere([
            'pasienmasukpenunjang_id' => $id
        ]);
    }

    public function selectAttr($select = [])
    {
        $default = [
            'pasienmasukpenunjang_id',
            'pasienkirimkeunitlain_id',
            'kelaspelayanan_id',
            'jeniskasuspenyakit_id',
            'pasienadmisi_id',
            'pegawai_id',
            'ruangan_id',
            'pasien_id',
            'pendaftaran_id',
            'no_masukpenunjang',
            'tglmasukpenunjang',
        ];
        $select = !empty($select) ? $select : $default;
        return $this->select($select);
    }
}
