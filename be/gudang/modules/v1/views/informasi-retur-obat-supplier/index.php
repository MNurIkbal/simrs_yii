<?php

use Doco\components\DocoHelpers;

?>

<table border="1" cellpadding="1" cellspacing="0" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Retur</th>
            <th>No Retur</th>
            <th>No Penerimaan</th>
            <th>No Faktur</th>
            <th>Supplier</th>
            <th>Nama Obat</th>
            <th>Qty Retur</th>
            <th>Alasan Retur</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $no = 1;
            foreach ($query as $value) :
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= isset($value['tgl_retur']) ? date('d M Y', strtotime($value['tgl_retur'])) : '' ?></td>
                <td><?= isset($value['no_returpenerimaanobat']) ? $value['no_returpenerimaanobat'] : '' ?></td>
                <td><?= isset($value['no_penerimaan']) ? $value['no_penerimaan'] : '' ?></td>
                <td><?= isset($value['no_faktur']) ? $value['no_faktur'] : '' ?></td>
                <td><?= isset($value['supplier_nama']) ? $value['supplier_nama'] : '' ?></td>
                <td><?= isset($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '' ?></td>
                <td style="text-align: right;">
                    <?= !is_null($value['qty_input']) ? 
                            DocoHelpers::formatNumber($value['qty_input']) .' '. $value['satuanunit_nama'] :
                            DocoHelpers::formatNumber($value['qty_retur']) . ' ' . $value['satuanunit_nama'] 
                    ?>
                </td>
                <td><?= isset($value['alasan_retur']) ? $value['alasan_retur'] : '' ?></td>
            </tr>
        <?php
            $no++;
            endforeach;
        ?>
    </tbody>
</table>
