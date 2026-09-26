<?php

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class MarkDeletedAction extends Action {
    public function run($id) {
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $transApotek = $listTrans = json_encode([]);
        try {
            $cacheLabel = 'addObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;
            $urutObatPasien = 'urutObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;
            $cacheLabelTrackStock = 'trackObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;

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

                    if (!count($transApotek)) {
                        Yii::$app->cache->delete($cacheLabel);
                        Yii::$app->cache->delete($urutObatPasien);
                        $cache = true;
                    }

                    $rke = $transApotek[$id]['r_ke'];
                    if($rke != '-'){
                        foreach ($transApotek as $tempKey => $value) {
                            if($value['r_ke'] != '-'){
                                $transApotek[$tempKey]['kronis'] = false;
                            }
                        }
                    }else{
                        $transApotek[$id]['kronis'] = false;
                    }
                    $transApotek[$id]['subtotal'] = 0;

                    $listTrans = json_encode($transApotek);
                    $result = true;
                }

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
            }
            
            $transApotek[$id]['subtotal'] = 0;
            $listTrans = json_encode($transApotek);
            Yii::$app->cache->set($cacheLabel, $listTrans);
            
            $kelompok_resep = [];
            foreach ($transApotek as $item_obat) {
                if($item_obat['kronis'] != false){
                    if(!isset($item_obat['posisi'])) {
                        continue;
                    }

                    $posisi = $item_obat['posisi'];

                    if ($item_obat['is_racikan']) {
                        $kelompok_resep[$item_obat['r_ke']][$posisi] = $item_obat;
                    } else {
                        $kelompok_resep['non'][$posisi] = $item_obat;
                    }
                }
            }

            $final_list = [];
            foreach ($kelompok_resep as $kelompok) {
                $final_list = array_merge($final_list, $kelompok);
            }
            
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