<?php

/**
 * @author: Setyabudi
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

/**
 * @tabel golonganoperasi_m
 */
class GolonganOperasiRepositories extends DocoRepositories
{
    public function selectAttr()
    {
        return $this->select([
            'golonganoperasi_id as id',
            'golonganoperasi_nama as text',
            'golonganoperasi_kode'
        ]);
    }

    public function findByName($value)
    {
        return $this->andWhere([
            'ILIKE', 'golonganoperasi_nama', $value
        ]);
    }
}
