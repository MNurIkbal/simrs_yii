<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-27 13:28:26
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
    .tbl-bordered tr#colored {
        background-color: #fdfd96;
    }
</style>

<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <th>No</th>
            <th>Tgl. Masuk</th>
            <th>Info Pasien</th>
            <th>Jenis Kelamin</th>
            <th>Dokter DPJP</th>
            <th>Kasus Penyakit</th>
            <th>Info Kamar</th>
            <th>Kategori</th>
       </tr>
    </thead>
    <tbody>
        <?php $counter = 1; foreach($data as $value) : ?>
           <tr>
                <td><?= $counter++; ?></td>
                <td><?= $value['tgl_pendaftaran']; ?></td>
                <td><?= $value['no_rekam_medik'].'<br>'.$value['no_pendaftaran'].'<br>'.$value['nama_pasien']; ?></td>
                <td><?= $value['jenis_kelamin']; ?></td>
                <td><?= $value['nama_pegawai']; ?></td>
                <td><?= $value['jeniskasuspenyakit_nama']; ?></td>
                <td><?= $value['ruangan_nama']; ?></td>
                <td><?= $value['kategori']; ?></td>
           </tr>
        <?php endforeach; ?>
    </tbody>
</table>