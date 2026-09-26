<?php

/**
* @author yaya
**/

namespace app\modules\v1\cache;

use Yii;

use app\modules\v1\models\LookupKeperawatan;

class Cache 
{

    public static function getLookUpKeperawatan($key, $duration = 3600)
    {
        return Yii::$app->cache->getOrSet($key, function ($cache) use($key) {
            return LookupKeperawatan::find()->where([
                'is_active' => true,
                'lookup_type' => $key
            ])->all();
        });
    }

}