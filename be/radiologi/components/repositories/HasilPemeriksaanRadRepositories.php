<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

/**
 * @tabel hasilpemeriksaanrad_t
 */
class HasilPemeriksaanRadRepositories extends DocoRepositories
{

    /**
     * pencarian berdasarkan pasienmasukpenunjang_id
     * @param  integer $id 
     * @return \yii\db\ActiveQuery
     */
    public function findByPenunjangId($id)
    {
        return $this->andWhere(['pasienmasukpenunjang_id' => $id]);
    }

    /**
     * pencarian berdasarkan hasilpemeriksaanrad_id
     * @param  integer $id 
     * @return \yii\db\ActiveQuery
     */
    public function findByHasilId($id)
    {
        return $this->andWhere(['hasilpemeriksaanrad_id' => $id]);
    }

    public function findByTindakanId($id)
    {
        return $this->andWhere(['daftartindakan_id' => $id]);
    }

}
