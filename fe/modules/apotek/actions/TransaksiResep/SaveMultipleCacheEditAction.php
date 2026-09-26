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

class SaveMultipleCacheEditAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $post = $request->post();
        $get = $request->get();

        try {
            $list_obat = $post['data'];
            $konfigFarmasi = $this->controller->getKonfigFarmasi(true);
            $embalase_racikan = $konfigFarmasi['embalase_racikan'];
            $embalase_nonracikan = $konfigFarmasi['embalase_nonracikan'];

            $cacheKey = isset($get['cacheKey']) ? $get['cacheKey'] : null;
            $cacheLabel = 'addObatEditReseptur' . $cacheKey;
            $urutLabel = 'urutObatEditReseptur' . $cacheKey;
            $cacheLabelTrackStock = 'trackObatEditReseptur' . $cacheKey;

            $cacheTrans = Yii::$app->cache->get($cacheLabel);
            $editStatus = Yii::$app->cache->get('editStatus');
            $transApotek = json_decode($cacheTrans, true);

            $no_urut = Yii::$app->cache->get($urutLabel);

            if ($no_urut === false || $no_urut < 1) {
                $start_urut = 0;
                Yii::$app->cache->set($urutLabel, $start_urut);
            }

            if (!isset($post['posisi'])) {
                $posisi = $no_urut + 1;
            }

            $racikan = ArrayHelper::map($transApotek, "r_ke", "r_ke");
            $racikan_post = ArrayHelper::map($list_obat, "r_ke", "r_ke");

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
                                $count_r_ke_reseptur[$value['r_ke']]++;
                            }
                        }
                    }
                }
            }

            $status_racikan = [];

            foreach ($racikan_post as $r_ke) {
                foreach ($list_obat as $key => $value) {
                    if (isset($value['r_ke'])) {
                        if($value['r_ke'] == $r_ke){
                            if (!isset($count_r_ke[$value['r_ke']])) {
                                $count_r_ke[$value['r_ke']] = 1;
                            } else {
                                $count_r_ke[$value['r_ke']]++;
                            }
                            $status_racikan[$value['r_ke']] = true;
                        }else{
                            $status_racikan[$value['r_ke']] = false;
                        }
                    }
                }
            }

            foreach ($list_obat as $obat_racik) {
                if($obat_racik['hargajual'] == 0) {
                    $embalase = 0;
                    $harga_dgn_embalase = 0;
                } else {
                    $harga_jual = $obat_racik['hargajual'] * $obat_racik['nilai_konversi'];
                    $harga_jual_oa = $harga_jual * $obat_racik['qty'];
                    $harga_netto_oa = ($obat_racik['harganetto'] * $obat_racik['nilai_konversi']) * $obat_racik['qty'];

                    $embalase = 0;
                    $harga_dgn_embalase = $harga_jual + ($embalase / $obat_racik['qty']);
                }

                $subtotal = $harga_dgn_embalase * $obat_racik['qty'];

                $return = [
                    'posisi' => $posisi,
                    'is_deleted' => false,
                    'embalase' => $embalase,
                    'pegawai_id' => $pegawai_id,
                    'obatalkes_id' => $obat_racik['obatalkes_id'],
                    'obatalkes_nama' => $obat_racik['obat_nama'],
                    'signa' => ( $obat_racik['signa_nama'] == '— Pilih —' || $obat_racik['signa_nama'] == '') ? '-' :  $obat_racik['signa_nama'],
                    'signa_id' => $obat_racik['signa'],
                    'racikan_id' => 1 ,
                    'jenis_racikan' => Yii::t('fe', 'Racikan'),
                    'qty' => $obat_racik['qty'],
                    'ppn' => $obat_racik['ppn'],
                    'kronis' => $obat_racik['kronis'],
                    'harganetto' => $obat_racik['harganetto'],
                    'hargajual' => $obat_racik['hargajual'],
                    'persendiscount' => $obat_racik['persendiscount'],
                    'jmldiscount' => $obat_racik['jmldiscount'],
                    'persenppn' => $obat_racik['persenppn'],
                    'jmlppn' => $obat_racik['jmlppn'],
                    'persenmargin' => $obat_racik['persenmargin'],
                    'jmlmargin' => $obat_racik['jmlmargin'],
                    'satuankecil_id' => $obat_racik['satuankecil_id'],
                    'harga' => $harga_dgn_embalase,
                    'is_racikan' => true,
                    'r_ke' => (int) $obat_racik['r_ke'],
                    'subtotal' => $subtotal,
                    'catatan' => $obat_racik['catatan'],
                    'nilai_konversi' => $obat_racik['nilai_konversi'],
                    'qty_konversi' => $obat_racik['qty_konversi'],
                    'satuaninput_id' => $obat_racik['satuaninput_id'],
                    'satuan_input' => $obat_racik['satuan_input'],
                    'satuankonversi_id' => $obat_racik['satuankonversi_id'],
                    'satuan_konversi' => $obat_racik['satuan_konversi'],
                    'harga_konversi' => $obat_racik['harga_kecil'],
                    'nilai_konversi' => $obat_racik['nilai_konversi'],
                    'etiket' => $obat_racik['catatan'],
                    'nama_racikan' => $obat_racik['nama_racikan'],
                    'satuan_racikan_id' => $obat_racik['satuan_racikan_id'],
                    'satuan_racikan_nama' => $obat_racik['satuan_racikan_nama'],
                    'qty_racikan' => $obat_racik['qty_racikan'],
                    'qty_tersedia' => $obat_racik['stok'],
                ];
                $transApotek[$posisi] = $return;
                $posisi++;

                $this->controller->trackStokResep(
                    /*cache_label*/     $cacheLabelTrackStock,
                    /*obatalkes_id*/    $return['obatalkes_id'],
                    /*qty*/             $return['qty_konversi']
                );
            }

            foreach($transApotek as $trans){
                if($trans['r_ke'] != "-" && @$status_racikan[$trans['r_ke']]){
                    if(isset($trans['obatalkespasien_id']) || isset($trans['resepturdetail_id'])){
                        if($editStatus == 'reseptur'){
                            if(!isset($trans['r_new_obat'])){
                                $trans['harga_konversi'] = $trans['hargajual'];
                            }else{
                                $embalase = ($embalase_racikan/$count_r_ke_reseptur[$trans['r_ke']])/$trans['qty'];
                                $trans['harga_konversi'] = $trans['hargajual'] - $embalase;
                            }
                            $embalase = ($embalase_racikan/$count_r_ke[$trans['r_ke']])/$trans['qty'];
                            $nilai_konversi = isset($trans['nilai_konversi']) ? $trans['nilai_konversi'] : 1;
                            $harga_jual = $trans['harga_konversi'] * $nilai_konversi;
                            $transApotek[$trans['posisi']]['hargajual'] = $harga_jual + $embalase;         
                        }
                    }
                }
                $transApotek[$trans['posisi']]['r_new_obat'] = true;
            }

            Yii::$app->cache->set($urutLabel, $posisi);
            $transApotek = $this->controller->recalculatingCache($transApotek);
            
            foreach ($transApotek as $key => $value) {
                if(isset($value['is_deleted']) && $value['is_deleted']) {
                    $transApotek[$key]['subtotal'] = 0;
                }
            }

            $listTrans = json_encode($transApotek);
            Yii::$app->cache->set($cacheLabel, $listTrans);

            $final_list = $this->controller->groupingResep($transApotek);

            $result['data'] = $final_list;
            $result['message'] = Yii::t('fe', 'Data berhasil di simpan');
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
}
