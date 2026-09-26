<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

class PendaftaranRepositories extends \yii\db\ActiveQuery
{
    /**
     * where is_sending
     * @param  [boolean] $value
     * @return object
     */
    public function findByRegisId($value)
    {
        return $this->andWhere([
            'pendaftaran_id' => $value
        ]);
    }

    public function registAttr()
    {
        return $this->select([
            'is_karcis',
            'kelaspelayanan_id',
            'carabayar_id',
            'penjamin_id',
            'pasienpulang_id',
            'pasienadmisi_id',
            'pasien_id'
        ]);
    }
}