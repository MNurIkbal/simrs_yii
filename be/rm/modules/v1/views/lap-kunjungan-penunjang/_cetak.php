<?php
use Doco\components\DocoHelpers;
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
            <th>Tanggal Masuk</th>
            <th>No Pendaftaran</th>   
            <th>Nomor Rekam Medik</th>
            <th>Nama Pasien</th>
            <th>Unit</th>
            <th>Instalasi</th>
            <th>Penjamin</th>
            <th>Jenis Pemeriksaan</th>
            <th>Nama Pemeriksaan</th>
            <th>Jumlah</th>
       </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1; 
            foreach ($data as $key => $value) : 
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date(' d M Y H:i:s', strtotime($value['tglmasukpenunjang'])) ?></td>
                <td><?= $value['no_pendaftaran'] ?></td>
                <td><?= $value['no_rekam_medik'] ?></td>
                <td><?= $value['nama_pasien'] ?></td>
                <td><?= $value['unit'] ?></td>
                <td><?= $value['instalasi_nama'] ?></td>
                <td><?= $value['penjamin_nama'] ?></td>
                <td><?= $value['jeniskegiatantindakan_nama'] ?></td>
                <td><?= $value['daftartindakan_nama'] ?></td>
                <td><?= $value['jumlah_tindakan'] ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
