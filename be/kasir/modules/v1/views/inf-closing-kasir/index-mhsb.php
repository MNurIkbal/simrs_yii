<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-05-16 16:02:30
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-04-11 11:14:09
 */
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
?>

<table border="1" cellpadding="1" cellspacing="1" style="width:100%;border-collapse: collapse;">
    <thead>
        <tr>
            <td width="3%" style="text-align: center;"><b>No</b></td>
            <td width="10%" style="text-align: center;"><b>Tanggal</b></td>
            <td width="14%" style="text-align: center;"><b>Info Pasien</b></td>
            <td width="14%" style="text-align: center;"><b>No Kwitansi</b></td>
            <td width="10%" style="text-align: center;"><b>Cara Bayar</b></td>
            <td width="10%" style="text-align: center;"><b>Metode Pembayaran Non Tunai</b></td>
            <td width="11%" style="text-align: center;"><b>Tagihan</b></td>
            <td width="10%" style="text-align: center;"><b>Tunai</b></td>
            <td width="10%" style="text-align: center;"><b>Debit Card</b></td>
            <td width="10%" style="text-align: center;"><b>Credit Card</b></td>
            <td width="10%" style="text-align: center;"><b>Transfer</b></td>
            <td width="10%" style="text-align: center;"><b>Penjamin</b></td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            $val_pembayaran = [];
            $credit_card = DocoConstants::PEMBAYARAN_CREDIT_CARD;
            $bank_transfer = DocoConstants::PEMBAYARAN_BANK_TRANSFER;
            $debit_card = DocoConstants::PEMBAYARAN_DEBIT_CARD;
            $totalTagihan = $totalTunai = $totalNonTunai = $totalDijamin = $totalCreditCard = $totalBankTransfer = $totalDebitCard = 0; 
            foreach ($modelRincian as $value) :
                $totalDitagihkan = isset($value['total_tagihan']) ? $value['total_tagihan'] : 0;
                $totalCash = isset($value['total_tunai']) ? $value['total_tunai'] : 0;
                $totalNonCash = isset($value['total_nontunai']) ? $value['total_nontunai'] : 0;
                $totalPayer = isset($value['total_dijamin']) ? $value['total_dijamin'] : 0;
                $creditCard_val = $bankTransfer_val = $debitCard_val = 0;
                $totalTagihan = $totalTagihan + $totalDitagihkan;
                $totalTunai = $totalTunai + $totalCash;
                $totalNonTunai = $totalNonTunai + $totalNonCash;
                $totalDijamin = $totalDijamin + $totalPayer;

                $penjaminNama = isset($value['penjamin_nama']) ? $value['penjamin_nama'] : '';
                $namaPasien = isset($value['nama_pasien']) ? $value['nama_pasien'] : '';
                $carabayarNama = isset($value['carabayar_nama']) ? $value['carabayar_nama'] : '';
                $noPendaftaran = isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '';
                $noPembayaran= isset($value['no_pembayaran']) ? $value['no_pembayaran'] : '';
                $noRekamMedik= isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '';
                $infoPasien = isset($value['no_pendaftaran']) ? $noPendaftaran.' / '.$namaPasien : '-';
                $caraBayar = isset($value['carabayar_nama']) ? $carabayarNama.' / '.$penjaminNama .' / '. $noRekamMedik: '-'; 
                $tgl_closingkasir = isset($value['tgl_closingkasir']) ? date('d-M-Y', strtotime($value['tgl_closingkasir'])) : '';
                $metode_pembayaran = isset($value['metode_pembayaran']) ? $value['metode_pembayaran'] : '';
                $metodePembayaranNama = isset($value['metode_pembayaran_nama']) ? $value['metode_pembayaran_nama'] : '';
                if(!empty($metode_pembayaran))
                {
                    $val_pembayaran = explode(",",$metode_pembayaran);
                    foreach ($val_pembayaran as $value_bayar){
                        $explode = explode("=",$value_bayar);
                        if((int)$explode[0] == $credit_card){
                            $creditCard_val += (int)$explode[1];
                        }
                        if((int)$explode[0] == $bank_transfer){
                            $bankTransfer_val += (int)$explode[1];
                        }
                        if((int)$explode[0] == $debit_card){
                            $debitCard_val += (int)$explode[1];
                        }
                    }
                }
                $totalCreditCard += $creditCard_val;
                $totalBankTransfer += $bankTransfer_val;
                $totalDebitCard += $debitCard_val;
                ?>

            <tr>
                <td style="text-align: center;"><?= $no ?></td>
                <td><?= $tgl_closingkasir ?></td>
                <td><?= $infoPasien; ?></td>
                <td><?= $noPembayaran; ?></td>
                <td><?= $caraBayar; ?></td>
                <td><?= $metodePembayaranNama; ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($totalDitagihkan, 0) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($totalCash, 0) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($debitCard_val) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($creditCard_val) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($bankTransfer_val) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($totalPayer, 0) ?></td>
            </tr>
            
        <?php $no++; endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" style="text-align: right;"><strong></td>
            <td style="text-align: right;"><strong><?= DocoHelpers::formatNumber($totalTagihan, 0) ?></strong></td>
            <td style="text-align: right;"><strong><?= DocoHelpers::formatNumber($totalTunai, 0) ?></strong></td>
            <td style="text-align: right;"><strong><?= DocoHelpers::formatNumber($totalDebitCard, 0) ?></strong></td>
            <td style="text-align: right;"><strong><?= DocoHelpers::formatNumber($totalCreditCard, 0) ?></strong></td>
            <td style="text-align: right;"><strong><?= DocoHelpers::formatNumber($totalBankTransfer, 0) ?></strong></td>
            <td style="text-align: right;"><strong><?= DocoHelpers::formatNumber($totalDijamin, 0) ?></strong></td>
        </tr>
    </tfoot>
</table>