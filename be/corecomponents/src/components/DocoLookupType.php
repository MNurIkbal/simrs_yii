<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 *
 * Pengambilan ID Constant
 */

namespace Doco\components;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\models\Lookup;
use app\modules\v1\cache\Cache;

class DocoLookupType
{

    public function actionGetLookupType($lookup_type)
    {
        return $this->dataByType($lookup_type);
    }

    /**
     * This function will return lookup by types
     * 
     * @param Array/String $types
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function dataByTypes($types)
    {
        if (!is_array($types)) {
            return $this->dataByType($types);
        } else {
            // first check the cache
            $result = $typeQuery = [];
            foreach ($types as $type) {
                $cache = Yii::$app->cache->get("cache-list-lookup" . $type);
                if ($cache) {
                    $result[$type] = $cache;
                } else {
                    $typeQuery[] = $type;
                }
            }
            if (!empty($typeQuery)) {
                $lookupRecord = Lookup::find()
                    ->select(['lookup_id', 'lookup_type', 'lookup_name', 'lookup_value', 'lookup_kode'])
                    ->andWhere(['in', 'lookup_type', $typeQuery])
                    ->andWhere(['is_deleted' => false, 'is_active' => true])
                    ->asArray()
                    ->all();
                foreach ($lookupRecord as $record) {
                    $result[$record['lookup_type']][] = $record;
                }

                foreach ($types as $type) {
                    // setting cache
                    if (!isset($result[$type])) {
                        $result[$type] = [];
                    }
                    if (!empty($result[$type])) {
                        $cache = Yii::$app->cache->set("cache-list-lookup" . $type, $result[$type]);
                    }
                }
            }
            return $result;
        }
    }

    /**
     * This function return of lookup and checking to cache before querying
     * 
     * @param String $types
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    private function dataByType($type)
    {
        $getCache = Yii::$app->cache->get("cache-list-lookup" . $type);
        if (!$getCache) {
            $lookupQuery = Lookup::find();
            $lookupQuery->select([
                'lookup_id',
                'lookup_type',
                'lookup_name',
                'lookup_value',
                'lookup_kode'
            ]);
            $lookupQuery->where([
                'lookup_type' => $type,
                'is_deleted' => false
            ]);
            $result = $lookupQuery->asArray()->all();
            if (count($result) > 0) {
                $getCache = $result;
                Yii::$app->cache->set("cache-list-lookup" . $type, $getCache);
            }
        }
        return !empty($getCache) ? $getCache : [];
    }
}
