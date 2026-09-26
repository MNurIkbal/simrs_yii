<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-24 09:50:31
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-08-09 11:29:27
 */

?>

<style type="text/css">
    .col-print-1 {width:8%;  float:left;}
    .col-print-2 {width:16%; float:left;}
    .col-print-3 {width:25%; float:left;}
    .col-print-4 {width:33%; float:left;}
    .col-print-5 {width:42%; float:left;}
    .col-print-6 {width:50%; float:left;}
    .col-print-7 {width:58%; float:left;}
    .col-print-8 {width:66%; float:left;}
    .col-print-9 {width:75%; float:left;}
    .col-print-10{width:83%; float:left;}
    .col-print-11{width:92%; float:left;}
    .col-print-12{width:100%; float:left;}
    .text-center {
        text-align: center;
    }
    .text-left {
        text-align: left;
    }
</style>

<h3 class="text-center"><?= Yii::t('app', 'Order Obat Rawat Inap') ?></h3>
<div class="row">
    <div class="col-print-5">
        <table border="0" cellpadding="0" cellspacing="0" style="width:100%; font-size: 9px; border-collapse: collapse;">
            <tr>
                <th class="text-left"><?= Yii::t('app', "Nama pasien") ?></th>
                <td>: <?= $reseptur['nama_pasien'] ?></td>
            </tr>
            <tr>
                <th class="text-left"><?= Yii::t('app', "No rekam medik") ?></th>
                <td>: <?= $reseptur['no_rekam_medik'] ?></td>
            </tr>
            <tr>
                <th class="text-left"><?= Yii::t('app', "Tanggal lahir") ?></th>
                <td>: <?= $reseptur['tanggal_lahir'] ?></td>
            </tr>
            <tr>
                <th class="text-left"><?= Yii::t('app', "Jenis kelamin") ?></th>
                <td>: <?= $reseptur['jenis_kelamin'] ?></td>
            </tr>
            <tr>
                <th class="text-left"><?= Yii::t('app', "Umur") ?></th>
                <td>: <?= $reseptur['umur'] ?></td>
            </tr>
            <tr>
                <th class="text-left"><?= Yii::t('app', "Ruangan / kelas") ?></th>
                <td>: <?= isset($reseptur['ruangan_reseptur']) ? $reseptur['ruangan_reseptur'] . ' / ' . $reseptur['kelaspelayanan_nama'] : '' ?></td>
            </tr>
            <tr>
                <th class="text-left"><?= Yii::t('app', "Dokter DPJP") ?></th>
                <td>: <?= $reseptur['nama_pegawai'] ?></td>
            </tr>
            <tr>
                <th class="text-left"><?= Yii::t('app', "Penjamin") ?></th>
                <td>: <?= $reseptur['penjamin_nama'] ?></td>
            </tr>
        </table>
    </div>
    <div class="col-print-2"></div>
    <div class="col-print-5">
        <table border="0" cellpadding="0" cellspacing="0" style="width:100%; font-size: 9px; border-collapse: collapse;">
            <tr>
                <th class="text-left"><?= Yii::t('app', "Berat Badan") ?></th>
                <td>: <?= $reseptur['berat_badan'] ?></td>
            </tr>
            <tr>
                <th class="text-left"><?= Yii::t('app', "Tinggi Badan") ?></th>
                <td>: <?= $reseptur['tinggi_badan'] ?></td>
            </tr>
            <tr>
                <th class="text-left"><?= Yii::t('app', "Luas Permukaan Tubuh") ?></th>
                <td>: <?= $reseptur['luas_tubuh'] ?></td>
            </tr>
            <tr>
                <th class="text-left"><?= Yii::t('app', "Status Kehamilan") ?></th>
                <td>: <?= (isset($reseptur['is_hamil']) && $reseptur['is_hamil'] == true) ? Yii::t('app', 'Ya') : Yii::t('app', 'Tidak') ?></td>
            </tr>
            <tr>
                <th class="text-left"><?= Yii::t('app', "Diagnosa") ?></th>
                <td>: <?= $reseptur['diagnosa_text'] ?></td>
            </tr>
            <tr>
                <th></th>
                <td></td>
            </tr>
            <tr>
                <th></th>
                <td></td>
            </tr>
            <tr>
                <th></th>
                <td></td>
            </tr>
        </table>
    </div>
</div>
<br>
<br>
<table border="1" cellpadding="0" cellspacing="0" style="width:100%; font-size: 9px; border-collapse: collapse;">
    <tr>
        <th rowspan="2"><?= Yii::t('app', 'Tanggal') ?></th>
        <th rowspan="2"><?= Yii::t('app', 'R/ Nama Obat & Bentuk Sediaan') ?></th>
        <th rowspan="2"><?= Yii::t('app', 'Jumlah') ?></th>
        <th rowspan="2"><?= Yii::t('app', 'Signa/Rute Pemberian Obat') ?></th>
        <th colspan="7"><?= Yii::t('app', 'Review') ?></th>
    </tr>
    <tr>
        <th><?= Yii::t('app', 'Interaksi') ?></th>
        <th><?= Yii::t('app', 'Duplikasi') ?></th>
        <th><?= Yii::t('app', 'Dosisi') ?></th>
        <th><?= Yii::t('app', 'Alergi') ?></th>
        <th><?= Yii::t('app', 'Kontradiksi') ?></th>
        <th><?= Yii::t('app', 'Review Note') ?></th>
        <th><?= Yii::t('app', 'Waktu Review') ?></th>
    </tr>
    <!-- Cek -->
    <?php if (!empty($resepturDetail)): ?>
        <?php foreach ($resepturDetail as $value): ?>
            <tr>
                <td><?= date('j F Y', strtotime($value['tglreseptur'])) ?></td>
                <td><?= $value['obatalkes_nama'] ?></td>
                <td><?= $value['qty_reseptur'] ?> <?= $value['satuan_input'] ?></td>
                <td><?php 
                if( !empty($value['signa_nama']) ){
                    echo $value['signa_nama'];
                } else {
                    if(!empty($value['signa'])){
                        echo $value->signa['text'];
                    }
                }
                ?></td>
                <td><?= $value['interaksi'] ?></td>
                <td><?= $value['duplikasi'] ?></td>
                <td><?= $value['dosisi'] ?></td>
                <td><?= $value['alergi'] ?></td>
                <td><?= $value['kontradiksi'] ?></td>
                <td><?= $value['review_note'] ?></td>
                <td><?= $value['nama_pegawai'].'<br>'.$value['wkt_review'] ?></td>
            </tr>
        <?php endforeach ?>
    <?php endif ?>
</table>
<br>
<br>
<span style="font-size: 9px"><?= Yii::t('app', 'Dicetak oleh : ').$pegawai['nama_pegawai'].Yii::t('app', ' Tanggal : ').date('d F Y h:i:s', strtotime('NOW')) ?></span>