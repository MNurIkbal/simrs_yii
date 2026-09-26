<?php

use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;

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
</style>

<table border="1" cellpadding="0" cellspacing="0" style="width:100%; font-size: 11px; border-collapse: collapse;">
    <tr>
        <th>No</th>
        <th style="padding-left: 5px; padding-right: 5px;">Ruangan</th>
        <th style="padding-left: 5px; padding-right: 5px;">Penjamin</th>
        <th style="padding-left: 5px; padding-right: 5px;">Kelompok Pemeriksaan</th>
        <th style="padding-left: 5px; padding-right: 5px;">Jenis Pemeriksaan</th>
        <th style="padding-left: 5px; padding-right: 5px;">Nama Pemeriksaan</th>
        <th style="padding-left: 5px; padding-right: 5px;">Kelas Pelayanan</th>
        <th style="padding-left: 5px; padding-right: 5px;">Tarif Total</th>
        <th style="padding-left: 5px; padding-right: 5px;">Cyto Tindakan (%)</th>
        <th style="padding-left: 5px; padding-right: 5px;">Diskon Tindakan (%)</th>

    </tr>
    <?php
    $no = 1;
    foreach ($data as $key => $value) {
    	$ruangan_nama = ArrayHelper::getValue($value, 'ruangan_nama');
    	$penjamin_nama = ArrayHelper::getValue($value, 'penjamin_nama');
    	$nama_kelompok = ArrayHelper::getValue($value, 'nama_kelompok');
    	$jenispemeriksaanlab_nama = ArrayHelper::getValue($value, 'jenispemeriksaanlab_nama');
    	$pemeriksaanlab_nama = ArrayHelper::getValue($value, 'pemeriksaanlab_nama');
    	$kelaspelayanan_nama = ArrayHelper::getValue($value, 'kelaspelayanan_nama');
    	$harga_tariftindakan = ArrayHelper::getValue($value, 'harga_tariftindakan', '0');
    	$persencyto_tindakan = ArrayHelper::getValue($value, 'persencyto_tindakan');
    	$persendiskon_tindakan = ArrayHelper::getValue($value, 'persendiskon_tindakan');
    ?>
    	<tr>
    		<td><?= $no; ?></td>
    		<td style="padding-left: 5px; padding-right: 5px;"><?= $ruangan_nama ?></td>
    		<td style="padding-left: 5px; padding-right: 5px;"><?= $penjamin_nama ?></td>
    		<td style="padding-left: 5px; padding-right: 5px;"><?= $nama_kelompok ?></td>
    		<td style="padding-left: 5px; padding-right: 5px;"><?= $jenispemeriksaanlab_nama ?></td>
    		<td style="padding-left: 5px; padding-right: 5px;"><?= $pemeriksaanlab_nama ?></td>
    		<td style="padding-left: 5px; padding-right: 5px;"><?= $kelaspelayanan_nama ?></td>
    		<td style="padding-left: 5px; padding-right: 5px;"><?= DocoHelpers::formatNumber($harga_tariftindakan) ?></td>
    		<td style="padding-left: 5px; padding-right: 5px;"><?= $persencyto_tindakan ?></td>
    		<td style="padding-left: 5px; padding-right: 5px;"><?= $persendiskon_tindakan ?></td>
    	</tr>
    <?php $no++; } ?>
</table>