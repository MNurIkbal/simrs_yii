<?php
use Doco\components\DocoHelpers;
?>
<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
        font-family: Arial;
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
        <th>No SEP</th>
        <th>Tanggal Masuk</th>
        <th>Tanggal Keluar</th>
        <th>Instalasi / Ruangan</th>
        <th>Tagihan (Rp.)</th>
        <th>Jumlah Pasien Bayar (Rp.)</th>
        <th>Piutang (Rp.)</th>
        <th>Piutang (Telah Bayar) (Rp.)</th>
        <th>Jumlah Bayar (Rp.)</th>
        <th>Sisa Tagihan (Rp.)</th>
       </tr>
    </thead>
    <tbody>
        <?php
            $no = 1;
            foreach ($data as $key => $value) {
                $namaPasien = $value['nama_pasien'];
                $namaPasien = "<b>$namaPasien</b>";
                $noRekamMedik = $value['no_rekam_medik'];
                $noPendaftaran = $value['no_pendaftaran'];
                $instalasi = $value['instalasi_nama'];
                $instalasi = "<b>$instalasi</b>";
                $ruangan = $value['ruangan_nama'];
                $dataPasien = "$namaPasien </br> $noPendaftaran";
                $instalasiRuangan = "$instalasi / </br> $ruangan";
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $dataPasien ?></td>
                <td><?= $value['no_pembayaran'] ?></td>
                <td><?= $value['nosep'] ?></td>
                <td><?= !empty($value['tgl_pendaftaran']) ? date('d M Y',strtotime($value['tgl_pendaftaran'])) : null ?></td>
                <td><?= !empty($value['tglpasienpulang']) ? date('d M Y',strtotime($value['tglpasienpulang'])) : null ?></td>
                <td><?= $instalasiRuangan ?></td>
                <td style="text-align: right;">Rp.<?= DocoHelpers::formatNumber($value['total_tagihan']) ?></td>
                <td style="text-align: right;">Rp.<?= DocoHelpers::formatNumber($value['jumlah_telahbayar'] - $value['bayar_alokasi']) ?></td>
                <td style="text-align: right;">Rp.<?= DocoHelpers::formatNumber($value['jumlah_piutang']) ?></td>
                <td style="text-align: right;">Rp.<?= DocoHelpers::formatNumber($value['jumlah_bayar'] - $value['bayar_alokasi']) ?></td>
                <td style="text-align: right;">Rp.<?= DocoHelpers::formatNumber($value['bayar_alokasi']) ?></td>
                <td style="text-align: right;">
                    Rp.<?= DocoHelpers::formatNumber(($value['jumlah_piutang'] - (($value['jumlah_bayar'] - $value['bayar_alokasi']) + $value['bayar_alokasi']))) ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>