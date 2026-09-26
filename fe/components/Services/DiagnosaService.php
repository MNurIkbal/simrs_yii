<?php

namespace app\components\Services;

use Yii;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\components\Services\Contracts\CacheDiagnosaInterface;
use yii\helpers\ArrayHelper;

class DiagnosaService implements CacheDiagnosaInterface
{
    public function getCacheDiagnosa($controllerRest, $url)
    {
        ini_set('memory_limit', '-1');
        $request = Yii::$app->request;
        $type_icd = $request->get('type_icd');
        $type_ina = $request->get('type_ina');
        $term = $request->get('term');
        $not_in = $request->get('not_in');

        /**
         * Cek contains special character ignore titik
         */
        if (preg_match('/[^a-zA-Z0-9.]/', $term)) {
            return DocoHelpers::response([
                'result' => []
            ]);
        }
        
        try {
            if(! empty($type_ina) && $type_ina == 1) {
                $cache_diagnosa = Yii::$app->cache->get("cache_diagnosa_ina");
                if (!$cache_diagnosa) {
                    $response = $controllerRest->get($url.'?type=ina');
                    $response = json_decode($response->getBody(), true);
                    $diagnosa = $response['response']['diagnosa'];
                    Yii::$app->cache->set("cache_diagnosa_ina", $diagnosa);
                    $cache_diagnosa = $diagnosa;
                }
            } else {
                $cache_diagnosa = Yii::$app->cache->get("cache_diagnosa");
                if (!$cache_diagnosa) {
                    $response = $controllerRest->get($url.'?type=unu');
                    $response = json_decode($response->getBody(), true);
                    $diagnosa = $response['response']['diagnosa'];
                    Yii::$app->cache->set("cache_diagnosa", $diagnosa);
                    $cache_diagnosa = $diagnosa;
                }
            }
            $term = '/' . strtolower($term) . '/';
            $find_data = array_filter($cache_diagnosa, function ($a) use ($term) {
                $a = str_replace(".", "", $a);
                $term = str_replace(".", "", $term);
                return preg_grep($term, $a);
            });

            $data = [];
            $dataSearch = [];
            if ($find_data) {
                if (!$not_in) {
                    $not_in = [];
                }
                foreach ($find_data as $value) {
                    if ( strtolower(trim($value['tabularlist_versi'])) == strtolower(trim($type_icd)) ) {
                        if (!in_array($value['diagnosa_id'], $not_in)) {
                            $data[] = [
                                'id' => $value['diagnosa_id'],  
                                'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'],
                                'kode' => $value['diagnosa_kode'],
                                'validcode' => $value['validcode'],
                                'accpdx' => isset($value['accpdx']) ? $value['accpdx'] : null,
                                'asterik' => isset($value['asterik']) ? $value['asterik'] : null,
                                'ina_grouper' => isset($value['ina_grouper']) ? $value['ina_grouper'] : null,
                                'validcode_idrg' => isset($value['validcode_idrg']) ? $value['validcode_idrg'] : null,
                            ];

                            $dataSearch[] = [
                                'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'],
                                'kode' => $value['diagnosa_kode'],
                                'kode_lower' => $value['diagnosa_kode_lower'],
                                'diagnosa_nama' => $value['diagnosa_nama'],
                                'diagnosa_nama_lower'=> $value['diagnosa_lower']
                            ];  
                        }
                    }
                }
            }

            $find_data_2 = array_filter($dataSearch, function ($a) use ($term) {
                $a = str_replace(".", "", $a);
                $term = str_replace(".", "", $term);
                return preg_grep($term, $a);
            });

            $hasilSearch = [];
            $mapArray = ArrayHelper::map($find_data_2, 'kode', 'kode');
            foreach ($data as $key => $value) {
                if(in_array($value['kode'],$mapArray)) {
                    $hasilSearch[] = $value;
                }   
            }
            
        } catch (RequestException $e) {
            $hasilSearch = [];
        }
        return DocoHelpers::response([
            'result' => $hasilSearch
        ]);
    }
}
