<?php

namespace app\modules\pendaftaran\components;

use Yii;
use app\components\DocoConstants;

class Lookup 
{
    /**
     * @method get value from lookup Transaksi
     *  
     * @param int $kodeId (opt)
     * @param str $type (opt) 
     * 
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function getValueFromLookupT($kodeId = null, $type = null)
    {
        $result = [];
        $dataLookupT = Yii::$app->cache->get(DocoConstants::CACHE_LOOKUP_TRANSAKSI);
        if(!$dataLookupT) {
            $getData = Yii::$app->docoRest->pendaftaran->get('allow/get-data-lookup-transaksi',[]);
            $dataLookupT = Yii::$app->cache->get(DocoConstants::CACHE_LOOKUP_TRANSAKSI);
        }
        
        $results = array_filter($dataLookupT, function($v, $k) use ($type, $kodeId) {
            if(!is_null($kodeId) && is_null($type)) {
                return $v['kode_id'] == $kodeId;
            } else if (is_null($kodeId) && !is_null($type)) {
                return $v['kode_transaksi'] == $type;
            } else if (!is_null($kodeId) && !is_null($type)) {
                return $v['kode_transaksi'] == $type && $v['kode_id'] == $kodeId;
            } else {
                return $v;
            }
        }, ARRAY_FILTER_USE_BOTH);

        if(!empty($results)) {
            $jmlData = count($results);
            foreach($results as $k => $v) {
                if($jmlData > 1) {
                    $result[] = $v;
                } else {
                    $result = $v;
                }
            }
        }

        return $result;
    }
}
