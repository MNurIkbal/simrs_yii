<?php

use Doco\components\DocoHelpers;

?>
<table class="tbl tbl-bordered" width="100%">
    <tr>
        <th>No</th>
        <th width="15%">Racikan / Non Racikan</th>
        <th>R Ke-</th>
        <th>Nama Obat</th>
        <th>Satuan Kecil</th>
        <th>Signa</th>
        <th>Qty</th>
    </tr>
    <?php 
    if(!empty($data) ){
    $counter = 1;
    foreach ($data as $value) :
    ?>
        <tr>
            <td><?= $counter++ ?></td>
            <td><?= $value['racikan_nama'] ?></td>
            <td class="text-center" width='8px'><?= $value['rke'] ?></td>
            <td><?= $value['obatalkes_nama'] ?></td>
            <td><?= $value['satuan_kecil'] ?></td>
            <td><?= $value['signa_nama'] ?></td>
            <td class="text-center" width='8px'><?= DocoHelpers::formatNumber($value['qty_reseptur']); ?></td>
        </tr>
    <?php endforeach; 
    } else {
        ?>
        <tr>
            <td colspan=7 style="text-align: center">Data Tidak Tersedia</td>
        </tr>
        <?php
    }
    ?>
</table><br>