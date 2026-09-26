<?php 
    use Doco\components\DocoHelpers;
    $patientAmount = (int) $pembayaran['total_ditagihkan'] - (int) $pembayaran['total_dijamin'];
    $payerAmount = $pembayaran['total_dijamin'];
    $isNaik = $konfigSystem['is_pembulatankeatas'];
    $satuan = $konfigSystem['satuanpembulatan'];
?>
<style>
    .tbl-header {
        border: 1px solid black;
    }

    .tbl-header td {
        padding: 3px;
    }
    .tbl-bordered {
        border-collapse: collapse;
        font-size: 12px;
    }

    .tbl-footer {
        border-collapse: collapse;
        font-size: 12px;
    }

    .tbl-bordered thead th {
        border: 1px solid black;
        padding: 3px;
    }
    .tbl-bordered tbody td {
        border: 1px solid black;
        padding: 3px;
    }
    .number {
        text-align: right
    }
    .center {
        text-align: center
    }
    .border-bottom {
        border-bottom: 1px solid;
    }
</style>
<table width="100%" class="tbl-bordered">
<thead>
    <tr>
        <th style="width:5%">No.</th>
        <th style="width:35%">Service Item(s)</th>
        <th style="width:5%">Qty</th>
        <th style="width:5%">UOM</th>
        <th style="width:10%">Unit Price</th>
        <th style="width:20%">Payer Amount</th>
        <th style="width:20%">Patient Amount</th>
    </tr>
</thead>
<tbody>
	<?php 
        $totalItem = 0;
        $total = 0; 
        foreach ($data as $key => $value) : 
            $totalItem += count($value);
    ?>
        <tr>
            <td colspan="7"><strong><?= $key ?></strong> </td>
        </tr>
        <?php $no = 1; foreach ($value as $k => $v) : 
            $total = $total + $v['sub_total'];
        ?>
            <tr>
                <td class="center"><?= $no++ ?></td>
                <td><?= $v['tindakan_obat_nama'] ?></td>
                <td class="number"><?= $v['qty'] ?></td>
                <td></td>
                <td class="number"><?= DocoHelpers::formatNumber($v['tarif_satuan']) ?></td>
                <td class="number"><?= $dijamin ? DocoHelpers::formatNumber($v['sub_total']) : 0 ?></td>
                <td class="number"><?= !$dijamin ? DocoHelpers::formatNumber($v['sub_total']) : 0 ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
</tbody>
<?php 
    $totalAmountPayer = $isPayer ? (int) $pembayaran['total_tagihan'] : 0;
    $totalAmountPatient = !$isPayer ? (int) $pembayaran['total_tagihan'] : 0;

    $adminPayer = $isPayer ? (int) $pembayaran['total_administrasi'] : 0;
    $adminPatient = !$isPayer ? (int) $pembayaran['total_administrasi'] : 0;

    $discPayer = $isPayer ? (int) $pembayaran['discount'] : 0;
    $discPatient = !$isPayer ? (int) $pembayaran['discount'] : 0;

    $uangMuka = (int) $pembayaran['penggunaan_uangmuka'];
    $dept = (int) $pembayaran['total_sisatagihan'];

    $amountPayer = $isPayer 
        ? (int) $pembayaran['total_ditagihkan'] + $pembayaran['penggunaan_uangmuka'] + $pembayaran['total_sisatagihan'] : 0;
    $amountPatient = ($totalAmountPatient + $adminPatient) - ($discPatient + $uangMuka + $dept);
    $payerNomin = $payerAmount >= $amountPayer ? $amountPayer : $payerAmount;
?>
</table>
<?php if ($totalItem >= 10) : 
?>
<pagebreak />
<?php
    endif;
?>
<table width="100%" class="tbl-footer">
    <tbody>
        <tr>
            <td colspan="5" class="number" style="width:60%">Total Amount :</td>
            <td class="number" style="width:20%"><?= DocoHelpers::formatNumber($totalAmountPayer) ?></td>
            <td class="number" style="width:20%"><?= DocoHelpers::formatNumber($totalAmountPatient) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">Administration Fee:</td>
            <td class="number"><?= DocoHelpers::formatNumber($adminPayer) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($adminPatient) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">Discount :</td>
            <td class="number"><?= DocoHelpers::formatNumber($discPayer) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($discPatient) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">DP :</td>
            <td class="number">0</td>
            <td class="number"><?= DocoHelpers::formatNumber($uangMuka) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">DEBT :</td>
            <td class="number">0</td>
            <td class="number"><?= DocoHelpers::formatNumber($dept) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">Amount :</td>
            <td class="number border-bottom"><?= DocoHelpers::formatNumber($amountPayer)?></td>
            <td class="number border-bottom"><?= DocoHelpers::formatNumber($amountPatient) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">Total Amount Covered by :</td>
            <?php 
            $pembulatanPayer = DocoHelpers::pembulatan($payerAmount, $isNaik, $satuan);
            $pembulatanPatient = DocoHelpers::pembulatan($amountPayer + $amountPatient - $payerNomin, $isNaik, $satuan);
             ?>
            <td class="number"><?= DocoHelpers::formatNumber($pembulatanPayer['total']); ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($pembulatanPatient['total']) ?></td>
        </tr>
        <?php
            if (!empty($payerAmount)) :
        ?>
            <tr>
                <td style="text-align: left;" colspan="7" class="number border-bottom">
                    Payer Amount in words : <?= DocoHelpers::terbilangToEnglish($pembulatanPayer['total']) ?> Rupiahs</td>
            </tr>
        <?php
            endif;
        ?>

        <?php
            if (!empty($patientAmount)) :
        ?>
            <tr>
                <td style="text-align: left;" colspan="7" class="number border-bottom">
                Patient amount in words : <?= DocoHelpers::terbilangToEnglish($pembulatanPatient['total']) ?> Rupiahs</td>
            </tr>
        <?php
            endif;
        ?>

        <tr>
            <td colspan="3">Deposite Balance (<?= date('d M Y H:i') ?>)</td>
            <td colspan="4"> : <?= DocoHelpers::formatNumber($pembayaran['penggunaan_uangmuka']) ?></td>
        </tr>
        <tr>
            <td colspan="3">Remarks</td>
            <td colspan="4"> : <?= $pembayaran['catatan'] ?></td>
        </tr>
        <tr>
            <td colspan="3">Receipt Payment from</td>
            <td colspan="4"> : <?= $received ?></td>
        </tr>
        <?php
            if ($pembayaran['total_tunai'] > 0) :
        ?>
            <tr>
                <td></td>
                <td colspan="5">Cash : </td>
                <td class="number border-bottom"><?= DocoHelpers::formatNumber($pembayaran['total_tunai']) ?></td>
            </tr>
        <?php
            endif;
        ?>
        <?php
            if (!empty($listMetode)) :
                foreach ($listMetode as $value) :
                    $metode = preg_replace("/^\w+ - /", '', $value['metode_bayar']);
        ?>
            <tr>
                <td></td>
                <td colspan="5"><?= $value['no_kartu'] ?> - <?= $metode ?></td>
                <td class="number border-bottom"><?= DocoHelpers::formatNumber($value['total_dibayar']) ?></td>
            </tr>
        <?php
                endforeach;
            endif; 
        ?>
        <?php
            if (!empty($qPayer)) :
                foreach ($qPayer as $value) :
                    $nama = preg_replace("/^\w+ - /", '', $value['penjamin_nama']);
        ?>
            <tr>
                <td></td>
                <td colspan="5">by Payer: <?= $nama ?></td>
                <td class="number border-bottom"><?= DocoHelpers::formatNumber($value['total_dijamin']) ?></td>
            </tr>
        <?php
                endforeach;
            endif; 
        ?>
        <tr>
            <?php $ending = $balance > 0 ? DocoHelpers::formatNumber($pembayaran['total_tunai'] - $pembulatanPatient['total']) : 0; ?>
            <td colspan="5"></td>
            <td>Ending Balance :</td>
            <td class="number border-bottom"><?= $ending ?></td>
        </tr>
    </tbody>
</table>

