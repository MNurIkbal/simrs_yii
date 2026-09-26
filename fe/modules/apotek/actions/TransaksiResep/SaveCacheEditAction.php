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

class SaveCacheEditAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $post = $request->post();
        $get = $request->get();
        try {
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $posisi = 0;
            $is_racikan = isset($post['is_racikan']) ? true : false;
            $r_ke = isset($post['r_ke']) ? $post['r_ke'] : null;
            $racikanId = isset($post['racikan_id']) ? $post['racikan_id'] : null;

            $transApotek = [];
            $harga_jual = $post['hargajual'] * $post['nilai_konversi'];
            $harga_jual_oa = $harga_jual * $post['qty'];
            $harga_netto_oa = ($post['harganetto']*$post['nilai_konversi']) * $post['qty'];
        
            $cacheKey = isset($get['cacheKey']) ? $get['cacheKey'] : null;
            $cacheLabel = 'addObatEditReseptur' . $cacheKey;
            $urutLabel = 'urutObatEditReseptur' . $cacheKey;
            $cacheLabelTrackStock = 'trackObatEditReseptur' . $cacheKey;

            $konfigFarmasi = $this->controller->getKonfigFarmasi(true);
            $embalase_racikan = $konfigFarmasi['embalase_racikan'];
            $embalase_nonracikan = $konfigFarmasi['embalase_nonracikan'];

            $cacheTrans = Yii::$app->cache->get($cacheLabel);
            $transApotek = json_decode($cacheTrans, true);
            $no_urut = Yii::$app->cache->get($urutLabel);

            if ($no_urut === false || $no_urut < 1) {
                $start_urut = 0;
                Yii::$app->cache->set($urutLabel, $start_urut);
            }

            if (!isset($post['posisi'])) {
                $posisi = $no_urut + 1;
            }

            $res_transApotek = [];
            $idObat_same = false;
            $position = 0;
            $count_r_ke = [];

            if (count($transApotek) > 0) {
                foreach ($transApotek as $key => $value) {
                    if(!isset($value['obatalkes_id'])) {
                        continue;
                    }

                    $type_racikan = ($racikanId == "on") ? 1 : 2 ;
                    $dataValidate = $value['obatalkes_id'] . '-' . $value['racikan_id'] . '-' . $value['r_ke'];
                    $checkValidate = $post['obatalkes_id'] . '-' . $type_racikan . '-' . $r_ke;

                    if ($dataValidate == $checkValidate && !$value['is_deleted']) {
                        return DocoHelpers::response([
                          'message' => 'Obat sudah diinputkan!'
                        ],500);
                    }

                    if (isset($value['r_ke']) && $value['r_ke'] == $r_ke) {
                        if (!isset($count_r_ke[$value['r_ke']])) {
                            $count_r_ke[$value['r_ke']] = 1;
                        } else {
                            $count_r_ke[$value['r_ke']] = $count_r_ke[$value['r_ke']]++;
                        }
                    }
                }
            }

            if ($r_ke != null && isset($count_r_ke[$r_ke]) && $count_r_ke[$r_ke] > 0) {
                foreach ($transApotek as $noUrut => $detail_obat) {
                    if ($detail_obat['r_ke'] == $r_ke) {
                        $embalase = 0;
                        if ($racikanId == "on") {
                            $jumlah_obat = isset($count_r_ke[$r_ke]) && $count_r_ke[$r_ke] > 0 ?
                                $count_r_ke[$r_ke] : 0;
                            $embalase = $embalase_racikan / ($jumlah_obat + 1);
                        } else {
                            $embalase = $embalase_nonracikan;
                        }

                        $hrgaJual = $detail_obat['hargajual'] * $detail_obat['qty_konversi'];
                        $transApotek[$noUrut]['harga'] = $hrgaJual + ($embalase / $detail_obat['qty']);
                        $transApotek[$noUrut]['subtotal'] = ( $hrgaJual + ($embalase / $detail_obat['qty'])) * $detail_obat['qty'];
                    }
                }
            }

            $embalase = 0;
            if ($racikanId == "on") {
                $jumlah_obat = isset($count_r_ke[$r_ke]) && $count_r_ke[$r_ke] > 0 ?
                     $count_r_ke[$r_ke] : 0;
                $embalase = $embalase_racikan / ($jumlah_obat + 1);
            } else {
                $embalase = $embalase_nonracikan;
            }

            if($harga_jual == 0) {
                $harga_dgn_embalase = 0;
                $embalase = 0;
            } else {
                $harga_dgn_embalase = $harga_jual + ($embalase / $post['qty']);
                $harga_dgn_embalase = ceil($harga_dgn_embalase);
            }

            $return = [
                'posisi' => $posisi,
                'is_deleted' => false,
                'pegawai_id' => $pegawai_id,
                'obatalkes_id' => $post['obatalkes_id'],
                'obatalkes_nama' => $post['obat_nama'],
                'signa' => ( $post['signa_nama'] == '— Pilih —' || $post['signa_nama'] == '') ? '-' :  $post['signa_nama'],
                'signa_id' => is_numeric($post['signa']) ? $post['signa'] : null,
                'racikan_id' => ($racikanId == "on") ? 1 : 2 ,
                'jenis_racikan' => ($racikanId == "on") ? Yii::t('fe', 'Racikan') : Yii::t('fe', 'Non Racikan'),
                'qty' => $post['qty'],
                'ppn' => $post['ppn'],
                'kronis' => $post['kronis'],
                'harganetto' => $post['harganetto'],
                'hargajual' => $post['hargajual'],
                'persendiscount' => $post['persendiscount'],
                'jmldiscount' => $post['jmldiscount'],
                'persenppn' => $post['persenppn'],
                'jmlppn' => $post['jmlppn'],
                'persenmargin' => $post['persenmargin'],
                'jmlmargin' => $post['jmlmargin'],
                'satuankecil_id' => $post['satuankecil_id'],
                'harga' => $harga_dgn_embalase,
                'embalase' => $embalase,
                'is_racikan' => $is_racikan,
                'r_ke' => $r_ke,
                'nilai_konversi' => $post['nilai_konversi'],
                'subtotal' => $harga_dgn_embalase * $post['qty'],
                'catatan' => strip_tags($post['catatan']),
                'nilai_konversi' => $post['nilai_konversi'],
                'qty_konversi' => $post['qty_konversi'],
                'satuaninput_id' => $post['satuaninput_id'],
                'satuan_input' => $post['satuan_input'],
                'satuankonversi_id' => $post['satuankonversi_id'],
                'satuan_konversi' => $post['satuan_konversi'],
                'harga_konversi' => $post['harga_kecil'],
                'etiket' => strip_tags($post['catatan']),
                'qty_tersedia' => $post['stok'],
            ];

            $transApotek[$posisi] = $return;
            $this->controller->trackStokResep(
                /*cache_label*/     $cacheLabelTrackStock,
                /*obatalkes_id*/    $return['obatalkes_id'],
                /*qty*/             $return['qty_konversi']
            );

            Yii::$app->cache->delete($urutLabel);
            Yii::$app->cache->set($urutLabel, $posisi);
            $listTrans = json_encode($transApotek);

            $final_list = $this->controller->groupingResep($transApotek);

            Yii::$app->cache->set($cacheLabel, $listTrans);

            $result['data'] = $final_list;
            $result['message'] = Yii::t('fe', 'Data berhasil di simpan');
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
}
