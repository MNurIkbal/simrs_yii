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
            <th>Tanggal Pendaftaran</th>
            <th>No. Pendaftaran</th>
            <th>No. Rekam Medik</th>
            <th>Nama Pasien</th>   
            <th>Tanggal Konsul</th>
            <th>Dokter Pengirim</th>
            <th>Ruangan Asal</th>
            <th>Dokter Rujukan</th>
            <th>Ruangan Tujuan</th>
       </tr>
    </thead>
    <tbody>
        <?php $counter=1; foreach($data as $row) : ?>
           <tr>
                <td><?= $counter ?></td>
                <td><?= date('d/m/Y H:i:s',strtotime($row['tgl_pendaftaran']));?></td>
                <td><?= $row['no_pendaftaran'] ?></td>
                <td><?= $row['no_rekam_medik'] ?></td>
                <td><?= $row['nama_pasien'] ?></td>
                <td><?= date('d/m/Y H:i:s',strtotime($row['tgl_konsulpoli']));?></td>
                <td><?= $row['nama_dokter'] ?></td>
                <td><?= $row['ruangan_asal'] ?></td>
                <td><?= $row['dok_mengkonsul'] ?></td>
                <td><?= $row['ruangan_tujuan'] ?></td>
           </tr>
        <?php $counter++; endforeach; ?>
    </tbody>
</table>