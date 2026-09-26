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
use app\modules\v1\models\ProfilRumahSakit;

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

    public static function getListRangeTanggal()
    {
        $query = Lookup::find()
            ->where(['lookup_type' => 'range_bulan'])
            ->select("lookup_id, lookup_name");
        return $query->all();
    }
    
    public static function getProfile()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::GET_PROFILE_RS, function ($cache) {
            $query = ProfilRumahSakit::find()->select(['nama_rumahsakit', 'alamatlokasi_rumahsakit', 'no_telp_profilrs'])->where(['profilrs_id' => 1]);
            return $query->one();
        });
    }
}