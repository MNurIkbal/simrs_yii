<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\Repositories;

use app\modules\v1\models\Lookup;
use Doco\Repositories\LookUpTransaksiRepositories;

class StatusPeriksaRepositories extends LookUpTransaksiRepositories
{
    /**
     * @method getStatusPeriksa (Get Status Periksa Rajal & Ranap)
     * @return Object
     */
    public static function getStatusPeriksa()
    {
        return Lookup::find()
            ->where(['lookup_type' => 'status_periksa'])
            ->orWhere(['lookup_type' => 'status_ranap'])
            ->all();
    }

    /**
     * @method getStatusPeriksaRanap (Get Status Periksa Ranap)
     * @return Array
     */
    public static function getStatusPeriksaRanap()
    {
        return Lookup::find()
            ->select([
                'lookup_id',
                'lookup_type',
                'lookup_name',
                'lookup_value'
            ])
            ->where(['lookup_type' => 'status_ranap'])
            ->asArray()
            ->all();
    }

    /**
     * @method getStatusPeriksaRajal (Get Status Periksa Rajal)
     * @return Array
     */
    public static function getStatusPeriksaRajal()
    {
        return Lookup::find()
            ->select([
                'lookup_id',
                'lookup_type',
                'lookup_name',
                'lookup_value'
            ])
            ->where(['lookup_type' => 'status_periksa'])
            ->asArray()
            ->all();
    }


}

?>