<?php

/**
 * @author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\Repositories;

/**
 * @tabel infopasienradiologi_v
 */
class PemeriksaanPasienRadiologiRepositories extends BaseRepositories
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
     * pencarian berdasarkan daftartindakan_id
     * @param  integer $id 
     * @return \yii\db\ActiveQuery
     */
    public function findByDaftarTindakanId($id)
    {
        return $this->andWhere(['daftartindakan_id' => $id]);
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
     * Pencarian between tglmasukpenunjang
     * @param  datetime $start 
     * @param  datetime $end
     * @return \yii\db\ActiveQuery
     */
    public function betweenTglMasuk($start, $end)
    {
        return $this->betweenCondition('tglmasukpenunjang', $start, $end);
    }

    /**
     * Pencarian between tanggal_lahir
     * @param  datetime $start 
     * @param  datetime $end
     * @return \yii\db\ActiveQuery
     */
    public function betweenTglLahir($start, $end)
    {
        return $this->betweenCondition('tanggal_lahir', $start, $end);
    }

    /**
     * pencarian Not In Status Penunjang
     * @param  array|string $status
     * @return \yii\db\ActiveQuery
     */
    public function notInStatusPenunjang($status)
    {
        return $this->andWhere(['not',['status_penunjang' => $status]]);
    }

    /**
     * pencarian status penunjang is null
     * @return \yii\db\ActiveQuery
     */
    public function orStatusIsNull()
    {
        return $this->orWhere(['status_penunjang' => null]);
    }

    public function noInStatusPeriksa($status)
    {
        return $this->andWhere(['not',['status_periksa' => $status]]);
    }

}
