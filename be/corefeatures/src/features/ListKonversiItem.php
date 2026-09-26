<?php

namespace SirsCore\features;

use Yii;
use Doco\components\DocoConstants;
use SirsCore\models\SatuanKonversiView;

class ListKonversiItem
{
    public static function getData(
        $type = DocoConstants::JENIS_OBAT, 
        $expiration_duration = 86400,
        $itemKey = false
    ) {
        $type = strtolower($type);

        if($type == DocoConstants::JENIS_OBAT) {
            $key = 'all-satuan-konversi-obat';
        } else {
            $key = 'all-satuan-konversi-barang';
        }

        $data = Yii::$app->cache->get($key);

        if(empty($data)) {
            $listKonversi = [];
            $fetchData = SatuanKonversiView::find()
                ->where(['is_active' => 't', 'jenis' => $type])
                ->orderBy('satuan_besar')->asArray()->all();
            
            if($itemKey) {
                foreach ($fetchData as $_satuan) {
                    $listKonversi[$_satuan['obatalkes_id']][$_satuan['satuanbesar_id']] = $_satuan;
                }
            } else {
                $listKonversi = $fetchData;
            }

            $setCache = Yii::$app->cache->set($key, $listKonversi, $expiration_duration);
            $data = Yii::$app->cache->get($key);
        }

        return $data;
    }
}
