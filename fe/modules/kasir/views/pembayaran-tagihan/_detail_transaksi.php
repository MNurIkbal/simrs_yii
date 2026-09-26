<?php

use app\components\DocoConstants;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$list_cara_bayar = ArrayHelper::map($caraBayar,'carabayar_id', 'carabayar_nama');
$list_penjamin = ArrayHelper::map($penjamin,'penjamin_id', 'penjamin_nama');

?>
<style type="text/css">
.dataTables_scroll {
    height: 441px !important;
    max-height: 441px !important;
    position: relative !important;
}
td.bg-yellow {
    background-color: #e5e509;
}
td.bg-yellow:hover {
    background-color: #e5e509 !important;
}
.table-hover > tbody > tr:hover td.bg-yellow {
  background-color: #e5e509 !important;
}
</style>
<div class="panel panel-default panel-bordered" style="height:537px;">
    <a id="info-heading" data-toggle="collapse" href="#detailtransaksi" role="button" aria-expanded="false" aria-controls="detailtransaksi" >
        <div class="panel-heading flex-container">
            <h6 class="panel-title"><?= Yii::t('fe', 'Detail Transaksi') ?></h6>
            <ul class="icons-list">
                <li><em id="chevron" class="fa fa-chevron-down"></em></li>
            </ul>
        </div>
    </a>

    <div class="panel-body collapse multi-collapse in" id="detailtransaksi">
            <div id="error_TagihanPasienFormdetail_tagihan"></div>
            <table id="detail-tagihan" class="table table-striped table-condensed table-hover" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th id="no"><?=\Yii::t("fe", "No");?></th>
                        <th id="tanggal"><?=\Yii::t("fe", "Tanggal");?></th>
                        <th id="instalasi"><?=\Yii::t("fe", "Instalasi/Ruangan");?></th>
                        <th id="kategori"><?=\Yii::t("fe", "Kategori");?></th>
                        <th id="tindakan"><?=\Yii::t("fe", "Tindakan/Obat");?></th>
                        <th id="qty"><?=\Yii::t("fe", "Qty");?></th>
                        <th id="harga"><?=\Yii::t("fe", "Harga");?>(Rp)</th>
                        <th id="cyto"><?=\Yii::t("fe", "Cito (Rp)");?></th>
                        <th id="cyto"><?=\Yii::t("fe", "Diskon ");?></th>
                        <th id="subtotal"><?=\Yii::t("fe", "Sub Total");?>(Rp)</th>
                        <th id="penjamin"><?=\Yii::t("fe", "Penjamin");?></th>
                        <th id="dijamin"><?=\Yii::t("fe", "Dijamin");?>(Rp)</th>
                        <th id="harusbayar"><?=\Yii::t("fe", "Harus Bayar");?>(Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // echo "<pre>";var_dump($model->detail_tagihan);
                        $_cache = [
                            'tindakan' => [],
                            'obat' => []
                        ];
                        $_mappPenjamin = $_mapCaraBay = [];
                        $total_tagihan = 0;
                        $total_ranap = 0;
                        if (!empty($model->pasienadmisi_id)) {
                            $total_ranap = !empty($totalTagihan) ? $totalTagihan : 0;
                        }
                        if (!empty($model->pasienmasukpenunjang_id)) {
                            $id = $model->pendaftaran_id;
                    }
                        $statusSet = false;
                        $tmpTableTransaksi = $tmpPenjamin = $optPenjamin = [];
                        $tmpBayarInsurance = [];
                        $optPenjamin = !empty($listPenjamin) ? $listPenjamin : [];
                        $biayaAdmin = isset($model->tmpTagihan['adm']) ? $model->tmpTagihan['adm'] : [];
                        $plafonPayer = isset($model->tmpTagihan['plafon_payer']) ? $model->tmpTagihan['plafon_payer'] : 0;
                        $plafonSubpayer = isset($model->tmpTagihan['plafon_subpayer']) ? $model->tmpTagihan['plafon_subpayer'] : 0;
                        $excessPasien = isset($model->tmpTagihan['excess_pasien']) ? $model->tmpTagihan['excess_pasien'] : 0;

                        $diskon_total_all = isset($biayaAdmin['nominal_diskon']) ? $biayaAdmin['nominal_diskon'] : 0 ;
                        $is_diskonadm_dijamin = isset($biayaAdmin['dijamin']) ? $biayaAdmin['dijamin'] : 0 ;
                        if(isset($biayaAdmin['defaultPenjamin']['id'])){
                            $adm_penjamin_id = $biayaAdmin['defaultPenjamin']['id'];
                        } else {
                            $adm_penjamin_id = 0;
                        }
                        $adm_penjamin = isset($biayaAdmin['dijamin']) ? $biayaAdmin['dijamin'] : 0 ;
                        $harga_admin = isset($biayaAdmin['harga']) ? $biayaAdmin['harga'] : 0 ;

                        $tmpAdmSubtotal = isset($biayaAdmin['subtotal_origin']) ? $biayaAdmin['subtotal_origin'] : 0;
                        $pasienAsuransi = false;
                        $arrDefaultTmp = [];
                        $isDefaultTmp = !empty($model->tmpTagihan['obat'] || $model->tmpTagihan['tindakan']) ? 'true' : 'false';
                        if ($model->detail_tagihan) :
                            $no = 1;
                            foreach ($model->detail_tagihan as $i => $value) :
                                $additionalData = isset($value['additional_data']) ? $value['additional_data'] : [];
                                $remarks = '';
                                if(!empty($additionalData)) {
                                    $additionalData = json_decode($additionalData, true);
                                    $remarks = isset($additionalData['remarks']) ? $additionalData['remarks'] : '';
                                }
                                $is_akomodasi = isset($value['is_akomodasi']) ? $value['is_akomodasi'] : false;
                                $tindakan_obat_nama = isset($value['tindakan_obat_nama']) ? $value['tindakan_obat_nama'] : '';
                                $dokterpenanggungjawab_nama = isset($value['dokterpenanggungjawab_nama']) ? $value['dokterpenanggungjawab_nama'] : '';
                                $pelayananId = isset($value['pelayanan_id']) ? $value['pelayanan_id'] : [];
                                $subTotalItem = isset($value['sub_total']) ? round($value['sub_total'],2) : 0;                                
                                $total_tagihan += $subTotalItem;
                                $is_paket = ($value['kelompoktindakan_nama'] == 'kelompok_paket')
                                                ? true : false;
                                $groupBayar = isset($value['groupcarabayar_id']) ? $value['groupcarabayar_id'] : null;
                                // flag untuk data yang tidak ada pada cara bayar baru
                                $background = '';
                                if (isset($value['is_valid']) && $value['is_valid'] === false && $value['is_valid'] !== null) {
                                    $background = 'background-color: #fdb7b7;';
                                }
                                $diskonItem = 0;
                                $dijamin = 0;
                                $totalDibayar = $subTotalItem - $diskonItem;
                                $namaPenjamin = $model->penjamin_nama;
                                $isPenjamin = $checkPenjamin = false;
                                $tindakan_obat_id = !empty($value['tindakan_obat_id']) ? $value['tindakan_obat_id'] : null;

                                if ($model->group_carabayar !== DocoConstants::GROUP_UMUM) {
                                    $pasienAsuransi = true;
                                    $dijamin = $subTotalItem - $diskonItem;
                                    $totalDibayar = 0;
                                    $isPenjamin = $checkPenjamin = true;
                                    //$tmpBayarInsurance += $dijamin;
                                }

                                $defaultPenjamin = [
                                    'id' => $model->penjamin_id,
                                    'text' => $model->penjamin_nama,
                                    'selected' => true
                                ];

                                $tindakan_nama = isset($value['tindakan_obat_id'])
                                ? (in_array($value['tindakan_obat_id'],$model->tindakan_visitdokter ) && $is_akomodasi == false ? $tindakan_obat_nama. ' - ' . $dokterpenanggungjawab_nama : $tindakan_obat_nama )
                                : $tindakan_obat_nama;
                                
                                $tindakan_nama = !empty($remarks) ? $tindakan_nama.' - '.$remarks : $tindakan_nama;
                                $isObat = !empty($value['is_obat']) ? true : false;

                                $cytoOrigin = isset($value['tarif_cyto']) ? $value['tarif_cyto'] : 0;
                                $hargaOrigin = isset($value['tarif_satuan']) ? $value['tarif_satuan'] : 0;
                                $penyulitOrigin = isset($value['tarifpenyulit_tindakan']) ? $value['tarifpenyulit_tindakan'] : 0;
                                if (!empty($value['is_overwrite'])) {
                                    $cytoOrigin = isset($value['cyto_origin']) ? $value['cyto_origin'] : 0;
                                    $hargaOrigin = isset($value['harga_origin']) ? $value['harga_origin'] : 0;
                                    $penyulitOrigin = isset($value['penyulit_origin']) ? $value['penyulit_origin'] : 0;
                                }
                                $defaultTmp = [
                                    'tanggal' => isset($value['tgl_pelayanan']) ? date('d-M-Y',strtotime($value['tgl_pelayanan'])) : '',
                                    'instalasi' => isset($value['ruangan_pelayanan']) ? $value['ruangan_pelayanan'] : '',
                                    'tindakan' => isset($tindakan_nama) ? $tindakan_nama : '',
                                    'kelompoktindakan_nama' => $value['kelompoktindakan_nama'],
                                    'qty' => isset($value['qty']) ? $value['qty'] : 0,
                                    'harga' => isset($value['tarif_satuan']) ? $value['tarif_satuan'] : 0,
                                    'harga_origin' => $hargaOrigin,
                                    'cyto' => isset($value['tarif_cyto']) ? $value['tarif_cyto'] : 0,
                                    'cyto_origin' => $cytoOrigin,
                                    'penyulit' => isset($value['tarifpenyulit_tindakan']) ? $value['tarifpenyulit_tindakan'] : 0,
                                    'penyulit_origin' => $penyulitOrigin,
                                    'subtotal' => !empty($subTotalItem) ? $subTotalItem - $diskonItem : 0,
                                    'subtotal_origin' => !empty($subTotalItem) ? $subTotalItem : 0,
                                    'penjamin' => $namaPenjamin,
                                    'dijamin' => $dijamin,
                                    'totalDibayar' => $totalDibayar,
                                    'tindakan_obat_id' => $value['tindakan_obat_id'],
                                    'isPenjamin' => $isPenjamin,
                                    'checkPenjamin' => $checkPenjamin,
                                    'defaultPenjamin' => $defaultPenjamin,
                                    'value' => $value,
                                    'nominal_diskon' => $diskonItem,
                                    'persen_diskon' => 0,
                                    'keterangan' => '',
                                    'is_obat' => $isObat,
                                    'pelayanan_id' => $pelayananId,
                                    'kelaspelayanan_id' => !empty($value['kelaspelayanan_id']) ?$value['kelaspelayanan_id']: null,
                                    'kelompoktindakan_id' => !empty($value['kelompoktindakan_id']) ?$value['kelompoktindakan_id']: null,
                                    'lob_id' => !empty($value['lob_id']) ?$value['lob_id']: null,
                                    'penjamingrade_id' => !empty($value['penjamingrade_id']) ?$value['penjamingrade_id']: null,
                                    'plafon_payer' => $plafonPayer,
                                    'plafon_subpayer' => $plafonSubpayer,
                                    'excess_pasien' => $excessPasien
                                ];
                                $arrTmpTableTransaksi = $defaultTmp;

                                if ($isObat && !empty($pelayananId)) {
                                    $arrTmpTableTransaksi = isset($model->tmpTagihan['obat'][$pelayananId]) 
                                                                ? $model->tmpTagihan['obat'][$pelayananId] : $defaultTmp;
                                } else {
                                    if (!empty($pelayananId)) {
                                        $arrTmpTableTransaksi = isset($model->tmpTagihan['tindakan'][$pelayananId])
                                                                ? $model->tmpTagihan['tindakan'][$pelayananId] : $defaultTmp;
                                    }
                                }
                                $diskonItemTmp = isset($arrTmpTableTransaksi['nominal_diskon']) ? (float) $arrTmpTableTransaksi['nominal_diskon'] : 0;
                                $arrTmpTableTransaksi['qty'] = isset($value['qty']) ? $value['qty'] : 0;
                                $arrTmpTableTransaksi['tanggal'] = isset($value['tgl_pelayanan']) ? date('d-M-Y',strtotime($value['tgl_pelayanan'])) : '';
                                $arrTmpTableTransaksi['harga'] = isset($value['tarif_satuan']) ? $value['tarif_satuan'] : 0;
                                $arrTmpTableTransaksi['cyto'] = isset($value['tarif_cyto']) ? $value['tarif_cyto'] : 0;
                                $arrTmpTableTransaksi['subtotal'] = !empty($subTotalItem) ? $subTotalItem - $diskonItemTmp : 0;
                                $arrTmpTableTransaksi['subtotal_origin'] = $subTotalItem;
                                $arrTmpTableTransaksi['penjamingrade_id'] = !empty($value['penjamingrade_id']) ? $value['penjamingrade_id']: null;
                                $arrTmpTableTransaksi['lob_id'] = !empty($value['lob_id']) ? $value['lob_id']: null;
                                $arrTmpTableTransaksi['kelompoktindakan_id'] = !empty($value['kelompoktindakan_id']) ? $value['kelompoktindakan_id']: null;
                                $arrTmpTableTransaksi['kelaspelayanan_id'] = !empty($value['kelaspelayanan_id']) ? $value['kelaspelayanan_id']: null;
                                $arrTmpTableTransaksi['pelayanan_id'] = !empty($value['pelayanan_id']) ? $value['pelayanan_id']: null;
                                
                                $tmpTableTransaksi[] = $arrTmpTableTransaksi;
                                $arrDefaultTmp[] = $defaultTmp;

                                if (!isset($tmpPenjamin[$value['penjamin_pelayanan_id']]) && $groupBayar !== DocoConstants::GROUP_UMUM) {
                                        $tmpPenjamin[$value['penjamin_pelayanan_id']] = true;
                                        $optPenjamin[] = $defaultPenjamin;
                                }

                                $row = [
                                    'penjamin_pelayanan_id' => $model->penjamin_id,
                                    'carabayar_pelayanan_id' => $model->carabayar_id,
                                    'tindakan_obat_id' => $value['tindakan_obat_id'],
                                    'kelaspelayanan_id' => $value['kelaspelayanan_id'],
                                    'kelompoktindakan_id' => $value['kelompoktindakan_id'],
                                    'pasien_id' => $value['pasien_id'],
                                    'pendaftaran_id' => $value['pendaftaran_id'],
                                    'dokterpenanggungjawab_id' => $value['dokterpenanggungjawab_id'],
                                    'pasienmasukpenunjang_id' => $value['pasienmasukpenunjang_id'],
                                    'qty' => $value['qty'],
                                    'ruangan_id' => $value['ruangan_id'],
                                    'instalasi_id' => $value['instalasi_id'],
                                    'sub_total' => $subTotalItem - $diskonItem,
                                    'tarif_satuan' => !empty($value['tarif_satuan'])
                                            ? $value['tarif_satuan'] : 0,
                                    'tarif_cyto' => !empty($value['tarif_cyto'])
                                            ? $value['tarif_cyto'] : 0,
                                    'pelayanan_id' => $value['pelayanan_id'],
                                    'group_jaminan' => $model->group_carabayar,
                                    'is_paket' => $is_paket,
                                    'is_valid' => isset($value['is_valid']) ? $value['is_valid'] : null,
                                    'penjamin' => $namaPenjamin,
                                    'dijamin' => $dijamin,
                                    'harusbayar' => $totalDibayar,
                                    'nominal_diskon' => isset($value['discount']) ? $value['discount'] : 0,
                                    'persen_diskon' => 0,
                                    'keterangan' => ''
                                ];
                                $flag = 'tindakan';
                                if (!empty($value['is_obat'])) :
                                    $flag = 'obat';
                                    $_cache['obat'][$value['pelayanan_id']] = $row;
                                else :
                                    $_cache['tindakan'][$value['pelayanan_id']] = $row;
                                endif;

                                if (!empty($value['list_penjamin'])) {
                                    $_mappPenjamin[$value['pelayanan_id']] = $value['list_penjamin'];
                                }

                                if (!empty($value['list_cara_bayar'])) {
                                    $_mapCaraBay[$value['pelayanan_id']] = $value['list_cara_bayar'];
                                }

                                if (!$statusSet && isset($value['penggunaan_uangmuka'])
                                    && isset($value['biaya_administrasi'])) :
                                    $model->pengguna_uang_muka = $value['penggunaan_uangmuka'];
                                    $model->biaya_administrasi = $value['biaya_administrasi'];
                                    $model->is_ecollect = $value['e_collection'];
                                    $model->nama_pemilik = $value['nama_pemrekening'];
                                    $model->nomor_rekening = $value['no_rekening'];
                                    $statusSet = true;
                            endif
                    ?>
                    <tr data-id="<?= $value['pelayanan_id'] ?>" style="<?php echo $background ?>">
                        <td class="text-center">
                            <?= $no ?>
                        </td>
                        <td>
                            <?= isset($value['tgl_pelayanan'])
                                    ? date('d-M-Y',strtotime($value['tgl_pelayanan']))
                                    : '' ?>
                        </td>
                        <td>
                            <?= isset($value['instalasi_pelayanan'])
                                    ? $value['instalasi_pelayanan']
                                    : '' ?> / <?= isset($value['ruangan_pelayanan'])
                                    ? $value['ruangan_pelayanan']
                                    : '' ?>
                        </td>
                        <td>
                            <?= isset($value['kelompoktindakan_nama'])
                                    ? $value['kelompoktindakan_nama']
                                    : '' ?>
                           
                        </td>
                        <td>
                            <?= isset($tindakan_nama)
                                    ? $tindakan_nama
                                    : '' ?> 
                           
                        </td>
                        <td class="text-right">
                            <?= isset($value['qty'])
                                    ? DocoHelpers::formatNumber($value['qty'])
                                    : 0 ?>
                        </td>
                        <td class="text-right tarif-satuan" id="tarif-satuan-<?php echo $no ?>">
                            <?= isset($value['tarif_satuan'])
                                    ? DocoHelpers::formatNumber($value['tarif_satuan'])
                                    : 0 ?>
                        </td>
                        <td class="text-right tarif-cyto" id="tarif-cyto-<?php echo $no ?>">
                            <?= isset($value['tarif_cyto'])
                                    ? DocoHelpers::formatNumber($value['tarif_cyto'])
                                    : 0 ?>
                        </td>

                        <td class="text-right tarif-diskon bg-yellow"" id="tarif-diskon-<?php echo $no ?>">
                            <?=  DocoHelpers::formatNumber($diskonItem) ?>
                        </td>
                        
                        <td class="text-right sub-total" id="sub-total-<?php echo $no ?>">
                            <?= !empty($subTotalItem)
                                    ? DocoHelpers::formatNumber($subTotalItem - $diskonItem)
                                    : 0 ?>
                        </td>

                        <td class="nama-penjamin" id="nama-penjamin-<?php echo $no ?>">
                            nama penjamin
                        </td>

                        <td class="text-right total-dijamin" id="total-dijamin-<?php echo $no ?>">
                            10000
                        </td>

                        <td class="text-right harus-dibayar" id="harus-dibayar-<?php echo $no ?>">
                            100
                        </td>
                    </tr>
                    <?php $no++; endforeach; ?>
                    <?php else : ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-center" colspan="8">
                            <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php endif; ?>
                    <?php
                        // Pembulatan Tagihan dan Asuransi
                        $val_rounded = $konfigSistem['satuanpembulatan'];
                        $total_pembulatan_tagihan = !empty($val_rounded) ? ceil($total_tagihan / $val_rounded) * $val_rounded : $total_tagihan;
                        $pembulatan_tagihan = $total_pembulatan_tagihan - $total_tagihan;
                        $subsidi_asuransi = isset($header['total_asuransi'])
                        ? $header['total_asuransi'] : 0;
                        // $total_pembulatan_asuransi = ceil($tmpBayarInsurance / 100) * 100;
                        // if($total_pembulatan_asuransi - $tmpBayarInsurance > 0 ){
                        //     $total_pembulatan_tagihan += 100;
                        // }
                        //$pembulatan_asuransi = $total_pembulatan_asuransi - $subsidi_asuransi;
                        
                        //Awalnya di arahkan ke $subsidi_asuransi dan $total_tagihan langsung
                        $model->total_tagihan = $total_pembulatan_tagihan;                        
                        //$model->subsidi_asuransi = $total_pembulatan_asuransi;

                        $model->biaya_administrasi += isset($header['total_administrasi'])
                            ? $header['total_administrasi'] : 0;
                        $model->uang_diterima = isset($header['total_pembayaran_pasien'])
                            ? $header['total_pembayaran_pasien'] : $total_pembulatan_tagihan;
                    ?>

                </tbody>
            </table>
    </div>
</div>

<?php 
// Ini untuk Init data di js

if(empty($optPenjamin)){
    $optPenjamin[] = !empty($defaultPenjamin) ? $defaultPenjamin : [] ;
}else{
    $tmpOptPenjamin = [];
    foreach ($optPenjamin as $k => $v){
        $id = isset($v['id']) ? $v['id'] : null;
        $tmpOptPenjamin[$id] = $v;
    }
    $optPenjamin = array_values($tmpOptPenjamin);
}

$_caraBayarPasien = !empty($model->carabayar_id) ? $model->carabayar_id : null;
$_cache = json_encode($_cache);
$_caraBayar = json_encode($caraBayar);
$_penjamin = json_encode($optPenjamin);
$_info = json_encode($model->attributes);
$curentUrl = Yii::$app->request->url;
$is_pembulatan = isset($konfigSistem['is_pembulatankeatas'])
    ? $konfigSistem['is_pembulatankeatas'] : false;
//satuan dari awalnya 50 dioverride jadi 100
$satuan = isset($konfigSistem['satuanpembulatan'])
    ? $konfigSistem['satuanpembulatan'] : 0;
$_mapCaraBay = json_encode($_mapCaraBay);
$_mappPenjamin = json_encode($_mappPenjamin);
$_tipe_pasien = isset($_GET['tipe']) ? $_GET['tipe'] : null;
$_penjualan_resep_id = !is_null($_tipe_pasien) 
    && ($_tipe_pasien == 'pasien_bebas' || $_tipe_pasien == 'pasien_rs') ? $_GET['id'] : null;
$isRekap = 0;
if (!empty($tmpAdmSubtotal) && $tmpAdmSubtotal == $total_admin) {
    /** handling ketika tambah / hapus tindakan */
    $admDiskon = !empty($biayaAdmin['nominal_diskon']) ? $biayaAdmin['nominal_diskon'] : 0;
    $biayaAdmin['subtotal'] = $total_admin - $admDiskon;
    $biayaAdmin['harga'] = $total_admin;
    $biayaAdmin['harga_origin'] = $total_admin;
    $tmpTableTransaksi[] = $biayaAdmin;
    $isRekap = 1;
}

$tmpTableTransaksi = json_encode($tmpTableTransaksi);
$_dateNow = date('d-M-Y');
$pendaftaran_id = !empty($model->pendaftaran_id) ? $model->pendaftaran_id : null;
$pasienmasukpenunjang_id = !empty($model->pasienmasukpenunjang_id) ? $model->pasienmasukpenunjang_id : null;
$isEditTagihan = !empty($konfigSistem['edit_billing']) ? 1 : 0;
$isSetPlafon = !empty($konfigSistem['is_set_plafon']) ? 1 : 0;
$tgl_pendaftaran = !empty($model->tgl_pendaftaran) ? $model->tgl_pendaftaran : null;

$this->registerJs('
var _id = "'.DocoHelpers::encrypt($id) .'";
var _caraBayarPasien = ' . $_caraBayarPasien .';
var _totalBiayaRanap = ' . $total_ranap .';
var _listCaraBayar = ' . $_caraBayar .';
var _listPenjamin = ' . $_penjamin .';
var _mapCaraBay = ' . $_mapCaraBay .';
var _dateNow = "' . $_dateNow .'";
var _mappPenjamin = ' . $_mappPenjamin .';
var tmpTableTransaksi = ' . $tmpTableTransaksi .';
var _totalDijamin = 0;
var isChanges = false;
var _info = ' . $_info .';
var _status = ' . $disabled .';
var _isKarcis = ' . $isKarcis .';
var _groupCaraBayar = {};
var _printKarcis = true;
var _jumUM = _totalTagihan = _subsidiAsuran = _oldPembayaran = 0;
var _typePasien = "'.DocoHelpers::encrypt($jenisAntrian).'";
var _cache = '. $_cache .';
var _tipe_pasien = "'. $_tipe_pasien .'";
var _penjualan_resep = "'.$_penjualan_resep_id.'";
var _administrasi_transaksi = '.$model->biaya_administrasi.';
var _is_pembulatan = "'. $is_pembulatan .'";
var _satuanpembulatan = "'. $satuan .'";
var _jasa = "'.$model->jasa.'";
var _administrasi = "'.$model->administrasi.'";
var _instalasi_id = "'.$model->instalasi_id.'";
var _adm_persen = "'.$adm_persen.'";
var _biaya_adm_maksimal = "'.$biaya_adm_maksimal.'";
var _total_admin = "'.$total_admin.'";
var _currentUrl = "'.$curentUrl.'";
var _noKartu = "'.$model->no_kartu.'";
var _pendaftaran_id = "'.$pendaftaran_id.'";
var _pasienmasukpenunjang_id = "'.$pasienmasukpenunjang_id.'";
var _isRekap = '. $isRekap . ';
var _isEditTagihan = '. $isEditTagihan .';
var _isSetPlafon = '. $isSetPlafon .';
var _tglPendaftaran = "'.$tgl_pendaftaran.'";
var _kontrakPenjamin = [];
var _tdTindakan = "'.DocoConstants::TD_TINDAKAN.'";
var _tdKelompok = "'.DocoConstants::TD_KELOMPOK.'";
var _tdKelas = "'.DocoConstants::TD_KELAS.'";
var _pasienAsuransi = "'.$pasienAsuransi.'";
var _arrDefaultTmp = '.json_encode($arrDefaultTmp).';
var _arrDefaultTmp = JSON.stringify(_arrDefaultTmp);
var _isDefaultTmp = '.$isDefaultTmp.';
var _pembulatan_tagihan = '. $pembulatan_tagihan .';
var _total_pembulatan_tagihan = '. $total_pembulatan_tagihan .';
var diskon_adm_val = '.$diskon_total_all.';
var is_diskonadm_dijamin = '.$is_diskonadm_dijamin.';
var adm_penjamin_id = '.$adm_penjamin_id.';
var harga_admin = '.$harga_admin.';

// Event Ready
$(document).ready(function(){
    tmpTablePenjamin = '.json_encode($listPenjamin).'
    // generateTablePenjamin()
});
', View::POS_END);

$this->registerJs($this->render('js/kasir.js'), View::POS_END);
?>
