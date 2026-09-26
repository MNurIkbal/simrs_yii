<?php

use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;

?>

<style type="text/css">
    tr,
    th {
        font-family: Arial;
    }

    tr,
    td {
        font-family: Arial;
    }

    .tbl-bordered {
        border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }

    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }

</style>

<table width="100%" class="tbl-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Data Pasien</th>
            <th>No Invoice</th>
            <th>Tanggal Masuk</th>
            <th>Tanggal Keluar</th>
            <th>No SEP</th>
            <th>Instalasi / Ruangan</th>
            <th>Tagihan</th>
            <th>Jumlah Dibayarkan Pasien</th>
            <th>Jumlah Diskon</th>
            <th>Jumlah Pengajuan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $numCounter = 1;
        $sumTotalTagihan = 0;
        $sumTotalSudahBayar = 0;
        $sumTotalTotalDiscount = 0;
        $sumTotalAsuransi = 0;
        foreach ($datas as $key => $value) {
            $namaPasien = ArrayHelper::getValue($value, 'nama_pasien');
            $noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik');
            $noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');
            $ruangan = ArrayHelper::getValue($value, 'ruangan_nama');
            $instalasi = ArrayHelper::getValue($value, 'instalasi_nama');
            $caraBayar = ArrayHelper::getValue($value, 'carabayar_nama');
            $penjamin = ArrayHelper::getValue($value, 'penjamin_nama');
            $noInvoice = ArrayHelper::getValue($value, 'no_pembayaran');
            $tanggalMasuk = ArrayHelper::getValue($value, 'tgl_pendaftaran');
            $tanggalKeluar = ArrayHelper::getValue($value, 'tglpasienpulang');
            $totalTagihan = ArrayHelper::getValue($value, 'text_total_tagihan');
            $totalSudahBayar = ArrayHelper::getValue($value, 'text_total_sdh_bayar');
            $totalSisaTagihan = ArrayHelper::getValue($value, 'text_total_sisa_tagihan');
            $totalAsuransi = ArrayHelper::getValue($value, 'text_total_asuransi');
            $totalDiscount = ArrayHelper::getValue($value, 'text_total_discountpembayaran');
            $sumTotalTagihan += ArrayHelper::getValue($value, 'total_tagihan');
            $sumTotalSudahBayar += ArrayHelper::getValue($value, 'total_sdh_bayar');
            $sumTotalTotalDiscount += ArrayHelper::getValue($value, 'total_discountpembayaran');
            $sumTotalAsuransi += ArrayHelper::getValue($value, 'total_asuransi');
            $noSep = ArrayHelper::getValue($value, 'nosep');
            $dataPasien = "<b>$namaPasien</b> <br /> $noPendaftaran <br /> $noRekamMedik";
            $instalasiRuangan = "<b>$instalasi /</b> <br /> $ruangan";
            $caraBayarPenjamin = "<b>$caraBayar /</b> <br /> $penjamin";
            echo "<tr>";
            echo "<td>$numCounter</td>";
            echo "<td>$dataPasien</td>";
            echo "<td>$noInvoice</td>";
            echo "<td>$tanggalMasuk</td>";
            echo "<td>$tanggalKeluar</td>";
            echo "<td>$noSep</td>";
            echo "<td>$instalasiRuangan</td>";
            echo "<td>$totalTagihan</td>";
            echo "<td>$totalSudahBayar</td>";
            echo "<td>$totalDiscount</td>";
            echo "<td>$totalAsuransi</td>";
            echo "</tr>";
            $numCounter++;
        }
        $sumTotalTagihan = "Rp." . DocoHelpers::formatNumber($sumTotalTagihan, '0');
        $sumTotalSudahBayar = "Rp." . DocoHelpers::formatNumber($sumTotalSudahBayar, '0');
        $sumTotalTotalDiscount = "Rp." . DocoHelpers::formatNumber($sumTotalTotalDiscount, '0');
        $sumTotalAsuransi = "Rp." . DocoHelpers::formatNumber($sumTotalAsuransi, '0');
        echo "<tr>";
        echo "
        <td colspan='7' style='text-align: right;'>
            <span style='margin-right: 10px; font-weight: bold;'>Total</span>
        </td>";
        echo "<td>$sumTotalTagihan</td>";
        echo "<td>$sumTotalSudahBayar</td>";
        echo "<td>$sumTotalTotalDiscount</td>";
        echo "<td>$sumTotalAsuransi</td>";
        echo "</tr>";
        ?>
    </tbody>
</table>