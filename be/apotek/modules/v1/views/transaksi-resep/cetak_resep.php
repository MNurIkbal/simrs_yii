<?php 
use Doco\components\DocoHelpers;
?>
<style type="text/css">
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
    .align-right{
        text-align: right;
    }
    .tbl-bordered td{
        font-size: 12px;
    }
</style>
<table class="tbl-bordered" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td><strong>No</strong></td>
            <td><strong>Racikan / Non Racikan</strong></td>
            <td><strong>R ke</strong></td>
            <td><strong>Nama Obat Alkes</strong></td>
            <td><strong>Signa</strong></td>
            <td class="align-right"><strong>Harga Satuan (Rp.)</strong></td>
            <td><strong>Qty</strong></td>
            <td><strong>Satuan</strong></td>
            <td><strong>Catatan</strong></td>
            <td class="align-right"><strong>Sub Total (Rp.)</strong></td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            $total = 0;
            foreach ($detail as $value) {
                $sub_total = $value['hargasatuan_reseptur'] * $value['qty_reseptur'];
                $harga = $value['hargajual_reseptur'] / $value['qty_reseptur'];
                //$total += $sub_total;
                $total += $value['hargajual_reseptur'];
        ?>
            <tr style="font-size: 12px;">
                <td><?= $no ?></td>
                <td><?= ($value['racikan_id'] == 1 ) ? 'Racikan' : 'Non Racikan' ?></td>
                <td><?= ($value['rke'] == 0 || $value['rke'] == NULL ) ? '-' : $value['rke'] ?></td>
                <td><?= isset($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '' ?></td>
                <td width="20%"><?= ($value['signa_oa'] == 0 || $value['signa_oa'] == NULL ) ? '-' : $value['signa_oa'] ?></td>
                <td width="20%" class="align-right"><?= isset($harga) ? DocoHelpers::formatNumber($harga) : '' ?></td>
                <td><?= $value['qty_reseptur'] ?></td>
                <td><?= $value['satuan_input'] ?></td>
                <td><?= $value['etiket_reseptur'] ?></td>
                <td class="align-right"><?= DocoHelpers::formatNumber($value['hargajual_reseptur']) ?></td>
            </tr>
        <?php
            $no++;
            }
        ?>
            <?php
                if ($type == 344) {
                ?>
                    <tr>
                        <td class="align-right" colspan="9">Total (Rp.)</td>
                        <td class="align-right"><?= DocoHelpers::formatNumber($total) ?></td>
                    </tr>
                    
             <?php
                }
                elseif ($type == 343 || $type == 345 ) {
                 ?>
                    <tr>
                        <td class="align-right" colspan="7">Total Tagihan Obat Alkes</td>
                        <td class="align-right"><?= DocoHelpers::formatNumber($total) ?></td>
                    </tr>
                    <tr>
                        <td class="align-right" colspan="7">Administrasi</td>
                        <td class="align-right"><?= DocoHelpers::formatNumber($biayaadministrasi) ?></td>
                    </tr>
                    <tr>
                        <td class="align-right" colspan="7">Jasa Racik</td>
                        <td class="align-right"><?= DocoHelpers::formatNumber($totaltarifservice) ?></td>
                    </tr>
                    <tr>
                        <td class="align-right" colspan="7">Pembulatan</td>
                        <td class="align-right"><?= DocoHelpers::formatNumber($pembulatanharga) ?></td>
                    </tr>
                    <tr>
                        <td class="align-right" colspan="7">Total Tagihan Pasien</td>
                        <?php

                        $totalTagihan = $total + $biayaadministrasi + $totaltarifservice + $pembulatanharga;
                        ?>
                        <td class="align-right"><?= DocoHelpers::formatNumber($totalTagihan) ?></td>
                    </tr>
            <?php
                }
            ?>
    </tbody>
</table>
