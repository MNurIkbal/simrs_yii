<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-06-29 15:21:57
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-12-11 13:22:50
 */

use app\modules\v1\models\SatuanKonversi;

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
    <thead>
        <tr>
            <th>No.</th>
            <th>Kode Obat Alkes</th>
            <th>Nama Obat Alkes</th>
            <th>Qty Pesan</th>
            <th>Qty Terima</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
        foreach ($detail as $row) : ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= !empty($row['obatalkes_kode']) ? $row['obatalkes_kode'] : "-" ?></td>
                <td><?= $row['obatalkes_nama'] ?></td>
                <td><?= $row['qty_pesan'] . ' ' . $row['satuan_besar'] ?></td>
                <td><?= $row['qty_terima'] . ' ' . $row['satuan_besar'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
