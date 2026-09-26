<?php

/**
 * @Author: Sunarko
 * @Date:   2018-08-09 17:45:12
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
<table style="font-size:12">
<?php if ($tgl_pendaftaran) { ?>
    <tr>
        <td>Tanggal Pendaftaran</td>
        <td>:</td>
        <td><?= $tgl_pendaftaran ?></td>
    </tr>
<?php } ?>
<?php if ($dokter_pemeriksa) { ?>
    <tr>
        <td>Dokter Pemeriksa</td>
        <td>:</td>
        <td><?= $dokter_pemeriksa ?></td>
    </tr>
<?php } ?>
<?php if ($ruangan_asal) { ?>
    <tr>
        <td>Asal Ruangan</td>
        <td>:</td>
        <td><?= $ruangan_asal ?></td>
    </tr>
<?php } ?>
<?php if ($carabayar) { ?>
    <tr>
        <td>Cara Bayar</td>
        <td>:</td>
        <td><?= $carabayar ?></td>
    </tr>
<?php } ?>
<?php if ($penjamin) { ?>
    <tr>
        <td>Penjamin</td>
        <td>:</td>
        <td><?= $penjamin ?></td>
    </tr>
<?php } ?>
</table><br>
<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <th>No</th>
            <th>No Antrian</th>
            <th>Tanggal Pendaftaran</th>
            <th>Ruangan Asal</th>
            <th>No. Pendaftaran</th>
            <th>No. Rekam Medik</th>
            <th>Nama Pasien</th>
            <th>Jenis Kelamin</th>
            <th>Cara  Bayar </th>
            <th>Penjamin </th>
            <th>Dokter Pemeriksa</th>
            <th>Status</th>
            <th>Catatan</th>
       </tr>
    </thead>
    <tbody>
        <?php $counter=1; foreach($data as $row) : ?>
           <tr>
                <td><?= $counter ?></td>
                <td><?= $row['no_antrian'] ?></td>
                <td><?= date('d-m-Y H:i:s', strtotime($row['tgl_pendaftaran'])) ?></td>
                <td><?= $row['ruangan_nama'] ?></td>
                <td><?= $row['no_pendaftaran'] ?></td>
                <td><?= $row['no_rekam_medik'] ?></td>
                <td><?= $row['nama_pasien'] ?></td>
                <td><?= $row['jenis_kelamin'] ?></td>
                <td><?= $row['carabayar_nama'] ?></td>
                <td><?= $row['penjamin_nama'] ?></td>
                <td><?= $row['nama_pegawai'] ?></td>
                <td><?= $row['status_periksa'] ?></td>
                <td><?= $row['alasan_batal'] ?></td>
           </tr>
        <?php $counter++; endforeach; ?>
    </tbody>
</table>