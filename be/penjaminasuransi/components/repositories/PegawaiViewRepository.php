<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

class PegawaiViewRepository extends \yii\db\ActiveQuery
{
    const LIMIT = 100;

    /**
     * where jabatanId
     * @param  [string] $value
     * @return object
     */
    public function findByJabatanId($value)
    {
        return $this->andWhere([
            'jabatan_id' => $value
        ]);
    }

    /**
     * where lastUpdated
     * @return object
     */
    public function findLastUpdated()
    {
        return $this->andWhere(['not', ['pegawai_last_modified_date' => null]]);
    }

    /**
     * order yang terbaru
     * @return object
     */
    public function orderByLastUpdated()
    {
        return $this->orderBy([
            'pegawai_last_modified_date' => SORT_DESC
        ]);
    }
}
