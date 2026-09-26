<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-05-16 16:02:30
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-04-11 11:14:09
 */
use Doco\components\DocoHelpers;
?>

<table border="1" cellpadding="1" cellspacing="1" style="width:100%;border-collapse: collapse;">
    <thead>
        <tr>
            <td width="1" style="text-align: center;"><b>No</b></td>
            <td width="8%" style="text-align: center;"><b>Tanggal</b></td>
            <td width="15%" style="text-align: center;"><b>Info Pasien</b></td>
            <td width="15%" style="text-align: center;"><b>Cara Bayar</b></td>
            <td width="15%" style="text-align: center;"><b>Metode Pembayaran Non Tunai</b></td>
            <td width="10%" style="text-align: center;"><b>Tagihan</b></td>
            <td width="10%" style="text-align: center;"><b>Tunai</b></td>
            <td width="10%" style="text-align: center;"><b>Non Tunai</b></td>
            <td width="10%" style="text-align: center;"><b>Penjamin</b></td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            $totalTagihan = $totalTunai = $totalNonTunai = $totalDijamin = 0; 
            foreach ($modelRincian as $value) : 
                $totalTagihan = $totalTagihan + $value['total_tagihan'];
                $totalTunai = $totalTunai + $value['total_tunai'];
                $totalNonTunai = $totalNonTunai + $value['total_nontunai'];
                $totalDijamin = $totalDijamin + $value['total_dijamin'];

                $penjaminNama = isset($value['penjamin_nama']) ? $value['penjamin_nama'] : '';
                $namaPasien = isset($value['nama_pasien']) ? $value['nama_pasien'] : '';
                $carabayarNama = isset($value['carabayar_nama']) ? $value['carabayar_nama'] : '';
                $noPendaftaran = isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '';
                $noRekamMedik= isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '';
                $infoPasien = isset($value['no_pendaftaran']) ? $noPendaftaran.' / '.$namaPasien .' / '. $noRekamMedik : '-';
                $caraBayar = isset($value['carabayar_nama']) ? $carabayarNama.' / '.$penjaminNama : '-'; 
                $tgl_closingkasir = isset($value['tgl_closingkasir']) ? date('d-M-Y', strtotime($value['tgl_closingkasir'])) : '';
                $metodePembayaranNama = isset($value['metode_pembayaran_nama']) ? $value['metode_pembayaran_nama'] : '';
                ?>

            <tr>
                <td style="text-align: center;"><?= $no ?></td>
                <td><?= $tgl_closingkasir ?></td>
                <td><?= $infoPasien; ?></td>
                <td><?= $caraBayar; ?></td>
                <td><?= $metodePembayaranNama; ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['total_tagihan'], 0) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['total_tunai'], 0) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['total_nontunai'], 0) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['total_dijamin'], 0) ?></td>
            </tr>
            
        <?php $no++; endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" style="text-align: right;"><strong></td>
            <td style="text-align: right;"><strong><?= DocoHelpers::formatNumber($totalTagihan, 0) ?></strong></td>
            <td style="text-align: right;"><strong><?= DocoHelpers::formatNumber($totalTunai, 0) ?></strong></td>
            <td style="text-align: right;"><strong><?= DocoHelpers::formatNumber($totalNonTunai, 0) ?></strong></td>
            <td style="text-align: right;"><strong><?= DocoHelpers::formatNumber($totalDijamin, 0) ?></strong></td>
        </tr>
    </tfoot>
</table>