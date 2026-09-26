<?php

/**
* @author yaya
**/

namespace app\modules\v1\cache;

use Yii;

use Doco\components\DocoConstants;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\KonfigSystem;

class Cache {

    public static function getListRuangan($instalasi_id = '')
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_RUANGAN .'-'. $instalasi_id, 
                            function ($cache) use ($instalasi_id) {
            $query = Ruangan::find()->where(['is_active' => true]);
            if ($instalasi_id) {
                $query->andWhere(['instalasi_id' => $instalasi_id]);
            }
            return $query->all();
        });
    }

    public static function getListInstalasi()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_INSTALASI, function ($cache) {
            $query = Instalasi::find()->where(['is_active' => 't']);
            return $query->all();
        });
    }

    public static function getListStatus()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::STATUS_KIRIM, function ($cache) {
            $query = Lookup::find()->where(['lookup_type' => 'status_kirim']);
            return $query->all();
        });
    }

    public static function getKonfigSistem()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S , function ($cache) {
            return KonfigSystem::find()->asArray()->one();
        });
    }

}