<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

class PenjualanResepRepositories extends \yii\db\ActiveQuery
{
    /**
     * where is_sending
     * @param  [boolean] $value
     * @return object
     */
    public function findByRecipeId($value)
    {
        return $this->andWhere([
            'penjualanresep_id' => $value
        ]);
    }

    public function findByStatus($value)
    {
        return $this->andWhere([
            'status_bayar' => $value
        ]);
    }

    public function transAttr()
    {
        return $this->select([
            'kelaspelayanan_id',
            'penjamin_id',
            'carabayar_id',
            'biayaadministrasi',
        ]);
    }
}