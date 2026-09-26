<?php 
    use Doco\components\DocoHelpers;
    use Doco\components\DocoConstants;
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
    .blank_row {
        height: 30px !important;
        background-color: #FFFFFF;
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
        $totalItem = 0;
        $totalAmountPayer = $totalAmountPatient = 0;
        if(!empty($detail)) :
        foreach ($detail as $key => $value) : 
            $totalItem += count($value);
            $totalAmountPayer += $value['tarif_dijamin'];
            $totalAmountPatient += $value['tarif_dibayarkan'];
    ?>
            <tr>
                <td style="width:5%"><?= $no ?></td>
                <td style="width:60%"><?= $value['kelompoktindakan_nama'] ?></td>
                <td class="number" style="width:17.5%">
                    <?= DocoHelpers::formatNumber($value['tarif_dijamin']) ?></td>
                <td class="number" style="width:17.5%">
                    <?= DocoHelpers::formatNumber($value['tarif_dibayarkan']) ?></td>
            </tr>
    <?php 
            $no++;
        endforeach; 
        else :
    ?>
    <tr>
        <td colspan="4" style="width:2px"> &nbsp;</td>
    </tr>
    <?php endif; ?>
</tbody>
</table>
<?php 
$isNaik = $konfigSystem['is_pembulatankeatas'];
$satuan = $konfigSystem['satuanpembulatan'];
$invPatient = DocoConstants::INV_PASIEN;
$invPayer = DocoConstants::INV_PENJAMIN;
$invTotal = DocoConstants::INV_TOTAL;
$totalAdm = (int) $pembayaran['total_administrasi'];
$discount = (int) $pembayaran['discount'];
$uangMuka = (int) $pembayaran['penggunaan_uangmuka'];
$piutang = (int) $pembayaran['total_sisatagihan'];

$adminPayer = ($jenis_invoice == $invPayer) ? $totalAdm : 0;
$adminPatient = ($jenis_invoice == $invPatient) ? $totalAdm : 0;

$discPayer = ($jenis_invoice == $invPayer) ? $discount : 0;
$discPatient = ($jenis_invoice == $invPatient) ? $discount : 0;
$deposit = ($jenis_invoice == $invPatient) ? $uangMuka : 0;
$debt = ($jenis_invoice == $invPatient) ? $piutang : 0;
$grandTotalPayer = $totalAmountPayer + $adminPayer - $discPayer - $deposit - $debt;
$grandTotalPatient = $totalAmountPatient + $adminPatient - $discPatient - $deposit - $debt;
$pembulatanPayer = DocoHelpers::pembulatan($grandTotalPayer, $isNaik, $satuan);
$pembulatanPatient = DocoHelpers::pembulatan($grandTotalPatient, $isNaik, $satuan);
?>
<?php if (count($totalItem) >= 10) : ?>
    <pagebreak />
<?php endif; ?>

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
        <td class="number"><?= DocoHelpers::formatNumber($deposit) ?></td>
    </tr>
    <tr>
        <td colspan="2" class="number">DEBT:</td>
        <td class="number">0</td>
        <td class="number"><?= DocoHelpers::formatNumber($debt) ?></td>
    </tr>
    <tr>
        <td colspan="2" class="number"><strong>Rounded Bill Amount :</strong></td>
        <td class="number border-bottom"><?= DocoHelpers::formatNumber($pembulatanPayer['pembulatan'])?></td>
        <td class="number border-bottom"><?= DocoHelpers::formatNumber($pembulatanPatient['pembulatan']) ?></td>
    </tr>
    <tr>
        <td colspan="2" class="number">Total amount cover by payer:</td>
        <td class="number"><?= DocoHelpers::formatNumber($pembulatanPayer['total']) ?></td>
        <td class="number"><?= DocoHelpers::formatNumber($pembulatanPatient['total']) ?></td>
    </tr>
    <tr class="blank_row">
        <td colspan="4"></td>
    </tr>
    <?php if($jenis_invoice == $invPayer || $jenis_invoice == $invTotal) : ?>
        <tr>
            <td colspan="4">Payer Amount in words: <?= DocoHelpers::terbilangToEnglish($pembulatanPayer['total']) ?> Rupiahs</td>
        </tr>
    <?php endif; ?>

    <?php if($jenis_invoice == $invPatient || $jenis_invoice == $invTotal) : ?>
        <tr>
            <td colspan="4">Patient Amount in words: <?= DocoHelpers::terbilangToEnglish($pembulatanPatient['total']) ?> Rupiahs</td>
        </tr>
        <tr>
            <td colspan="4" class="border-bottom"></td>
        </tr>
        <tr>
            <td colspan="4">Received payment from : <?= $namaPasien ?></td>
        </tr>
        <?php if (!empty($pembayaran['total_tunai'])) : ?>
        <tr>
            <td colspan="3">Cash</td>
            <td class="number"><?= (!$isPayer) ? (DocoHelpers::formatNumber($pembayaran['total_tunai'])) : (DocoHelpers::formatNumber($pembayaran['total_tunai'])) ?></td>
        </tr>
    <?php endif; endif; ?>

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
        if ($jenis_invoice == $invPayer || $jenis_invoice == $invTotal && !empty($amountPayer)) :
    ?>
    <?php
        if (!empty($qPayer)) :
            foreach ($qPayer as $value) :
                $nama = preg_replace("/^\w+ - /", '', $value['penjamin_nama']);
                $total_dijamin = $value['total_dijamin'];
    ?>
            <tr>
                <td colspan="3">by Payer: <?= $nama ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($total_dijamin) ?></td>
            </tr>
    <?php
            endforeach;
        endif; 
    endif;
    ?>
    <?php if($jenis_invoice == $invTotal || $jenis_invoice == $invPatient) : ?>
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
    <?php endif; ?>
</tbody>
</table>

