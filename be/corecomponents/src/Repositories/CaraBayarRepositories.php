<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\Repositories;

use Doco\models\CaraBayar;
use app\modules\v1\models\Lookup;
use Doco\components\DocoRestActiveFilter;
use Doco\components\constans\LookupConstans;
use Doco\Repositories\LookUpTransaksiRepositories;

class CaraBayarRepositories extends LookUpTransaksiRepositories
{
    /**
     * @method getStatusBayar (Get Status Bayar)
     * @return Array
     */
    public static function getStatusBayar()
    {
        return Lookup::find()
            ->select([
                'lookup_id',
                'lookup_type',
                'lookup_name',
                'lookup_value'
            ])
            ->where(['lookup_type' => LookupConstans::STATUS_BAYAR])
            ->asArray()
            ->all();
    }

    /**
     * @method getAllCaraBayar (Get All Cara Bayar)
     * @return Object
     */
    public static function getAllCaraBayar()
    {
        $model = new CaraBayar();
        $query = $model::find()->all();
        $caraBayar = DocoRestActiveFilter::advancedFilter($model, $query);
        return $caraBayar;
    }
}

?>