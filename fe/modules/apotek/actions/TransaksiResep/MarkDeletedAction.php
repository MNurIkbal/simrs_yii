<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\TransaksiResep;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class MarkDeletedAction extends Action {
    public function run($id, $cacheKey = null) {
        $transApotek = $listTrans = json_encode([]);
        try {
            $cacheLabel = 'addObatEditReseptur' . $cacheKey;
            $urutObatPasien = 'urutObatEditReseptur' . $cacheKey;
            $cacheLabelTrackStock = 'trackObatEditReseptur' . $cacheKey;

            $cacheTrans = Yii::$app->cache->get($cacheLabel);
            $editStatus = Yii::$app->cache->get('editStatus');

            if ($cacheTrans !== false) {
                $transApotek = json_decode($cacheTrans, true);

                // Check array key != posisi,
                // This block create $tempKey to prevent duplicate key
                foreach ($transApotek as $key => $value) {
                    if (!isset($value['posisi']) || $key == $value['posisi']) {
                        continue;
                    }

                    $tempKey = 'temp_' . $key;
                    $transApotek[$tempKey] = $value;
                    unset($transApotek[$key]);
                }

                // Re-map array key by removing $tempKey
                foreach ($transApotek as $tempKey => $value) {
                    if (!isset($value['posisi']) || $tempKey == $value['posisi']) {
                        continue;
                    }

                    $transApotek[$value['posisi']] = $value;
                    unset($transApotek[$tempKey]);
                }

                if (isset($transApotek[$id]) && isset($transApotek[$id]['obatalkes_id'])) {
                    $obat = $transApotek[$id];
                    $this->controller->trackStokResep(
                        /*cache_label*/   $cacheLabelTrackStock,
                        /*obat_alkes_id*/ $obat['obatalkes_id'],
                        /*qty*/           abs($obat['qty_konversi']) * -1
                    );

                    if (!count($transApotek)) {
                        Yii::$app->cache->delete($cacheLabel);
                        Yii::$app->cache->delete($urutObatPasien);
                        $cache = true;
                    }

                    $transApotek[$id]['is_deleted'] = true;
                    $transApotek[$id]['subtotal'] = 0;

                    $listTrans = json_encode($transApotek);
                    $result = true;
                }

                $konfigFarmasi = $this->controller->getKonfigFarmasi(true);
                $embalase_racikan = $konfigFarmasi['embalase_racikan'];

                $racikan = ArrayHelper::map($transApotek, "r_ke", "r_ke");

                $count_r_ke = [];
                $count_r_ke_reseptur = [];
                foreach ($racikan as $r_ke) {
                    foreach ($transApotek as $key => $value) {
                        if (isset($value['r_ke']) && $value['r_ke'] == $r_ke) {
                            if (!isset($count_r_ke[$value['r_ke']])) {
                                $count_r_ke[$value['r_ke']] = 1;
                                $count_r_ke_reseptur[$value['r_ke']] = 1;
                            } else {
                                if($value['is_deleted'] == false){
                                    $count_r_ke[$value['r_ke']]++;
                                }
                                $count_r_ke_reseptur[$value['r_ke']]++;
                            }
                        }
                    }
                }

                // hitung ulang harga untuk resep obat
                foreach($transApotek as $trans){
                    if(
                        isset($trans['r_ke']) &&
                        ($trans['r_ke'] != "-" || $trans['r_ke'] != null)
                    ) {
                        if(isset($trans['obatalkespasien_id']) || isset($trans['resepturdetail_id'])) {
                            if($editStatus == 'reseptur'){
                                $embalase = ($embalase_racikan/$count_r_ke_reseptur[$trans['r_ke']])/$trans['qty'];
                                $trans['harga_konversi'] = $trans['hargajual'] - $embalase;
                                $embalase = ($embalase_racikan/$count_r_ke[$trans['r_ke']])/$trans['qty'];
                                $nilai_konversi = isset($trans['nilai_konversi']) ? $trans['nilai_konversi'] : 1;
                                $harga_jual = $trans['harga_konversi'] * $nilai_konversi;
                                $transApotek[$trans['posisi']]['hargajual'] = $harga_jual + $embalase;
                            }
                        }
                    }
                }
            }
            // Update Harga Obat
            $transApotek = $this->controller->recalculatingCache($transApotek, true);
            $transApotek[$id]['subtotal'] = 0;

            $listTrans = json_encode($transApotek);
            Yii::$app->cache->set($cacheLabel, $listTrans);
            $final_list = $this->controller->groupingResep($transApotek);
            $return['detail'] = $final_list;
            $return['message'] = 'Success';

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            echo json_encode($e->getMessage()); die;
            echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
}
