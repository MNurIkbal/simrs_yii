<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

class PengajuanKlaimRepository extends \yii\db\ActiveQuery
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
}
