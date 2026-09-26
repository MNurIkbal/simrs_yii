<?php
use Doco\components\DocoHelpers;
?>
<style type="text/css">
    .td{
        font-size = 2.5em;
    }
    .tbl-bordered {
    border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
        font-size:14;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
        font-size:13;
    }
</style>

<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <th>No</th>
            <th>Tanggal Pendaftaran</th>
            <th>No Rekam Medik</th>
            <th>Nama Pasien</th>
            <th>Instalasi / Ruangan</th>
            <th>Dokter</th>
            <th>Status Pasien</th>
       </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($detail as $key => $value) : ?>
            <tr>
                <td width="5%" style="text-align: center;"><?= $no++ ?></td>
                <td width="12%"><?= date('d M Y', strtotime($value['tgl_pendaftaran']))  ?></td>
                <td><?= $value['no_rekam_medik'] ?></td>
                <td><?= $value['nama_pasien'] ?></td>
                <td><?= $value['instalasi_nama'].' / '.$value['ruangan_nama'] ?></td>
                <td><?= $value['nama_pegawai'] ?></td>
                <td width="10%"><?= $value['status_konfirmasirm'] ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
