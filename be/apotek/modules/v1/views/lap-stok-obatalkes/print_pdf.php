<?php

/**
 * @Author: Sunarko
 * @Date:   2018-08-15 14:20:03
 * @Last Modified by:
 * @Last Modified time:
 */

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
            <th>No</th>
            <th>Periode Stok</th>
            <th>Ruangan</th>
            <th>Nama Obat Alkes</th>
            <th>Qty Masuk</th>
            <th>Qty Keluar</th>
            <th>Qty Dipesan</th>
            <th>Qty Tersedia</th>
            <th>Stok</th>
       </tr>
    </thead>
    <tbody>
        <?php $counter=1; foreach($data as $row) : ?>
           <tr>
                <td><?= $counter ?></td>
                <td><?= $row['periodestok_nama'] ?></td>
                <td><?= $row['ruangan_nama'] ?></td>
                <td><?= $row['obatalkes_namalain'] ?></td>
                <td><?= $row['qty_masuk'] ?></td>
                <td><?= $row['qty_keluar'] ?></td>
                <td><?= $row['qty_dipesan'] ?></td>
                <td><?= $row['qty_tersedia'] ?></td>
                <td><?= $row['qty_stok'] ?></td>
           </tr>
        <?php $counter++; endforeach; ?>
    </tbody>
</table>