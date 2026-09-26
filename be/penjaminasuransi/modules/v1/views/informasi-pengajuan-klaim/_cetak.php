<?php

use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;

 ?>

<style type="text/css">
    * {
        font-family: Arial;
    }

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
        font-size: 11px;
    }

    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
        font-size: 11px;
    }
</style>

<table width="100%" class="tbl-bordered">
    <thead style="font-family: Arial;">
        <tr>
            <th>No</th>
            <th>Tanggal Pengajuan</th>
            <th>Tanggal Jatuh Tempo</th>
            <th>No Pengajuan</th>
            <th>Cara Bayar / Penjamin</th>
            <th>Total Pengajuan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody style="font-family: Arial;">
        <?php
        $no = 1;
        $sumValuePiutang = 0;
        foreach ($data as $key => $value) {
            $tglPengajuanKlaim = DocoHelpers::coalesce(date(' d M Y', strtotime($value['tgl_pengajuanklaim'])), '');
            $tglJatuhTempo = DocoHelpers::coalesce(date('d M Y', strtotime($value['tgl_jatuhtempo'])), '');
            $noPengajuanKlaim = ArrayHelper::getValue($value, 'no_pengajuanklaim');
            $caraBayarNama = ArrayHelper::getValue($value, 'carabayar_nama');
            $penjaminNama = ArrayHelper::getValue($value, 'penjamin_nama');
            $caraBayarPenjamin = "<b>$caraBayarNama/</b> <br>$penjaminNama";
            $valuePiutang = ArrayHelper::getValue($value, 'total_piutang');
            $sumValuePiutang += $valuePiutang;
            $valuePiutangFormatted = "Rp.". DocoHelpers::formatNumber($valuePiutang, 0);
            $valueSPengajuanKlaim = ArrayHelper::getValue($value, 's_pengajuanklaim');
            echo "<tr>";
            echo "<td>$no</td>";
            echo "<td>$tglPengajuanKlaim</td>";
            echo "<td>$tglJatuhTempo</td>";
            echo "<td>$noPengajuanKlaim</td>";
            echo "<td>$caraBayarPenjamin</td>";
            echo "<td>$valuePiutangFormatted</td>";
            echo "<td>$valueSPengajuanKlaim</td>";
            echo "</tr>";
            $no++;
        }
        $sumValuePiutangFormatted = DocoHelpers::formatNumber($sumValuePiutang, 0);
        echo "<tr>";
        echo "<td colspan='5' style='text-align: right;'>Total Pengajuan</td>";
        echo "<td colspan='2'>Rp.$sumValuePiutangFormatted</td>";
        echo "</tr>";
        ?>
    </tbody>
</table>