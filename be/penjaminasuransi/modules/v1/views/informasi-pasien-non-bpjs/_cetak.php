<?php 

use Doco\components\DocoHelpers; 
use yii\helpers\ArrayHelper;

?>

<style type="text/css">
    tr, th {
        font-family: Arial;
    }

    tr, td {
        font-family: Arial;
    }

    .tbl-bordered {
        border-collapse: collapse;
        overflow : wrap;
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

<table autosize="1" width="100%" class="tbl-bordered">
	<thead>
	   <tr>
            <th style="width:3%">No</th>
            <th style="width:8%">Data Pasien</th>
            <th style="width:9%">Tanggal Masuk /<br/> Tanggal Keluar</th>
            <th style="width:8%">No Invoice</th>
            <th style="width:9%">Cara Bayar / Penjamin</th>
            <th style="width:8%">Instalasi / Ruangan</th>
            <th style="width:9%">Tagihan</th>
            <th style="width:9%">Jumlah Dibayarkan Pasien</th>
            <th style="width:9%">Jumlah Piutang</th>
            <th style="width:9%">Jumlah Pembayaran</th>
            <th style="width:9%">Jumlah Diskon</th>
            <th style="width:9%">Sisa Tagihan</th>
            <th style="width:9%">Status Pengajuan</th>
            <!-- <th>Status SKD</th> -->
            <!-- <th>Status Koreksi</th> -->
       </tr>
	</thead>
	<tbody>
    	<?php
            $totalTagihan = 0;
            $totalSdhBayar = 0;
            $totalAsuransi = 0;
            $totalJumlahPembayaran = 0;
            $totalDiscountPembayaran = 0;
            $totalSisaTagihan = 0;
            $no = 1;
            foreach ($data as $key => $value) :
                $total_tagihan = isset($value['total_ditagihkan']) ? $value['total_ditagihkan'] : $value['total_tagihan'];
                $totalTagihan += $total_tagihan;

                $total_sdh_bayar = ArrayHelper::getValue($value, 'total_sdh_bayar');
                $totalSdhBayar += $total_sdh_bayar;

                $total_asuransi = ArrayHelper::getValue($value, 'total_asuransi');
                $totalAsuransi += $total_asuransi;

                $jumlah_pembayaran = ArrayHelper::getValue($value, 'jumlah_pembayaran');
                $totalJumlahPembayaran += $jumlah_pembayaran;

                $total_discountpembayaran = ArrayHelper::getValue($value, 'total_discountpembayaran');
                $totalDiscountPembayaran += $total_discountpembayaran;

                $total_sisa_tagihan = ArrayHelper::getValue($value, 'total_sisa_tagihan');
                $totalSisaTagihan += $total_sisa_tagihan;

        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= '<b>' . $value['nama_pasien'] . '</b>' . '<br>' . $value['no_rekam_medik'] . '<br>' . $value['no_pendaftaran'] ?></td>
                <td style=""><?= DocoHelpers::coalesce(date('d M Y', strtotime($value['tgl_pendaftaran'])), '').' / '.DocoHelpers::coalesce(date('d M Y', strtotime($value['tglpasienpulang'])), '')  ?></td>
                <td><?= DocoHelpers::coalesce($value['no_invoice'], '-') ?></td>
                <td><?= '<b>' . $value['carabayar_nama'] . '</b>' . ' / ' . '<br>' . $value['penjamin_nama'] ?></td>
                <td><?= '<b>' . $value['instalasi_nama'] . '</b>' . ' / ' . '<br>' . $value['ruangan_nama'] ?></td>
                <td>Rp.<?= DocoHelpers::formatNumber($total_tagihan, 0) ?></td>
                <td>Rp.<?= DocoHelpers::formatNumber($value['total_sdh_bayar'], 0) ?></td>
                <td>Rp.<?= DocoHelpers::formatNumber($value['total_asuransi'], 0) ?></td>
                <td>Rp.<?= DocoHelpers::formatNumber($value['jumlah_pembayaran'], 0) ?></td>
                <td> Rp.<?= DocoHelpers::formatNumber($value['total_discountpembayaran'], 0) ?></td>
                <td>Rp.<?= DocoHelpers::formatNumber($value['total_sisa_tagihan'], 0) ?></td>
                <td><?= DocoHelpers::coalesce($value['statuspengajuan_nama'], '-') ?></td>
                <!-- <td><?= $value['status_skd'] ?></td> -->
                <!-- <td><?= $value['status_verif'] ?></td> -->
            </tr>
        <?php endforeach; 
        $formated_total_tagihan = DocoHelpers::formatNumber($totalTagihan, 0);
        $formated_total_sdh_bayar = DocoHelpers::formatNumber($totalSdhBayar, 0);
        $formated_total_asuransi = DocoHelpers::formatNumber($totalAsuransi, 0);
        $formated_total_jumlah_pembayaran = DocoHelpers::formatNumber($totalJumlahPembayaran, 0);
        $formated_total_discountpembayaran = DocoHelpers::formatNumber($totalDiscountPembayaran, 0);
        $formated_total_sisa_tagihan = DocoHelpers::formatNumber($totalSisaTagihan, 0);
        ?>
        <tr>
        <td colspan='6' style='text-align: right; font-weight: bold;'>Total</td>;
        <td colspan='1'>Rp.<?= $formated_total_tagihan ?></td>;
        <td colspan='1'>Rp.<?= $formated_total_sdh_bayar ?></td>;
        <td colspan='1'>Rp.<?= $formated_total_asuransi ?></td>;
        <td colspan='1'>Rp.<?= $formated_total_jumlah_pembayaran ?></td>;
        <td colspan='1'>Rp.<?= $formated_total_discountpembayaran ?></td>;
        <td colspan='1'>Rp.<?= $formated_total_sisa_tagihan ?></td>;
        <td colspan='1'></td>;
        </tr>;
    </tbody>
</table>