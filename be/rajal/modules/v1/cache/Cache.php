<?php

/**
* @author yaya
**/

namespace app\modules\v1\cache;

use Yii;
use Doco\components\DocoConstants;


class Cache {

    public static function getNilaiUang()
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_NILAI_UANG, function ($cache) {
            return Lookup::find()->where([
                'lookup_type' => 'nilai_uang'
            ])->orderBy('lookup_urutan ASC')->all();
        });
    }

}