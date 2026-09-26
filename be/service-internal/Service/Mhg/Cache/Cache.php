<?php

/**
* @author yaya
**/

namespace Integrasi\Service\Mhg\Cache;

use Yii;
use Integrasi\Components\DocoConstants;
use Integrasi\Service\Mhg\Models\Shift;
use Integrasi\Service\Mhg\Models\LookupTransaksi;

class Cache 
{
    public static function getShift()
    {
        return Yii::$app->cache->getOrSet('MHG-' . DocoConstants::CACHE_SHIFT , function ($cache) {
            return Shift::find([
                'shift_id',
                'shift_nama',
                'shift_namalainnya',
                'shift_kode'
            ])->where([
                'is_active' => true
            ])->asArray()->all();
        });
    }

    public static function lookupTransaksi()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_LOOKUP_TRANSAKSI, function ($cache) {
            return LookupTransaksi::find()
                ->asArray()
                ->all();
        });
    }

    public static function lookTeleRoom()
    {
        $result = '';
        $key = 'ruangan_telekonsultasi';

        return Yii::$app->cache->getOrSet('MHG-' . $key , function ($cache) use($key, $result) {
            $data = self::lookupTransaksi();
            if(!empty($data)) {
                $results = array_filter($data, function($v, $k) use ($key) {
                    return $v['kode_transaksi'] == $key;
                }, ARRAY_FILTER_USE_BOTH);
            }

            if(isset($results)) {
                foreach($results as $k => $v) {
                        $result = (object) $v;
                }
            }

            return $result;
        }); 
    }
}