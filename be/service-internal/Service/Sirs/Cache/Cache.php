<?php

/**
* @author yaya
**/
namespace Integrasi\Service\Sirs\Cache;

use Yii;
use Integrasi\Components\DocoConstants;
use Integrasi\Service\Sirs\Models\Penjamin;
use Integrasi\Service\Sirs\Models\KonfigSystem;
use Integrasi\Service\Sirs\Models\KonfigTarif;
use Integrasi\Service\Sirs\Models\Lookup;

class Cache 
{
    public static function getPenjaminCaraBayar($penjamin_id ='')
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_PENJAMIN.'-'. $penjamin_id, function ($cache) use ($penjamin_id) {
            return Penjamin::find()->select([ 
                'carabayar_id' 
            ])->where([
                'is_active' => true,
                'penjamin_id' => $penjamin_id 
            ])->asArray()->one();
        });
    }
    
    public static function getKonfigSistem()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S , function ($cache) {
            return KonfigSystem::find()->asArray()->one();
        });
    }
    
    public static function getKonfigTarif()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_CACHE_CONFIG_TARIF , function ($cache) {
            return KonfigTarif::find()->asArray()->one();
        });
    }

    public static function getKonfigSystem()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S, function ($cache) {
            return KonfigSystem::find()->asArray()->one();
        });
    }

    public static function getLookupByType($type)
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_CACHE_LOOKUP_BY_TYPE .'-'. $type, function ($cache) use ($type) {
            return Lookup::find()->andWhere([
                'lookup_type' => $type,
                'is_active' => true,
            ])->asArray()->all();
        });
    }
}