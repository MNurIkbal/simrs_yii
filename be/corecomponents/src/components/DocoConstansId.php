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
use Doco\components\DocoHelpers;
use Doco\models\ConstantsId;
use app\modules\v1\cache\Cache;

class DocoConstansId
{

    public function actionGetId($kode)
    {
        $getCache = Yii::$app->cache->get("cache-constant-id");
        if ($getCache == false || !isset($getCache[$kode])) {
            $data = ConstantsId::find()->asArray()->all();
            $tmp = [];
                foreach ($data as $key => $value) {
                        $tmp[$value['kode_transaksi']] = $value['kode_id'];
                }
            $getCache = $tmp;
            Yii::$app->cache->set("cache-constant-id",$tmp);
        }
        return isset($getCache[$kode]) ? $getCache[$kode] : null;
    }

    public function actionGetAdditional($kode, $convertToArray = false)
    {
        $getCache = Yii::$app->cache->get("cache-constant-additional");
        if ($getCache == false || !isset($getCache[$kode])) {
            $data = ConstantsId::find()->asArray()->all();
            $tmp = [];
                foreach ($data as $key => $value) {
                    $tmp[$value['kode_transaksi']] = $value['additional_value'];
                }
            $getCache = $tmp;
            Yii::$app->cache->set("cache-constant-additional",$tmp);
        }
        return isset($getCache[$kode]) 
                ? $convertToArray ? json_decode($getCache[$kode], true) : $getCache[$kode] : null;
    }
}