<?php 
    use Doco\components\DocoHelpers;
    $total = 0; 
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
            <th width="5%"><strong>No</strong> </th>
            <th width="12%"><strong>Trans Date</strong></th>
            <th width="15%"><strong>Service(s)</strong></th>
            <th width="5%"><strong>Qty</strong></th>
            <th width="10%"><strong>Price</strong></th>
            <th width="10%"><strong>Discount</strong></th>
            <th width="5%"><strong>Cyto</strong></th>
            <th width="10%"><strong>Cyto Price</strong></th>
            <th width="10%"><strong>Total</strong></th>
        </tr>
    </thead>
    <tbody>
        <?php if($biaya_admin > 0) : ?>
            <tr>
                <td colspan="9" style="text-align: left;background-color: #F7DC6F;">
                    <strong>Biaya Administrasi</strong>
                </td>
            </tr>
            <tr>
                <td class="center">1</td>
                <td><?= date('d/m/Y', strtotime($pembayaran['tgl_pembayaran'])) ?></td>
                <td>Biaya Administrasi</td>
                <td class="number">1</td>
                <td class="number"><?= DocoHelpers::formatNumber($biaya_admin) ?></td>
                <td class="number">0</td>
                <td>No</td>
                <td class="number">0</td>
                <td class="number"><?= DocoHelpers::formatNumber($biaya_admin) ?></td>
            </tr>
        <?php endif; ?>
        <?php foreach ($dataTindakan as $ruangan => $kelompok) : ?>
        <tr>
            <td colspan="9" style="text-align: left;background-color: #D5D8DC;">
                <strong><?= $ruangan ?></strong>
            </td>
        </tr>
        <?php foreach ($kelompok as $key => $value) : $totalAll = 0; ?>
            <tr>
                <td colspan="9" style="text-align: left;background-color: #F7DC6F;">
                    <strong><?= $key ?></strong>
                </td>
            </tr>
            <?php $no = 1; foreach ($value as $val) : 
                $qty = $val['qty'];
                $harga_satuan = $val['harga_satuan'];
                $tarif_diskon = $val['tarif_diskon'];
                $tarifcyto_tindakan = $val['tarifcyto_tindakan'];
                $cyto_tindakan = ($val['cyto_tindakan']) ? 'Yes' : 'No';
                $tindakan = $val['tindakan_obat'];
                $tgl_pelayanan = $val['tgl_pelayanan'];
                $grandTotal = (($qty*$harga_satuan) - ($tarif_diskon + $tarifcyto_tindakan));
                $totalAll += $grandTotal;
            ?>
            <tr>
                <td class="center"><?= $no ?></td>
                <td><?= date('d/m/Y', strtotime($tgl_pelayanan)) ?></td>
                <td><?= $tindakan ?></td>
                <td class="number"><?= $qty ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($harga_satuan) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($tarif_diskon) ?></td>
                <td><?= $cyto_tindakan ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($tarifcyto_tindakan) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($grandTotal) ?></td>
            </tr>
            <?php $no++; endforeach; $total += $totalAll; ?>
            <tr>
                <td colspan="8" class="number"><b>Total Tagihan <?= $key ?> </b></td>
                <td class="number" width="15%"><b><?= DocoHelpers::formatNumber($totalAll) ?></b></td>
            </tr>
            <?php endforeach; ?>
            <?php endforeach; ?>
    </tbody>
</table>

<table width="100%" class="tbl-bordered">
    <tbody>
        <tr>
            <td colspan="8" class="number"><b>Grand Total : </b></td>
            <td class="number" width="15%">
                <b><?= DocoHelpers::formatNumber($total+$biaya_admin) ?></b>
            </td>
        </tr>
    </tbody>
</table>