<?php

/**
 * @Author  : Sunarko
 * @Date    : 2018-08-14 16:23:53
 * @Last Modified by    :  
 * @Last Modified time  :  
 * @Description : membuat laporan daftar pasien rawat inap
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
            <th>Tanggal Masuk</th>
            <th>Tanggal Keluar</th>
            <th>No. Rekam Medik</th>
            <th>No. Pendaftaran</th>
            <th>Nama Pasien</th>
            <th>Jenis Kelamin</th>
            <th>Dokter</th>
            <th>Cara Bayar / Penjamin</th>
            <th>Kelas Pelayanan / Kelas Tagihan</th>
            <th>Jenis Kasus Penyakit</th>
            <th>Ruangan</th>
            <th>Lama Rawat</th>
            <th>Status</th>
            <th>Catatan</th>
       </tr>
    </thead>
    <tbody>
        <?php $counter=1; foreach($data as $row) : ?>
           <tr>
                <td><?= $counter ?></td>
                <td><?= date('d F Y H:i:s', strtotime($row['Tanggal Masuk'])) ?></td>
                <td><?= ($row['Tanggal Keluar']) ? date('d F Y H:i:s', strtotime($row['Tanggal Keluar'])) : "" ; ?></td>
                <td><?= $row['No. Pendaftaran'] ?></td>
                <td><?= $row['No. Rekam Medik'] ?></td>
                <td><?= $row['Nama Pasien'] ?></td>
                <td><?= $row['Jenis Kelamin'] ?></td>
                <td><?= $row['Dokter'] ?></td>
                <td><?= $row['Cara Bayar'].' / '.$row['Penjamin'] ?></td>
                <td><?= $row['Kelas Pelayanan'] ?></td>
                <td><?= $row['Jenis Kasus Penyakit'] ?></td>
                <td><?= $row['Ruangan'] ?></td>
                <td><?= ($row['Lama Rawat']) ? $row['Lama Rawat']." Hari" : "" ?></td>
                <td><?= $row['status_ranap_nama'] ?></td>
                <td><?= $row['alasan_batal'] ?></td>
           </tr>
        <?php $counter++; endforeach; ?>
    </tbody>
</table>