<?php
/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\cache;

use Yii;
use Doco\components\constans\AntrianConstants;
use Doco\components\DocoConstants;
use Doco\Services\Cache as GeneralCache;
use app\modules\v1\models\KonfigSystemDetail;
use app\modules\v1\models\CaraBayar;

class Cache {

    const EXPIRED_CACHE = 3600;
    
    public static function getKonfigAntrian()
    {
        $konfig = GeneralCache::getKonfigSistem();
        return Yii::$app->cache->getOrSet(AntrianConstants::KONFIG_ANTRIAN, function ($cache) use($konfig) {
            if ($konfig['is_slider'] == 0) {
                $konfig_detail = KonfigSystemDetail::find()
                ->where(['is_foto' => true])
                ->all();
            } else {
                $konfig_detail = KonfigSystemDetail::find()
                ->where(['is_foto' => false])
                ->all();
            }
    
            return [
                'header' => $konfig['header'],
                'header_detail' => $konfig['header_detail'],
                'footer' => $konfig['footer'],
                'path_logoheader' => $konfig['path_logoheader'],
                'is_slider' => $konfig['is_slider'],
                'url_slider' => $konfig['url_slider'],
                'is_banyakloket' => $konfig['is_banyakloket'],
                'slides' => $konfig_detail
            ];
        });

    }
    
    /**
    * @var $duration intger untuk seting duration cache
    * @return array
    **/

    public static function getCaraBayar($duration = 3600)
    {
        return Yii::$app->cache->getOrSet(DocoConstants::CACHE_CB, function ($cache) {
            return CaraBayar::find()->where([
                'is_active' => true
            ])->all();
        });
    }
}