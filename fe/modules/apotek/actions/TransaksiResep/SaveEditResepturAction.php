<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\TransaksiResep;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class SaveEditResepturAction extends Action {
    public function run($id) {
        try {
            $request = Yii::$app->request->get();
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $post = Yii::$app->request->post();
            $arr_det = $post['det'];
            $arr_kronis = $post['kronis'];
            $cacheKey = isset($post['cacheKey']) ? $post['cacheKey'] : null;
            $cacheLabel = 'addObatEditReseptur' . $cacheKey;
            $urutLabel = 'urutObatEditReseptur' . $cacheKey;
            $cacheTrans = Yii::$app->cache->get($cacheLabel);
            $listObat = json_decode($cacheTrans, true);
            if(count($listObat) < 1){
                return DocoHelpers::response([
                    'response' => [
                        'title' => 'Terjadi Kesalahan',
                        'message' => 'Obat Tidak Boleh Kosong!'
                    ]
                ], 500);
            }

            $transaksiObatMinus = false;
            $konfigFarmasi = $this->controller->getKonfigFarmasi(true);
            if (isset($konfigFarmasi['is_transaksiobat_0']) && $konfigFarmasi['is_transaksiobat_0'] == true) {
                $transaksiObatMinus = true;
            }

            $totalQtyRke = [];
            foreach ($listObat as $key => $value) {
                if($listObat[$key]['is_deleted'] == false) {
                    if (isset($value['r_ke'])) {
                        if (!isset($totalQtyRke[$value['r_ke']])) {
                            $totalQtyRke[$value['r_ke']] = $arr_det[$value['posisi']];
                        } else {
                            $totalQtyRke[$value['r_ke']] += $arr_det[$value['posisi']];
                        }
                    }
                }
            }

            $embalaseRacikan = isset($konfigFarmasi['embalase_racikan']) ? $konfigFarmasi['embalase_racikan'] : 0;
            $embalaseNonRacikan = isset($konfigFarmasi['embalase_nonracikan']) ? $konfigFarmasi['embalase_nonracikan'] : 0;

            // mapping det ke listObat
            $jmlHapusObat = 0;
            $index = $totalharga_netto = $embalase = 0;
            foreach ($listObat as $key => $value) {
                if($listObat[$key]['is_deleted'] == false){
                    $listObat[$key]['det'] = $arr_det[$value['posisi']];
                    $listObat[$key]['is_kronis'] = $arr_kronis[$value['posisi']];
                    if(isset($value['embalase'])) {
                        $embalase = $value['embalase'];
                    }

                    $listObat[$key]['subtotal'] = 0;
                    $listObat[$key]['subtotal_harganetto'] = 0;

                    if($listObat[$key]['det']>0){
                        if (isset($value['r_ke']) && $value['r_ke'] > 0) {
                            $harga = isset($value['hargajual_tanpaembalase']) ? $value['hargajual_tanpaembalase'] : $value['hargajual'];
                            $harga = ceil($harga) + ($embalaseRacikan / ceil($totalQtyRke[$value['r_ke']]));
                            $value['harga'] = ceil($harga);
                            $listObat[$key]['harga'] = ceil($harga);
                        } else {
                            $harga = isset($value['hargajual_tanpaembalase']) ? $value['hargajual_tanpaembalase'] : $value['hargajual'];
                            $harga = ceil($harga) + ($embalaseNonRacikan / ceil($arr_det[$value['posisi']]));
                            $value['harga'] = ceil($harga);
                            $listObat[$key]['harga'] = ceil($harga);
                        }

                        $listObat[$key]['subtotal'] = ($value['harga'] + ($embalase / $arr_det[$value['posisi']])) * $arr_det[$value['posisi']];
                        $listObat[$key]['subtotal_harganetto'] = ($value['harganetto'] * ($value['qty_konversi']/$value['qty']));
                    }

                    $totalharga_netto += $listObat[$key]['subtotal_harganetto'];
                    $index++;

                    if (ArrayHelper::getValue($value, 'is_deleted')) {
                        $jmlHapusObat++;
                    }

                    if(
                        $listObat[$key]['det'] > $listObat[$key]['qty_tersedia'] 
                        && $listObat[$key]['det'] > 0
                        && !$transaksiObatMinus
                    ) {
                        return DocoHelpers::response([
                            'response' => [
                                'title' => 'Terjadi Kesalahan',
                                'message' => "Stok obat {$listObat[$key]['obatalkes_nama']} tidak tersedia."
                            ]
                        ], 422);
                    }
                }
            }

            if(count($listObat) == $jmlHapusObat) {
                // validasi jumlah obat is deleted
                return DocoHelpers::response(['response' => ['title' => 'Terjadi kesalahan !', 'message' => 'Obat pada resep tidak boleh kosong.']], 422);
            }
            
            $post = [
                'reseptur_id' => $id,
                'ruangan_id' => Yii::$app->docoVars->workspace("ruangan_id"),
                'listObat' => $listObat,
                'totalharga_netto' => $totalharga_netto,
                'totalharga_jual' => !empty($post['totalharga_jual']) ? $post['totalharga_jual'] : 0,
                'keterangan' => !empty($post['keterangan']) ? $post['keterangan'] : null,
                'biayaadministrasi' => !empty($post['biayaadministrasi']) ? $post['biayaadministrasi'] : 0
            ];

            $response = Yii::$app->docoRest->apotek->request('POST', 'reseptur/edit-detail', [
                'json'        => $post,
                'query' => [
                    'id' => $id,
                ]
            ]);

            $response = json_decode($response->getBody(),true);
            if ($response['metadata']['status'] == 200) {
                Yii::$app->cache->delete('reseptur' . $ruangan_id.'-'.$pegawai_id);
                Yii::$app->cache->delete('addObatRs' . $ruangan_id.'-'.$pegawai_id);
            }
            return DocoHelpers::response($response,false,true);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => json_decode($e->getResponse()->getBody()->getContents())],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}
