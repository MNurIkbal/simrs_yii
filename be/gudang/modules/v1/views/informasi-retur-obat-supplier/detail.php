<?php

use Doco\components\DocoHelpers;

?>

<table border="1" cellpadding="1" cellspacing="0" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>No Penerimaan</th>
            <th>No Faktur</th>
            <th>Nama Obat</th>
            <th>Qty Terima</th>
            <th>Tanggal Kadaluarsa</th>
            <th>No Batch</th>
            <th>Qty Retur</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $no = 1;
            foreach ($query as $value) :
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= isset($value['no_penerimaan']) ? $value['no_penerimaan'] : '' ?></td>
                <td><?= isset($value['no_faktur']) ? $value['no_faktur'] : '' ?></td>
                <td><?= isset($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '' ?></td>
                <td><?= isset($value['qty_besar']) ? DocoHelpers::formatNumber($value['qty_besar']) . ' ' . $value['satuanunit_nama'] : '' ?></td>
                <td><?= isset($value['tgl_kadaluarsa']) ? date('d M Y',strtotime($value['tgl_kadaluarsa'])) : '' ?></td>
                <td><?= isset($value['no_batch']) ? $value['no_batch'] : '' ?></td>
                <td><?= isset($value['qty_retur']) ? DocoHelpers::formatNumber($value['qty_retur']) .' '. $value['satuanunit_nama'] : '' ?></td>
            </tr>
        <?php
            $no++;
            endforeach;
        ?>
    </tbody>
</table>