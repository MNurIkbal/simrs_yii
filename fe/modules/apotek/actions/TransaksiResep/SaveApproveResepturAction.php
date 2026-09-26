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

class SaveApproveResepturAction extends Action {
    public function run($id) {
        try {
            $request = Yii::$app->request->get();
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $post = Yii::$app->request->post();
            $arr_det = $post['det'];

            $cacheLabel = 'addObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;
            $urutLabel = 'urutObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;
            $cacheLabelTrackStock = 'trackObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;
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

            // mapping det ke listObat
            $jmlHapusObat = 0;
            $index = $totalharga_netto = $embalase = 0;
            foreach ($listObat as $key => $value) {
                $det = $arr_det[$value['posisi']];
                $det_hitung = ceil($det);
                $listObat[$key]['det'] = $det;
                if(isset($value['embalase'])) {
                    $embalase = $value['embalase'];
                }

                if($det_hitung > 0) {
                    $listObat[$key]['subtotal'] = ($value['harga'] + ($embalase / $det_hitung)) * $det_hitung;
                    $listObat[$key]['subtotal_harganetto'] = ($value['harganetto'] * ($value['qty_konversi']/$value['qty'])) * $det_hitung;
                } else {
                    $listObat[$key]['subtotal'] = 0;
                    $listObat[$key]['subtotal_harganetto'] = 0;
                }
                $totalharga_netto += $listObat[$key]['subtotal_harganetto'];
                
                $index++;

                if (ArrayHelper::getValue($value, 'is_deleted')) {
                    $jmlHapusObat++;
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

            $response = Yii::$app->docoRest->apotek->request('POST', 'reseptur/approve-reseptur', [
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
            $error = json_decode($e->getResponse()->getBody(), true);
            return DocoHelpers::response($error);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}