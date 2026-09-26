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
            <th rowspan="2">No.</th>
            <th rowspan="2">Tanggal Pendaftaran</th>
            <th rowspan="2">No Pendaftaran</th>   
            <th rowspan="2">Nomor Rekam Medik</th>
            <th rowspan="2">Nama Pasien</th>
            <th rowspan="2">Cara Bayar</th>
            <th rowspan="2">Penjamin</th>
            <th rowspan="2">Dokter</th>
            <th rowspan="2">Ruangan</th>
            <th rowspan="2">Kelas Pelayanan</th>
            <th colspan="2">Komponen Pendapatan</th>
            <th rowspan="2">Total (Rp.)</th>
       </tr>
       <tr>
           <th>Jasa Rumah Sakit (Rp.)</th>
           <th>Jasa Pelayanan (Rp.)</th>
       </tr>
    </thead>
    <tbody>
        <?php 
            $totalRs = $totalJp = 0;
            $no = 1; 
            foreach ($data as $key => $value) : 
                $totalRs += $value['jasa_rumahsakit'];
                $totalJp += $value['jasa_layanan'];
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date(' d M Y', strtotime($value['tgl_pendaftaran'])) ?></td>
                <td><?= $value['no_pendaftaran'] ?></td>
                <td><?= $value['no_rekam_medik'] ?></td>
                <td><?= $value['nama_pasien'] ?></td>
                <td><?= $value['carabayar_nama'] ?></td>
                <td><?= $value['penjamin_nama'] ?></td>
                <td><?= $value['nama_pegawai'] ?></td>
                <td><?= $value['ruangan_nama'] ?></td>
                <td><?= $value['kelaspelayanan_nama'] ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['jasa_rumahsakit'],0) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['jasa_layanan'],0) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['total'],0) ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10" style="text-align: center;"><b>TOTAL</b></td>
            <td style="text-align: right;"><b><?= DocoHelpers::formatNumber($totalRs) ?></b></td>
            <td style="text-align: right;"><b><?= DocoHelpers::formatNumber($totalJp) ?></b></td>
            <td style="text-align: right;"><b><?= DocoHelpers::formatNumber($totalRs + $totalJp) ?></b></td>
        </tr>
    </tfoot>
</table>
