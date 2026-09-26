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
        <th style="width:35%">Keterangan</th>
        <th style="width:5%">Qty</th>
        <th style="width:5%">Satuan</th>
        <th style="width:10%">Tarif</th>
        <th style="width:20%">Dibayar Asuransi</th>
        <th style="width:20%">Dibayar Pasien</th>
    </tr>
</thead>
<tbody>
    <?php 
        $totalItem = 0;
        $total = 0; 
        $totalAmountPayer = $totalAmountPatient = 0;
        foreach ($data as $key => $value) : 
            $totalItem += count($value);

    ?>
        <tr>
            <td colspan="7"><strong><?= $key ?></strong> </td>
        </tr>
        <?php $no = 1; foreach ($value as $k => $v) : 
            $total = $total + $v['sub_total'];
            $totalAmountPayer = $totalAmountPayer + $v['tarif_dijamin'];
            $totalAmountPatient = $totalAmountPatient + $v['tarif_dibayarkan'];
        ?>
            <tr>
                <td class="center"><?= $no++ ?></td>
                <td><?= $v['tindakan_obat_nama'] ?></td>
                <td class="number"><?= $v['qty'] ?></td>
                <td></td>
                <td class="number"><?= DocoHelpers::formatNumber($v['tarif_satuan']) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($v['tarif_dijamin']) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($v['tarif_dibayarkan']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
</tbody>
<?php 
    $adminPayer = $isPayer ? (int) $pembayaran['total_administrasi'] : 0;
    $adminPatient = !$isPayer ? (int) $pembayaran['total_administrasi'] : 0;
    $discPayer = $isPayer ? (int) $pembayaran['discount'] : 0;
    $discPatient = !$isPayer ? (int) $pembayaran['discount'] : 0;
    $uangMuka = (int) $pembayaran['penggunaan_uangmuka'];
    $debt = (int) $pembayaran['total_sisatagihan'];

    $grandTotalPayer = $totalAmountPayer + $adminPayer - $discPayer;
    if($uangMuka > 0) {
        $grandTotalPatient = $uangMuka - ($totalAmountPatient + $adminPatient - $discPatient - $debt);
    }
    else {
        $grandTotalPatient = $totalAmountPatient + $adminPatient - $discPatient - $debt;
    }
    
    $pembulatanPayer = DocoHelpers::pembulatan($grandTotalPayer, $isNaik, $satuan);
    $pembulatanPatient = DocoHelpers::pembulatan($grandTotalPatient, $isNaik, $satuan);
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
            <td colspan="5" class="number" style="width:60%">Sub Total :</td>
            <td class="number" style="width:20%"><?= DocoHelpers::formatNumber($totalAmountPayer) ?></td>
            <td class="number" style="width:20%"><?= DocoHelpers::formatNumber($totalAmountPatient) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">Biaya Administrasi:</td>
            <td class="number"><?= DocoHelpers::formatNumber($adminPayer) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($adminPatient) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">Diskon :</td>
            <td class="number"><?= DocoHelpers::formatNumber($discPayer) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($discPatient) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">Uang Muka :</td>
            <td class="number">0</td>
            <td class="number"><?= DocoHelpers::formatNumber($uangMuka) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">Piutang :</td>
            <td class="number">0</td>
            <td class="number"><?= DocoHelpers::formatNumber($debt) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number"><strong>Pembulatan :</strong></td>
            <td class="number border-bottom"><?= DocoHelpers::formatNumber($pembulatanPayer['pembulatan'])?></td>
            <td class="number border-bottom"><?= DocoHelpers::formatNumber($pembulatanPatient['pembulatan']) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">Total yang dibayar :</td>
            <td class="number"><?= DocoHelpers::formatNumber($pembulatanPayer['total']); ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($pembulatanPatient['total']) ?></td>
        </tr>
        <?php if (!empty($payerAmount)) : ?>
            <tr>
                <td style="text-align: left;" colspan="7" class="number border-bottom">
                    Total yang dibayar Asuransi : <?= DocoHelpers::Terbilang($pembulatanPayer['total']) ?> Rupiah</td>
            </tr>
        <?php endif; ?>

        <?php if (!empty($patientAmount)) : ?>
            <tr>
                <td style="text-align: left;" colspan="7" class="number border-bottom">
                Total yang dibayar Pasien : <?= DocoHelpers::Terbilang($pembulatanPatient['total']) ?> Rupiah</td>
            </tr>
        <?php endif; ?>
        <tr>
            <td colspan="3"></td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td colspan="3">Penggunaan Uang Muka (<?= date('d M Y H:i') ?>)</td>
            <td colspan="4"> : <?= DocoHelpers::formatNumber($pembayaran['penggunaan_uangmuka']) ?></td>
        </tr>
        <tr>
            <td colspan="3">Catatan</td>
            <td colspan="4"> : <?= $pembayaran['catatan'] ?></td>
        </tr>
        <tr>
            <td colspan="3">Sudah diterima dari</td>
            <td colspan="4"> : <?= $received ?></td>
        </tr>
        <?php if ($pembayaran['total_tunai'] > 0) : ?>
            <tr>
                <td></td>
                <td colspan="5">Tunai : </td>
                <td class="number border-bottom"><?= DocoHelpers::formatNumber($pembayaran['total_tunai']) ?></td>
            </tr>
        <?php endif; ?>
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
                <td colspan="5">Asuransi: <?= $nama ?></td>
                <td class="number border-bottom"><?= DocoHelpers::formatNumber($value['total_dijamin']) ?></td>
            </tr>
        <?php
                endforeach;
            endif; 
        ?>
        <tr>
            <?php $ending = $balance > 0 ? DocoHelpers::formatNumber($pembayaran['total_tunai'] - $pembulatanPatient['total']) : 0; ?>
            <td colspan="5"></td>
            <td>Sisa :</td>
            <td class="number border-bottom"><?= $ending ?></td>
        </tr>
    </tbody>
</table>

