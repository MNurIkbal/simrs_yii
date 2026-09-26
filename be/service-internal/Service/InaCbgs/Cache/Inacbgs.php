<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Integrasi\Service\InaCbgs\Cache;

use Yii;
use Integrasi\Components\DocoConstants;
use Integrasi\Service\InaCbgs\Models\GroupInaCbg;

class Inacbgs 
{

    public static function getMappInacbgs()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_INA_CBG , function ($cache) {
            $qCbgs = GroupInaCbg::find()
                        ->select([
                            'groupinacbg_id',
                            'groupinacbg_nama',
                            'inacbgs_field'
                        ])
                        ->asArray()
                        ->all();
            $listMapp = [];
            foreach ($qCbgs as $value) {
                $listMapp[$value['groupinacbg_nama']] = $value['inacbgs_field'];
            }

            return $listMapp;
        });
    }

}