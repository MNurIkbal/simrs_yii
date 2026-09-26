<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Integrasi\Service\Sirs\Repositories;

use Integrasi\Components\Repositories\DocoRepositories;
/**
 * @tabel jadwaldokter_m
 */
class JadwalDokterRepositoris extends DocoRepositories
{

    public function findByJadwalId($id)
    {
        return $this->andWhere(['jadwaldokter_id' => $id]);
    }

    public function orderByPrimary()
    {
        return $this->orderBy(['jadwaldokter_id' => SORT_DESC]);
    }

    public function selectAttr($select = [])
    {
        $selectSql = array_merge([
            'jadwaldokter_id',
            'jadwaldokter_tgl',
            'jadwaldokter_mulai',
            'jadwaldokter_tutup',
            'is_loaddokter',
            'jumlah_loaddokter',
            'is_bersedia',
        ],$select);

        return $this->select($selectSql);
    }
}
