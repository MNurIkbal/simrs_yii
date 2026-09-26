<?php
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;

?>
<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
        font-family: Arial;
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
    <thead>
       <tr>
            <th>No.</th>
            <th>Tanggal Pengajuan</th>
            <th>Tanggal Pembayaran</th>
            <th>No Pembayaran</th>
            <th>No Pengajuan</th>
            <th>Cara Bayar / Penjamin</th>
            <th>Total Pengajuan (Rp.)</th>
            <th>Telah Bayar (Rp.)</th>
            <th>Total Pembayaran (Rp.)</th>
            <th>Sisa (Rp.)</th>
       </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($data as $key => $value) { 
            $caraBayar = ArrayHelper::getValue($value, 'carabayar_nama');
            $caraBayar = "<b>" . $caraBayar ."</b>";
            $penjamin = ArrayHelper::getValue($value, 'penjamin_nama');
            $caraBayarPenjamin = $caraBayar . " / <br/>" . $penjamin;
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date(' d M Y', strtotime($value['tgl_pengajuanklaim'])) ?></td>
                <td><?= !empty($value['tgl_terimabayarklaim']) ? date('d M Y', strtotime($value['tgl_terimabayarklaim'])) : '' ?></td>
                <td><?= $value['no_terimabayarklaim'] ?></td>
                <td><?= $value['no_pengajuanklaim'] ?></td>
                <td><?= $caraBayarPenjamin ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['total_pengajuan']) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['jumlah_pembayaran']) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['total_pembayaran']) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['sisa']) ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>