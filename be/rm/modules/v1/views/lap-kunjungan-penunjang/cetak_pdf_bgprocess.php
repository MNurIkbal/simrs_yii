<?php 
use yii\helpers\ArrayHelper; 
?>
<style type="text/css">
    .col-print-1 {
        width: 8%;
        float: left;
    }
    .col-print-2 {
        width: 16%;
        float: left;
    }
    .col-print-3 {
        width: 25%;
        float: left;
    }
    .col-print-4 {
        width: 33%;
        float: left;
    }
    .col-print-5 {
        width: 42%;
        float: left;
    }
    .col-print-6 {
        width: 50%;
        float: left;
    }
    .col-print-7 {
        width: 58%;
        float: left;
    }
    .col-print-8 {
        width: 66%;
        float: left;
    }
    .col-print-9 {
        width: 75%;
        float: left;
    }
    .col-print-10 {
        width: 83%;
        float: left;
    }
    .col-print-11 {
        width: 92%;
        float: left;
    }
    .col-print-12 {
        width: 100%;
        float: left;
    }
    .text-center {
        text-align: center;
    }
    .text-left {
        text-align: left;
    }
    tr.noBorder th {
        border: 0;
    }
    tr > th {
        padding-left: 5px;
        padding-right: 5px;
    }
</style>
<table border="1" cellpadding="0" cellspacing="0" style="width:100%; font-size: 11px; border-collapse: collapse;">
    <thead>
        <tr>
            <th width="20px">No</th>
            <th width="50px" style='padding-left: 5px; padding-right: 5px' >Tanggal Masuk</th>
            <th style='padding-left: 5px; padding-right: 5px'>Data Pasien</th>
            <th style="padding-left: 5px; padding-right: 5px">Unit</th>
            <th style="padding-left: 5px; padding-right: 5px">Instalasi</th>
            <th style='padding-left: 5px; padding-right: 5px'>Cara Bayar/<br/> Penjamin</th>
            <th style='padding-left: 5px; padding-right: 5px'>Jenis Pemeriksaan</th>
            <th style='padding-left: 5px; padding-right: 5px'>Nama Pemeriksaan</th>
            <th width="20px" style='padding-left: 5px; padding-right: 5px'>Jumlah</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $no = 1;
        foreach ($datas as $key => $value):
            $tglMasukPenunjang = ArrayHelper::getValue($value, 'tglmasukpenunjang');
            $namaPasien = ArrayHelper::getValue($value, 'nama_pasien');
            $noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');
            $noRm = ArrayHelper::getValue($value, 'no_rekam_medik');
            $dataPasien = "$noPendaftaran<br/>$namaPasien<br/>$noRm";
            $caraBayar = ArrayHelper::getValue($value, 'carabayar_nama');
            $penjamin = ArrayHelper::getValue($value, 'penjamin_nama');
            $caraBayarPenjamin = "$caraBayar /<br/>$penjamin";
            $instalasi = ArrayHelper::getValue($value, 'instalasi_nama');
            $unit = ArrayHelper::getValue($value, 'unit');
            $jenisPemeriksaan = ArrayHelper::getValue($value, 'jeniskegiatantindakan_nama');
            $namaPemeriksaan = ArrayHelper::getValue($value, 'daftartindakan_nama');
            $jumlah = ArrayHelper::getValue($value, 'jumlah_tindakan');
    ?>
        <tr>
            <td style='text-align: center;'><?= $no++ ?></td>
            <td style='padding-left: 5px; padding-right: 5px'><?= $tglMasukPenunjang ?></td>
            <td style='padding-left: 5px; padding-right: 5px'><?= $dataPasien ?></td>
            <td style='padding-left: 5px; padding-right: 5px'><?= $unit ?></td>
            <td style='padding-left: 5px; padding-right: 5px'><?= $instalasi ?></td>
            <td style='padding-left: 5px; padding-right: 5px'><?= $caraBayarPenjamin ?></td>
            <td style='padding-left: 5px; padding-right: 5px'><?= $jenisPemeriksaan ?></td>
            <td style='padding-left: 5px; padding-right: 5px'><?= $namaPemeriksaan ?></td>
            <td style='padding-left: 5px; padding-right: 5px'><?= $jumlah ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr class="noBorder">
            <th colspan="6"></th>
            <th style="text-align: left; padding: 5px;">Total Jenis <br> Pemeriksaan <?= ArrayHelper::getValue($footerData, 'jumlah_jeniskegiatan') ?></th>
            <th style="text-align: left; padding: 5px;">Total Nama <br> Pemeriksaan <?= ArrayHelper::getValue($footerData, 'jumlah_tindakan') ?></th>
            <th style="text-align: left; padding: 5px;">Total <br> Jumlah <?= ArrayHelper::getValue($footerData, 'jumlah_hasil') ?></th>
        </tr>
    </tfoot>
</table>