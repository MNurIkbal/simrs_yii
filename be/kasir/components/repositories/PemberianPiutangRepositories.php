<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

class PemberianPiutangRepositories extends \yii\db\ActiveQuery
{

    /**
     * where pendaftaran_id
     * @param  integer $value
     * @return object
     */
    public function findByPendaftaranId($value)
    {
        return $this->andWhere([
            'pendaftaran_id' => $value
        ]);
    }

    /**
     * where penjualanresep_id
     * @param  integer $value
     * @return object
     */
    public function findByPenjualanResepId($value)
    {
        return $this->andWhere([
            'penjualanresep_id' => $value
        ]);
    }

    public function selectAttr($attributes = [])
    {
        $default = [
            'pemberianpiutang_id',
            'total_piutang'
        ];

        return $this->select($default);
    }
}