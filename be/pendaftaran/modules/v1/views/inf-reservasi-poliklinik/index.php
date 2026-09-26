<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-03-29 17:00:45
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-29 17:11:45
 */
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
<table class="tbl-bordered" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>No. Antrian</th>
            <th>Info Pasien</th>
            <th>Tanggal Lahir</th>
            <th>Keterangan</th>
            <th>No. Asuransi</th>
            <th>Poli Tujuan</th>
            <th>Dokter</th>
            <th>Cara Bayar</th>
            <th>Penjamin</th>
            <th>Jam Pelayanan</th>
            <th>Jam Kunjungan</th>
            <th>Tanggal Daftar</th>
            <th>Tanggal Kunjungan</th>
            <th>Status Pendaftaran</th>
            <th>Jenis Pendaftaran</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 0;
        foreach ($data as $key => $value) {
            $no++;
            ?>
            <tr>
                <td><?=$no?></td>
                <td><?=$value->no_antrian?></td>
                <td><?=$value->no_pendaftaranol . '<br>' . $value->no_rekam_medik . ' - <br>' . $value->nama_depan . (($value->nama_pasien) ? $value->nama_pasien : $value->nama_pasien_ol) ?></td>
                <td><?=date('d M Y', strtotime($value->tanggal_lahir))?></td>
                <td><?=$value->keterangan?></td>
                <td><?=$value->no_asuransi?></td>
                <td><?=$value->ruangan_nama?></td>
                <td><?=$value->nama_pegawai?></td>
                <td><?=$value->carabayar_nama?></td>
                <td><?=$value->penjamin_nama?></td>
                <td><?=date('H:i', strtotime($value->penjamin_nama)).' - '.date('H:i', strtotime($value->jam_tutup))?></td>
                <td><?=date('H:i', strtotime($value->jam_kunjungan))?></td>
                <td><?=DocoHelpers::convDateTime($value->tgl_pendaftaran)?></td>
                <td><?=DocoHelpers::convDateTime($value->tgl_kunjungan)?></td>
                <td><?=$value->status_daftar?></td>
                <td><?=$value->jenis_reservasinama?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

