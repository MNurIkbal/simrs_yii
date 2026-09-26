<?php

/**
 * @Author  : M.ilhamsyah.P
 * @Date    : 2020-08-10 13:23:53
 * @Last Modified by    :  
 * @Last Modified time  :  
 * @Description : membuat laporan pasien konsul
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
            <th>Nama Barang</th>
            <th>Qty</th>
            <th>Nama Satuan besar</th>
            <th>Qty</th>
            <th>Nama Satuan kecil</th>
            <th>Keterangan</th>
       </tr>
    </thead>
    <tbody>
        <?php foreach($data as $row) : ?>
            <tr>
                    <td>Nomor Pemakaian</td>
                    <td><?= $row[0]['no_pemakaian'] ?></td>
                    <td>Nama Penginput</td>
                    <td><?= $row[0]['pegawai'] ?></td>
                    <td>Tnaggal Pemakaian</td>
                    <td><?= $row[0]['tanggal'] ?></td>
                    <td></td>
            </tr>
            <tr>
                <td>No</td>
                <td>Nama Barang</td>
                <td>Qty</td>
                <td>Nama Satuan Besar</td>
                <td>Qty</td>
                <td>Nama Satuan Kecil</td>
                <td>keterangan</td>
            </tr>
            <?php $a=1; foreach($row as $key) : ?>
            <tr>
                    <td><?= $a ?></td>
                    <td><?= $key['nama_barang'] ?></td>
                    <td><?= $key['qty']?></td>
                    <td><?= $key['satuan_besar'] ?></td>
                    <td><?= $key['qty']?></td>
                    <td><?= $key['stauan_kecil'] ?></td>
                    <td><?= $key['keterangan']?></td>
                ];
            </tr>
            <?php $a++; endforeach; ?>
        <?php endforeach; ?>
    </tbody>
</table>