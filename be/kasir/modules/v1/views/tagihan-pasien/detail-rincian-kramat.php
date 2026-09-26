<?php 
use Doco\components\DocoHelpers;
?>
<style>
    .tbl-bordered {
        border-collapse: collapse;
    }
    .tbl-bordered th {
        border-top: 2px solid black; border-bottom: 2px solid black;
        padding: 5px;
        font-size: 12px;
        font:Arial;
    }
    .tbl-bordered td {
        border: 0px solid black;
        padding: 5px;
        font-size: 12px;
        font:Arial;
    }
    .text-right {
        text-align: right;
    }
    .text-center {
        text-align: center;
    }
    
    .tbl-footer {
        border-collapse: collapse;
        font-size: 12px;
    }
</style>

<table class="tbl-bordered" style="width:100%;">
    <thead>
        <tr>
            <th><?= Yii::t('app', 'No') ?></th>
            <th><?= Yii::t('app', 'Tanggal Tindakan') ?></th>
            <th><?= Yii::t('app', 'Nama Tindakan') ?></th>
            <th><?= Yii::t('app', 'Qty') ?></th>
            <th><?= Yii::t('app', 'Tarif Satuan') ?></th>
            <th><?= Yii::t('app', 'Tarif Cito') ?></th>
            <th><?= Yii::t('app', 'Jumlah Tarif') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php
    $no = 1;
    foreach ($data as $pelayanan => $valPelayanan) :  ?> 
    <tr>
        <td colspan="7"><?= strtoupper($pelayanan) ?></td>
    </tr>
    <?php foreach ($valPelayanan as $ruangan => $valRuangan) : ?>
    <tr>
        <td style="letter-spacing: 1px;"></td>
        <td colspan="6"><?= strtoupper($ruangan) ?></td>
    </tr>
    <?php foreach ($valRuangan as $kelompok => $valKelompok) : ?>
    <tr>
        <td style="letter-spacing: 1px;"></td>
        <td colspan="6"><?= strtoupper($kelompok) ?></td>
    </tr>
    <?php 
        $subTotal = 0;
        foreach ($valKelompok as $key => $value ) : 
        $subTotal += $value['sub_total'];
    ?>
    <tr>
        <td width="5%" style=""><?= $no++ ?></td>
        <td width="12%" style=""><?= date('d M Y', strtotime($value['tgl_pelayanan'])).'<br/>'.date('H:i:s', strtotime($value['tgl_pelayanan'])) ?></td>
        <td style=""><?= $value['tindakan_obat_nama'] ?>   
        <?= in_array($value['tindakan_obat_id'], $tindakan) 
        ? "/ ".$value['kelaspelayanan_nama']." - ".$value['dokterpenanggungjawab_nama'] : ''?>
        <?= ($value['is_akomodasi'] == true) ? " / ".$value['ruangan_pelayanan']." / ".$value['kelaspelayanan_nama'] : '' ?>
        </td>
        <td width="5%" class="text-right"><?= $value['qty'] ?></td>
        <td width="10%" class="text-right"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
        <td width="10%" class="text-right"><?= DocoHelpers::formatNumber($value['tarif_cyto']) ?></td>
        <td width="15%" class="text-right"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="6" class="text-right"><b>TOTAL TAGIHAN <?= strtoupper($ruangan) ?>: </b></td>
        <td class="text-right"><b><?= DocoHelpers::formatNumber($subTotal) ?></b></td>
    </tr>
    <?php endforeach; ?>
    <?php endforeach; ?>
    <?php endforeach; ?>

    <?php 
    $tindakan_akomodasi = isset($data_akomodasi['tindakan_akomodasi']) ? $data_akomodasi['tindakan_akomodasi'] : [];
    if(!empty($tindakan_akomodasi)) : ?>
        <tr>
        <td></td>
        <td colspan="6">AKOMODASI SEMENTARA</td>
    </tr>
    <?php
        $subTotal = 0;
        foreach ($tindakan_akomodasi as $value ) : 
        $subTotal += $value['tarif_tindakan'];
    ?>
        <tr>
            <td width="5%"><?= $no++ ?></td>
            <td width="12%"><?= date('d M Y', strtotime($value['tgl_tindakan'])).'<br/>'.date('H:i:s', strtotime($value['tgl_tindakan'])) ?></td>
            <td><?= $value['daftartindakan_nama']." / ".$value['ruangan_nama']." / ".$value['kelaspelayanan_nama'] ?></td>
            <td width="5%"><?= $value['qty_tindakan'] ?></td>
            <td width="10%"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
            <td width="10%"><?= DocoHelpers::formatNumber($value['tarifcyto_tindakan']) ?></td>
            <td width="15%"><?= DocoHelpers::formatNumber($value['tarif_tindakan']) ?></td>
        </tr>
    <?php endforeach; ?>
        <tr>
            <td colspan="6" class="text-right"><b>TOTAL AKOMODASI SEMENTARA : </b></td>
            <td class="text-right"><b><?= DocoHelpers::formatNumber($subTotal) ?></b></td>
        </tr>
    <?php endif; ?>
    <?php 
    if (!empty($biayaAdmin)) :
    $total += $biayaAdmin; 
    ?>
        <tr>
            <td width="5%"><?= $no ?></td>
            <td width="15%"> </td>
            <td><?= $data_admin ?></td>
            <td width="5%"></td>
            <td width="10%"></td>
            <td width="10%"></td>
            <td width="15%" class="text-right"><?= DocoHelpers::formatNumber($biayaAdmin) ?></td>
        </tr>
    <?php
    endif; ?>
    </tbody>
</table>
<br> <hr>
<table class="tbl-bordered" style="width:100%;">
    <?php
        $tagihanBelumBayar = !empty ($tagihan_belumbayar) ? $tagihan_belumbayar  : 0;
        $subsidiAsuransi = !empty ($nominal_dijamin) ? (int) $nominal_dijamin  : 0;
        $uangMuka = !empty ($sisa_uangmuka) ? (int) $sisa_uangmuka  : 0;
        $akomodasiSementara = !empty($data_akomodasi['total_akomodasi']) ? (int)$data_akomodasi['total_akomodasi'] : 0;
        $totalSisaTagihan = ($tagihanBelumBayar + $biayaAdmin) - $subsidiAsuransi - $uangMuka + $akomodasiSementara;
    ?>
    <tr>
        <td colspan="6"><b>Tagihan :</b> </td>
        <td class="text-right"><b><?= DocoHelpers::formatNumber($tagihanBelumBayar) ?> </b></td>
    </tr>
    <tr>
        <td colspan="6"><b>Biaya Administrasi :</b></td>
        <td class="text-right"><b><?= DocoHelpers::formatNumber($biayaAdmin) ?> </b></td>
    </tr>
    <tr>
        <td colspan="6"><b>Subsidi Asuransi :</b></td>
        <td class="text-right"><b><?= DocoHelpers::formatNumber($subsidiAsuransi) ?> </b></td>
    </tr>
    <tr>
        <td colspan="6"><b>Uang Masuk :</b></td>
        <td class="text-right"><b><?= DocoHelpers::formatNumber($uangMuka) ?> </b></td>
    </tr>
    <tr>
        <td colspan="6"><b>Biaya Akomodasi Sementara :</b></td>
        <td class="text-right"><b><?= DocoHelpers::formatNumber((isset($data_akomodasi['total_akomodasi']) && $data_akomodasi['total_akomodasi'] != null)?$data_akomodasi['total_akomodasi'] : 0) ?> </b></td>
    </tr>
    <tr>
        <td colspan="6"><b>Total Sisa Tagihan :</b></td>
        <td class="text-right"><b><?= DocoHelpers::formatNumber($totalSisaTagihan) ?> </b></td>
    </tr>
</table>

