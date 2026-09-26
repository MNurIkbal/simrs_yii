<?php

/**
 * @Author: rizal
 * @Date:   2018-11-23 10:20:03
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
    .tbl-bordered tr#colored {
        background-color: #fdfd96;
    }
</style>

<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <th>No</th>
            <th>Tgl Masuk</th>
            <th>Info Pasien</th>
            <!-- <th>Jenis Kelamin</th> -->
            <th>Tgl Lahir</th>
            <th>Diagnosa</th>
            <th>Jenis Diet</th>
            <th>Dokter DPJP</th>
            <th>Info Cara Bayar</th>
            <th>Hak Kelas / Kelas Saat Ini</th>
            <th>Info Kamar</th>
            <th>Status Rawat Inap</th>
            <th>Status Asesmen</th>
       </tr>
    </thead>
    <tbody>
        <?php $counter=1; foreach($data as $value) : ?>
           <tr id='<?= $value['skor'] >= 2 ? 'colored' : ''; ?>'>
                <td><?= $counter++; ?></td>
                <td><?= date('d-m-Y H:i:s', strtotime($value['tgl_admisi'])); ?></td>
                <td><?= $value['no_rekam_medik'] . '<br>' . $value['no_pendaftaran'] . '<br>' . $value['nama_pasien'] . ' (' . ($value['jenis_kelamin'] == 'Perempuan' ? 'P' : 'L') . ')'; ?></td>
                <!-- <td><?= $value['jenis_kelamin'] ?></td> -->
                <td><?= date('d-M-Y', strtotime($value['tanggal_lahir'])) ?></td>
                <td><?= $value['diagnosa_nama'] ? json_decode($value['diagnosa_nama'])->text : '' ?></td>
                <td><?= $value['jenisdiet_nama'] ?></td>
                <td><?= $value['dokter_admisi'] ?></td>
                <td><?= $value['carabayar_nama'] . '<br>' . $value['penjamin_nama']; ?></td>
                <td><?= $value['hak_kelas_nama'] . ' / ' . $value['kelas_pelayanan']; ?></td>
                <td><?= $value['ruangan_nama'] . '<br>' . $value['kamarruangan_nokamar'] . ' - ' . $value['no_tempattidur']; ?></td>
                <td><?= $value['stat_ranap']; ?></td>
                <td><?= $value['stat_asesmen_gizi']; ?></td>
           </tr>
        <?php endforeach; ?>
    </tbody>
</table>