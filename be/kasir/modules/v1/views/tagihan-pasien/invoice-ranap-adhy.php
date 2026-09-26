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
</style>
<table width="100%" class="tbl-bordered">
<thead>
    <tr>
        <th>No.</th>
        <th>Keterangan</th>
        <th>Dibayar Asuransi</th>
        <th>Dibayar Pasien</th>
    </tr>
</thead>
<tbody>
    <?php 
        $no = 1;
        $totalItem = 0;
        $totalAmountPayer = $totalAmountPatient = 0;
        foreach ($data as $key => $value) :
            $totalItem += count($value);
            $totalAmountPayer = $totalAmountPayer + $value['total_dijamin'];
            $totalAmountPatient = $totalAmountPatient + $value['total_dibayar'];
    ?>
            <tr>
                <td style="width:5%"><?= $no ?></td>
                <td style="width:60%"><?= $value['kelompoktindakan_nama'] ?></td>
                <td class="number" style="width:17.5%"><?= DocoHelpers::formatNumber($value['total_dijamin']) ?></td>
                <td class="number" style="width:17.5%"><?= DocoHelpers::formatNumber($value['total_dibayar']) ?></td>
            </tr>
    <?php 
            $no++;
        endforeach; 
    ?>
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
$adminPatient = ($jenis_invoice == $invTotal || $jenis_invoice == $invPatient) ? $totalAdm : 0;

$discPayer = ($jenis_invoice == $invPayer) ? $discount : 0;
$discPatient = ($jenis_invoice == $invTotal || $jenis_invoice == $invPatient) ? $discount : 0;
$deposit = ($jenis_invoice == $invTotal || $jenis_invoice == $invPatient) ? $uangMuka : 0;
$debt = ($jenis_invoice == $invTotal || $jenis_invoice == $invPatient) ? $piutang : 0;
$grandTotalPayer = $totalAmountPayer + $adminPayer;
$grandTotalPatient = ($totalAmountPatient + $adminPatient + $debt) - $discPatient - $deposit ;

$pembulatanPayer = DocoHelpers::pembulatan($grandTotalPayer, $isNaik, $satuan);
$pembulatanPatient = DocoHelpers::pembulatan($grandTotalPatient, $isNaik, $satuan);
?>
<?php
    if (count($totalItem) >= 10) :
?>
    <pagebreak />
<?php
    endif;
?>
<table width="100%" class="tbl-footer">
<tbody>
    <tr>
        <td colspan="2" class="number" style="width:65%">Sub Total:</td>
        <td class="number" style="width:17.5%"><?= DocoHelpers::formatNumber($totalAmountPayer) ?></td>
        <td class="number" style="width:17.5%"><?= DocoHelpers::formatNumber($totalAmountPatient) ?></td>
    </tr>
    <tr>
        <td colspan="2" class="number">Biaya Administrasi:</td>
        <td class="number"><?= DocoHelpers::formatNumber($adminPayer) ?></td>
        <td class="number"><?= DocoHelpers::formatNumber($adminPatient) ?></td>
    </tr>
    <tr>
        <td colspan="2" class="number">Diskon:</td>
        <td class="number"><?= DocoHelpers::formatNumber($discPayer) ?></td>
        <td class="number"><?= DocoHelpers::formatNumber($discPatient) ?></td>
    </tr>
    <tr>
        <td colspan="2" class="number">Uang Muka:</td>
        <td class="number">0</td>
        <td class="number"><?= DocoHelpers::formatNumber($deposit) ?></td>
    </tr>
    <tr>
        <td colspan="2" class="number">Piutang:</td>
        <td class="number">0</td>
        <td class="number"><?= DocoHelpers::formatNumber($debt) ?></td>
    </tr>
    <tr>
        <td colspan="2" class="number"><strong>Pembulatan :</strong></td>
        <td class="number border-bottom"><?= DocoHelpers::formatNumber($pembulatanPayer['pembulatan'])?></td>
        <td class="number border-bottom"><?= DocoHelpers::formatNumber($pembulatanPatient['pembulatan']) ?></td>
    </tr>
    <tr>
        <td colspan="2" class="number">Total yang harus dibayar :</td>
        <td class="number"><?= DocoHelpers::formatNumber($pembulatanPayer['total']) ?></td>
        <td class="number"><?= DocoHelpers::formatNumber($pembulatanPatient['total']) ?></td>
    </tr>
    <?php
        if ($jenis_invoice == $invPayer || $jenis_invoice == $invTotal && !empty($pembulatanPatient['total'])) :
    ?>
        <tr>
            <td colspan="4">Total yang dibayar Pasien : <?= DocoHelpers::Terbilang($pembulatanPatient['total']) ?> Rupiah</td>
        </tr>
    <?php
        endif;
    ?>

    <?php
        if ($jenis_invoice == $invPayer || $jenis_invoice == $invTotal && !empty($pembulatanPayer['total'])) :
    ?>
        <tr>
            <td colspan="4">Total yang dibayar Asuransi : <?= DocoHelpers::Terbilang($pembulatanPayer['total']) ?> Rupiah</td>
        </tr>
    <?php
        endif;
    ?>
    <?php
        if ($jenis_invoice == $invPayer || $jenis_invoice == $invTotal) :
    ?>
    <tr>
        <td colspan="4" class="border-bottom"></td>
    </tr>
    <tr>
        <td colspan="4">Sudah diterima dari : <?= $nama_pasien ?></td>
    </tr>
    <?php
        if (!empty($pembayaran['total_tunai'])) :
    ?>
    <tr>
        <td colspan="3">Tunai</td>
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
        endif;
    ?>
    <?php
        if (!empty($qPayer)) :
            foreach ($qPayer as $value) :
                $nama = preg_replace("/^\w+ - /", '', $value['penjamin_nama']);
    ?>
            <tr>
                <td colspan="3">Asuransi: <?= $nama ?></td>
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
            <?php $ending = $pembayaran['total_kembalian'] > 0 ? $pembayaran['total_tunai'] - $pembulatanPatient['total'] : 0; ?>
        <td colspan="2"></td>
        <td>Sisa :</td>
        <td class="number border-bottom"><?= DocoHelpers::formatNumber(abs($ending)); ?></td>
    </tr>
</tbody>
</table>


