<?php 
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants; 
$isNaik = $konfigSystem['is_pembulatankeatas'];
$satuan = $konfigSystem['satuanpembulatan'];
?>
<style>
    .tbl-bordered {
        border-collapse: collapse;
        font-size: 12px;
    }

    .tbl-bordered thead th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered tbody td {
        border: 1px solid black;
        padding: 5px;
    }

    .tbl-bordered tfoot td {
        padding: 3px;
    }

    .footer  {
        padding: 3px;
    }

    .number {
        text-align: right
    }
    .center {
        text-align: center
    }
    .box {
      width: 200px;
      border: 1px solid;
      padding: 10px;
      margin: 0;
    }
    .tbl-summary thead tr th:nth-child(2) {
        border-bottom: 1px solid !important;
    }
    .tbl-summary tbody tr:nth-child(4) th {
        border-bottom: 1px solid !important;
    }
    .tbl-summary tbody tr:nth-child(4) td {
        border-bottom: 1px solid !important;
    }
    .tbl-summary tbody tr:nth-child(6) td {
        border-bottom: 1px solid !important;
    }
    .header-box {
        background-color: #ffffff;
        filter: alpha(opacity=40);
        opacity: 0.95;
        border:1px solid;
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
            $no = 1; 
            $totalItem = 0;
            $total = 0; 
            $totalAmountPayer = $totalAmountPatient = 0;
            if(!empty($data)) : 
            foreach ($data as $key => $value) :
                $totalItem += count($value);
        ?>
            <tr>
                <td rowspan="<?= count($value) + 1 ?>" style="width:2px"> <?= $no++ ?></td>
                <td colspan="6"><strong><?= $key ?></strong> </td>
            </tr>
            <?php 
                foreach ($value as $k => $v) : 
                    $total += $v['sub_total'];
                    $totalAmountPayer = $totalAmountPayer + $v['tarif_dijamin'];
                    $totalAmountPatient = $totalAmountPatient + $v['tarif_dibayarkan'];
                    $tindakan = ($v['is_konsultasi']) ? $v['dokter_tindakan'] : $v['tindakan_obat_nama'];
            ?>
                <tr>
                    <td><?= $tindakan ?></td>
                    <td class="number"><?= $v['qty'] ?></td>
                    <td></td>
                    <td class="number"><?= DocoHelpers::formatNumber($v['tarif_satuan']) ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($v['tarif_dijamin']) ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($v['tarif_dibayarkan']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; else : ?>
            <tr>
                <td colspan="7" style="width:2px"> &nbsp;</td>
            </tr>
   <?php endif; ?>
    </tbody>
</table>
<?php 
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
<table width="100%" class="tbl-bordered">
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
        <td class="number"><?= DocoHelpers::formatNumber($debt) ?></td>
    </tr>
    <tr>
        <td colspan="5" class="number"><strong>Rounded Bill Amount :</strong></td>
        <td class="number border-bottom"><?= DocoHelpers::formatNumber($pembulatanPayer['pembulatan'])?></td>
        <td class="number border-bottom"><?= DocoHelpers::formatNumber($pembulatanPatient['pembulatan']) ?></td>
    </tr>
    <tr>
        <td colspan="5" class="number">Total Amount Covered by :</td>
        <?php 
        
         ?>
        <td class="number"><?= DocoHelpers::formatNumber($pembulatanPayer['total']); ?></td>
        <td class="number"><?= DocoHelpers::formatNumber($pembulatanPatient['total']) ?></td>
    </tr>
    <?php
        if ($jenis_invoice == $invPayer || $jenis_invoice == $invTotal) :
    ?>
        <tr>
            <td style="text-align: left;" colspan="7" class="number border-bottom">
                Payer Amount in words : <?= DocoHelpers::terbilangToEnglish($pembulatanPayer['total']) ?> Rupiahs</td>
        </tr>
    <?php
        endif;
    ?>

    <?php if ($jenis_invoice == $invPatient || $jenis_invoice == $invTotal) : ?>
        <tr>
            <td style="text-align: left;" colspan="7" class="number border-bottom">
            Patient amount in words : <?= DocoHelpers::terbilangToEnglish($pembulatanPatient['total']) ?> Rupiahs</td>
        </tr>
        <tr>
            <td colspan="6">Received payment from : <?= $nama_pasien ?> </td>
        </tr>
        <tr>
            <td colspan="6">Cash</td>
            <td class="number"><?= DocoHelpers::formatNumber($pembayaran['total_tunai']) ?></td>
        </tr>
    <?php endif; ?>

    <?php
        if (!empty($listMetode)) :
            foreach ($listMetode as $value) :
                $metode = preg_replace("/^\w+ - /", '', $value['metode_bayar']);
    ?>
            <tr>
                <td colspan="5"><?= $value['no_kartu'] ?> - <?= $metode ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['total_dibayar']) ?></td>
            </tr>
    <?php
            endforeach;
        endif; 
    ?>
    <?php
        if ($jenis_invoice == $invPayer || $jenis_invoice == $invTotal && !empty($listPayer)) :
            foreach ($listPayer as $value) :
                $nama = preg_replace("/^\w+ - /", '', $value['penjamin_nama']);
    ?>
            <tr>
                <td colspan="5">by Payer: <?= $nama ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['total_dijamin']) ?></td>
            </tr>
    <?php
            endforeach;
        endif; 
    ?>
    
    <?php if($jenis_invoice == $invTotal || $jenis_invoice == $invPatient) : ?>
    <tr>
        <td colspan="6"></td>
        <td class="border-bottom"></td>
    </tr>
    <tr>
        <?php $ending = $pembayaran['total_kembalian'] > 0 ? DocoHelpers::formatNumber($pembayaran['total_tunai'] - $pembulatanPatient['total']) : 0; ?>
        <td colspan="6" class="number"><strong>Ending Balance :</strong></td>
        <td class="number"><?= $ending ?></td>
    </tr>
    <?php endif; ?>
</table>