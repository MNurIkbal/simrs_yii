<?php

use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

//umur
$d1 = new DateTime(date("y-m-d"));
$d2 = new DateTime($getHeader['tanggal_lahir']);
$diff = $d2->diff($d1);
?>

<style type="text/css">
    .tbl-resep {
        font-family: Tahoma, serif;
        font-size: 11px;
        margin-top: 10px;
        table-layout: fixed;
        width: 100%;
        padding: 0;
    }
    .tbl-resep tr {
        /* padding: 1px; */
    }
    .tbl-resep tr td {
        /* padding: 1px; */
        font-size: 11px;
    }
    .tbl-resep tr td.text-bigger {
        font-size: 24px;
        font-weight: bold;
    }

    .tbl-info {
        width: 100%;
        table-layout:fixed;
        font-size: 8px;
    }
    .tbl-info tr td {
        /* padding: 2px 4px; */
        /* height: 40px; */
        vertical-align: center;
    }

    .tbl-judul {
        width: 100%;
        table-layout:fixed;
        font-size: 12px;
    }
    .tbl-judul tr td {
        padding: 2px;
        height: 30px;
        vertical-align: center;
        text-align: center;
        border-spacing: 10px;
    }
    .tbl-judul .table {
        display: table;
        border-collapse: separate;
        border-spacing: 10px;
    }
    .tbl-judul .row {
        display: table-row;
    }
    .tbl-judul .cell {
        display: table-cell;
        padding: 10px;
        background-color: gold;
    }
    .tbl-judul tr td.text-bigger {
        font-size: 24px;
    }
    
    .tbl-footer {
        width: 100%;
        table-layout:fixed;
        font-size: 10px;
    }
    .tbl-footer tr {
        border-right: 1pt solid black;
    }
    #footer {
        position: fixed;
        bottom: 0;
        width: 100%;
    }

    @media print {
        
        /* .tbl-resep {page-break-after: always;} */
        /* prevent blank page at end */
        /* .tbl-resep:last-of-type {page-break-after: auto;} */
        .pageBreak {page-break-after: always;}
    }

</style>
<?php
$nama_rs = !empty($profil_rs->nama_rumahsakit) ? $profil_rs->nama_rumahsakit : "Rumah Sakit";
$alamat = !empty($profil_rs->alamatlokasi_rumahsakit) ? $profil_rs->alamatlokasi_rumahsakit : "-";
$telp = !empty($profil_rs->no_telp_profilrs) ? "Telp" . $profil_rs->no_telp_profilrs : "-";
$profile = $alamat . ", " .  $telp;
$title = ($type == "resep") ? "Resep" : "Salinan Resep";
$bb = !empty($getHeader['berat_badan']) ? $getHeader['berat_badan'] : '-';
$tb = !empty($getHeader['tinggi_badan']) ? $getHeader['tinggi_badan'] : '-';
$str_bb_tb =  $bb . ' Kg / ' . $tb . ' cm';
$no_pendaftaran = !empty($getHeader['no_pendaftaran']) ? $getHeader['no_pendaftaran'] : '-';
$kelaspelayanan_nama = !empty($getHeader['kelaspelayanan_nama']) ? $getHeader['kelaspelayanan_nama'] : '-';
$diperiksa = !empty($status_worklist[DocoConstants::WORKLIST_QC]['nama_pegawai']) ? $status_worklist[DocoConstants::WORKLIST_QC]['nama_pegawai'] : '';
$diserahkan = !empty($status_worklist[DocoConstants::WORKLIST_SIAP_DISERAHKAN]['nama_pegawai']) ? $status_worklist[DocoConstants::WORKLIST_SIAP_DISERAHKAN]['nama_pegawai'] : '';
$index_all = 0;
?>

<table class="tbl-info" style = "border:1; border-style:groove;">
    <tbody>
        <tr>
            <td style="width: 19%;"><?= Yii::t('app', 'Tgl. Resep') ?></td>
            <td style="width: 1%;">:</td>
            <td style="width: 30%;"><?= !empty($getHeader['tgl_resep_dibuat']) ? date("d M Y H:i", strtotime($getHeader['tgl_resep_dibuat'])) : '-' ?></td>
            <td style="width: 19%;" colspan=""><?= Yii::t('app', 'Nama Pasien') ?></td>
            <td style="width: 1%;">:</td>
            <td style="width: 30%;">  <?= !empty($getHeader['nama']) ? $getHeader['nama'] : '-'  ?> </td>
        </tr>
        <tr>
            <td style="width: 19%;"><?= Yii::t('app', 'Dokter') ?></td>
            <td style="width: 1%;">:</td>
            <td style="width: 30%;"><?= !empty($getHeader['nama_pegawai']) ? $getHeader['nama_pegawai'] : '-' ?></td>
            <td style="width: 19%;"><?= Yii::t('app', 'Umur') ?></td>
            <td style="width: 1%;">:</td>
            <td ><?= $diff->y.' Tahun '.$diff->m.' Bulan'; ?></td>
        </tr>
        <tr style = "border:1; border-style:groove;">
            <td style="width: 19%;"><?= Yii::t('app', 'No . Resep') ?></td>
            <td style="width: 1%;">:</td>
            <td style=""><?= !empty($getHeader['nomor']) ? $getHeader['nomor'] : '-' ?></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
<table class="tbl-resep"  style = "">
    <?php
    if(!empty($data_obat['non_racikan'])){
        $total = count($data_obat['non_racikan'])-1;
        $total_nonracikan = count($data_obat['non_racikan']);
        foreach($data_obat['non_racikan'] as $index => $value):
            $index_all =+ $index+1;
        ?>
            <tr>
                <td style="width: 10%;" class="text-bigger">R/</td>
                <td colspan="2"><?=$value['obatalkes_nama'].' '.$value['qty_transaksi'].' '.$value['satuan_input'];?></td>
            </tr>
            <tr>
                <td style="width: 10%;"> </td>
                <td style="" colspan="2"><i>s. </i><?=$value['signa_nama'];?></td>
            </tr>
            <tr>
                <td style="width: 10%;"> </td>
                <td style="" colspan="2"><?= !empty($value['etiket'])?$value['etiket']:'-';?></td>
            </tr>
            <?php if($type == 'salinan') { 
                $det = !empty($value['det']) ? $value['det'] : null;
                $qty = !empty($value['qty_transaksi']) ? $value['qty_transaksi'] : null;
                $satuan_input = !empty($value['satuan_input']) ? $value['satuan_input'] : '';
                $str_det = $det != 0 ? ($det < $qty ? '<i>det</i> ' . $det.' '.$satuan_input : "") : '<i>ne det </i>';
                ?>
                <tr>
                    <td style="width: 10%;"> </td>
                    <td>
                        <?= ($qty != $det) ? "<hr style='border: none; height: 5px; background-color: #000; width:100%;' />" : "" ?>
                    </td>
                    <td style="width: 35%;">&nbsp;<strong> <?= $str_det;?></strong></td>
                </tr>
            <?php } ?>
        <?php
            endforeach; 
        } ?>
        <tr> <td colspan="3">&nbsp;</td> </tr>
    <?php
    if(!empty($data_obat['racikan'])){
        $total_racikan = count($data_obat['racikan']);
        foreach($data_obat['racikan'] as $index => $value):
            ?>
            <tr>
                <td style="width: 10%;" valign="top" class="text-bigger">R/</td>
                <td colspan="2">
                    <table class="" style = "tbl-resep">
                        <?php 
                        if(!empty($value) && is_array($value)):
                            foreach($value as $i => $val):
                                $index_all =$index_all + 1;
                                $det = !empty($val['det']) ? $val['det'] : null;
                                $qty = !empty($val['qty_transaksi']) ? $val['qty_transaksi'] : null;
                                $satuan_input = !empty($val['satuan_input']) ? $val['satuan_input'] : '';
                                // $str_det = ($det < $qty) && $det != null ? '<i>det</i> ' . $det.' '.$satuan_input : '<i>no det </i>' ;
                                $str_det = $det != 0 ? '<i>det</i> ' . $det.' '.$satuan_input : '<i>ne det </i>' ;
                                $str_det = ($qty == $det) ? "" : $str_det;
                                ?>
                                <tr>
                                    <td style="" colspan="2"><i> </i><?=$val['obatalkes_nama'].' '.$qty.' '.$val['satuan_input'] ?> </td>
                                </tr>
                                <tr>
                                    <td style="" colspan="2"><i>s. </i><?=$val['signa_nama'];?></td>
                                </tr>
                                <tr >
                                    <td style="" colspan="2"><?= !empty($val['etiket']) ? $val['etiket']:'-'; ?></td>
                                </tr>
                                <?php 
                                    if($type == 'salinan') :
                                        ?>
                                        <tr>
                                            <td>
                                                <?= ($qty != $det) ? "<hr style='border: none; height: 5px; background-color: #000; width:100%;' />" : ""; ?> 
                                            </td>
                                            <td style="width: 35%;">&nbsp;<strong> <?= $str_det;?></strong></td>
                                        </tr>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="2"> &nbsp;</td>
                                        </tr>

                                <?php endif; 
                            endforeach;
                        endif;
                        ?>
                    </table>
                </td>
            </tr> 
        <?php 
        endforeach;  ?>
    <?php
    }
    ?>
</table>
<?php

if ($type == 'salinan') : ?>
    <div style = "margin-top:20px;position:fixed; bottom:0;">
        <div style="text-align: right; font-size: 11px; font-family: Tahoma, serif; padding: 3px 65px 20px 0; ">PCC</div>
    </div>
<?php endif; ?>