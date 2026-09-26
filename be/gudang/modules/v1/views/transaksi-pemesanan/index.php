<?php
    use Doco\components\DocoHelpers;
?>
<table  border="1" style="width:100%; border-collapse: collapse;">
    <thead>
        <tr>
            <td>No</td>
            <td>Nama Barang</td>
            <td>Qty Pemesanan</td>
            <td>Qty Konversi</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            foreach ($detail as $value) {
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= isset($value['barang_nama']) ? $value['barang_nama'] : '' ?></td>
                <td style="text-align: right;">
                    <?= isset($value['qty_besar']) ? DocoHelpers::formatNumber($value['qty_besar']) .' '. $value['satuan_besar'] : '' ?>
                </td>
                <td style="text-align: right;">
                    <?= isset($value['qty_kecil']) ? DocoHelpers::formatNumber($value['qty_kecil']) . ' ' . $value['satuan_kecil'] : '' ?>
                </td>
            </tr>
        <?php
            $no++;
            }
        ?>
    </tbody>
</table>

