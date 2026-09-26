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
use app\modules\v1\models\PegawaiView;

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

    public static function getListPegawaiRuangan($ruangan_id)
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_PEGAWAI_RUANGAN .'-'. $ruangan_id, 
                            function ($cache) use ($ruangan_id) {
            $data = PegawaiView::find()
                        ->andWhere(['is_active' => true]);

            if ($ruangan_id) {
                $data->andWhere(['ruangan_id' => $ruangan_id]);
            }

            $data->orderBy('ruangan_id');
            return $data->asArray()->all();
        });
    }

}