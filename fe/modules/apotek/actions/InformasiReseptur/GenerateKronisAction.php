<?php

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DHtml;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use app\components\helpers\HandlingValueHelper;
use Doco\apotek\components\filler\ResepDetailFiller;
use app\models\reseptur\GeneralResepturForm;
use Doco\apotek\models\TransaksiResepForm;
use GuzzleHttp\Exception\RequestException;

class GenerateKronisAction extends Action {
    protected $_title = "Generate Resep Kronis";

    public function run($id, $nomor = null) {
        $title = 'Generate Resep Kronis';
        $id = DocoHelpers::decrypt($_GET['id']);
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);
        $data = [];

        $_requestDataResep = Yii::$app->cache->get('info-resep-' . $nomor);
        if (empty($_requestDataResep)) {
            $_requestDataResep = $this->controller->getInfoResep($nomor);
        }

        $_responDataResep = isset($_requestDataResep['response']) ? $_requestDataResep['response'] : [];
        $data_resep = $_responDataResep['data'];
        if($data_resep['jenis'] == 'resep'){
            $id = $data_resep['penjualanresep_id'];
        }else{
            $id = $data_resep['reseptur_id'];
        }
        $data_resep['nosep'] = isset($data_resep['nosep_bpjs']) ? $data_resep['nosep_bpjs'] : $data_resep['nosep'];

        $data = [];
        $data['detail_resep'] = Yii::$app->cache->get('data-obat-' . $nomor);
        $signa_ids = ArrayHelper::getColumn($data['detail_resep'], 'signa_id');
        $add_data = $this->controller->guzzleExec(Yii::$app->docoRest->apotek,[
            'url' => 'allow/view-info-resep',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'jenis' => $data_resep['jenis'],
                    'id' => $id,
                    'signa_ids' => $signa_ids,
                    'kode' => 'kronis_limit'
                ]
            ],
        ]);

        $decId = $id;
        $data['master_signa'] = $add_data['master_signa'];
        $data['data_penjualanresep'] = $add_data['data_penjualanresep'];

        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $transApotek = 'null';
        $konfigFarmasi = $this->controller->getKonfigFarmasi(false);
        $LookupTransaksi = $add_data['kode_transaksi'];
        $konfig_kronis = isset($konfigFarmasi['hari_resep_kronis']) ? $konfigFarmasi['hari_resep_kronis'] : 0;
        $limit_kronis = isset($LookupTransaksi['kode_id']) ? $LookupTransaksi['kode_id'] : 0;
        $apotek = json_decode($transApotek, true);
        $pasien = !empty($data_resep['nama']) ? $data_resep['nama'] : null;
        $antrian = !empty($data_resep['antrian_id']) ? $data_resep['antrian_id'] : 0;
        $bpjs = !empty($data_resep['nosep_bpjs']) ? $data_resep['nosep_bpjs'] : '';
        $dokter = !empty($data_resep['pegawai_id']) ? $data_resep['pegawai_id'] : null;
        $master_signa = !empty($data['master_signa']) ? $data['master_signa'] : [];
        $master_signa = ArrayHelper::index($master_signa, 'signa_id');
        $penjualanresep_id = isset($data_resep['penjualanresep_id']) ? $data_resep['penjualanresep_id'] : 0;
        $id_reseptur = isset($data_resep['reseptur_id']) ? $data_resep['reseptur_id'] : 0;
        $biayaadministrasi = !empty($data_resep['biayaadministrasi']) ? $data_resep['biayaadministrasi'] : 0;
        $penjamin_id = isset($data_resep['penjamin_id']) ? $data_resep['penjamin_id'] : null;
        $carabayar_id = isset($data_resep['carabayar_id']) ? $data_resep['carabayar_id'] : null;
        
        $cacheLabel = 'addObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;
        Yii::$app->cache->set($cacheLabel, null);
        $cacheLabelTrackStock = 'trackObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;
        Yii::$app->cache->set($cacheLabelTrackStock, null);
        $cacheLabelTrackEdit = "trackObatEditReseptur";
        Yii::$app->cache->set('editStatus','resep');

        $list_cache = isset($data['detail_resep']) ? $data['detail_resep'] : [] ;
        $list = [];
        if(count($list_cache) > 0):
            foreach($list_cache as $key => $value):
                if (!$value['is_kronis']) {
                    continue;
                }
                $qty_hitung = isset($value['det']) ? $value['det'] : $value['qty_reseptur'];
                $qty_signa = isset($value['qty_obat_signa']) ? (int) $value['qty_obat_signa'] : 1;
                $qty_signa = $qty_signa > 0 ? $qty_signa : 1;
                $iterasi_signa = isset($value['iterasi_signa']) ? (int) $value['iterasi_signa'] : 1;
                $harga_jual_oa = $value['hargajual_satuan'] * $qty_hitung;
                $hari = $limit_kronis - $konfig_kronis;
                $qty = $qty_signa * $iterasi_signa * $hari;
                $signa_text = '-';
                $additional_data = json_decode($value['additional_data'], true);
                if (!empty($value['signa'])) {
                    $jsonSigna = is_string($value['signa']) ? json_decode($value['signa'],true) : $value['signa'];
                    if(empty($jsonSigna['id'])){
                        $signa_text = $jsonSigna['text'];
                        // $hari = '-';
                        // $qty = '';
                    }
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
                    'qty_signa'         => isset($master_signa[$value['signa_id']]['qty_obat']) ? $master_signa[$value['signa_id']]['qty_obat'] : 0,
                    'iterasi'           => isset($master_signa[$value['signa_id']]['iterasi']) ? $master_signa[$value['signa_id']]['iterasi'] : 0,
                    'racikan_id'        => $value['racikan_id'],
                    'is_racikan'        => $value['racikan_id'] == 1 ? true : false,
                    'r_ke'              => !empty($value['rke']) ? $value['rke'] : '-',
                    'jenis_racikan'     => ($value['racikan_id'] == 1) ? Yii::t('fe', 'Racikan') : Yii::t('fe', 'Non Racikan'),
                    'qty'               => $qty,
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
                    'nilai_konversi'    => isset($additional_data['nilai_konversi']) ? $additional_data['nilai_konversi'] : 0,
                    'satuaninput_id'    => isset($value['satuaninput_id']) ? $value['satuaninput_id'] : 0,
                    'satuan_input'      => isset($value['satuan_input']) ? $value['satuan_input'] : 0,
                    'satuankonversi_id' => isset($value['satuankonversi_id']) ? $value['satuankonversi_id'] : 0,
                    'satuan_konversi'   => isset($value['satuan_konversi']) ? $value['satuan_konversi'] : 0,
                    'harga_konversi'    => isset($value['harga_konversi']) ? $value['harga_konversi'] : 0,
                    'etiket'            => isset($value['etiket']) ? strip_tags($value['etiket']) : '-',
                    'catatan'            => isset($value['catatan']) ? strip_tags($value['catatan']) : '-',
                    'nama_racikan' => !empty($value['nama_racikan']) ? $value['nama_racikan'] : "",
                    'satuan_racikan_id' => !empty($value['satuan_racikan_id']) ? $value['satuan_racikan_id'] : "",
                    'satuan_racikan_nama' => !empty($value['satuan_racikan_nama']) ? $value['satuan_racikan_nama'] : "",
                    'qty_racikan' => !empty($value['qty_racikan']) ? $value['qty_racikan'] : "",
                    'kronis'            => !empty($value['is_kronis']) ? $value['is_kronis'] : "",
                    'hari'          => $hari
                ];
            endforeach;
        endif;

        $list_obat = json_encode($list);
        Yii::$app->cache->set('addObatEditReseptur' . $ruangan_id . '-' . $pegawai_id, $list_obat);
        $urutObatRs =  Yii::$app->cache->get('urutObatEditReseptur' . $ruangan_id . '-' . $pegawai_id);
        $final_list = $this->controller->groupingResep($list);
        $transApotek = json_encode($final_list);
        $master_signa = json_encode($master_signa);

        return $this->controller->renderAjax('_modal_generate_kronis', compact(
            'title','urutObatRs','bpjs', 'decId',
            'pasien', 'transApotek','biayaadministrasi',
            'dokter','id_reseptur','penjualanresep_id',
            'cacheLabel','cacheLabelTrackStock','cacheLabelTrackEdit',
            'master_signa', 'penjamin_id', 'carabayar_id', 'antrian', 'data_resep'
        ));
    }
}