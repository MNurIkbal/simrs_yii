<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-17 10:14:28
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
            <th>Tanggal Permintaan</th>
            <th>No. Permintaan</th>
            <th>Pembayaran</th>
            <th>Ruangan/Kamar</th>
            <th>No. Pendaftaran</th>
            <th>No. Rekam Medik</th>
            <th>Nama Pasien</th>
            <th>Jenis Kelamin</th>
            <th>Tanggal Lahir</th>
            <th>Jenis Diet</th>
            <th>Diagnosa</th>
            <th>Alergi</th>
            <th>Penjamin</th>
            <th>Cara Bayar</th>
            <th>Status</th>
       </tr>
    </thead>
    <tbody>
        <?php $counter = 1; foreach($data as $value) : ?>
            <?php $dataPembayaran = ($value['is_ditagihkan'] != null) && ($value['is_ditagihkan'] != '') ? 'Ditagihkan' : 'Tidak Ditagihkan'; ?>
           <tr>
                <td><?= $counter++; ?></td>
                <td><?= $value['tgl_permintaanmakan']; ?></td>
                <td><?= $value['no_permintaanmakan']; ?></td>
                <td><?= $dataPembayaran; ?></td>
                <td><?= $value['ruangan_nama'].'/'.$value['kamarruangan_nokamar'].' - '.$value['no_tempattidur'] ?></td>
                <td><?= $value['no_pendaftaran']; ?></td>
                <td><?= $value['no_rekam_medik']; ?></td>
                <td><?= $value['nama_pasien']; ?></td>
                <td><?= $value['jenis_kelamin']; ?></td>
                <td><?= date('d M Y',strtotime($value['tanggal_lahir'])); ?></td>
                <td><?= !empty($value['jenisdiet_nama']) ? $value['jenisdiet_nama'] : '-' ?></td>
                <td><?= $value['diagnosa']; ?></td>
                <td><?= !empty($value['riwayat_alergi']) ? $value['riwayat_alergi'] : '-' ?></td>
                <td><?= $value['penjamin_nama']; ?></td>
                <td><?= $value['carabayar_nama']; ?></td>
                <td><?= $value['status_permintaan']; ?></td>
           </tr>
        <?php endforeach; ?>
    </tbody>
</table>