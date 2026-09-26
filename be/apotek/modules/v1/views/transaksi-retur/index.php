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
</style>
<table width="100%" class="tbl-bordered">
  <thead  style="font-size: 13px">
        <tr>
            <th>No</th>
            <th>Nama Obat Alkes</th>
            <?= $is_retur_pendaftaran ? "<th>No Resep</th>" : "" ?>
            <th>Tanggal Kadaluarsa</th>
            <th>Harga Satuan (Rp.)</th>
            <th>Qty Terjual</th>
            <th>Total Penjualan (Rp.)</th>
            <th>Qty Retur</th>
            <th>Total Retur (Rp.)</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $no = 1;
            $total = 0;
            foreach ($detail as $value) {
                $harga_retur = $value['qty_retur'] * $value['hargasatuan'];
                $total += $harga_retur;
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= isset($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '' ?></td>
                <?= $is_retur_pendaftaran ? "<td>". $value['noresep_detail'] ."</td>" : "" ?>
                <td><?= isset($value['tglkadaluarsa']) ? date('d M Y',strtotime($value['tglkadaluarsa'])) : '' ?></td>
                <td style="text-align: right;"><?= isset($value['hargasatuan']) ? DocoHelpers::formatNumber($value['hargasatuan']) : 0 ?></td>
                <td style="text-align: right;"><?= isset($value['qty_oa']) ? DocoHelpers::formatNumber($value['qty_oa']) : 0 ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['qty_oa'] * $value['hargasatuan'])?></td>
                <td style="text-align: right;"><?= isset($value['qty_retur']) ? DocoHelpers::formatNumber($value['qty_retur']) : 0 ?></td>
                <td style="text-align: right;"><?= isset($value['qty_retur']) ? DocoHelpers::formatNumber($harga_retur) : 0 ?></td>
            </tr>
        <?php
            $no++;
            }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan=<?= $is_retur_pendaftaran ? "8" : "7" ?> style="text-align: center;"><b>TOTAL</b></td>
            <td style="text-align: right;"><?= DocoHelpers::formatNumber($total) ?></td>
        </tr>
    </tfoot>
</table>
