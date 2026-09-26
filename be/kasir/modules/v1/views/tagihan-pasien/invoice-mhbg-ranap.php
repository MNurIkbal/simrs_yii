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
    
    .tbl-footer {
        border-collapse: collapse;
        font-size: 12px;
    }

    .tbl-bordered {
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
        <th>No.</th>
        <th>Service(s)</th>
        <th>Payer Amount</th>
        <th>Patient Amount</th>
    </tr>
</thead>
<tbody>
    <?php 
        $no = 1;
        foreach ($detail as $key => $value) :
    ?>
            <tr>
                <td style="width:5%"><?= $no ?></td>
                <td style="width:60%"><?= $value['kelompoktindakan_nama'] ?></td>
                <td class="number" style="width:17.5%"><?= $isPayer ? DocoHelpers::formatNumber($value['total_amount']) : 0 ?></td>
                <td class="number" style="width:17.5%"><?= !$isPayer ? DocoHelpers::formatNumber($value['total_amount']) : 0 ?></td>
            </tr>
    <?php 
            $no++;
        endforeach; 
    ?>
</tbody>
</table>
<?php
    if (count($detail) >= 10) :
?>
    <pagebreak />
<?php
    endif;
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

<table width="100%" class="tbl-footer">
<tbody>
    <tr>
        <td colspan="2" class="number" style="width:65%">Total Amount:</td>
        <td class="number" style="width:17.5%"><?= DocoHelpers::formatNumber($totalAmountPayer) ?></td>
        <td class="number" style="width:17.5%"><?= DocoHelpers::formatNumber($totalAmountPatient) ?></td>
    </tr>
    <tr>
        <td colspan="2" class="number">Administration Fee:</td>
        <td class="number"><?= DocoHelpers::formatNumber($adminPayer) ?></td>
        <td class="number"><?= DocoHelpers::formatNumber($adminPatient) ?></td>
    </tr>
    <tr>
        <td colspan="2" class="number">Discount:</td>
        <td class="number"><?= DocoHelpers::formatNumber($discPayer) ?></td>
        <td class="number"><?= DocoHelpers::formatNumber($discPatient) ?></td>
    </tr>
    <tr>
        <td colspan="2" class="number">DP:</td>
        <td class="number">0</td>
        <td class="number"><?= DocoHelpers::formatNumber($uangMuka) ?></td>
    </tr>
        <tr>
            <td colspan="2" class="number">DEBT:</td>
            <td class="number">0</td>
            <td class="number"><?= DocoHelpers::formatNumber($dept) ?></td>
        </tr>
    <tr>
        <td colspan="2" class="number">Amount:</td>
        <td class="number border-bottom"><?= DocoHelpers::formatNumber($amountPayer)?></td>
        <td class="number border-bottom"><?= DocoHelpers::formatNumber($amountPatient) ?></td>
    </tr>
    <tr>
        <?php 
          $pembulatanPayer = DocoHelpers::pembulatan($payerAmount, $isNaik, $satuan);
          $pembulatanPatient = DocoHelpers::pembulatan($amountPayer + $amountPatient - $payerNomin, $isNaik, $satuan);
        ?>
        <td colspan="2" class="number">Total amount cover by payer:</td>
        <td class="number"><?= DocoHelpers::formatNumber($pembulatanPayer['total']) ?></td>
        <td class="number"><?= DocoHelpers::formatNumber($pembulatanPatient['total']) ?></td>
    </tr>
    <?php
        if (!empty($patientAmount)) :
    ?>
        <tr>
            <td colspan="4">Patient Amount in words: <?= DocoHelpers::terbilangToEnglish($pembulatanPatient['total']) ?> Rupiahs</td>
        </tr>
    <?php
        endif;
    ?>

    <?php
        if (!empty($payerAmount)) :
    ?>
        <tr>
            <td colspan="4">Payer Amount in words: <?= DocoHelpers::terbilangToEnglish($pembulatanPayer['total']) ?> Rupiahs</td>
        </tr>
    <?php
        endif;
    ?>
    <tr>
        <td colspan="4" class="border-bottom"></td>
    </tr>

    <tr>
        <td colspan="4">Received payment from : <?= $namaPasien ?></td>
    </tr>
    <?php
        if (!empty($pembayaran['total_tunai'])) :
    ?>
            <tr>
                <td colspan="3">Cash</td>
                <td class="number"><?= DocoHelpers::formatNumber($pembayaran['total_tunai']) ?></td>
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
                <td colspan="3"><?= $value['no_kartu'] ?> - <?= $metode ?></td>
                <td class="number"><?= DocoHelpers::formatNumber((int) $value['total_dibayar']) ?></td>
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
                <td colspan="3">by Payer: <?= $nama ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['total_dijamin']) ?></td>
            </tr>
    <?php
            endforeach;
        endif; 
    ?>
    <tr>
        <td colspan="3"></td>
        <td class="border-bottom"></td>
    </tr>
    <tr>
    		<?php $ending = $pembayaran['total_kembalian'] > 0 ? DocoHelpers::formatNumber($pembayaran['total_tunai'] - $pembulatanPatient['total']) : 0; ?>
        <td colspan="2"></td>
        <td>Ending Balance :</td>
        <td class="number border-bottom"><?= $ending ?></td>
    </tr>
</tbody>
</table>

