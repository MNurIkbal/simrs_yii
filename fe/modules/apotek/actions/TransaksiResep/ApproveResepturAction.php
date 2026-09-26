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
use app\components\DHtml;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\TransaksiResepForm;
use Doco\apotek\components\filler\ResepDetailFiller;
use Doco\apotek\components\filler\EditResepturFiller;
use app\components\helpers\HandlingValueHelper;

class ApproveResepturAction extends Action {
    public function run($id) {
        $title = 'Approve Reseptur';
        $cache = Yii::$app->cache;
        $model = new TransaksiResepForm;
        $reseptur_id = DocoHelpers::decrypt($id);
        $data = EditResepturFiller::getData($reseptur_id);
        $decId = $reseptur_id;
        $list_signa = ArrayHelper::map($data['data_signa'], 'signa_id', 'kode_nama');
        $data_signa = json_encode($data['data_signa']);
        $checkBackdate = (DHtml::cekHakAkses('is-backdate')) ? '1' : '2';
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $no_resep = !empty($data['data_resep']['noresep']) ? $data['data_resep']['noresep'] : '-';
        $no_pendaftaran = !empty($data['data_resep']['no_pendaftaran']) ? $data['data_resep']['no_pendaftaran'] : '-';
        $no_rekam_medik = !empty($data['data_resep']['no_rekam_medik']) ? $data['data_resep']['no_rekam_medik'] : '-';
        $nama_pasien = !empty($data['data_resep']['nama_pasien']) ? $data['data_resep']['nama_pasien'] : '-';
        $nama_pegawai = !empty($data['data_resep']['nama_pegawai']) ? $data['data_resep']['nama_pegawai'] : '-';
        $statusReseptur = $data['data_resep']['status_reseptur_id'];
        $nomor = DocoHelpers::encrypt(HandlingValueHelper::nullValue($no_resep));
        //sanitize
        $nama_pasien = strip_tags(str_replace("&nbsp;", " ", htmlentities($nama_pasien)));
        $nama_pegawai = strip_tags(str_replace("&nbsp;", " ", htmlentities($nama_pegawai)));

        $instalasi_nama = !empty($data['data_resep']['instalasi_reseptur']) ? $data['data_resep']['instalasi_reseptur'] : '-';
        $ruangan_nama = !empty($data['data_resep']['ruangan_reseptur']) ? $data['data_resep']['ruangan_reseptur'] : '-';
        $carabayar_nama = !empty($data['data_resep']['carabayar_nama']) ? $data['data_resep']['carabayar_nama'] : '-';
        $penjamin_nama = !empty($data['data_resep']['penjamin_nama']) ? $data['data_resep']['penjamin_nama'] : '-';
        $tanggal_lahir = !empty($data['data_resep']['tanggal_lahir']) ? date('d-m-Y', strtotime($data['data_resep']['tanggal_lahir'])) : '-';
        $berat_badan = !empty($data['data_resep']['berat_badan']) ? $data['data_resep']['berat_badan'] : '-';
        $tinggi_badan = !empty($data['data_resep']['tinggi_badan']) ? $data['data_resep']['tinggi_badan'] : '-';
        $penjualanresep_id = !empty($data['data_resep']['penjualanresep_id']) ? $data['data_resep']['penjualanresep_id'] : null;
        $biayaadministrasi = !empty($data['data_resep']['biaya_administrasi']) ? $data['data_resep']['biaya_administrasi'] : 0;
        $status_bayar = !empty($data['data_resep']['status_bayar']) ? $data['data_resep']['status_bayar'] : null;

        if(isset($data['data_resep']['diagnosa_id'])){
            $diagnosa = $data['data_resep']['diagnosa_nama'];
        } else if(isset($data['data_resep']['diagnosa_text'])) {
            $diagnosa = $data['data_resep']['diagnosa_text'];
        } else {
            $diagnosa = null;
        }

        $cacheLabel = 'addObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;
        Yii::$app->cache->set($cacheLabel, null);
        $cacheLabelTrackStock = 'trackObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;
        Yii::$app->cache->set($cacheLabelTrackStock, null);
        $cacheLabelTrackEdit = "trackObatEditReseptur";

        $alergi = !empty($data['data_alergi']) ? $data['data_alergi'] : null;
        $iter = !empty($data['data_resep']['iter']) ? $data['data_resep']['iter'] : 0;
        $temp_cache = [];

        $transApotek = 'null';
        $urutObatPasien = 'null';
        $get_dataResep = isset($data['data_resep']) ? $data['data_resep'] : [] ;
        $disabled = false;
        $display = 'block';
        $catatan = '';
        if (count($get_dataResep) > 0) {
            if ($get_dataResep['status_reseptur'] == 'Sudah Diproses') {
                $disabled = true;
                $display = 'none';
            }
            $catatan = $get_dataResep['catatan'];
        }

        $apotek = json_decode($transApotek, true);
        $list_cache = isset($data['detail_resep']) ? $data['detail_resep'] : [] ;
        $data_racikan = isset($data['data_racikan']) ? $data['data_racikan'] : [] ;
        $list = [];
        if(count($list_cache) > 0):
            foreach($list_cache as $key => $value):
                $qty_hitung = isset($value['det']) ? $value['det'] : $value['qty_oa'];
                $harga_jual_oa = $value['hargajual_satuan'] * $qty_hitung;
                $signa_text = '-';
                if(!empty($value['signa'])){
                    $jsonSigna = json_decode($value['signa'],true);
                    $signa_text = $jsonSigna['text'];
                }
                $list[$key] = [
                    'posisi'            => $key,
                    'is_deleted'        => false,
                    'pegawai_id'        => $pegawai_id,
                    'resepturdetail_id' => $value['resepturdetail_id'],
                    'obatalkes_id'      => $value['obatalkes_id'],
                    'obatalkes_nama'    => $value['obatalkes_nama'],
                    'signa'             => ($value['signa_nama'] == null) ? $signa_text : $value['signa_nama'],
                    'signa_id'          => $value['signa_id'],
                    'racikan_id'        => $value['racikan_id'],
                    'is_racikan'        => $value['racikan_id'] == 1 ? true : false,
                    'r_ke'              => !empty($value['rke']) ? $value['rke'] : '-',
                    'jenis_racikan'     => ($value['racikan_id'] == 1) ? Yii::t('fe', 'Racikan') : Yii::t('fe', 'Non Racikan'),
                    'qty'               => $value['qty_transaksi'],
                    'det'               => !is_null($value['det_transaksi']) ? $value['det_transaksi'] : $value['qty_transaksi'],
                    'harganetto'        => ($value['harga_netto'] != null) ? $value['harga_netto'] : 0,
                    'hargajual'         => $value['hargajual_satuan'],
                    'ppn'               => isset($value['ppn']) ? $value['ppn'] : 0,
                    'persendiscount'    => isset($value['persendiscount']) ? $value['persendiscount'] : 0,
                    'jmldiscount'       => isset($value['jmldiscount']) ? $value['jmldiscount'] : 0,
                    'persenppn'         => isset($value['persenppn']) ? $value['persenppn'] : 0,
                    'jmlppn'            => isset($value['jmlppn']) ? $value['jmlppn'] : 0,
                    'persenmargin'      => isset($value['persenmargin']) ? $value['persenmargin'] : 0,
                    'jmlmargin'         => isset($value['jmlmargin']) ? $value['jmlmargin'] : 0,
                    'satuankecil_id'    => $value['satuankecil_id'],
                    'harga'             => $value['hargajual_satuan'],
                    'subtotal'          => $harga_jual_oa,
                    'qty_konversi'      => isset($value['qty_konversi']) ? $value['qty_konversi'] : 0,
                    'satuaninput_id'    => isset($value['qty_konversisatuaninput_id']) ? $value['satuaninput_id'] : 0,
                    'satuan_input'      => isset($value['satuan_input']) ? $value['satuan_input'] : 0,
                    'satuankonversi_id' => isset($value['satuankonversi_id']) ? $value['satuankonversi_id'] : 0,
                    'satuan_konversi'   => isset($value['satuan_konversi']) ? $value['satuan_konversi'] : 0,
                    'harga_konversi'    => isset($value['harga_konversi']) ? $value['harga_konversi'] : 0,
                    'etiket'            => isset($value['etiket']) ? strip_tags($value['etiket']) : '-',
                    'catatan'            => isset($value['catatan']) ? strip_tags($value['catatan']) : '-',
                    'nama_racikan' => !empty($value['nama_racikan']) ? $value['nama_racikan'] : "",
                    'satuan_racikan_id' => !empty($value['satuan_racikan_id']) ? $value['satuan_racikan_id'] : "",
                    'satuan_racikan_nama' => !empty($value['satuan_racikan_nama']) ? $value['satuan_racikan_nama'] : "",
                    'qty_racikan' => !empty($value['qty_racikan']) ? $value['qty_racikan'] : ""
                ];
            endforeach;
            Yii::$app->cache->set('urutObatEditReseptur' . $ruangan_id . '-' . $pegawai_id, count($list_cache) - 1);
        endif;

        $list_obat = json_encode($list);
        Yii::$app->cache->set('addObatEditReseptur' . $ruangan_id . '-' . $pegawai_id, $list_obat);
        $urutObatRs =  Yii::$app->cache->get('urutObatEditReseptur' . $ruangan_id . '-' . $pegawai_id);

        // grouping dan re arrange urutan resep
        $final_list = $this->controller->groupingResep($list);

        $transApotek = json_encode($final_list);
        $konfig_pembulatan = isset($data['konfig']['pembulatanharga']) ? $data['konfig']['pembulatanharga'] : 0;
        $isPembulatan = isset($data['konfigsys']['is_pembulatankeatas']) ? $data['konfigsys']['is_pembulatankeatas'] : false;
        $satuanPembulatan = isset($data['konfigsys']['satuanpembulatan']) ? $data['konfigsys']['satuanpembulatan'] : 0;

        $penjamin_id = ArrayHelper::getValue($data['data_resep'],'penjamin_id',1);
        $kelaspelayanan_id = ArrayHelper::getValue($data['data_resep'],'kelaspelayanan_id',1);

        $list_obat_ruangan = $list_obat_ruangan_options = [];
        if(isset($data['obat_ruangan']) && is_array($data['obat_ruangan']) && count($data['obat_ruangan'])){
            foreach ($data['obat_ruangan'] as $obat) {
                $list_obat_ruangan[$obat['obatalkes_id']] = $obat['obatalkes_nama'];
                $list_obat_ruangan_options[$obat['obatalkes_id']] = [
                    'disabled' => ($obat['qty_tersedia'] <= 0)
                ];
            }
        }

        $type = 'reseptur';
        $satuan_unit = ArrayHelper::map($data['satuan_unit'], 'satuanunit_id', 'satuanunit_nama');

        return $this->controller->render('approve-reseptur', get_defined_vars());
    }
}
