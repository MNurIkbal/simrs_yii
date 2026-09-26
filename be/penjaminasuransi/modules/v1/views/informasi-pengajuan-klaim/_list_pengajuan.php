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
        $no = 1;
        $sumTotalTagihan = 0;
        $sumJumlahTelahBayar = 0;
        $sumJumlahPiutang = 0;
        $sumDiskon = 0;
        foreach ($data as $key => $value) {
            $namaPasien = ArrayHelper::getValue($value, 'nama_pasien');
            $noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');
            $noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik');
            $dataPasien = "<b>$namaPasien</b> <br> $noPendaftaran $noRekamMedik";
            $noInvoice = ArrayHelper::getValue($value, 'no_pembayaran', '-');
            $tglPendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran');
            if ($tglPendaftaran) $tglPendaftaran = date('d M Y', strtotime($tglPendaftaran));
            $tglPasienPulang = ArrayHelper::getValue($value, 'tglpasienpulang');
            if ($tglPasienPulang) $tglPasienPulang = date('d M Y', strtotime($tglPasienPulang));
            $noSep = ArrayHelper::getValue($value, 'nosep', '-');
            $instalasiNama = ArrayHelper::getValue($value, 'instalasi_nama');
            $ruanganNama = ArrayHelper::getValue($value, 'ruangan_nama');
            $instalasiRuangan = "<b>$instalasiNama</b> / <br> $ruanganNama";
            $totalTagihan = ArrayHelper::getValue($value, 'total_tagihan');
            $totalTagihanFormatted = DocoHelpers::formatNumber($totalTagihan);
            $jumlahTelahBayar = ArrayHelper::getValue($value, 'jumlah_telahbayar');
            $jumlahTelahBayarFormatted = DocoHelpers::formatNumber($jumlahTelahBayar);
            $jumlahPiutang = ArrayHelper::getValue($value, 'jumlah_piutang');
            $jumlahPiutangFormatted = DocoHelpers::formatNumber($jumlahPiutang);
            $jumlahDiskon = ArrayHelper::getValue($value, 'total_discountpembayaran');
            $jumlahDiskonFormated = DocoHelpers::formatNumber($jumlahDiskon);
            $sumDiskon += $jumlahDiskon; 
            $sumTotalTagihan += $totalTagihan;
            $sumJumlahTelahBayar += $jumlahTelahBayar;
            $sumJumlahPiutang += $jumlahPiutang;
            echo "<tr>";
            echo "<td>$no</td>";
            echo "<td>$dataPasien</td>";
            echo "<td>$noInvoice</td>";
            echo "<td>$tglPendaftaran</td>";
            echo "<td>$tglPasienPulang</td>";
            echo "<td>$noSep</td>";
            echo "<td>$instalasiRuangan</td>";
            echo "<td>Rp.$totalTagihanFormatted</td>";
            echo "<td>Rp.$jumlahTelahBayarFormatted</td>";
            echo "<td>Rp.$jumlahDiskonFormated</td>";
            echo "<td>Rp.$jumlahPiutangFormatted</td>";
            echo "</tr>";
            $no++;
        }
        $sumTotalTagihanFormatted = DocoHelpers::formatNumber($sumTotalTagihan);
        $sumJumlahTelahBayarFormatted = DocoHelpers::formatNumber($sumJumlahTelahBayar);
        $sumJumlahPiutangFormatted = DocoHelpers::formatNumber($sumJumlahPiutang);
        $sumJumlahDiskonFormatted = DocoHelpers::formatNumber($sumDiskon);
        echo "<tr>";
        echo "<td colspan='7' style='text-align: right;font-weight:bold;'>Total</td>";
        echo "<td>Rp.$sumTotalTagihanFormatted</td>";
        echo "<td>Rp.$sumJumlahTelahBayarFormatted</td>";
        echo "<td>Rp.$sumJumlahDiskonFormatted</td>";
        echo "<td>Rp.$sumJumlahPiutangFormatted</td>";
        echo "</tr>";
        ?>
    </tbody>
</table>