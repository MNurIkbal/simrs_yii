<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Integrasi\Components\Repositories;

/**
 * @tabel infopasienlabdetail_v
 */
class InfoPasienLabDetailViewRepositories extends DocoRepositories
{

    /**
     * pencarian berdasarkan pendaftaran_id
     * @param  integer $id 
     * @return \yii\db\ActiveQuery
     */
    public function findByRegistId($id)
    {
        return $this->andWhere(['pendaftaran_id' => $id]);
    }

}
