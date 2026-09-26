<?php

namespace app\components\repositories;

use yii\db\ActiveQuery;

class InfoDaftarPaketFisioDetailViewRepository extends ActiveQuery
{
    /**
     * @method findByDaftarPaketFisioId (Find By Daftar Paket Fisioterapi ID)
     * @param Integer $id (daftarpaketfisio_id)
     * @return Object
     */
    public function findByDaftarPaketFisioId($id)
    {
        return $this->andWhere(['daftarpaketfisio_id' => $id]);
    }
}
