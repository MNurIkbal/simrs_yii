<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Integrasi\Components\Repositories;

/**
 * @tabel hasilpemeriksaanrad_v
 */
class HasilPemeriksaanRadViewRepositories extends DocoRepositories
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
     * pencarian berdasarkan tindakanpelayanan_id
     * @param  integer $id 
     * @return \yii\db\ActiveQuery
     */
    public function findByTindakanPelId($id)
    {
        return $this->andWhere(['tindakanpelayanan_id' => $id]);
    }

    /**
     * pencarian berdasarkan daftartindakan_id
     * @param  integer $id 
     * @return \yii\db\ActiveQuery
     */
    public function findByDaftarTindakanId($id)
    {
        return $this->andWhere(['daftartindakan_id' => $id]);
    }

    /**
     * pencarian berdasarkan tgl_hasilrad
     * @param  datetime $value 
     * @return \yii\db\ActiveQuery
     */
    public function findByTglHasil($value)
    {
        return $this->andWhere(['tgl_hasilrad' => $value]);
    }

    /**
     * pencarian berdasarkan tgl_verifikasi
     * @param  datetime $value 
     * @return \yii\db\ActiveQuery
     */
    public function findByTglVerif($value)
    {
        return $this->andWhere(['tgl_verifikasi' => $value]);
    }

    /**
     * pencarian berdasarkan tgl_verifikasi
     * @param  datetime $value 
     * @return \yii\db\ActiveQuery
     */
    public function findByTglVerifNotNull()
    {
        return $this->andWhere(['not', ['tgl_verifikasi' => null]]);
    }

}
