<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

class PengajuanKlaimViewRepository extends \yii\db\ActiveQuery
{
    const LIMIT = 100;

    /**
     * where pengajuanKlaimId
     * @param  [string] $value
     * @return object
     */
    public function findByPengajuanKlaimId($value)
    {
        return $this->andWhere([
            'pengajuanklaim_id' => $value
        ]);
    }

    /**
     * where caraBayarId
     * @param  [string] $value
     * @return object
     */
    public function findByCaraBayarId($value)
    {
        return $this->andWhere([
            'carabayar_id' => $value
        ]);
    }

    /**
     * where penjaminId
     * @param  [string] $value
     * @return object
     */
    public function findByPenjaminId($value)
    {
        return $this->andWhere([
            'penjamin_id' => $value
        ]);
    }
}
