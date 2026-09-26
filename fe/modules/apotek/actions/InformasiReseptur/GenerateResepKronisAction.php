<?php

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use app\components\DocoConstants;

class GenerateResepKronisAction extends Action {
    public function run() {
        try {
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $request = Yii::$app->request;
            $post = $request->post();
            $cacheLabel = 'addObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;
            $urutObatPasien = 'urutObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;
            $cacheLabelTrackStock = 'trackObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;
            $cacheTrans = Yii::$app->cache->get($cacheLabel);
            $list_obat = json_decode($cacheTrans, true);
            $val_catatan = $post['val_catatan'];
            $val_qty = $post['val_qty'];
            $val_signa = $post['val_signa'];

            // // mapping data ke list_obat
            foreach ($list_obat as $key => $value) {
                $list_obat[$key]['etiket'] = $val_catatan[$value['posisi']];
                $list_obat[$key]['qty'] = $val_qty[$value['posisi']];
                $list_obat[$key]['signa'] = $val_signa[$value['posisi']];
            }

            if (count($list_obat) <= 0) {
                return DocoHelpers::response(['response'=>['title'=>'Terjadi Kesalahan','message' => 'Obat Tidak Boleh Kosong!']],500);
            }
            
            $kelompok_resep = [];
            foreach ($list_obat as $item_obat) {
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
            
            $dataPost = [
                "jenispenjualan" => DocoConstants::JUAL_BEBAS,
                "carabayar_id" => isset($post['carabayar_id']) ? $post['carabayar_id'] : "",
                "penjamin_id" => isset($post['penjamin_id']) ? $post['penjamin_id'] : "",
                "ruangan_id" => $ruangan_id,
                "list_obat" => $final_list,
                "sep" => $post['sep'],
                "penjualanresep_asal" => $post['penjualanresep_id'] != 0 ? $post['penjualanresep_id'] : "",
                "reseptur_asal" =>$post['reseptur_id'] != 0 ? $post['reseptur_id'] : "",
                "pembeli" => $post['nama_pembeli'],
                "dokter" => !empty($post['dokter']) ? $post['dokter'] : "",
                "total_obat" => $post['total_obat'],
                "biayaadministrasi" => $post['biayaadministrasi'],
                "totalharga_netto" => $post['totalharga_netto'],
                "tglpenjualan" => date('Y-m-d 00:00:00'),
                "antrian_id" => $post['antrian_id'] != 0 ? $post['antrian_id'] : "",
                'pasien_id' => $post['pasien_id'],
            ];
            
            $response = $this->controller->guzzleExec(Yii::$app->docoRest->apotek,[
                'url' => 'inf-reseptur/generate-resep-kronis',
                'method' => 'POST',
                'payload' => [
                    'form_params' => $dataPost
                ],
                'returnResponse' => true
            ]);

            if ($response['meta']['code'] == 200) {
                Yii::$app->cache->delete($cacheLabel);
            } else {
                $response['status'] = false;
                $response['data_obat'] = $list_obat;
            }
            
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
}