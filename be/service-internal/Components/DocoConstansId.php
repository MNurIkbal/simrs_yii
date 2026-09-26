<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 *
 * Pengambilan ID Constant
 */

namespace Integrasi\Components;

use Yii;

class DocoConstansId
{

    public function actionGetId($kode)
    {
        $getCache = Yii::$app->cache->get("cache-constant-id");
        if ($getCache == false || !isset($getCache[$kode])) {
            $data = Yii::$app->db->createCommand("
                SELECT * FROM lookuptransaksi_m
            ")->queryAll();
            $tmp = [];
                foreach ($data as $key => $value) {
                        $tmp[$value['kode_transaksi']] = $value['kode_id'];
                }
            $getCache = $tmp;
            Yii::$app->cache->set("cache-constant-id",$tmp);
        }
        return isset($getCache[$kode]) ? $getCache[$kode] : null;
    }
}